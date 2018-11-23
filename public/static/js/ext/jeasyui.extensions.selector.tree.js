/**
* jQuery EasyUI 1.4.3
* Copyright (c) 2009-2015 www.jeasyui.com. All rights reserved.
*
* Licensed under the GPL license: http://www.gnu.org/licenses/gpl.txt
* To use it on other terms please contact us at info@jeasyui.com
* http://www.jeasyui.com/license_commercial.php
*
* jQuery EasyUI selector 扩展
* jeasyui.extensions.selector.tree.js
* 开发 落阳
* 最近更新：2016-04-05
*
* 依赖项：
*   1、jquery.jdirk.js
*   2、jeasyui.extensions.dialog.sealed.sample.js （快速灵活打开dialog，需要该扩展支持）
*   3、jeasyui.extensions.selector.base.js、jeasyui.extensions.selector.css
*   4、jeasyui.extensions.base.loading.js、jeasyui.extensions.base.css （tree加载的遮罩层，需要该扩展支持）
*
* Copyright (c) 2016 Lixilin personal All rights reserved.
*/
(function ($) {

    $.util.namespace("$.easyui.showSelector.tree");

    var self = $.easyui.showSelector.self.methods;

    function initialize(options) {

        var treeOptions = options.treeOptions ? options.treeOptions : {};
        //计算 dialog 的最大和最小宽度
        var diaMinWidth = 250,
            diaRealWidth = options.width ? (options.width < diaMinWidth ? diaMinWidth : options.width) : diaMinWidth + 50;
        var opts = $.extend({
            title: "选择数据，" + (!treeOptions.checkbox ? "单选" : "多选"),
        }, options, { width: diaRealWidth, minWidth: diaMinWidth });
        delete opts.treeOptions;

        var value = self.getSelected(!treeOptions.checkbox, opts.selected), tempData = self.getOriginalSelected(value);

        var tree = null,
            dia = $.easyui.showDialog($.extend({}, opts, {
                content: "
",
                toolbar: "",
                onSave: function () {
                    if ($.isFunction(opts.onEnter)) {
                        return opts.onEnter.call(tree[0], value);
                    }
                }
            }));
        var panelBody = dia.dialog("body"), container = dia.find(".grid-selector-container"), tree = container.addClass("grid-selector");

        $.util.delay(function () {
            var treeOpts = $.extend({ animate: true, lines: true }, treeOptions,
                {
                    dnd: false,
                    onSelect: function (node) {
                        if ($.isFunction(treeOptions.onSelect)) {
                            treeOptions.onSelect.apply(this, arguments);
                        }
                        if (!treeOptions.checkbox)
                        { refreshValue(); }
                    },
                    onCheck: function (node, check) {
                        if ($.isFunction(treeOptions.onCheck)) {
                            treeOptions.onCheck.apply(this, arguments);
                        }
                        if (treeOptions.checkbox)
                        { refreshValue(); }
                    },
                    onBeforeLoad: function (node, param) {
                        var pass = true;
                        if ($.isFunction(treeOptions.onBeforeLoad)) {
                            pass = treeOptions.onBeforeLoad.apply(this, arguments);
                        }
                        if (pass) { $.easyui.loading({ locale: panelBody, msg: "数据加载中..." }) };
                        return pass;
                    },
                    onLoadSuccess: function (node, data) {
                        if ($.isFunction(treeOptions.onLoadSuccess)) {
                            treeOptions.onLoadSuccess.apply(this, arguments);
                        }
                        $.easyui.loaded(panelBody);
                        if (tempData && data.length > 0) {
                            if (treeOptions.checkbox) {
                                if ($.util.likeArrayNotString(tempData)) {
                                    $.each(tempData, function (i, val) {
                                        var tempNode = tree.tree("find", val);
                                        if (tempNode) { tree.tree("check", tempNode.target); }
                                    });
                                }
                            }
                            else {
                                if ($.util.isString(tempData)) {
                                    var tempNode = tree.tree("find", tempData);
                                    if (tempNode) { tree.tree("select", tempNode.target); }
                                }
                            }
                        }
                    },
                    onLoadError: function (p1, p2, p3) {
                        if ($.isFunction(treeOptions.onLoadError)) {
                            treeOptions.onLoadError.apply(this, arguments);
                        }
                        $.easyui.loaded(panelBody);
                    }
                }),
                refreshValue = function () {
                    if (!treeOpts.checkbox) {
                        var node = tree.tree("getSelected");
                        value = node ? node : null;
                    } else {
                        value = tree.tree("getChecked");
                    }
                };
            tree.tree(treeOpts);
            dia.extSelector = {};
            $.extend(dia.extSelector, { tree: tree });
        });

        return dia;
    }

    //  增加自定义扩展方法 $.easyui.showSelector.tree；该方法弹出一个 包含一个 easyui-tree 控件的选择框窗口；
    $.easyui.showSelector.tree = function (options) {
        options = options || {};
        var opts = $.extend({}, $.easyui.showSelector.tree.defaults, options);

        return initialize(opts);
    };

    // 继承 $.easyui.showDialog 的属性，并增加了以下属性
    $.easyui.showSelector.tree.defaults = $.extend({}, $.easyui.showDialog.defaults, {

        height: 360,
        minHeight: 360,
        title: "数据选择框",
        iconCls: "icon-hamburg-zoom",
        maximizable: true,
        collapsible: true,
        saveButtonText: "确定",
        saveButtonIconCls: "icon-ok",
        enableApplyButton: false,

        // 表示已选项，可以是String类型（多个则以英文逗号相连），也可以是Array类型。
        selected: null,

        //这是一个 function 对象，表示点击“确定”按钮时回调的函数；
        onEnter: function (selected) { },

        //这是一个 JSON-Object 对象，具体格式参考 easyui-tree 方法的参数 options 的格式；
        treeOptions: {}
    });


})(jQuery);