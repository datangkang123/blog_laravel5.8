@extends('layouts.admin')

@section('title', __('Add Article'))

@section('css')
    <link rel="stylesheet" href="{{ asset('statics/editormd/css/editormd.min.css') }}">
    <style>
        #bjy-content{
            z-index: 1000;
        }
    </style>
@endsection

@section('nav', __('Add Article'))

@section('content')


    <ul id="myTab" class="nav nav-tabs bar_tabs">
        <li>
            <a href="{{ url('admin/article/index') }}">{{ __('Article List') }}</a>
        </li>
        <li class="active">
            <a href="{{ url('admin/article/create') }}">{{ __('Add Article') }}</a>
        </li>
    </ul>
    <form class="form-horizontal " action="{{ url('admin/article/store') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <table class="table table-striped table-bordered table-hover">
            <tr>
                <th width="7%">{{ __('Category') }}</th>
                <td>
                    <select class="form-control" name="category_id">
                        <option value="">{{ __('Select Category') }}</option>
                        @foreach($category as $v)
                            <option value="{{ $v->id }}" @if(old('category_id')) selected="selected" @endif>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>{{ __('Title') }}</th>
                <td>
                    <input class="form-control" type="text" name="title" value="{{ old('title') }}">
                </td>
            </tr>
            <tr>
                <th>{{ __('Author') }}</th>
                <td>
                    <input class="form-control" type="text" name="author" value="@if(empty(old('author'))){{ $author }}@else{{ old('author') }}@endif">
                </td>
            </tr>
            <tr>
                <th>{{ __('Keywords') }}</th>
                <td>
                    <input class="form-control" type="text" placeholder="{{ __('Separated by commas') }}" name="keywords" value="{{ old('keywords') }}">
                </td>
            </tr>
            <tr>
                <th>{{ __('Tag') }}</th>
                <td>
                    @foreach($tag as $v)
                        {{ $v['name'] }}<input class="bjy-icheck" type="checkbox" name="tag_ids[]" value="{{ $v['id'] }}" @if(in_array($v['id'], old('tag_ids', []))) checked="checked" @endif> &emsp;
                    @endforeach
                    <i class="fa fa-plus-square" style="font-size: 20px;cursor: pointer" data-toggle="modal" data-target="#bjy-tag-modal"></i>
                </td>
            </tr>
            <tr>
                <th>{{ __('Cover') }}</th>
                <td>
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 220px; height: 150px;">

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
                    <textarea class="form-control modal-sm" name="description" rows="7" placeholder="{{ __('If it is empty, intercept the first 300 words of the article content.') }}">{{ old('description') }}</textarea>
                </td>
            </tr>
            <tr>
                <th>{{ __('Content') }}</th>
                <td>
                    <div id="bjy-content">
                        <textarea name="markdown">{{ old('markdown') }}</textarea>
                    </div>
                </td>
            </tr>
            <tr>
                <th>{{ __('Topping') }}</th>
                <td>
                    {{ __('Yes') }} <input class="bjy-icheck" type="radio" name="is_top" value="1"> &emsp;&emsp;
                    {{ __('No') }} <input class="bjy-icheck" type="radio" name="is_top" value="0" checked="checked">
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

    {{--添加标签--}}
    <div class="modal fade" id="bjy-tag-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">{{ __('Add Tag') }}</h4>
                </div>
                <div class="modal-body text-center">
                    <form class="form-inline" role="form">
                        <input class="form-control bjy-tag-name" type="text" placeholder="{{ __('Name') }}">
                        <button type="button" class="btn btn-success js-add-tag">{{ __('Submit') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
				file   : "<input type='file' name='video' accept='audio/mp4, video/mp4'/><div class='progress progress-striped active'><div class='progress-bar progress-bar-success' id='jdt' aria-valuenow='25' aria-valuemin='0' aria-valuemax='100' style='width: 0%;'>0%</div></div>",
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
                    contentType: false,  //必须false才会自动加上正确的Content-Type
                    processData: false,  //必须false才会避开jQuery对 formdata 的默认处理
                    //进度条
                    xhr: function(){ //获取ajaxSettings中的xhr对象，为它的upload属性绑定progress事件的处理函数  
            		myXhr = $.ajaxSettings.xhr();  
                if(myXhr.upload){ //检查upload属性是否存在  
                    //绑定progress事件的回调函数  
                    myXhr.upload.addEventListener('progress',progressHandlingFunction, false);   
                }  
                return myXhr; //xhr对象返回给jQuery使用  
            },
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
                
			function progressHandlingFunction(e){
				var curr=e.loaded;
				var total=e.total;
				jdt = (curr / total * 100).toFixed(1) + '%'; //上传进度百分比，保留1位小数
				$("#jdt").css("width",jdt);
				$("#jdt").text(jdt);
			}
            }
            //提交保存前，删除上传框，防止写入数据库错误
			$("[type='submit']").click(function(){
				 $("[name='video']").remove();
			});

        }
            });
        });

        // 添加标签
        $('.js-add-tag').click(function () {
            var postData = {
                name: $('.bjy-tag-name').val()
            }
            $.ajax({
                type: 'POST',
                url: '{{ url('admin/tag/store') }}',
                dataType: 'json',
                data: postData,
                success: function (response) {
                    var redioStr = response.name+'<input class="bjy-icheck" type="checkbox" name="tag_ids[]" value="'+response.id+'" checked="checked"> &emsp;';
                    $('.fa-plus-square').before(redioStr);
                    icheckInit();
                    $('#bjy-tag-modal').modal('hide');
                },
                error: function (response) {
                    $.each(response.responseJSON.errors, function (k, v) {
                        alert(v);
                    })
                }
            })
        })
    </script>

@endsection


