@extends('layouts.admin')

@section('title', __('Edit Article'))

@section('css')
    <link rel="stylesheet" href="{{ asset('statics/editormd/css/editormd.min.css') }}">
    <style>
        #bjy-content{
            z-index: 9999;
        }
    </style>
@endsection

@section('nav', __('Edit Article'))

@section('content')

    <ul id="myTab" class="nav nav-tabs bar_tabs">
        <li>
            <a href="{{ url('admin/article/index') }}">{{ __('Article List') }}</a>
        </li>
        <li class="active">
            <a href="{{ url('admin/article/create') }}">{{ __('Edit Article') }}</a>
        </li>
    </ul>
    <form class="form-horizontal " action="{{ url('admin/article/update', [$article->id]) }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <table class="table table-striped table-bordered table-hover">
            <tr>
                <th width="7%">{{ __('Category') }}</th>
                <td>
                    <select class="form-control" name="category_id">
                        @foreach($category as $v)
                            <option value="{{ $v->id }}" @if($article->category_id === $v->id) selected="selected" @endif>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>{{ __('Title') }}</th>
                <td>
                    <input class="form-control" type="text" name="title" value="{{ $article->title }}">
                </td>
            </tr>
            <tr>
                <th>{{ __('Slug') }}</th>
                <td>
                    <input class="form-control" type="text" name="slug" value="{{ $article->slug }}">
                </td>
            </tr>
            <tr>
                <th>{{ __('Author') }}</th>
                <td>
                    <input class="form-control" type="text" name="author" value="{{ $article->author }}">
                </td>
            </tr>
            <tr>
                <th>{{ __('Keywords') }}</th>
                <td>
                    <input class="form-control" type="text" name="keywords" value="{{ $article->keywords }}">
                </td>
            </tr>
            <tr>
                <th>{{ __('Tag') }}</th>
                <td>
                    @foreach($tag as $v)
                        {{ $v['name'] }}<input class="bjy-icheck" type="checkbox" name="tag_ids[]" value="{{ $v['id'] }}" @if(in_array($v['id'], $article->tag_ids)) checked="checked" @endif> &emsp;
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>{{ __('Cover') }}</th>
                <td>
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 180px; height: 180px;">
                            <img src="{{ asset($article->cover) }}" alt="{{ __('Cover') }}">
                            <input type="hidden" name="cover" value="{{ $article->cover }}">
                        </div>
                        <div>
                            <span class="btn btn-default btn-file">
                                <span class="fileinput-new">{{ __('Select Image') }}</span>
                                <span class="fileinput-exists">{{ __('Change') }}</span>
                                <input type="file" name="cover">
                            </span>
                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">{{ __('Delete') }}</a>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>{{ __('Description') }}</th>
                <td>
                    <textarea class="form-control modal-sm" name="description" rows="7" placeholder="{{ __('If it is empty, intercept the first 300 words of the article content.') }}">{{ $article->description }}</textarea>
                </td>
            </tr>
            <tr>
                <th>{{ __('Content') }}</th>
                <td>
                    <div id="bjy-content">
                        <textarea name="markdown">{{ $article->markdown }}</textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <th>{{ __('Topping') }}</th>
                <td>
                    {{ __('Yes') }} <input class="bjy-icheck" type="radio" name="is_top" value="1" @if($article->is_top === 1) checked @endif> &emsp;&emsp;
                    {{ __('No') }} <input class="bjy-icheck" type="radio" name="is_top" value="0" @if($article->is_top === 0) checked @endif>
                </td>
            </tr>
            <tr>
                <th></th>
                <td>
                    <input class="btn btn-success" type="submit" value="{{ __('Submit') }}">
                </td>
            </tr>
        </table>
    </form>

@endsection

@section('js')
    <script src="{{ asset('statics/editormd/editormd.min.js') }}"></script>
    @if(config('app.locale') !== 'zh-CN')
        <script src="{{ asset('statics/editormd/languages/en.js') }}"></script>
    @endif
    <script>
        var testEditor;

        $(function() {
            // You can custom @link base url.
            editormd.urls.atLinkBase = "https://github.com/";

            testEditor = editormd("bjy-content", {
                autoFocus : false,
                width     : "100%",
                height    : 720,
                toc       : true,
                //atLink    : false,    // disable @link
                //emailLink : false,    // disable email address auto link
                todoList  : true,
                placeholder: "{{ __('Enter article content') }}",
                toolbarAutoFixed: false,
                path: '{{ asset('/statics/editormd/lib') }}/',  //你的path路径（原资源文件中lib包在我们项目中所放的位置）
                emoji: true,//emoji表情，默认关闭
                codeFold : true, //代码折叠功能
                toolbarIcons : ['undo', 'redo', 'bold', 'del', 'italic', 'quote', 'uppercase', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'list-ul', 'list-ol', 'hr', 'link', 'reference-link', 'image', 'code', 'code-block', 'table', 'emoji', 'html-entities', 'watch', 'pagebreak','testIcon','file'],
                
				toolbarIconsClass : {
				testIcon : "fa-gears"  // 指定一个FontAawsome的图标类
				},
				//视频上传按钮
				toolbarCustomIcons : {
				file   : "<input type='file' name='video' accept='audio/mp4, video/mp4'/>",
				//faicon : "<i class='fa fa-star' onclick='alert('faicon');'></i>"
				},
				
				lang : {
				toolbar : {
				file : "上传视频",
                testIcon : "插入视频链接",  // 自定义按钮的提示文本，即title属性
				}
			},
			
			// 自定义工具栏按钮的事件处理
			toolbarHandlers : {
            /**
             * @param {Object}      cm         CodeMirror对象
             * @param {Object}      icon       图标按钮jQuery元素对象
             * @param {Object}      cursor     CodeMirror的光标对象，可获取光标所在行和位置
             * @param {String}      selection  编辑器选中的文本
             */
				testIcon : function(cm, icon, cursor, selection) {

                //var cursor    = cm.getCursor();     //获取当前光标对象，同cursor参数
                //var selection = cm.getSelection();  //获取当前选中的文本，同selection参数
				//https://bk.dzbfsj.com/wp-content/uploads/2018/10/%E5%8C%86%E5%8C%86.mp4
                // 替换选中文本，如果没有选中文本，则直接插入<video src='movie.ogg' width='100%'' controls='controls'></video>
                cm.replaceSelection("<video src='" + selection + "' width='100%' controls poster='/uploads/images/bjt.jpg'></video>");
                // 如果当前没有选中的文本，将光标移到要输入的位置
                if(selection === "") {
				cm.setCursor(cursor.line, cursor.ch + 1);
                }

                // this == 当前editormd实例
                console.log("testIcon =>", this, cm, icon, cursor, selection);
            },
        },
                //开启图片上传，并设置上传的控制器
                imageUpload: true,
                imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
                imageUploadURL : '{{ url('admin/article/uploadImage') }}',
                
            //上传完成视频后
			onload : function(){
				$("[name='video']").bind('change', function(){
				uploadvideo();
            });
            
            function uploadvideo() { //  判断是否有选择上传文件
                var imgPath = $("[name='video']").val();
                if (imgPath == "") {
                    alert("请选择视频文件！");
                    return;
                }
                //判断上传文件的后缀名
                var strExtension = imgPath.substr(imgPath.lastIndexOf('.') + 1);
                if (strExtension != 'mp4') {
                    alert("请选择mp4格式的视频文件");
                    return;
                }
                //借助XMLHttpRequest Level 2  的FormData对象实现二进制文件上传
                var formData = new FormData();
                formData.append("video",$("[name='video']").get(0).files[0]);//添加文件到FormData对象***关键
                console.log(formData.get("video"));//通过get方法获得name为video元素的value值
                //上传视频文件
                $.ajax({
                    type: "POST",
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ url('admin/article/uploadvideo') }}',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        console.log(data.data[0]);
                        //上传成功，将视频地址插入html5代码中
                        var url = "<video src='" + data.data[0].path + "' width='100%' controls poster='/uploads/images/bjt.jpg'></video>";
                        console.log(url);
                        testEditor.cm.replaceSelection(url);//插入当前编辑器中
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("上传失败，请检查网络后重试");
                    }
                });
            }
            //提交保存前，删除上传框，防止写入数据库错误
			$("[type='submit']").click(function(){
				 $("[name='video']").remove();
			});

        }
            });
        });
    </script>

@endsection


