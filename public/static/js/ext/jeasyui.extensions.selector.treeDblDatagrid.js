/**
* jQuery EasyUI 1.4.3
* Copyright (c) 2009-2015 www.jeasyui.com. All rights reserved.
*
* Licensed under the GPL license: http://www.gnu.org/licenses/gpl.txt
* To use it on other terms please contact us at info@jeasyui.com
* http://www.jeasyui.com/license_commercial.php
*
* jQuery EasyUI selector 扩展
* jeasyui.extensions.selector.treeDblDatagrid.js
* 开发 落阳
* 最近更新：2016-04-08
*
* 依赖项：
*   1、jquery.jdirk.js
*   2、jeasyui.extensions.dialog.sealed.sample.js （快速灵活打开dialog，需要该扩展支持）
*   3、jeasyui.extensions.toolbar.js、jeasyui.extensions.toolbar.css [可灵活依赖] （当需要额外工具条功能时，需要依赖）
*   4、jeasyui.extensions.base.isEasyUI.js、jeasyui.extensions.base.current.js [可灵活依赖] （当额外工具条需要按钮查询功能时，需要依赖）
*   5、jeasyui.extensions.base.loading.js、jeasyui.extensions.base.css （tree加载的遮罩层，需要该扩展支持）
*   6、jeasyui.extensions.selector.base.js、jeasyui.extensions.selector.css
*
* Copyright (c) 2016 Lixilin personal All rights reserved.
*/
(function ($) {

    $.util.namespace("$.easyui.showSelector.treeDblDatagrid");

    var self = $.easyui.showSelector.self.methods;
    $.extend(self, {

        //  根据 tree 节点对象组装查询参数的默认方式
        paramsBuilderForTree: function (node) { return { nodeId: node.id }; },

        //  根据 tree 节点对象组装的查询参数去筛选 datagrid 数据的默认方式
        dataQueryerForTree: function (params, datagrid) { datagrid.datagrid("load", params); },

        //  待选datagrid的选中行操作
        selectRow: function (dgOpts1, row, dg2, refresh) {
            var idField = dgOpts1.idField, idValue = idField ? row[idField] : row;
            var isExists = dg2.datagrid("getRowIndex", idValue) > -1;
            if (!isExists) {
                if (dgOpts1.singleSelect) {
                    dg2.datagrid("loadData", []);
                }
                dg2.datagrid("appendRow", row);
                if ($.isFunction(refresh)) { refresh(); }
            }
        },

        //  待选datagrid的取消选中行操作
        unselectRow: function (dgOpts1, row, dg2, refresh) {
            var idField = dgOpts1.idField, idValue = idField ? row[idField] : row;
            var index = dg2.datagrid("getRowIndex", idValue);
            if (index > -1) {
                dg2.datagrid("deleteRow", index);
                if ($.isFunction(refresh)) { refresh(); }
            }
        },

        //  待选datagrid的全部选中操作
        selectAllRow: function (idField, datagrid1, refresh) {
            var rows = datagrid1.datagrid("getRows"), selectedRows = datagrid1.datagrid("getSelections");
            if (rows.length == selectedRows.length) { return; }
            var selectedRowsIDs = $.array.map(selectedRows, function (row) { return idField ? row[idField] : undefined; }),
                unselectedRows = rows.length > 0 ? $.array.filter(rows, function (row) { return idField ? !$.array.contains(selectedRowsIDs, row[idField]) : true; }) : [];

            $.each(unselectedRows, function (i, val) {
                if (idField) { datagrid1.datagrid("selectRecord", val[idField]); }
                else { datagrid1.datagrid("selectRow", datagrid1.datagrid("getRowIndex", val)); }
            });
            if ($.isFunction(refresh)) { refresh(); }
        },

        //  待选datagrid的全部取消选中操作
        unselectAllRow: function (datagrid1, datagrid2, refresh) {
            datagrid2.datagrid("loadData", []);
            datagrid1.datagrid("clearSelections");
            if ($.isFunction(refresh)) { refresh(); }
        },

        //  待选datagrid首次数据加载完毕后根据“原始已选项”选中行
        selectRowOnFirst: function (dg1, data) {
            if (!data) { return; }
            if ($.util.likeArrayNotString(data)) {
                $.each(data, function (i, val) {
                    dg1.datagrid("selectRecord", val);
                });
            } else {
                dg1.datagrid("selectRecord", data);
            }
        },

        //  待选datagrid非首次数据加载完毕后
        //  1:对 datagrid1 中选中的、而在 datagrid2 中未选中的行进行取消选中操作
        //  2:对 datagrid2 中选中的、而在 datagrid1 中未选中的行进行选中操作
        selectRowOnNotFirst: function (idField, datagrid1, datagrid2) {
            var selectedRows2 = datagrid2.datagrid("getRows"),
            selectedRowsIDs2 = $.array.map(selectedRows2, function (row) { return idField ? row[idField] : undefined; }),
            rows1 = datagrid1.datagrid("getRows"),
            selectedRows1 = datagrid1.datagrid("getSelections"),
            selectedRowsIDs1 = $.array.map(selectedRows1, function (row) { return idField ? row[idField] : undefined; }),
            moreRows = [], unselectedRows = [], lessRows = [];

            //找到 datagrid1 中选中的、而在 datagrid2 中未选中的行
            moreRows = selectedRows1.length > 0 ? $.array.filter(selectedRows1, function (row) { return idField ? !$.array.contains(selectedRowsIDs2, row[idField]) : true; }) : [];
            //找到 datagrid1 中未选中的行
            unselectedRows = rows1.length > 0 ? $.array.filter(rows1, function (row) { return idField ? !$.array.contains(selectedRowsIDs1, row[idField]) : true; }) : [];
            //找到 datagrid2 中选中的、而在 datagrid1 中未选中的行
            lessRows = unselectedRows.length > 0 ? $.array.filter(unselectedRows, function (row) { return idField ? $.array.contains(selectedRowsIDs2, row[idField]) : true; }) : [];

            $.each(moreRows, function (i, row) {
                var idValue = idField ? row[idField] : row;
                var index = datagrid1.datagrid("getRowIndex", idValue);
                datagrid1.datagrid("unselectRow", index);
            });
            $.each(lessRows, function (i, row) {
                if (idField) { datagrid1.datagrid("selectRecord", row[idField]); }
                else { datagrid1.datagrid("selectRow", datagrid1.datagrid("getRowIndex", row)); }
            });
        },

        //  已选datagrid中移除行操作
        removeRow: function (idField, row, datagrid1, datagrid2, refresh) {
            var idValue = idField ? row[idField] : row;
            var index = datagrid1.datagrid("getRowIndex", idValue);
            if (index > -1) {
                datagrid1.datagrid("unselectRow", index);
            }
            else {
                datagrid2.datagrid("deleteRow", datagrid2.datagrid("getRowIndex", idValue));
                if ($.isFunction(refresh)) { refresh(); }
            }
        },

        //  已选datagrid加载已选数据
        loadSelectedData: function (selected, selectedUrl, selectedMethod, selectedFilter, datagrid2, refresh) {
            if ($.string.isNullOrWhiteSpace(selectedUrl) || !selected) { return; }
            var str = "";
            if ($.util.isArray(selected)) {
                str = selected.join(",");
            }
            else if ($.util.isString(selected)) {
                str = selected;
            }
            if (!$.string.isNullOrWhiteSpace(str)) {
                $.ajax({
                    url: selectedUrl,
                    type: selectedMethod && ["post", "get"].contains(selectedMethod) ? selectedMethod : "post",
                    async: true,
                    data: { selected: str },
                    dataType: "json",
                    success: function (data) {
                        if ($.isFunction(selectedFilter)) { data = selectedFilter.call(datagrid2[0], data, $.util.isString(selected) ? selected.split(",") : selected); }
                        datagrid2.datagrid("loadData", data);
                        if ($.isFunction(refresh)) { refresh(); }
                    }
                });
            }
        }
    });

    function initialize(options) {
        var treeOptions = options.treeOptions ? options.treeOptions : {};
        if (!$.isFunction(treeOptions.onSelectParamer)) { treeOptions.onSelectParamer = self.paramsBuilderForTree; }
        if (!$.isFunction(treeOptions.onSelectQueryer)) { treeOptions.onSelectQueryer = self.dataQueryerForTree; }
        var datagridOptions = options.datagridOptions ? options.datagridOptions : {};
        //计算 dialog 的最大和最小宽度
        var defaultCenterWith = 55,
            defaultTreeWidth = 180,
            treeMinWith = 150,
            treeMaxWith = 300,
            minDatagridWidth = self.getDataGridMinWidth(datagridOptions.pagination),
            treeRealWith = options.treeWidth ? (options.treeWidth < treeMinWith ? treeMinWith : (options.treeWidth > treeMaxWith ? treeMaxWith : options.treeWidth)) : defaultTreeWidth,
            diaMinWidth = (minDatagridWidth * 2) + defaultCenterWith + treeRealWith,
            diaRealWidth = options.width ? (options.width < diaMinWidth ? diaMinWidth : options.width) : diaMinWidth + 50;
        var opts = $.extend({
            title: "选择数据，" + (datagridOptions.singleSelect ? "单选" : "多选"),
        }, options, { width: diaRealWidth, minWidth: diaMinWidth, centerWidth: defaultCenterWith, treeWidth: treeRealWith });
        delete opts.datagridOptions;
        delete opts.treeOptions;

        var value = self.getSelected(datagridOptions.singleSelect, opts.selected), tempData = self.getOriginalSelected(value);

        var dg = null,
            dia = $.easyui.showDialog($.extend({}, opts, {
                content: "<div class=\"grid-selector-container\"></div>",
                toolbar: "",
                onSave: function () {
                    if ($.isFunction(opts.onEnter)) {
                        return opts.onEnter.call(dg[0], value);
                    }
                }
            }));

        var container = dia.find(".grid-selector-container"),
            width = (($.isNumeric(opts.width) ? opts.width : dia.outerWidth()) - opts.treeWidth - opts.centerWidth) / 2,
            leftPanel = ($.string.isNullOrWhiteSpace(opts.treeTitle) ? $("<div data-options=\"region: 'west', split: false, border: false\"></div>") : $("<div data-options=\"region: 'west', title: '" + opts.treeTitle + "',split: false, border: false\"></div>")).width(opts.treeWidth).appendTo(container),
            centerPanel = $("<div data-options=\"region: 'center', border: true, bodyCls: 'grid-selector-outer-center'\"></div>").appendTo(container),
            inLayout = $("<div />").appendTo(centerPanel),
            inLeftPanel = $("<div data-options=\"region: 'west', split: false, border: false\"></div>").width(width).appendTo(inLayout),
            inCenterPanel = $("<div data-options=\"region: 'center', border: true, bodyCls: 'grid-selector-buttons'\"></div>").appendTo(inLayout),
            inRightPanel = $("<div data-options=\"region: 'east', split: false, border: false\"></div>").width(width).appendTo(inLayout),
            tree = $("<ul />").appendTo(leftPanel),
            dg1 = $("<div />").appendTo(inLeftPanel),
            dg2 = dg = $("<div class=\"grid-selector\"></div>").appendTo(inRightPanel),
            btn1 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-last" }).tooltip({ content: "选择全部" }).appendTo(inCenterPanel),
            btn2 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-first" }).tooltip({ content: "取消全部" }).appendTo(inCenterPanel);

        inLayout.layout({ fit: true });
        container.layout({ fit: true });
        dia.selector = {
            tree: tree,
            datagrid1: dg1,
            datagrid2: dg2
        };

        $.util.delay(function () {
            var diaOpts = dia.dialog("options"), onResize = diaOpts.onResize, init = false,
                extToolbar = self.checkToolbar(opts.extToolbar, datagridOptions.toolbar),
                toolbarObj = extToolbar ? self.getToolbar(datagridOptions.toolbar) : null;
            var treeOpts = $.extend({ animate: true, lines: true }, treeOptions, {
                checkbox: false, dnd: false,
                onBeforeSelect: function (node) {
                    var pass = true;
                    if ($.isFunction(treeOptions.onBeforeSelect)) {
                        pass = treeOptions.onBeforeSelect.apply(this, arguments);
                    }
                    return pass;
                },
                onSelect: function (node) {
                    if ($.isFunction(treeOptions.onSelect)) { treeOptions.onSelect.apply(this, arguments); }
                    var params = treeOptions.onSelectParamer.call(this, node);
                    if (extToolbar) { $.extend(params, toolbarObj.toolbar("getValues")); }
                    treeOptions.onSelectQueryer.call(this, params, dg1);
                },
                onBeforeLoad: function (node, param) {
                    var pass = true;
                    if ($.isFunction(treeOptions.onBeforeLoad)) { pass = treeOptions.onBeforeLoad.apply(this, arguments); }
                    if (pass) { $.easyui.loading({ locale: leftPanel, msg: "数据加载中..." }); }
                    return pass;
                },
                onLoadSuccess: function (node, data) {
                    if ($.isFunction(treeOptions.onLoadSuccess)) { treeOptions.onLoadSuccess.apply(this, arguments); }
                    $.easyui.loaded(leftPanel);
                },
                onLoadError: function (p1, p2, p3) {
                    if ($.isFunction(treeOptions.onLoadError)) {
                        treeOptions.onLoadError.apply(this, arguments);
                    }
                    $.easyui.loaded(leftPanel);
                }
            });
            var dgOpts1 = $.extend({ striped: true, checkOnSelect: true, selectOnCheck: true, rownumbers: true }, datagridOptions, {
                title: "待选择项", fit: true, border: false, doSize: true,
                noheader: false, iconCls: null, collapsible: false, minimizable: false, maximizable: false, closable: false,
                toolbar: extToolbar ? toolbarObj.toolbar("toolbar") : null,
                onSelect: function (rowIndex, rowData) {
                    if ($.isFunction(datagridOptions.onSelect)) {
                        datagridOptions.onSelect.apply(this, arguments);
                    }
                    self.selectRow(datagridOptions, rowData, dg2, refreshValue);
                },
                onUnselect: function (rowIndex, rowData) {
                    if ($.isFunction(datagridOptions.onUnselect)) {
                        datagridOptions.onUnselect.apply(this, arguments);
                    }
                    self.unselectRow(datagridOptions, rowData, dg2, refreshValue);
                },
                onLoadSuccess: function (data) {
                    if ($.isFunction(datagridOptions.onLoadSuccess)) {
                        datagridOptions.onLoadSuccess.apply(this, arguments);
                    }
                    if (!init) {
                        self.selectRowOnFirst(dg1, tempData);
                        init = true;
                    }
                    else {
                        self.selectRowOnNotFirst(datagridOptions.idField, dg1, dg2);
                    }
                }
            }),
            dgOpts2 = $.extend({}, dgOpts1, {
                title: "已选择项", url: null, queryParams: {}, remoteSort: false, pagination: false, toolbar: null,
                onSelect: function (rowIndex, rowData) {
                    self.removeRow(datagridOptions.idField, rowData, dg1, dg2, refreshValue);
                },
                onUnselect: function (rowIndex, rowData) { },
                onLoadSuccess: function (data) { }
            }),
            refreshValue = function () {
                if (datagridOptions.singleSelect) {
                    var rows = dg2.datagrid("getRows");
                    value = rows.length > 0 ? rows[0] : null;
                }
                else {
                    var rows = dg2.datagrid("getRows");
                    value = $.array.clone(rows);
                }
            };

            if (btn1) { btn1.click(function () { self.selectAllRow(datagridOptions.idField, dg1, refreshValue); }); }
            if (btn2) { btn2.click(function () { self.unselectAllRow(dg1, dg2, refreshValue); }); }

            tree.tree(treeOpts);
            dg1.datagrid(dgOpts1);
            dg2.datagrid(dgOpts2);

            if (datagridOptions.pagination) { self.removePaginationMessage(dg1); }

            self.loadSelectedData(tempData, datagridOptions.selectedUrl, datagridOptions.selectedMethod, datagridOptions.selectedFilter, dg2, refreshValue);

            diaOpts.onResize = function (w, h) {
                if ($.isFunction(onResize)) { onResize.apply(this, arguments); }
                $.util.delay(function () {
                    if (self.checkResizable(diaOpts, w, h)) {
                        var ww = (dia.panel("options").width - diaOpts.treeWidth - diaOpts.centerWidth) / 2;
                        inLeftPanel.panel("resize", { width: ww });
                        inRightPanel.panel("resize", { width: ww });
                        inLayout.layout("resize");
                    }
                });
            };
        });

        return dia;
    }

    //  增加自定义扩展方法 $.easyui.showSelector.treeDblDatagrid；该方法弹出一个包含 一个 easyui-tree 和两个 easyui-datagrid 控件的选择框窗口；
    $.easyui.showSelector.treeDblDatagrid = function (options) {
        options = options || {};
        var opts = $.extend({}, $.easyui.showSelector.treeDblDatagrid.defaults, options);

        return initialize(opts);
    };



    // 继承 $.easyui.showDialog 的属性，并增加了以下属性
    $.easyui.showSelector.treeDblDatagrid.defaults = $.extend({}, $.easyui.showDialog.defaults, {

        height: 500,
        minHeight: 400,
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

        // Number 值，表示 tree 所在面板的宽度；
        treeWidth: 180,

        // String 值，表示 tree 所在面板的标题；
        treeTitle: null,

        // 这是一个 JSON-Object 对象，具体格式参考 easyui-tree 方法的参数 options 的格式；
        //      该参数格式在 easyui-tree 参数 options 格式基础上扩展了如下属性：
        //          onSelectParamer:这是一个 Function 对象，表示触发“tree 的 onSelect”事件时，利用该事件参数 node 对象组装最终 param 对象的方式；
        //                                      参数签名为 function(node)，node 表示触发 select 的 tree-node 对象；其 this 指向 easyui-tree 对象；
        //                             若不指定该参数，则 tree 的 select 操作将组装出参数 { nodeId:node.id } 去执行 onSelectQueryer 属性；
        //          onSelectQueryer:这是一个 Function 对象，表示触发“tree 的 onSelect”事件时，筛选 datagrid 数据的方式；
        //                                      参数签名为 function(params, datagrid)，params 表示通过 onSelectParamer 组装的参数对象，datagrid 表示要进行数据筛选的 easyui-datagrid 对象；其 this 指向 easyui-tree 对象；
        //                              若不指定该参数，则 tree 的 select 操作将以“调用 datagrid 的 load 方法”去查询 datagrid 数据；
        treeOptions: {},

        //这是一个 JSON-Object 对象，具体格式参考 easyui-datagrid 方法的参数 options 的格式；
        //      该参数格式在 easyui-dataGrid 参数 options 格式基础上扩展了如下属性：
        //          selectedUrl: String 类型值，表示当 selected 属性存在时，已选项 datagrid 要获取具体数据时要执行的url；
        //          selectedMethod: String 类型值，表示执行 selectedUrl 时的ajax方式，可选值有：post、get，默认为 post ；
        //          selectedFilter: Function 对象，表示当 selected 属性存在且 selectedUrl 属性存在时，已选项 datagrid 对获取到的数据的筛选方式；等同于 datagrid 的源生属性 loadFilter 的功能；
        //                              参数签名为 function(data, selected)，data 表示通过 selectedUrl 返回的数据对象；selected 表示已选的项数组对象；其 this 指向已选项 datagrid 对象；
        //                          若不指定 selectedFilter，则不处理通过 selectedUrl 返回的数据，并直接 loadData 给已选项 datagrid 对象；
        datagridOptions: {}
    });


})(jQuery);