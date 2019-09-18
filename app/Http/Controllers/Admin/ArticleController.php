<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\Store;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Category;
use App\Models\Config;
use App\Models\Tag;
use Baijunyao\LaravelUpload\Upload;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Article $articleModel)
    {
        $wd = $request->input('wd', '');

        if (empty($wd)) {
            $id = [];
        } else {
            $id = $articleModel->searchArticleGetId($wd);
        }

        $article = Article::with('category')
            ->orderBy('created_at', 'desc')
            ->when($wd, function ($query) use ($id) {
                return $query->whereIn('id', $id);
            })
            ->withTrashed()
            ->paginate(15);
        $assign = compact('article');

        return view('admin.article.index', $assign);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Category::all();
        $tag      = Tag::all();
        $author   = Config::where('name', 'AUTHOR')->value('value');
        $assign   = compact('category', 'tag', 'author');

        return view('admin.article.create', $assign);
    }

    /**
     * 配合editormd上传图片的方法
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage()
    {
        $result = Upload::image('editormd-image-file', 'uploads/article');
        //editor工具构造的file字段是editormd-image-file，如果需要处理图片，或上传视频，需要改/vendor/baijunyao/laravel-upload/src/upload.php文件
        if ($result['status_code'] === 200) {
            $data = [
                'success' => 1,
                'message' => $result['message'],
                'url'     => $result['data'][0]['path'],
            ];
        } else {
            $data = [
                'success' => 0,
                'message' => $result['message'],
                'url'     => '',
            ];
        }

        return response()->json($data);
    }
    
    //上传视频
        public function uploadvideo(Request $request)
    {
        $allowExtension = ['mp4','mov','mpg','mpeg','flv'];
        if (!$request->isMethod('POST')) { //判断是否是POST上传
			$data=[
			'status_code' => 500,
			'message' => '请求非法！'
		];
		return $data;
        }
        // 判断请求中是否包含name=video的文件
        $file = $request->file('video');
        if (!$file->isValid()) {
			$data=[
			'status_code' => 500,
			'message' => '上传的文件无效！'
		];
		return $data;
        }	
        	
		// 先去除两边空格
        $path = trim('uploads/article', '/');
        // 判断是否需要生成日期子目录
        $childPath = true;//设置是否要生成子目录
        $path = $childPath ? $path.'/'.date('Ymd') : $path;
        // 获取目录的绝对路径
        $publicPath = public_path($path.'/');
        // 如果目录不存在；先创建目录
        is_dir($publicPath) || mkdir($publicPath, 0755, true);
        $ext = $file->getClientOriginalExtension();//获取文件扩展名
		// 获取上传的文件名
		$oldName = $file->getClientOriginalName();
		// 获取文件后缀
		$extension = strtolower($file->getClientOriginalExtension());
		// 判断是否是允许的文件类型
		if (!empty($allowExtension) && !in_array($extension, $allowExtension)) {
			$data=[
                    'status_code' => 500,
                    'message' => $oldName . '的文件类型不被允许'
                ];
                return $data;
            }
		// 组合新的文件名
		$md5name = md5_file($file);//获取文件MD5值，防止重复上传
		$newName = $md5name . '.' . $extension;
		//$newName = uniqid() . '.' . $extension;//随机文件名
		if (file_exists($publicPath.$newName)){//如果文件已存在，则直接返回文件地址
			$success[] = [
				'name' => $oldName,
				'path' => '/'.$path.'/'.$newName
                ];
			}else{
		if (!$file->move($publicPath, $newName)) {  // 第一次上传，移动文件，判断是否失败
			$data=[
			'status_code' => 500,
			'message' => '保存文件失败'
			];
			return $data;
            } else {
			$success[] = [
				'name' => $oldName,
				'path' => '/'.$path.'/'.$newName
                ];
            }
          }
        //上传成功
        $data=[
            'status_code' => 200,
            'message' => '上传成功',
            'data' => $success
        ];
        return $data;
    }

    /**
     * 添加文章
     *
     * @param Store   $request
     * @param Article $article
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Store $request, Article $articleModel)
    {
        $data = $request->except('_token');

        if ($request->hasFile('cover')) {
            $result = Upload::file('cover', 'uploads/article');
            if ($result['status_code'] === 200) {
                $data['cover'] = $result['data'][0]['path'];
            }
        }

        $tag_ids = $data['tag_ids'];
        unset($data['tag_ids']);
        $article = Article::create($data);

        if ($article) {
            // 给文章添加标签
            $articleTag = new ArticleTag();
            $articleTag->addTagIds($article->id, $tag_ids);
        }

        return redirect('admin/article/index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $article          = Article::withTrashed()->find($id);
        $article->tag_ids = ArticleTag::where('article_id', $id)->pluck('tag_id')->toArray();
        $category         = Category::all();
        $tag              = Tag::all();
        $assign           = compact('article', 'category', 'tag');

        return view('admin.article.edit', $assign);
    }

    /**
     * 编辑文章
     *
     * @param Store      $request
     * @param Article    $articleModel
     * @param ArticleTag $articleTagModel
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Store $request, Article $articleModel, ArticleTag $articleTagModel, $id)
    {
        $data = $request->except('_token');

        // 上传封面图
        if ($request->hasFile('cover')) {
            $result = Upload::file('cover', 'uploads/article');
            if ($result['status_code'] === 200) {
                $data['cover'] = $result['data'][0]['path'];
            }
        }

        $tag_ids = $data['tag_ids'];
        unset($data['tag_ids']);
        $result = Article::find($id)->update($data);

        if ($result) {
            ArticleTag::where('article_id', $id)->forceDelete();
            $articleTagModel->addTagIds($id, $tag_ids);
        }

        return redirect()->back();
    }

    /**
     * 删除文章
     *
     * @param $id
     * @param Article $articleModel
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id, Article $articleModel, ArticleTag $articleTagModel)
    {
        Article::destroy($id);

        return redirect()->back();
    }

    /**
     * 恢复删除的文章
     *
     * @param         $id
     * @param Article $articleModel
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function restore($id, Article $articleModel, ArticleTag $articleTagModel)
    {
        Article::onlyTrashed()->find($id)->restore();

        return redirect()->back();
    }

    /**
     * 彻底删除文章
     *
     * @param            $id
     * @param Article    $articleModel
     * @param ArticleTag $articleTagModel
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function forceDelete($id, Article $articleModel, ArticleTag $articleTagModel)
    {
        Article::onlyTrashed()->find($id)->forceDelete();

        return redirect()->back();
    }

    /**
     * 批量替换功能视图
     *
     * @return \Illuminate\View\View
     */
    public function replaceView()
    {
        return view('admin.article.replaceView');
    }

    /**
     * 批量替换功能
     *
     * @param Request $request
     * @param Article $articleModel
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function replace(Request $request, Article $articleModel)
    {
        $search  = $request->input('search');
        $replace = $request->input('replace');
        $data    = Article::select('id', 'title', 'keywords', 'description', 'markdown', 'html')
            ->where('title', 'like', "%$search%")
            ->orWhere('keywords', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->orWhere('markdown', 'like', "%$search%")
            ->orWhere('html', 'like', "%$search%")
            ->get();
        foreach ($data as $k => $v) {
            Article::find($v->id)->update([
                'title'       => str_replace($search, $replace, $v->title),
                'keywords'    => str_replace($search, $replace, $v->keywords),
                'description' => str_replace($search, $replace, $v->description),
                'markdown'    => str_replace($search, $replace, $v->markdown),
                'html'        => str_replace($search, $replace, $v->html),
            ]);
        }

        return redirect()->back();
    }
}
