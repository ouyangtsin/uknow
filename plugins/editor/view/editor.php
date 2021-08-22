<div class="uk-editor">
    <textarea style="display: none;" id="content" name="{$name}">{$value}</textarea>
    <div id="editor"></div>
</div>
<link rel="stylesheet" href="/static/plugins/editor/css/editor.css">
<link rel="stylesheet" href="/static/plugins/editor/fonts/fonts.css">
<script src="/static/plugins/editor/js/editor.js"></script>
<script type="text/javascript">
    var editorPluginsExt = {
        attach: {
            init: function (editorSelector) {
                $(editorSelector + " .w-e-toolbar").append('<div class="w-e-menu"><input id="upload-file" type="file" style="display:none;" onchange="editorPluginsExt.attach.callback()" /><a class="we-upload-file" href="javascript:;" onclick="editorPluginsExt.attach.upFile()"><i title="上传附件" class="w-e-icon-upload2"></i></a></div>');
            },
            upFile: function () {
                $("#upload-file").click();
            },
            callback: function () {
                const $file = $('#upload-file');
                const fileElem = $file[0];
                const fileList = fileElem.files;
                const uploadImg = editor.uploadImg;
                const url = '';
                uploadImg.uploadFile(fileList, url);
            }
        },
        fullscreen: {
            FullEditor: {},
            init: function (editor) {
                let id = editor.id;
                editorPluginsExt.fullscreen.FullEditor[id] = editor;
                toolbar = editor.$toolbarElem[0];
                $(toolbar).append('' +
                    '<div class="w-e-menu btn-fullscreen" title="全屏" onclick="editorPluginsExt.fullscreen.run(\'' + id + '\')">' +
                    '<i class="icon-fullscreen"></i> ' +
                    '</div>'
                );
            },
            run: function (id) {
                let editor = editorPluginsExt.fullscreen.FullEditor[id];
                let container = $(editor.toolbarSelector);
                container.toggleClass('fullscreen-editor');
                $('#main_header').toggle();
            }
        },
        viewSource: {
            SourceEditor: {},
            init: function (editor) {
                let id = editor.id;
                editor.isHTML = false;
                editorPluginsExt.viewSource.SourceEditor[id] = editor;
                toolbar = editor.$toolbarElem[0];
                $(toolbar).append("" +
                    "<div class='w-e-menu btn-viewSource' title='查看源码' onclick='editorPluginsExt.viewSource.run(\"" + id + "\")'>" +
                    "<i class='icon-code'></i>" +
                    "</div>"
                );
            },
            run: function (id) {
                let editor = editorPluginsExt.viewSource.SourceEditor[id];
                let container = $(editor.toolbarSelector);
                editor.isHTML = !editor.isHTML;
                let _source = editor.txt.html();
                toolbar = editor.$toolbarElem[0];
                if (editor.isHTML) {
                    _source = _source.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/ /g, "&nbsp;");
                    $(toolbar).find('.btn-viewSource').css({"display": ""});
                } else {
                    _source = editor.txt.text().replace(/&lt;/ig, "<").replace(/&gt;/ig, ">").replace(/&nbsp;/ig, " ");
                    editor.change && editor.change();
                }
                container.toggleClass('view-source-editor');
                editor.txt.html(_source);
            }
        }
    };
    var E = wangEditor;
    window.wangEditor = wangEditor;
    var $text1 = $('#content');
    var content=$text1.val();
    var editor = new E('#editor');
    window.editor = editor;
    editor.customConfig.codeType={
        title:"选择代码类型:",
        type:[
            "Bash/Shell","C/C++","PHP","C#","JAVA","CSS","SQL","HTML"
        ]
    };

    editor.customConfig.onchangeTimeout = 1;
    editor.customConfig.uploadImgTimeout = parseInt("{$config['timeout']}");
    editor.customConfig.uploadImgMaxSize = parseInt("{$config['fileMaxSize']}")*1024*1024;

    editor.customConfig.customAlert = function (info) {
        layer.msg(info)
    };

    //上传字段
    editor.customConfig.uploadFileName = 'uk-upload-file';

    //上传图片
    editor.customConfig.uploadImgServer = "/api/upload/index?upload_type=img&path={$cat|default='common'}";
    editor.customConfig.uploadImgHooks = {
        fail: function (xhr, editor, result) {
            if(result.error){
                layer.msg(result.msg);
                return false;
            }
        },
        error: function error(xhr, editor) {},
    };

    //上传视频
    editor.customConfig.uploadVideoServer = "/api/upload/index?upload_type=file&path={$cat|default='common'}";
    editor.customConfig.uploadVideoHooks = {
        customInsert: function (insertVideo, result) {
            if(result.code)
            {
                insertVideo(result.data);
                layer.msg(result.msg);
            }else{
                layer.msg(result.msg);
            }
        }
    };
    editor.customConfig.onchange = function (html)
    {
        $text1.val(html);
    };
    editor.create();
    editor.txt.html(content);
    editorPluginsExt.attach.init('#editor');
    editorPluginsExt.fullscreen.init(editor);
</script>