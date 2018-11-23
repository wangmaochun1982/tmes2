/**
* jQuery EasyUI 1.4.3
* Copyright (c) 2009-2015 www.jeasyui.com. All rights reserved.
*
* Licensed under the GPL license: http://www.gnu.org/licenses/gpl.txt
* To use it on other terms please contact us at info@jeasyui.com
* http://www.jeasyui.com/license_commercial.php
*
* jQuery EasyUI dialog 封装
* jeasyui.extensions.dialog.sealed.sample.js
* 开发 流云
* 由 落阳 整理
* 最近更新：2016-03-31
*
* 依赖项：
*   1、jquery.jdirk.js
*
* Copyright (c) 2015 ChenJianwei personal All rights reserved.
*/
(function ($) {

    $.util.namespace("$.easyui");


    // 以 easyui-dialog 方法在当前浏览器窗口的顶级(可访问)窗体中弹出对话框窗口；该函数定义如下参数：
    //     options: 一个 JSON Object，具体格式参考 easyui-dialog 官方 api 中的属性列表。
    //              该参数在 easyui-dialog 官方 api 所有原属性列表基础上，增加如下属性：
    //          autoDestroy:
    //          locale:
    //          topMost:
    //          enableSaveButton:
    //          enableCloseButton:
    //          enableClearButton:
    //          onSave:
    //          onClose:
    //          onClear:
    //          saveButtonIndex:
    //          closeButtonIndex:
    //          clearButtonIndex:
    //          saveButtonText:
    //          clearButtonText:
    //          closeButtonText:
    //          saveButtonIconCls:
    //          clearButtonIconCls:
    //          closeButtonIconCls:
    //          buttonsPlain:
    //              另，重写 easyui-dialog 官方 api 的 buttons 属性，使其不支持 String - jQuery Selector 格式。
    //              落阳注：
    //                  对 array 类型的 buttons 做了处理，使得 buttons 中每项按钮元素
    //                              支持 hidden 属性，该属性表示按钮是否隐藏，其属性值支持 Boolean 值和 function 对象两种类型；
    //                                      当属性值为 function 类型时，参数签名为 function(btn) ，其中 btn 表示当前按钮对象。
    //                              使 handle 方法的参数签名为 funciton(dialog, btn) ，其中 dialog 表示当前打开的 easyui-dialog 对象，btn 表示当前按钮对象；
    //                              支持 index 属性，该属性表示按钮的位置次序，按 index 数值从小到大排序，其属性值支持 Number 类型；
    //                  对 array 类型的 toolbar 做了处理，使得 toolbar 中每项按钮元素
    //                              使 handle 方法的参数签名为 funciton(dialog, btn) ，其中 dialog 表示当前打开的 easyui-dialog 对象，btn 表示当前按钮对象；
    // 备注：
    // 返回值：返回弹出的 easyui-dialog 的 jQuery 链式对象。
    $.easyui.showDialog = function (options, frame) {

        var opts = $.extend({}, $.easyui.showDialog.defaults, options || {});
        var currentFrame = arguments[1] ? arguments[1] : $.util.currentFrame;

        if (opts.topMost && $.easyui.showDialog.ntopSelf()) {
            return $.util.$.easyui.showDialog(opts, currentFrame);
        }

        //if (!opts.onApply) { opts.onApply = opts.onSave; }
        //if (!opts.onSave) { opts.onSave = opts.onApply; }

        var onBeforeClose = opts.onBeforeClose,
            onClose = opts.onClose,
            onBeforeDestroy = opts.onBeforeDestroy;
        opts.onBeforeClose = function () {
            if (opts.closeAnimation && opts.shadow) {
                var shadow = $(this).dialog("shadow");
                if (shadow) {
                    shadow.hide();
                }
            }
            var ret = $.isFunction(onBeforeClose)
                ? onBeforeClose.apply(this, arguments)
                : undefined;
            return ($.isFunction($.fn.dialog.defaults.onBeforeClose) && $.fn.dialog.defaults.onBeforeClose.apply(this, arguments) == false)
                ? false
                : ret;
        };
        opts.onClose = function () {
            if ($.isFunction(onClose)) {
                onClose.apply(this, arguments);
            }
            if ($.isFunction($.fn.dialog.defaults.onClose)) {
                $.fn.dialog.defaults.onClose.apply(this, arguments);
            }
            if (opts.autoDestroy) {
                $(this).dialog("destroy");
            }
        };
        opts.onBeforeDestroy = function () {
            var ret = $.isFunction(onBeforeDestroy)
                ? onBeforeDestroy.apply(this, arguments)
                : undefined;
            return ($.isFunction($.fn.dialog.defaults.onBeforeDestroy) && $.fn.dialog.defaults.onBeforeDestroy.apply(this, arguments) == false)
                ? false
                : ret;
        };
        opts.closed = true;

        if (opts.locale) {
            opts.inline = true;
        }
        var t = $("<div></div>").appendTo(opts.locale ? opts.locale : "body");

        if ($.isArray(opts.toolbar)) {
            opts.toolbar = $.array.map(opts.toolbar, function (val) {
                return $.extend({}, val);
            });
            $.each(opts.toolbar, function (index, item) {
                var handler = item.handler;
                if ($.isFunction(handler)) {
                    item.handler = function () {
                        handler.call(this, t[0], item)
                    };
                }
            });
            if (!opts.toolbar.length) { opts.toolbar = null; }
        }

        var save =
            {
                id: $.util.guid("N") + "_save",
                text: opts.saveButtonText,
                iconCls: opts.saveButtonIconCls,
                index: opts.saveButtonIndex,
                hidden: opts.enableSaveButton ? false : true,
                handler: function (dia, btn) {
                    if ($.isFunction(opts.onSave) && opts.onSave.call(this, dia, btn) == false) {
                        return false;
                    }
                    $.util.delay(function () { $(dia).dialog("close"); });
                }
            },
            close = {
                id: $.util.guid("N") + "_close",
                text: opts.closeButtonText,
                iconCls: opts.closeButtonIconCls,
                index: opts.closeButtonIndex,
                hidden: opts.enableCloseButton ? false : true,
                handler: function (dia, btn) {
                    $(dia).dialog("close");
                }
            },
            clear = {
                id: $.util.guid("N") + "_clear",
                text: opts.clearButtonText,
                iconCls: opts.clearButtonIconCls,
                index: opts.clearButtonIndex,
                hidden: opts.enableClearButton ? false : true,
                handler: function (dia, btn) {
                    if ($.isFunction(opts.onClear) && opts.onClear.call(this, dia, btn) == false) {
                        return false;
                    }
                    $.util.delay(function () { $(dia).dialog("close"); });
                    //$(this).linkbutton("disable");
                }
            },
            buttons = [save, close, clear];

        opts.buttons = $.isArray(opts.buttons)
            ? $.array.map(opts.buttons, function (val) { return $.extend({}, val) })
            : [];
        $.array.merge(opts.buttons, buttons);

        opts.buttons = $.array.filter(opts.buttons, function (val) {
            return $.util.parseFunction(val.hidden, [val]) ? false : true;
        });
        $.each(opts.buttons, function (index, item) {
            var handler = item.handler;
            if ($.isFunction(handler)) {
                item.handler = function () {
                    handler.call(this, t[0], item);
                };
            }
        });
        if (opts.buttons.length) {
            $.array.sort(opts.buttons, function (a, b) {
                return ($.isNumeric(a.index) ? a.index : 0) - ($.isNumeric(b.index) ? b.index : 0);
            });
        } else {
            opts.buttons = null;
        }

        var topts,
            btns,
            toolbuttons;
        if (opts.href) {
            var onBeforeLoad = opts.onBeforeLoad || $.fn.dialog.defaults.onBeforeLoad,
                onLoad = opts.onLoad || $.fn.dialog.defaults.onLoad;
            opts.onBeforeLoad = function (param) {
                if ($.isFunction(onBeforeLoad)) {
                    onBeforeLoad.apply(this, arguments);
                }
                btns.linkbutton("disable");
                toolbuttons.attr("disabled", "disabled")
                topts.onBeforeLoad = onBeforeLoad;
            };
            opts.onLoad = function () {
                if ($.isFunction(onLoad)) {
                    onLoad.apply(this, arguments);
                }
                btns.linkbutton("enable");
                toolbuttons.removeAttr("disabled");
                topts.onLoad = onLoad;
            };
        }
        topts = t.dialog(opts).dialog("options");
        var state = $.data(t[0], "dialog"),
            dialog = t.dialog("dialog"),
            buttonbar = dialog.children(".dialog-button");

        btns = buttonbar.children("a");
        toolbuttons = t.dialog("header").find(".panel-tool a");

        var closeButton = btns.filter("#" + close.id),
            saveButton = btns.filter("#" + save.id),
            clearButton = btns.filter("#" + clear.id);
        save.target = saveButton[0];
        close.target = closeButton[0];
        clear.target = clearButton[0];

        $.extend(state, {
            saveButton: save,
            closeButton: close,
            clearButton: clear,
            save: function () { save.handler.call(save.target, t[0], save); },
            close: function () { close.handler.call(close.target, t[0], close); },
            clear: function () { clear.handler.call(clear.target, t[0], clear); }
        });
        $.extend(t, {
            state: state,
            saveButton: save,
            closeButton: close,
            clearButton: clear,
            save: state.save,
            close: state.close,
            clear: state.clear
        });

        if ($.type(topts.buttonsPlain) == "boolean") {
            $.each(btns, function (i, el) {
                setPlain(this, topts.buttonsPlain);
            });
        }
        t.dialog("open");

        return t;
    };


    $.easyui.showDialog.ntopSelf = function () {
        return !$.util.isUtilTop && $.util.$.easyui && $.util.$.easyui.showDialog;
    };

    // 该字段仅作为内部数据标识字段，请不要作为公开 API 使用。
    $.easyui.showDialog.caches = $.easyui.showDialog.ntopSelf() && $.util.$.easyui.showDialog.caches
        ? $.util.$.easyui.showDialog.caches
        : [];


    // 设置 linkbutton 的 plain 状态
    function setPlain(target, plain) {
        var t = $(target),
            state = $.data(target, "linkbutton"),
            opts = state.options;
        opts.plain = plain ? true : false;
        if (opts.plain) {
            t.addClass("l-btn-plain");
        } else {
            t.removeClass("l-btn-plain");
        }
    };


    // 定义 $.easyui.showDialog 方法打开 easyui-dialog 窗体的默认属性。
    // 备注：该默认属性定义仅在方法 $.easyui.showDialog 中被调用。
    $.easyui.showDialog.defaults = {
        title: "新建对话框",
        iconCls: "icon-standard-application-form",
        width: 600,
        height: 360,
        modal: true,
        collapsible: false,
        maximizable: false,
        closable: true,
        draggable: true,
        resizable: true,
        shadow: true,
        minimizable: false,
        toolbar: null,
        buttons: null,
        href: null,



        // 表示弹出的 easyui-dialog 窗体是否在关闭时自动销毁并释放浏览器资源；
        // Boolean 类型值，默认为 true。
        autoDestroy: true,

        // 表示将要打开的 easyui-dialog 的父级容器；可以是一个表示 jQuery 元素选择器的表达式字符串，也可以是一个 html-dom 或 jQuery-dom 对象。
        // 注意：如果为 null 或者 undefined 则表示父级容器为 body 标签。
        locale: null,

        // 是否在顶级窗口打开此 easyui-dialog 组件。
        topMost: false,


        // 是否启用保存按钮，保存按钮点击后会关闭模式对话框
        enableSaveButton: true,

        // 是否启用清除按钮
        enableClearButton: true,

        // 是否启用关闭按钮
        enableCloseButton: true,

        // 保存按钮的顺序索引号
        saveButtonIndex: 101,

        // 清除按钮的顺序索引号
        clearButtonIndex: 102,
		
        // 关闭按钮的顺序索引号
        closeButtonIndex: 103, 

        // 点击保存按钮触发的事件，如果该事件函数返回 false，则点击保存后窗口不关闭。
        // 该事件函数签名为 function (d) { }，其中 this 指向按钮本身的 DOM 对象，参数 d 表示当前 easyui-dialog 窗体 DOM 对象。
        onSave: null,

        // 点击应用按钮触发的事件，如果该事件函数返回 false，则点击应用后该按钮不被自动禁用。
        // 该事件函数签名为 function (d) { }，其中 this 指向按钮本身的 DOM 对象，参数 d 表示当前 easyui-dialog 窗体 DOM 对象。
        onClear: null,

        // 关闭窗口时触发的事件，同 easyui-dialog 的默认事件 onClose。
        onClose: null,

        // 保存按钮的文字内容
        saveButtonText: "确定",

        // 关闭按钮的文字内容
        closeButtonText: "取消",

        // 应用按钮的文字内容
        clearButtonText: "清除",

        // 保存按钮的图标样式
        saveButtonIconCls: "icon-save",

        // 应用按钮的图标样式
        clearButtonIconCls: "icon-application_form_delete",

        // 关闭按钮的图标样式
        closeButtonIconCls: "icon-cancel",

        // 底部按钮栏的所有按钮是否全部设置 plain 属性为 true 或者 false。
        buttonsPlain: false
    };

})(jQuery);