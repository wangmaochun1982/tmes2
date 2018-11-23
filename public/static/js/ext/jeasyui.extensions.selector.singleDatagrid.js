/**
* jQuery EasyUI 1.4.3
* Copyright (c) 2009-2015 www.jeasyui.com. All rights reserved.
*
* Licensed under the GPL license: http://www.gnu.org/licenses/gpl.txt
* To use it on other terms please contact us at info@jeasyui.com
* http://www.jeasyui.com/license_commercial.php
*
* jQuery EasyUI selector 扩展
* jeasyui.extensions.selector.singleDatagrid.js
* 开发 落阳
* 最近更新：2016-03-29
*
* 依赖项：
*   1、jquery.jdirk.js
*   2、jeasyui.extensions.dialog.sealed.sample.js （快速灵活打开dialog，需要该扩展支持）
*   3、jeasyui.extensions.toolbar.js、jeasyui.extensions.toolbar.css [可灵活依赖] （当需要额外工具条功能时，需要依赖）
*   4、jeasyui.extensions.base.isEasyUI.js、jeasyui.extensions.base.current.js [可灵活依赖] （当额外工具条需要按钮查询功能时，需要依赖）
*   5、jeasyui.extensions.selector.base.js、jeasyui.extensions.selector.css
*
* Copyright (c) 2016 Lixilin personal All rights reserved.
*/
(function ($) {

    $.util.namespace("$.easyui.showSelector.singleDatagrid");

    var self = $.easyui.showSelector.self.methods;

    function initialize(options) {
        var datagridOptions = options.datagridOptions ? options.datagridOptions : {};
        //计算 dialog 的最大和最小宽度
        var minDatagridWidth = self.getDataGridMinWidth(datagridOptions.pagination),
            diaMinWidth = minDatagridWidth + 20,
            diaRealWidth = options.width ? (options.width < diaMinWidth ? diaMinWidth : options.width) : diaMinWidth + 50;
        var opts = $.extend({
            title: "选择数据，" + (datagridOptions.singleSelect ? "单选" : "多选"),
        }, options, { width: diaRealWidth, minWidth: diaMinWidth });
        delete opts.datagridOptions;

        var value = self.getSelected(datagridOptions.singleSelect, opts.selected), tempData = self.getOriginalSelected(value);
		var dg = null,
            dia = $.easyui.showDialog($.extend({}, opts, {
                content: "<table class=\"grid-selector-container\"></table>",
                toolbar: "",
                onSave: function () {
                    if ($.isFunction(opts.onEnter)) {
                        return opts.onEnter.call(dg[0], value);
                    }
                }
            }));
        var container = dia.find(".grid-selector-container"), dg = container.addClass("grid-selector");

        $.util.delay(function () {
            var dgOpts = $.extend({ striped: true, checkOnSelect: true, selectOnCheck: true, rownumbers: true }, datagridOptions, {
                noheader: true, fit: true, border: false, doSize: true,
                toolbar: self.checkToolbar(opts.extToolbar, datagridOptions.toolbar) ? self.getToolbarDiv(datagridOptions.toolbar) : null,
                onSelect: function (index, row) { refreshValue(); },
                onUnselect: function (index, row) { refreshValue(); },
                onSelectAll: function (rows) { refreshValue(); },
                onUnselectAll: function (rows) { refreshValue(); },
                onLoadSuccess: function (data) {
                    if ($.isFunction(datagridOptions.onLoadSuccess)) {
                        datagridOptions.onLoadSuccess.apply(this, arguments);
                    }
                    if (!tempData) { return; }
                    if ($.util.likeArrayNotString(tempData)) {
                        $.each(tempData, function (i, val) {
                            dg.datagrid("selectRecord", val);
                        });
                    } else {
                        dg.datagrid("selectRecord", tempData);
                    }
                }
            }),
            refreshValue = function () {
                var tOpts = dg.datagrid("options");
                if (dgOpts.singleSelect) {
                    var row = dg.datagrid("getSelected");
                    value = row ? row : null;
                } else {
                    value = dg.datagrid("getSelections");
                }
            };
            dg.datagrid(dgOpts);
            if (datagridOptions.pagination) { self.removePaginationMessage(dg); }

            dia.selector = {};
            $.extend(dia.selector, { datagrid: dg });
        });

        return dia;
    }

    //  增加自定义扩展方法 $.easyui.showSelector.singleDatagrid；该方法弹出一个 包含一个 easyui-datagrid 控件的选择框窗口；
    $.easyui.showSelector.singleDatagrid = function (options) {
        options = options || {};
        var opts = $.extend({}, $.easyui.showSelector.singleDatagrid.defaults, options);

        return initialize(opts);
    };

    // 继承 $.easyui.showDialog 的属性，并增加了以下属性
    $.easyui.showSelector.singleDatagrid.defaults = $.extend({}, $.easyui.showDialog.defaults, {

        height: 360,
        minHeight: 360,
        title: "数据选择框",
        iconCls: "icon-hamburg-zoom",
        maximizable: true,
        collapsible: true,
        saveButtonText: "确定",
        saveButtonIconCls: "icon-ok",
        enableApplyButton: false,

        // Boolean 值，表示是否向datagrid扩展工具条；
        extToolbar: false,

        // 表示已选项，可以是String类型（多个则以英文逗号相连），也可以是Array类型。
        selected: null,

        //这是一个 function 对象，表示点击“确定”按钮时回调的函数；
        onEnter: function (selected) { },

        //这是一个 JSON-Object 对象，具体格式参考 easyui-datagrid 方法的参数 options 的格式；
        datagridOptions: {}
    });



})(jQuery);