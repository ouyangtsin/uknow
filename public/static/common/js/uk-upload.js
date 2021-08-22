const Upload = {
    api: {
    getAllowMime: function () {
        var allowMime = {};
        $.ajax({
            url: '/api/upload/index',
            dataType: 'json',
            success: function (res) {
                allowMime = JSON.parse(res);
            }
        });
        window.allowMime = allowMime;
        return allowMime;
    },

    check: function (data) {
        var checkInfo = {};
        $.ajax({
            url: '/api/upload/check',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (res) {
                checkInfo = {
                    fileSize: res.fileSize,
                    fileExt: res.fileExt
                };
            }
        });
        window.checkInfo = checkInfo;
        return checkInfo;
    },

    image: function (element, callback, option) {
        const checkInfo = Upload.api.check({accept: 'images'});
        const path = $(element).attr('data-path');
        const type = $(element).attr('data-type');
        const config = {
            elem: element,
            url: baseUrl + "/api/upload/upload?path=" + path + "&type=" + type,
            data: {},
            drag: true,
            field: 'uk-upload-file',
            auto: true, //选择文件后不自动上传,
            count: {total: 0, uploaded: 0},
            multiple: $(element).data('multiple') ? $(element).data('multiple') : 0,
            number: $(element).data('number') ? $(element).data('number') : 0,
            size: checkInfo.fileSize,
            exts: checkInfo.fileExt,
            accept: 'images',
            //input: $('[name="_field_"]'.replace('_field_', option.field)),
            headers: '',
            before: function (obj) {
                //layer.load();
            },
            choose: function (object) {
                const files = this.files = object.pushFile();
                object.preview(function (index, file, result) {
                    if (config.size > 0 && files[index].size > config.size) {
                        delete files[index];
                        return UK.api.error('文件大小超出上传限制！');
                    }
                    UK.api.success('上传进度 <span data-upload-progress>0%</span>');
                    config.count.total++;
                    delete files[index];
                });
            },
            progress: function (number, elem) {
                $(element).triggerHandler('upload.progress', {event: arguments[2], file: arguments[3]});
                $('[data-upload-progress]').html(number + '%');
            },
            done: function (res, index, upload) {
                if (typeof callback === 'function') {
                    callback(res, index, upload);
                } else {
                    if (res.code) { //上传成功
                        delete this.files[index]; //删除文件队列已经上传成功的文件
                        return toastr.success(res.msg)
                    } else {
                        toastr.error(res.msg);
                    }
                    this.error(res, index, upload);
                }
            },
            allDone: function () {
                option.element.triggerHandler('upload.complete', {});
                option.element.html(option.element.data('html'));
            },
            error: function (res, index, upload) {
            }
        };
        const upload = layui.upload;
        option = $.extend(config, option || {});
        const uploader = upload.render(option);
    },

    upload: function (element, callable, option) {
        /*! 初始化变量 */
        option = {
            element: $(element),
            exts: [],
            mimes: [],
            files: {},
            cache: {},
            load: 0
        };
        option.count = {total: 0, uploaded: 0};
        option.size = option.element.data('size') || 0;
        option.safe = option.element.data('safe') ? 1 : 0;
        option.hload = option.element.data('hide-load') ? 1 : 0;
        option.field = option.element.data('field') || 'uk-file';
        option.input = $('[name="_field_"]'.replace('_field_', option.field));
        option.uptype = option.safe ? 'local' : option.element.attr('data-upload-type') || '';
        option.multiple = option.element.data('multiple') > 0;
        option.accept = option.element.data('accept') ? option.element.data('accept') : 'file';
        //上传初始化
        var checkInfo = Upload.api.check({accept: option.accept});
        option.exts = checkInfo.fileExt;
        option.size = checkInfo.fileSize;
        /*! 初始化上传组件 */
        option.uploader = layui.upload.render({
            auto: false,
            elem: element,
            accept: 'file',
            multiple: option.multiple,
            exts: option.exts,
            //acceptMime: option.mimes.join(','),
            choose: function (object) {
                var index;
                for (index in (option.files = object.pushFile())) {
                    if (option.size > 0 && option.files[index].size > option.size) {
                        return delete option.files[index], UK.api.error('文件大小超出上传限制！');
                    }
                    option.load = option.hload || UK.api.success('上传进度 <span data-upload-progress>0%</span>');
                    option.count.total++, option.files[index].index = index, option.cache[index] = option.files[index], delete option.files[index];
                    Upload.api.md5file(option.cache[index]).then(function (file) {
                        option.element.triggerHandler('upload.hash', file);
                        $.ajax("/api/upload/state", {
                            data: {
                                xkey: file.xkey,
                                uptype: option.uptype,
                                safe: option.safe,
                                name: file.name
                            },
                            method: 'post',
                            success: function (ret) {
                                file.xurl = ret.data.url;
                                if (parseInt(ret.code) === 404) {
                                    option.uploader.config.url = ret.data.server;
                                    option.uploader.config.data.key = ret.data.xkey;
                                    option.uploader.config.data.safe = ret.data.safe;
                                    option.uploader.config.data.uptype = ret.data.uptype;
                                    if (ret.data.uptype === 'qiniu') {
                                        option.uploader.config.data.token = ret.data.token;
                                    } else if (ret.data.uptype === 'alioss') {
                                        option.uploader.config.data.policy = ret.data.policy;
                                        option.uploader.config.data.signature = ret.data.signature;
                                        option.uploader.config.data.OSSAccessKeyId = ret.data.OSSAccessKeyId;
                                        option.uploader.config.data.success_action_status = 200;
                                        option.uploader.config.data['Content-Disposition'] = 'inline;filename=' + encodeURIComponent(file.name);
                                    }
                                    object.upload(file.index, file);
                                } else if (parseInt(ret.code) === 200) {
                                    option.uploader.config.done({
                                        uploaded: true,
                                        url: file.xurl
                                    }, file.index);
                                } else {
                                    UK.api.error(ret.info || ret.error.message || '文件上传出错！');
                                }
                            }
                        });
                    });
                }
            },
            progress: function (number) {
                option.element.triggerHandler('upload.progress', {event: arguments[2], file: arguments[3]});
                $('[data-upload-progress]').html(number + '%');
            },
            done: function (ret, index) {
                option.element.triggerHandler('upload.done', {file: option.cache[index], data: ret});
                if (++option.count.uploaded >= option.count.total && !option.hload) layer.close(option.load);
                if (typeof ret.code === 'number' && parseInt(ret.code) === 0) return UK.api.error(ret.info || '文件上传失败！');
                if (typeof option.cache[index].xurl !== 'string') return UK.api.error('无效的文件对象！');
                if (typeof ret.uploaded === 'undefined' && typeof option.cache[index].xurl === 'string') {
                    ret = {uploaded: true, url: option.cache[index].xurl};
                }
                if (ret.uploaded) {
                    if (typeof callable === 'function') {
                        callable.call(option.element, ret.url, option.cache[index]);
                    } else {
                        option.input.val(ret.url).trigger('change');
                    }
                } else {
                    UK.api.error(ret.info || ret.error.message || '文件上传出错！');
                }
            },
            allDone: function () {
                option.element.triggerHandler('upload.complete', {});
                option.element.html(option.element.data('html'));
                //option.hload || $.msg.close(option.load);
            }
        });
    },

    md5file: function (file) {
        var deferred = $.Deferred();
        file.xext = file.name.indexOf('.') > -1 ? file.name.split('.').pop() : 'tmp';

        /*! 兼容不能计算文件 HASH 的情况 */
        if (!window.FileReader) return $.when((function (date, chars) {
            date = new Date(), chars = 'abcdefhijkmnprstwxyz0123456789';
            this.xmd5 = '' + date.getFullYear() + (date.getMonth() + 1) + date.getDay() + date.getHours() + date.getMinutes() + date.getSeconds();
            while (this.xmd5.length < 32) this.xmd5 += chars.charAt(Math.floor(Math.random() * chars.length));
            return setFileXdata(file, this.xmd5), deferred.resolve(file, file.xmd5, file.xkey), deferred;
        }).call(this));

        /*! 读取文件并计算 HASH 值 */
        var spark = new SparkMD5.ArrayBuffer();
        var slice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice;
        file.chunk_idx = 0, file.chunk_size = 2097152, file.chunk_total = Math.ceil(this.size / this.chunk_size);
        return $.when(loadNextChunk(file));

        function setFileXdata(file, xmd5) {
            file.xmd5 = xmd5, file.xkey = file.xmd5.substr(0, 2) + '/' + file.xmd5.substr(2, 30) + '.' + file.xext;
            return delete file.chunk_idx, delete file.chunk_size, delete file.chunk_total, file;
        }

        function loadNextChunk(file) {
            this.reader = new FileReader();
            this.reader.onload = function (event) {
                spark.append(event.target.result);
                if (++file.chunk_idx < file.chunk_total) {
                    loadNextChunk(file);
                } else {
                    setFileXdata(file, spark.end());
                    deferred.resolve(file, file.xmd5, file.xkey);
                }
            };
            this.reader.onerror = function () {
                deferred.reject();
            };
            this.start = file.chunk_idx * file.chunk_size;
            this.loaded = (this.start + file.chunk_size >= file.size) ? file.size : this.start + file.chunk_size;
            this.reader.readAsArrayBuffer(slice.call(file, this.start, this.loaded));
            return deferred.notify(file, (this.loaded / file.size * 100).toFixed(2)), deferred;
        }
    }
},
};
window.Upload = Upload;