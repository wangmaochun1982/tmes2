/**
* jQuery EasyUI 1.4.3
* Copyright (c) 2009-2015 www.jeasyui.com. All rights reserved.
*
* Licensed under the GPL license: http://www.gnu.org/licenses/gpl.txt
* To use it on other terms please contact us at info@jeasyui.com
* http://www.jeasyui.com/license_commercial.php
*
* jQuery EasyUI selector 扩展
* jeasyui.extensions.selector.base.js
* 开发 落阳
* 最近更新：2016-03-29
*
* 依赖项：
*   1、jquery.jdirk.js
*
* Copyright (c) 2016 Lixilin personal All rights reserved.
*/
(function ($) {

    $.util.namespace("$.easyui.showSelector.self.methods");
    $.util.namespace("$.easyui.showSelector.helper");

    $.extend($.easyui.showSelector.self.methods, {

        //  获取datagrid的最小宽度
        getDataGridMinWidth: function (pagination) { return pagination ? 300 : 250; },

        //  对已选项进行重组，这将是调用回调函数onEnter时传递的参数，用来实现“打开选择器之后不进行任何操作就点击确定时依旧可以将原选项返回”的功能
        getSelected: function (singleSelect, selected) {
            return singleSelect ? (selected ? selected : "") : ($.util.likeArrayNotString(selected) ? selected : (selected ? ($.util.isString(selected) ? selected.split(',').remove("") : [selected]) : []));
        },

        //  最初的已选项，用来实现“datagrid1数据首次成功加载后自动选中已选项”的功能
        getOriginalSelected: function (selected) {
            return $.util.likeArrayNotString(selected) ? $.array.clone(selected) : selected;
        },

        //  检查是否需要扩展工具条
        checkToolbar: function (ext, toolbar) {
            return ext && $.util.likeArrayNotString(toolbar);
        },

        //  将toolbar的data对象组装成toolbar后返回
        getToolbar: function (toolbar) {
            return $("<div class=\"grid-selector-toolbar\"></div>").toolbar({ data: toolbar });
        },

        //  将toolbar的data对象组装成toolbar，并返回其div容器对象，可用于赋值datagrid的options的toolbar属性
        getToolbarDiv: function (toolbar) {
            return this.getToolbar(toolbar).toolbar("toolbar");
        },

        //  检查dialog调整尺寸之后是否需要对dialog内部布局调整尺寸
        checkResizable: function (dialogOptions, w, h) {
            var minWidth = $.isNumeric(dialogOptions.minWidth) ? dialogOptions.minWidth : 10,
                maxWidth = $.isNumeric(dialogOptions.maxWidth) ? dialogOptions.maxWidth : 10000,
                minHeight = $.isNumeric(dialogOptions.minHeight) ? dialogOptions.minHeight : 10,
                maxHeight = $.isNumeric(dialogOptions.maxHeight) ? dialogOptions.maxHeight : 10000;
            var resizable = true;
            if (w > maxWidth) { resizable = false; }
            if (w < minWidth) { resizable = false; }
            if (h > maxHeight) { resizable = false; }
            if (h < minHeight) { resizable = false; }
            return resizable;
        },

        //  移除datagrid的分页提示信息，如，当前第X页，共N记录
        removePaginationMessage: function (dg) {
            dg.datagrid("getPager").pagination({ displayMsg: "" });
            dg.datagrid("resize");
        }


    });

    $.extend($.easyui.showSelector.helper, {

        //  获取 selector 的查询参数
        //  toolbar：工具条对象
        //  selectorType：选择器类型，string格式，可以是以下值：SingleDataGrid、DblDataGrid、TreeDblDataGrid、AccordionDblDataGrid
        //  返回 Json-Object 对象，其中 params 属性表示查询参数对象，query 属性表示查询方式，thisArg 属性表示 query 中的this指向对象
        getQueryParams: function (toolbar, selectorType) {
            var param = toolbar.toolbar("getValues"), query = function (p, dg) { dg.datagrid("load", p); }, thisArg = undefined;
            var dg = this.getDatagrid(toolbar, selectorType);
			var pm = dg.datagrid("options").queryParams; 
			param = $.extend({}, param, pm);
            switch (selectorType) {
                case "SingleDataGrid":
                case "DblDataGrid":
                    break;
                case "TreeDblDataGrid":
                    var tree = undefined;
                    var dia = toolbar.currentDialog(), diaPanel = dia.dialog("body"), layout = diaPanel.find("div.layout:eq(0)"),
                        leftPanel = layout.layout("panel", "west"), temp = leftPanel.find("ul.tree");
                    if (temp.length > 0 && temp.isEasyUiComponent("tree")) { tree = temp.first(); }

                    if (tree) {
                        var treeOpts = tree.tree("options"), paramer = treeOpts.onSelectParamer, node = tree.tree("getSelected");
                        if (node) { $.extend(param, paramer.call(tree[0], node)); }
                        query = treeOpts.onSelectQueryer;
                        thisArg = tree[0];
                    }
                    break;
                case "AccordionDblDataGrid":
                    var dia = toolbar.currentDialog(), diaPanel = dia.dialog("body"),
                        layout = diaPanel.find("div.layout:eq(0)"), leftPanel = layout.layout("panel", "west"), temp = leftPanel.find("div.accordion");
                    if (temp.length > 0 && temp.isEasyUiComponent("accordion")) {
                        var accordion = temp.first(), accordionOpts = accordion.accordion("options"), multiple = accordionOpts.multiple, panels = [];
                        if (multiple) {
                            //accordion可同时展开多个panel，暂不考虑这种情况
                        }
                        else {
                            //存在一个逻辑问题：
                            // 打开面板1，选择里面的tree-node
                            // 打开面板2，面板1自动折叠
                            // 此时获取到已选择的面板是面板2，但面板2中并不存在已经选择的tree-node，这样就查询不到。
                            var panel = accordion.accordion("getSelected");
                            if (panel) {
                                panels.push(panel);
                            }
                        }
                        $.each(panels, function (i, p) {
                            var panelOpts = p.panel("options");
                            //queryable为true时表示该panel存在查询条件
                            if (panelOpts.queryable == true) {
                                //queryType表示查询条件在哪
                                switch (panelOpts.queryType) {
                                    case "tree":
                                        var tree = p.find("ul.tree");
                                        if (tree.length == 1 && tree.isEasyUiComponent("tree")) {
                                            //onSelectParamer表示查询条件怎么组装
                                            var treeOpts = tree.tree("options"), builder = treeOpts.onSelectParamer, node = tree.tree("getSelected");
                                            if (node) { $.extend(param, builder.call(tree[0], node)); }
                                            query = treeOpts.onSelectQueryer;
                                            thisArg = tree[0];
                                        }
                                        break;
                                }
                            }
                        });
                    }
                    break;
            }
            return { params: param, query: query, thisArg: thisArg };
        },

        //  获取 selector 的待选择 datagrid 对象
        //  toolbar：工具条对象
        //  selectorType：选择器类型，string格式，可以是以下值：SingleDataGrid、DblDataGrid、TreeDblDataGrid、AccordionDblDataGrid
        getDatagrid: function (toolbar, selectorType) {
            var dg = undefined;
            switch (selectorType) {
                case "SingleDataGrid":
                    dg = toolbar.currentDatagrid();
                    break;
                case "DblDataGrid":
                    dg = toolbar.currentDatagrid();
                    break;
                case "TreeDblDataGrid":
                    dg = toolbar.currentDatagrid();
                    break;
                case "AccordionDblDataGrid":
                    dg = toolbar.currentDatagrid();
                    break;
            }

            return dg;
        },

        //  查询，将自动组装参数对待选择 datagrid 进行数据查询
        //  toolbar：工具条对象
        //  selectorType：选择器类型，string格式，可以是以下值：SingleDataGrid、DblDataGrid、TreeDblDataGrid、AccordionDblDataGrid
        //  otherParam：额外参数对象
        //  searcher：数据查询器，Function 对象，参数签名 function(params, datagrid)，其中 params 表示查询参数对象，datagrid 表示要进行数据查询的 datagrid 对象
        //          若不指定 searcher，将使用默认查询器——datagrid.datagrid("load", params)，其 this 指向目标可能为 tree 或 datagrid
        doSearch: function (toolbar, selectorType, otherParam, searcher) {
            var pq = this.getQueryParams(toolbar, selectorType);
            var dg = this.getDatagrid(toolbar, selectorType); 
            if (dg) {
                (searcher && $.isFunction(searcher) ? searcher : pq.query).call(pq.thisArg ? pq.thisArg : dg[0], $.extend({}, pq.params, otherParam), dg);
            }
        }
    });

})(jQuery);