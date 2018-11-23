/**
* jQuery EasyUI 1.3.6
* Copyright (c) 2009-2014 www.jeasyui.com. All rights reserved.
*
* Licensed under the GPL or commercial licenses
* To use it on other terms please contact author: info@jeasyui.com
* http://www.gnu.org/licenses/gpl.txt
* http://www.jeasyui.com/license_commercial.php
*
* jQuery EasyUI gridselector Extensions 1.0 beta
* jQuery EasyUI gridselector 组件扩展
* jeasyui.extensions.gridselector1.6.js
* 二次开发 CYP
* 最近更新：2014-09-09
*
* 依赖项：
*   1、jquery.jdirk.js v1.0 beta late
*   2、jeasyui.extensions.js v1.0 beta late
*
* Copyright (c) 2013-2014 ChenJianwei personal All rights reserved.
* http://www.chenjianwei.org
*/
(function ($, undefined) {

    $.util.namespace("$.easyui");
	$.util.namespace("$.easyui.showSelector.self.methods");
    $.util.namespace("$.easyui.showSelector.helper");

	$.extend($.easyui.showSelector.helper, {

        //  获取 selector 的查询参数
        //  toolbar：工具条对象
        //  selectorType：选择器类型，string格式，可以是以下值：SingleDataGrid、DblDataGrid、TreeDblDataGrid、AccordionDblDataGrid
        //  返回 Json-Object 对象，其中 params 属性表示查询参数对象，query 属性表示查询方式，thisArg 属性表示 query 中的this指向对象

		getQueryParams: function (toolbar, selectorType) {
            var param = toolbar.toolbar("getValues"), query = function (p, dg) { dg.datagrid("load", p); }, thisArg = undefined;
            var dg = this.getDatagrid(toolbar, selectorType);
			var pm = dg.datagrid("options").queryParams;
			param = $.extend({}, pm, param);
			param.timestamp = Date.parse(new Date());//時間戳作為參數，解決緩存問題;
            switch (selectorType) {
                case "grid":
                case "dblgrid":
                    break;
                case "TreeDblgrid":
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
                case "AccordionDblgrid":
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
                case "grid":
                    dg = toolbar.currentDatagrid();
                    break;
                case "dblgrid":
                    dg = toolbar.currentDatagrid();
                    break;
                case "TreeDblgrid":
                    dg = toolbar.currentDatagrid();
                    break;
                case "AccordionDblgrid":
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
		},
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
			//datagridOptions.selectedUrl
			var rows = datagrid1.datagrid("getRows"), selectedRows = datagrid1.datagrid("getSelections");
			if (rows.length == selectedRows.length && rows === selectedRows) { return; }
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
			} else {
				datagrid2.datagrid("deleteRow", datagrid2.datagrid("getRowIndex", idValue));
				if ($.isFunction(refresh)) { refresh(); }
			}
		},
		//  将toolbar的data对象组装成toolbar后返回
		getToolbar: function (toolbar) {
			return $("<div class=\"grid-selector-toolbar\"></div>").toolbar({ data: toolbar });
		},
		getToolbarDiv: function (toolbar) {
				return this.getToolbar(toolbar).toolbar("toolbar");
		},
		//  已选datagrid加载已选数据
		loadSelectedData: function (selected, selectedUrl, selectedMethod, selectedFilter, datagrid2, refresh, rows, ceilds, inputName) {
			if ($.string.isNullOrWhiteSpace(selectedUrl) || !selected) { return; }
				var str = "";
			if ($.util.isArray(selected)) {
				str = selected.join(",");
			} else if ($.util.isString(selected)) {
				str = selected;
			}
			if (!$.string.isNullOrWhiteSpace(str)) {
				$.ajax({
					url: selectedUrl,
					type: selectedMethod && ["post", "get"].contains(selectedMethod) ? selectedMethod : "post",
					async: true,
					data: { selected: str,rows : rows ,fieldCols:ceilds,input_name:inputName},
					dataType: "json",
					success: function (data) {
						if(data){
						if ($.isFunction(selectedFilter)) { data = selectedFilter.call(datagrid2, data, $.util.isString(selected) ? selected.split(",") : selected); }
							datagrid2.datagrid("loadData", data);
						}
						if ($.isFunction(refresh)) { refresh(); }
					},
					error: function(e){
						console.log(e)
					}
				});
			}
		},
		//  根据 tree 节点对象组装查询参数的默认方式
        paramsBuilderForTree: function (node) { return { nodeId: node.id }; },

        //  根据 tree 节点对象组装的查询参数去筛选 datagrid 数据的默认方式
        dataQueryerForTree: function (params, datagrid) { datagrid.datagrid("load", params); }
	});


    var self = $.easyui.showSelector.self.methods;
    //  增加自定义扩展方法 $.easyui.showGridSelector；该方法弹出一个 easyui-datagrid 选择框窗口；该方法定义如下参数：
    //      options: 这是一个 JSON-Object 对象；
    //              具体格式参考 $.easyui.showDialog 方法的参数 options 的格式 和 easyui-datagrid 的初始化参数 options 的格式；
    //              该参数格式在 $.easyui.showDialog 和 easyui-datagrid 参数 options 格式基础上扩展了如下属性：
    //          extToolbar:
    //          selected:
    //          onEnter :
    //  返回值：返回弹出窗口的 easyui-dialog 控件对象(jQuery-DOM 格式)。
	$.easyui.showGridSelector = function (options) {
        var datagridOptions = options.datagridOptions ? options.datagridOptions : {};
        //计算 dialog 的最大和最小宽度
        var minDatagridWidth = self.getDataGridMinWidth(datagridOptions.pagination),
            diaMinWidth = minDatagridWidth + 20,
            diaRealWidth = options.width ? (options.width < diaMinWidth ? diaMinWidth : options.width) : diaMinWidth + 50;
        var module =getModule();//, local = "#"+module+"PagePanel";
		var opts = $.extend({
				width: 580, height: 280, minWidth: 580, minHeight: 280,
				title: "選擇數據，" + (options.singleSelect ? "單選" : "多選"),
				iconCls: "icon-hamburg-zoom",
				maximizable: true,
				collapsible: true,
				modal : true,
				inline: true,
				//locale: local,
				saveButtonText: "确定",
				saveButtonIconCls: "icon-ok",
				enableApplyButton: false,

				// Boolean 值，表示是否扩展工具条；
				extToolbar: false,

				// 表示已选项，可以是String类型（多个则以英文逗号相连），也可以是Array类型。
				selected: null,

				//这是一个 function 对象，表示点击“确定”按钮时回调的函数；
				onEnter: function (selected) { },

				//这是一个 JSON-Object 对象，具体格式参考 easyui-datagrid 方法的参数 options 的格式；
				datagridOptions: {}
        }, options, { width: diaRealWidth, minWidth: diaMinWidth });
        delete opts.datagridOptions;

		var buttons = [
            btnSave = {
                id: "save", text: "確定", iconCls: 'icon-ok',hidden: opts.enableSaveButton ? false : true,
                index: 101,
                handler: function () {
                    if ($.isFunction(opts.onEnter)) { return opts.onEnter.call(dg[0], value); }
                }
            }
        ];

        if (!$.util.likeArrayNotString(opts.buttons)) { opts.buttons = []; }
        $.array.merge(opts.buttons, buttons);
		opts.buttons = $.array.filter(opts.buttons, function (val) { return $.util.parseFunction(val.hidden, val) ? false : true; });
        $.array.sort(opts.buttons, function (a, b) {
            return ($.isNumeric(a.index) ? a.index : 0) - ($.isNumeric(b.index) ? b.index : 0);
        });


		if (opts.locale) {
            opts.inline = true;
        }
		var SltorDia = $("<div id='"+module+"SltorDia'></div>").appendTo(opts.locale ? opts.locale : "body");

        var value = self.getSelected(datagridOptions.singleSelect, opts.selected), tempData = self.getOriginalSelected(value);
        var dg = null,
			dia = SltorDia.dialog($.extend({}, opts, {
                content: "<div class=\"grid-selector-container\"></div>",
                toolbar: "",
                onSave: function () {
                    if ($.isFunction(opts.onEnter)) {
                        return opts.onEnter.call(dg[0], value);
                    }
                }
            }));

        var container = dia.find(".grid-selector-container");

        $.util.delay(function () {
			datagridOptions.sortName = datagridOptions.sortName ? datagridOptions.sortName: datagridOptions.idField;
            var dgOpts = $.extend({ striped: true, checkOnSelect: true, selectOnCheck: true, rownumbers: true }, datagridOptions, {
                noheader: true, fit: true, border: false, doSize: true, queryParams: opts.queryParams,
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
			/*if (self.checkToolbar(opts.extToolbar, opts.toolbar)) {
                container.append("<div data-options=\"region: 'north', split: false, border: false\"><div class=\"grid-selector-toolbar\"></div></div>" +
                    "<div data-options=\"region: 'center', border: false\"><div class=\"grid-selector\"></div></div>");
                try{
					container.find("div.grid-selector-toolbar").toolbar({ data: opts.toolbar });
				}catch(e){}
                dg = container.find("div.grid-selector");
                container.layout({ fit: true });
            } else {*/
                dg = container.addClass("grid-selector");
            //}

            dia.grid = dg.datagrid(dgOpts);
			if(datagridOptions.filterCol){
				dia.grid.datagrid('removeFilterRule');
				dia.grid.datagrid('enableFilter', datagridOptions.filterCol);
			}
            //dg.datagrid(dgOpts);
            if (datagridOptions.pagination) { self.removePaginationMessage(dg); }

            dia.selector = {};
            $.extend(dia.selector, { datagrid: dg });
        });

        return dia;

	}

    //  增加自定义扩展方法 $.easyui.showDblGridSelector；该方法弹出一个包含两个 easyui-datagrid 控件的选择框窗口；该方法定义如下参数：
    //      options: 这是一个 JSON-Object 对象；
    //              具体格式参考 $.easyui.showDialog 方法的参数 options 的格式 和 easyui-datagrid 的初始化参数 options 的格式；
    //              该参数格式在 $.easyui.showDialog 和 easyui-datagrid 参数 options 格式基础上扩展了如下属性：
    //          extToolbar:
    //          selected:
    //          onEnter :
    $.easyui.showDblGridSelector = function (options) {

        var datagridOptions = options.datagridOptions ? options.datagridOptions : {};
        //计算 dialog 的最大和最小宽度
        var defaultCenterWith = 45, minDatagridWidth = self.getDataGridMinWidth(datagridOptions.pagination),
            diaMinWidth = (minDatagridWidth * 2) + defaultCenterWith,
            diaRealWidth = options.width ? (options.width < diaMinWidth ? diaMinWidth : options.width) : diaMinWidth + 50;
        var module =getModule();//, local = "#"+module+"PagePanel";
		var opts = $.extend({
            title: "選擇數據，" + (datagridOptions.singleSelect ? "單選" : "多選"),
			width: 700, height: 360, minWidth: 580, minHeight: 280,
			iconCls: "icon-hamburg-zoom",
			maximizable: true,
			collapsible: true,
			modal : true,
			inline: true,
			//locale: local,
			saveButtonText: "确定",
			saveButtonIconCls: "icon-ok",
			enableApplyButton: false,
			// Boolean 值，表示是否扩展工具条；
			extToolbar: false,

			// 表示已选项，可以是String类型（多个则以英文逗号相连），也可以是Array类型。
			selected: null,

			//这是一个 function 对象，表示点击“确定”按钮时回调的函数；
			onEnter: function (selected) { },
			//这是一个 JSON-Object 对象，具体格式参考 easyui-datagrid 方法的参数 options 的格式；
			datagridOptions: {}
        }, options, { width: diaRealWidth, minWidth: diaMinWidth, centerWidth: defaultCenterWith });
        delete opts.datagridOptions;

		var value = self.getSelected(datagridOptions.singleSelect, opts.selected), tempData = self.getOriginalSelected(value);

		var buttons = [
            btnSave = {
                id: "save", text: "確定", iconCls: 'icon-ok',hidden: opts.enableSaveButton ? false : true,
                index: 101,
                handler: function () {
                    if ($.isFunction(opts.onEnter)) { return opts.onEnter.call(dg[0], value); }
                }
            }
        ];

        if (!$.util.likeArrayNotString(opts.buttons)) { opts.buttons = []; }
        $.array.merge(opts.buttons, buttons);
		opts.buttons = $.array.filter(opts.buttons, function (val) { return $.util.parseFunction(val.hidden, val) ? false : true; });
        $.array.sort(opts.buttons, function (a, b) {
            return ($.isNumeric(a.index) ? a.index : 0) - ($.isNumeric(b.index) ? b.index : 0);
        });


		if (opts.locale) {
            opts.inline = true;
        }
		var SltorDia = $("<div id='"+module+"SltorDia'></div>").appendTo(opts.locale ? opts.locale : "body");

        var dg = null,
            dia = SltorDia.dialog($.extend({}, opts, {
                content: "<div class=\"grid-selector-container\"></div>",
                toolbar: "",
                onSave: function () {
                    if ($.isFunction(opts.onEnter)) {
                        return opts.onEnter.call(dg[0], value);
                    }
                }
            }));
        var container = dia.find(".grid-selector-container"),
            width = (($.isNumeric(opts.width) ? opts.width : dia.outerWidth()) - opts.centerWidth) / 2,
            leftPanel = $("<div data-options=\"region: 'west', split: false, border: false\"></div>").width(width+20).appendTo(container),
            centerPanel = $("<div data-options=\"region: 'center', border: true, bodyCls: 'grid-selector-buttons'\"></div>").appendTo(container),
            rightPanel = $("<div data-options=\"region: 'east', split: false, border: false\"></div>").width(width-20).appendTo(container),
            dg1 = $("<div class=\"grid-unselector\"></div>").appendTo(leftPanel),
            dg2 = dg = $("<div class=\"grid-selector\"></div>").appendTo(rightPanel),
            btn1 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-last" }).tooltip({ content: "選擇全部" }).appendTo(centerPanel);
        if(opts.checkedBtn){
			var btn2 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-next" }).tooltip({ content: "選擇勾選" }).appendTo(centerPanel),
				btn3 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-prev" }).tooltip({ content: "取消勾選" }).appendTo(centerPanel);
		}
        var btn4 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-first" }).tooltip({ content: "取消全部" }).appendTo(centerPanel);

		/* ////----Toolbar在Table外，啟用此項目
		if (self.checkToolbar(opts.extToolbar, opts.toolbar)) {
			northPanel = $("<div data-options=\"region: 'north', split: false, border: false\"></div>").appendTo(container);
			try{
				$("<div class=\"grid-selector-toolbar\"></div>").appendTo(northPanel).toolbar({ data: opts.toolbar });;
			}catch(e){}
		}*/
        container.layout({ fit: true });
        dia.selector = {
            datagrid1: dg1,
            datagrid2: dg2
        };

        $.util.delay(function () {
			datagridOptions.sortName = datagridOptions.sortName ? datagridOptions.sortName: datagridOptions.idField;
            var diaOpts = dia.dialog("options"), onResize = diaOpts.onResize, init = false,ceilds = [],jrows = {},pagin=false;
			var dg2url,dg2Params = opts.queryParams;
			if(opts.remoteUrl){
				var dgfColumns = $.array.clone(datagridOptions.frozenColumns[0]),
					dgoColumns = $.array.clone(datagridOptions.columns[0]),
					dgColumns = $.array.merge(dgfColumns,dgoColumns),
					dglen = dgColumns.length,i=0,j=1,val,reg,rsfg;
				while(i<dglen){
					reg = /^[a-zA-Z_]+$/g;
					val = dgColumns[i++].field;
					rsfg = reg.test(val);
					if(val == opts.valueField){val =  'valuefield '+(rsfg?'(':'[') +val +(rsfg?')':']'); }
					else if(val == opts.textField){ val =  'textfield ' +(rsfg?'(':'[') +val +(rsfg?')':']'); }
					else{
						if(j>3){ val = "''"+' '+(rsfg?'(':'[')  + val +(rsfg?')':']'); }else{	val = 'colfield'+(j++)+' '+(rsfg?'(':'[')  + val +(rsfg?')':']');}
					}
					$.array.push(ceilds, val);
				}
				dg2url = datagridOptions.selectedUrl;
				dg2Params.fieldCols = ceilds;
				dg2Params.input_name = opts.iptname;
				opts.queryParams.idField = datagridOptions.idField;
				pagin = true;
			}else{
				dg2url = '';
				dg2Params.fieldCols = ceilds;
				dg2Params.input_name = opts.iptname;
				opts.queryParams.idField = datagridOptions.idField;
				pagin = true;
			}
            var dgOpts1 = $.extend({ striped: true, checkOnSelect: true, selectOnCheck: true, rownumbers: true }, datagridOptions, {
                title: "待選擇項", fit: true, border: false, doSize: true, queryParams: opts.queryParams,
                noheader: false, iconCls: null, collapsible: false, minimizable: false, maximizable: false, closable: false,
                toolbar: self.checkToolbar(opts.extToolbar, datagridOptions.toolbar) ? self.getToolbarDiv(datagridOptions.toolbar) : null,
                onSelect: function (rowIndex, rowData) {
                    if ($.isFunction(datagridOptions.onSelect)) {
                        datagridOptions.onSelect.apply(this, arguments);
                    }
					if(!opts.checkedBtn){
						if(opts.remoteUrl){
							var data=[];
							data[0] = rowData;
							jrows['data'] = data;
							jrows['input_name'] = opts.iptname;
							jrows['value_field'] = opts.valueField;
							jrows['text_field'] = opts.textField;
							$.util.postAjaxData(opts.remoteUrl+'/x_xinsCbSeltor',
													jrows,
													function(){
														dg1.datagrid("clearSelections");
														dg1.datagrid('reload');
														dg2.datagrid('reload');
													}
											);
						}else{
							self.selectRow(datagridOptions, rowData, dg2, refreshValue);
						}
					}
                },
                onUnselect: function (rowIndex, rowData) {
                    if ($.isFunction(datagridOptions.onUnselect)) {
                        datagridOptions.onUnselect.apply(this, arguments);
                    }
					if(!opts.remoteUrl && !opts.checkedBtn){
						self.unselectRow(datagridOptions, rowData, dg2, refreshValue);
					}
                },
                onLoadSuccess: function (data) {
                    if ($.isFunction(datagridOptions.onLoadSuccess)) {
                        datagridOptions.onLoadSuccess.apply(this, arguments);
                    }
					if(!opts.remoteUrl){
						if (!init) {
							self.selectRowOnFirst(dg1, tempData);
							init = true;
						}
						else {
							self.selectRowOnNotFirst(datagridOptions.idField, dg1, dg2);
						}
					}
                }
            }),
            dgOpts2 = $.extend({}, dgOpts1, {
                title: "已選擇項", url: dg2url, queryParams: dg2Params, remoteSort: false , pagination: pagin, toolbar: null,
                onSelect: function (rowIndex, rowData) {
					if(!opts.checkedBtn){
						if(opts.remoteUrl){
							var data=[];
							data[0] = rowData;
							jrows['data'] = data;
							jrows['input_name'] = opts.iptname;
							jrows['fields'] = ceilds;
							jrows['value_field'] = opts.valueField;
							jrows['text_field'] = opts.textField;
							$.util.postAjaxData(opts.remoteUrl+'/x_xdelCbSeltor',
													jrows,
													function(){
														dg1.datagrid("clearSelections");
														dg1.datagrid('reload');
														dg2.datagrid('reload');
													}
											);
						}else{
							self.removeRow(datagridOptions.idField, rowData, dg1, dg2, refreshValue);
						}
					}
                },
                onUnselect: function (rowIndex, rowData) { },
                onLoadSuccess: function (data) { if(opts.remoteUrl){refreshValue();} }
            }),
            refreshValue = function () {
                if (datagridOptions.singleSelect) {
                    var rows = dg2.datagrid("getRows");
                    value = rows.length > 0 ? rows[0] : null;
                } else {
                    var rows = dg2.datagrid("getRows");
                    value = $.array.clone(rows);
                }
            };
			//console.log(dgOpts2)
            if (btn1) {
				btn1.click(function () {
					if(opts.remoteUrl){
						var rows = dg1.datagrid("getRows"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						jrows['text_field'] = opts.textField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xinsAllCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												} );
					}else{
						self.selectAllRow(datagridOptions.idField, dg1, refreshValue);
					}
				});
			}
            if (btn2) {
				btn2.click(function () {
					if(opts.remoteUrl){
						var rows = dg1.datagrid("getChecked"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						jrows['text_field'] = opts.textField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xinsCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid("clearChecked");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												} );
					}else{
						$.each(data, function (i, val) { self.selectRow(datagridOptions, val, dg2, refreshValue) });
					}
				});
			}
            if (btn3) {
				btn3.click(function () {
					if(opts.remoteUrl){
						var rows = dg2.datagrid("getChecked"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xdelCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid("clearChecked");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												});
					}else{
						$.each(data, function (i, val) { self.removeRow(datagridOptions.idField, val, dg1, dg2, refreshValue); });

					}
				});

			}
            if (btn4) {
				btn4.click(function () {
					if(opts.remoteUrl){
						var rows = dg2.datagrid("getRows"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xclsCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												});
					}else{
						self.unselectAllRow(dg1, dg2, refreshValue);
					}
				});

			}
			dia.grid = dg1.datagrid(dgOpts1);
			if(datagridOptions.filterCol){
				dia.grid.datagrid('removeFilterRule');
				dia.grid.datagrid('enableFilter', datagridOptions.filterCol);
			}
            dia.valueGrid = dg2.datagrid(dgOpts2);

			if(datagridOptions.filterCol){
				dia.valueGrid.datagrid('removeFilterRule');
				dia.valueGrid.datagrid('enableFilter', datagridOptions.filterCol);
			}
            //dg1.datagrid(dgOpts1);
            //dg2.datagrid(dgOpts2);
            if (datagridOptions.pagination) { self.removePaginationMessage(dg1); }
			if(opts.remoteUrl) { self.removePaginationMessage(dg2); }

            self.loadSelectedData(tempData, datagridOptions.selectedUrl, datagridOptions.selectedMethod, datagridOptions.selectedFilter, dg2, refreshValue, opts.data, ceilds, opts.iptname);

            diaOpts.onResize = function (w, h) {
                if ($.isFunction(onResize)) { onResize.apply(this, arguments); }
                $.util.delay(function () {
                    if (self.checkResizable(diaOpts, w, h)) {
                        var ww = (dia.panel("options").width - diaOpts.centerWidth) / 2;
                        leftPanel.panel("resize", { width: ww });
                        rightPanel.panel("resize", { width: ww });
                        container.layout("resize");
                    }
                });
            };
        });

        return dia;
	};


    $.easyui.showTreeSelector = function (options) {
		var treeOptions = options.treeOptions ? options.treeOptions : {};
        //计算 dialog 的最大和最小宽度
		var module =getModule();//, local = "#"+module+"PagePanel";
        var diaMinWidth = 250,
            diaRealWidth = options.width ? (options.width < diaMinWidth ? diaMinWidth : options.width) : diaMinWidth + 50;
        var opts = $.extend({
            title: "選擇數據，" + (treeOptions.checkbox ? "單選" : "多選"),
			width: 400, height: 260, minWidth: 380, minHeight: 250,
			//locale: local,
			iconCls: "icon-hamburg-zoom",
			modal : true,
			inline: false,
			maximizable: true,
			collapsible: true,
			saveButtonText: "確定",
			saveButtonIconCls: "icon-ok",
			enableApplyButton: false,

			// 表示已选项，可以是String类型（多个则以英文逗号相连），也可以是Array类型。
			selected: null,

			//这是一个 function 对象，表示点击“确定”按钮时回调的函数；
			onEnter: function (selected) { },

			//这是一个 JSON-Object 对象，具体格式参考 easyui-tree 方法的参数 options 的格式；
			treeOptions: {}
        }, options, { width: diaRealWidth, minWidth: diaMinWidth });
        delete opts.treeOptions;

		var buttons = [
            btnSave = {
                id: "save", text: "確定", iconCls: 'icon-ok',hidden: opts.enableSaveButton ? false : true,
                index: 101,
                handler: function () {
                    if ($.isFunction(opts.onEnter)) { return opts.onEnter.call(tree[0], value); }
                }
            }
        ];

        if (!$.util.likeArrayNotString(opts.buttons)) { opts.buttons = []; }
        $.array.merge(opts.buttons, buttons);
		opts.buttons = $.array.filter(opts.buttons, function (val) { return $.util.parseFunction(val.hidden, val) ? false : true; });
        $.array.sort(opts.buttons, function (a, b) {
            return ($.isNumeric(a.index) ? a.index : 0) - ($.isNumeric(b.index) ? b.index : 0);
        });
        var value = self.getSelected(!treeOptions.checkbox, opts.selected), tempData = self.getOriginalSelected(value);

		if (opts.locale) {
            opts.inline = true;
        }

		var SltorDia = $("<div id='"+module+"SltorDia'></div>").appendTo(opts.locale ? opts.locale : "body");
        var tree = null,
            dia = SltorDia.dialog($.extend({}, opts, {
                content: "<ul class=\"grid-selector-container\"></ul>",
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
    };

    $.easyui.showTreeDblGridSelector = function (options) {
        var treeOptions = options.treeOptions ? options.treeOptions : {};
        if (!$.isFunction(treeOptions.onSelectParamer)) { treeOptions.onSelectParamer = self.paramsBuilderForTree; }
        if (!$.isFunction(treeOptions.onSelectQueryer)) { treeOptions.onSelectQueryer = self.dataQueryerForTree; }
        var datagridOptions = options.datagridOptions ? options.datagridOptions : {},dgfield = datagridOptions.idField;
        if (!$.isFunction(datagridOptions.selectedFilter)) { datagridOptions.selectedFilter = function (data, seled) { return $.array.filter(data, function (item) { return seled.contains(item[dgfield]); }); } }
		var module =getModule();//, local = "#"+module+"PagePanel";
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
            title: "選擇數據，" + (datagridOptions.singleSelect ? "單選" : "多選"),
			width: 600, height: 360, minWidth: 580, minHeight: 380,
			//locale: local,
			iconCls: "icon-hamburg-zoom",
			modal : true,
			inline: false,
			maximizable: true,
			collapsible: true,
			saveButtonText: "確定",
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
        }, options, { width: diaRealWidth, minWidth: diaMinWidth, centerWidth: defaultCenterWith, treeWidth: treeRealWith });
        delete opts.datagridOptions;
        delete opts.treeOptions;

		var buttons = [
            btnSave = {
                id: "save", text: "確定", iconCls: 'icon-ok',hidden: opts.enableSaveButton ? false : true,
                index: 101,
                handler: function () {
                    if ($.isFunction(opts.onEnter)) { return opts.onEnter.call(tree[0], value); }
                }
            }
        ];

        if (!$.util.likeArrayNotString(opts.buttons)) { opts.buttons = []; }
        $.array.merge(opts.buttons, buttons);
		opts.buttons = $.array.filter(opts.buttons, function (val) { return $.util.parseFunction(val.hidden, val) ? false : true; });
        $.array.sort(opts.buttons, function (a, b) {
            return ($.isNumeric(a.index) ? a.index : 0) - ($.isNumeric(b.index) ? b.index : 0);
        });

        var value = self.getSelected(datagridOptions.singleSelect, opts.selected), tempData = self.getOriginalSelected(value);

		if (opts.locale) {
            opts.inline = true;
        }
		var SltorDia = $("<div id='"+module+"SltorDia'></div>").appendTo(opts.locale ? opts.locale : "body");

        var dg = null,
            dia = SltorDia.dialog($.extend({}, opts, {
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
            btn1 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-last" }).tooltip({ content: "选择全部" }).appendTo(inCenterPanel);
			if(opts.checkedBtn){
				var btn2 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-next" }).tooltip({ content: "選擇勾選" }).appendTo(inCenterPanel),
					btn3 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-prev" }).tooltip({ content: "取消勾選" }).appendTo(inCenterPanel);
			}
			var btn4 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-first" }).tooltip({ content: "取消全部" }).appendTo(inCenterPanel);
        inLayout.layout({ fit: true });
        container.layout({ fit: true });
        dia.selector = {
            tree: tree,
            datagrid1: dg1,
            datagrid2: dg2
        };

        $.util.delay(function () {
			datagridOptions.sortName = datagridOptions.sortName ? datagridOptions.sortName: datagridOptions.idField;
            var diaOpts = dia.dialog("options"), onResize = diaOpts.onResize, init = false,ceilds = [],jrows = {},pagin=false,
                extToolbar = self.checkToolbar(opts.extToolbar, datagridOptions.toolbar),
                toolbarObj = extToolbar ? self.getToolbar(datagridOptions.toolbar) : null;
			var dg2url,dg2Params = $.extend({},opts.queryParams);
			if(opts.remoteUrl){
				var dgfColumns = datagridOptions.frozenColumns?$.array.clone(datagridOptions.frozenColumns[0]):[],
					dgoColumns = $.array.clone(datagridOptions.columns[0]),
					dgColumns = $.array.merge(dgfColumns,dgoColumns),
					dglen = dgColumns.length,i=0,j=1,val,reg,rsfg;
				while(i<dglen){
					reg = /^[a-zA-Z_]+$/g;
					val = dgColumns[i++].field;
					rsfg = reg.test(val);
					if(val == opts.valueField){val =  'valuefield '+(rsfg?'(':'[') +val +(rsfg?')':']'); }
					else if(val == opts.textField){ val =  'textfield ' +(rsfg?'(':'[') +val +(rsfg?')':']'); }
					else{
						if(j>3){ val = "''"+' '+(rsfg?'(':'[')  + val +(rsfg?')':']'); }else{	val = 'colfield'+(j++)+' '+(rsfg?'(':'[')  + val +(rsfg?')':']');}
					}
					$.array.push(ceilds, val);
				}
				delete dg2Params.first;
				dg2url = datagridOptions.selectedUrl;
				dg2Params.fieldCols = ceilds;
				dg2Params.input_name = opts.iptname;
				opts.queryParams.input_name = opts.iptname;
				opts.queryParams.idField = datagridOptions.idField;
				pagin = true;
			}
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
					$.extend(params, opts.queryParams);
                    if (extToolbar) { $.extend(params, toolbarObj.toolbar("getValues")); }
					params.first = false;
                    treeOptions.onSelectQueryer.call(this, params, dg1);
                },
                onBeforeLoad: function (node, param) {
					opts.first = true;
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
                title: "待選擇項", fit: true, border: false, doSize: true, queryParams: opts.queryParams,
                noheader: false, iconCls: null, collapsible: false, minimizable: false, maximizable: false, closable: false,
                toolbar: extToolbar ? toolbarObj.toolbar("toolbar") : null,
				onBeforeLoad: function (params) { return !params.first; },
                onSelect: function (rowIndex, rowData) {
					if(!opts.checkedBtn){
						if ($.isFunction(datagridOptions.onSelect)) {
							datagridOptions.onSelect.apply(this, arguments);
						}
						if(opts.remoteUrl){
							var data=[];
							data[0] = rowData;
							jrows['data'] = data;
							jrows['input_name'] = opts.iptname;
							jrows['value_field'] = opts.valueField;
							jrows['text_field'] = opts.textField;
							$.util.postAjaxData(opts.remoteUrl+'/x_xinsCbSeltor',
													jrows,
													function(){
														dg1.datagrid("clearSelections");
														dg1.datagrid('reload');
														dg2.datagrid('reload');
													} );
						}else{
							self.selectRow(datagridOptions, rowData, dg2, refreshValue);
						}
					}
                },
                onUnselect: function (rowIndex, rowData) {
                    if ($.isFunction(datagridOptions.onUnselect)) {
                        datagridOptions.onUnselect.apply(this, arguments);
                    }
					if(!opts.remoteUrl && !opts.checkedBtn){
						self.unselectRow(datagridOptions, rowData, dg2, refreshValue);
					}
                },
                onLoadSuccess: function (data) {
                    if ($.isFunction(datagridOptions.onLoadSuccess)) {
                        datagridOptions.onLoadSuccess.apply(this, arguments);
                    }
					if(!opts.remoteUrl){
						if (!init) {
							self.selectRowOnFirst(dg1, tempData);
							init = true;
						}
						else {
							self.selectRowOnNotFirst(datagridOptions.idField, dg1, dg2);
						}
					}
                }
            }),
            dgOpts2 = $.extend({}, dgOpts1, {
                title: "已選擇項", url: dg2url, queryParams: dg2Params, remoteSort: false , pagination: pagin, toolbar: null,
                onSelect: function (rowIndex, rowData) {
					if(!opts.checkedBtn){
						if(opts.remoteUrl){
							var data=[];
							data[0] = rowData;
							jrows['data'] = data;
							jrows['input_name'] = opts.iptname;
							jrows['fields'] = ceilds;
							jrows['value_field'] = opts.valueField;
							$.util.postAjaxData(opts.remoteUrl+'/x_xdelCbSeltor',
													jrows,
													function(){
														dg1.datagrid("clearSelections");
														dg1.datagrid('reload');
														dg2.datagrid('reload');
													} );
						}else{
							self.removeRow(datagridOptions.idField, rowData, dg1, dg2, refreshValue);
						}
					}
                },
                onUnselect: function (rowIndex, rowData) { },
                onLoadSuccess: function (data) { if(opts.remoteUrl){refreshValue();} }
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

            if (btn1) {
				btn1.click(function () {
					if(opts.remoteUrl){
						var rows = dg1.datagrid("getRows"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						jrows['text_field'] = opts.textField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xinsAllCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												} );
					}else{
						self.selectAllRow(datagridOptions.idField, dg1, refreshValue);
					}
				});
			}
			if (btn2) {
				btn2.click(function () {
					if(opts.remoteUrl){
						var rows = dg1.datagrid("getChecked"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						jrows['text_field'] = opts.textField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xinsCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid("clearChecked");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												} );
					}else{
						$.each(data, function (i, val) { self.selectRow(datagridOptions, val, dg2, refreshValue) });
					}
				});
			}
            if (btn3) {
				btn3.click(function () {
					if(opts.remoteUrl){
						var rows = dg2.datagrid("getChecked"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xdelCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid("clearChecked");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												});
					}else{
						$.each(data, function (i, val) { self.removeRow(datagridOptions.idField, val, dg1, dg2, refreshValue); });

					}
				});

			}
            if (btn4) {
				btn4.click(function () {
					if(opts.remoteUrl){
						var rows = dg2.datagrid("getRows"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xclsCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												});
					}else{
						self.unselectAllRow(dg1, dg2, refreshValue);
					}
				});
			}

            tree.tree(treeOpts);
			dia.grid = dg1.datagrid(dgOpts1);
			if(datagridOptions.filterCol){
				dia.grid.datagrid('removeFilterRule');
				dia.grid.datagrid('enableFilter', datagridOptions.filterCol);
			}
            dia.valueGrid = dg2.datagrid(dgOpts2);

			if(datagridOptions.filterCol){
				dia.valueGrid.datagrid('removeFilterRule');
				dia.valueGrid.datagrid('enableFilter', datagridOptions.filterCol);
			}
            //dg1.datagrid(dgOpts1);
            //dg2.datagrid(dgOpts2);

            if (datagridOptions.pagination) { self.removePaginationMessage(dg1); }
			if(opts.remoteUrl) {
				self.removePaginationMessage(dg2);
			}else{
				self.loadSelectedData(tempData, datagridOptions.selectedUrl, datagridOptions.selectedMethod, datagridOptions.selectedFilter, dg2, refreshValue, opts.data, ceilds, opts.iptname);
			}
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
	};

    $.easyui.showAccordionDblGridSelector = function (options) {
        var accordionOptions = options.accordionOptions ? options.accordionOptions : {};
        var accordionPanels = accordionOptions.panels && $.util.isArray(accordionOptions.panels) ? accordionOptions.panels : [];
        var datagridOptions = options.datagridOptions ? options.datagridOptions : {},dgfield = datagridOptions.idField;
        if (!$.isFunction(datagridOptions.selectedFilter)) { datagridOptions.selectedFilter = function (data, seled) { return $.array.filter(data, function (item) { return seled.contains(item[dgfield]); }); } }
		var module =getModule();//, local = "#"+module+"PagePanel";

        //计算 dialog 的最大和最小宽度
        var defaultCenterWith = 55,
            defaultAccordionWidth = 200, accordionMinWith = 150, accordionMaxWith = 300,
            minDatagridWidth = self.getDataGridMinWidth(datagridOptions.pagination),
            accordionRealWith = accordionOptions.width ? (accordionOptions.width < accordionMinWith ? accordionMinWith : (accordionOptions.width > accordionMaxWith ? accordionMaxWith : accordionOptions.width)) : defaultAccordionWidth,
            diaMinWidth = (minDatagridWidth * 2) + defaultCenterWith + accordionRealWith,
            diaRealWidth = options.width ? (options.width < diaMinWidth ? diaMinWidth : options.width) : diaMinWidth + 50;
        var opts = $.extend({
            title: "選擇數據，" + (datagridOptions.singleSelect ? "單選" : "多選"),
			width: 600, height: 360, minWidth: 580, minHeight: 380,
			//locale: local,
			iconCls: "icon-hamburg-zoom",
			modal : true,
			inline: false,
			maximizable: true,
			collapsible: true,
			accordionRealWith : accordionRealWith,
			saveButtonText: "確定",
			saveButtonIconCls: "icon-ok",
			enableApplyButton: false,

			// Boolean 值，表示是否向datagrid扩展工具条；
			extToolbar: false,

			// 表示已选项，可以是String类型（多个则以英文逗号相连），也可以是Array类型。
			selected: null,

			//这是一个 function 对象，表示点击“确定”按钮时回调的函数；
			onEnter: function (selected) { },

			//这是一个 JSON-Object 对象，具体格式参考 easyui-accordion 方法的参数 options 的格式；
			//      该参数格式在 easyui-accordion 参数 options 格式基础上扩展了如下属性：
			//          panels:这是一个 array 对象，其中每个数组元素都是 JSON-Object 对象，每个元素都表示一个在 easyui-accordion 控件的独立 panel 中要显示的对象；
			//              元素具体格式如下：
			//                  panelTitle:这是一个string格式的值，表示面板的标题，若不提供该参数，则默认以“面板[索引号]”为标题；
			//                  type:这是一个string格式的值，表示元素要显示的类型，值可以是 tree、content 其中之一，若不提供该参数则默认为 content；
			//                  typeOptions:当 type 不等于 content 时，这是一个 JSON-Object 对象，具体格式参考 type 参数对应的 easyui 控件的方法的参数 options 的格式；
			//                              当 type 为 content 时，这是一个 string格式对象，将直接以文本方式显示在 panel 中；
			//                              当 type 为 tree 时，typeOptions 在 easyui-tree 的 options 格式基础上还支持与“treeDblDatagrid 选择框”同样的扩展属性，具体如下：
			//                                  onSelectParamer：这是一个 Function 对象，表示触发“tree 的 onSelect”事件时，利用该事件参数 node 对象组装最终 param 对象的方式；
			//                                                      参数签名为 function(node)，node 表示触发 select 的 tree-node 对象；其 this 指向 easyui-tree 对象；
			//                                                      若不指定该参数，则 tree 的 select 操作将组装出参数 { nodeId:node.id } 去执行 onSelectQueryer 属性；
			//                                  onSelectQueryer：这是一个 Function 对象，表示触发“tree 的 onSelect”事件时，筛选 datagrid 数据的方式；
			//                                                      参数签名为 function(params, datagrid)，params 表示通过 onSelectParamer 组装的参数对象，datagrid 表示要进行数据筛选的 easyui-datagrid 对象；其 this 指向 easyui-tree 对象；
			//                                                      若不指定该参数，则 tree 的 select 操作将以“调用 datagrid 的 load 方法”去查询 datagrid 数据；
			accordionOptions:{},

			//这是一个 JSON-Object 对象，具体格式参考 easyui-datagrid 方法的参数 options 的格式；
			//      该参数格式在 easyui-datagrid 参数 options 格式基础上扩展了如下属性：
			//          selectedUrl: String 类型值，表示当 selected 属性存在时，已选项 datagrid 要获取具体数据时要执行的url；
			//          selectedMethod: String 类型值，表示执行 selectedUrl 时的ajax方式，可选值有：post、get，默认为 post ；
			//          selectedFilter: Function 对象，表示当 selected 属性存在且 selectedUrl 属性存在时，已选项 datagrid 对获取到的数据的筛选方式；等同于 datagrid 的源生属性 loadFilter 的功能；
			//                              参数签名为 function(data, selected)，data 表示通过 selectedUrl 返回的数据对象；selected 表示已选的项数组对象；其 this 指向已选项 datagrid 对象；
			//                          若不指定 selectedFilter，则不处理通过 selectedUrl 返回的数据，并直接 loadData 给已选项 datagrid 对象；
			datagridOptions: {}
        }, options, { width: diaRealWidth, minWidth: diaMinWidth, centerWidth: defaultCenterWith });
        delete opts.datagridOptions;
        delete opts.accordionOptions;

		var buttons = [
            btnSave = {
                id: "save", text: "確定", iconCls: 'icon-ok',hidden: opts.enableSaveButton ? false : true,
                index: 101,
                handler: function () {
                    if ($.isFunction(opts.onEnter)) { return opts.onEnter.call(dg[0], value); }
                }
            }
        ];

        if (!$.util.likeArrayNotString(opts.buttons)) { opts.buttons = []; }
        $.array.merge(opts.buttons, buttons);
		opts.buttons = $.array.filter(opts.buttons, function (val) { return $.util.parseFunction(val.hidden, val) ? false : true; });
        $.array.sort(opts.buttons, function (a, b) {
            return ($.isNumeric(a.index) ? a.index : 0) - ($.isNumeric(b.index) ? b.index : 0);
        });

		var value = self.getSelected(datagridOptions.singleSelect, opts.selected), tempData = self.getOriginalSelected(value);
		if (opts.locale) {
            opts.inline = true;
        }
		var SltorDia = $("<div id='"+module+"SltorDia'></div>").appendTo(opts.locale ? opts.locale : "body");

        var dg = null,
            dia = SltorDia.dialog($.extend({}, opts, {
                content: "<div class=\"grid-selector-container\"></div>",
                toolbar: "",
                onSave: function () {
                    if ($.isFunction(opts.onEnter)) {
                        return opts.onEnter.call(dg[0], value);
                    }
                }
            }));

        var container = dia.find(".grid-selector-container"),
            width = (($.isNumeric(opts.width) ? opts.width : dia.outerWidth()) - accordionRealWith - opts.centerWidth) / 2,
            leftPanel = $("<div data-options=\"region: 'west', split: false, border: false\"></div>").width(accordionRealWith).appendTo(container),
            centerPanel = $("<div data-options=\"region: 'center', border: true, bodyCls: 'grid-selector-outer-center'\"></div>").appendTo(container),
            inLayout = $("<div />").appendTo(centerPanel),
            inLeftPanel = $("<div data-options=\"region: 'west', split: false, border: false\"></div>").width(width).appendTo(inLayout),
            inCenterPanel = $("<div data-options=\"region: 'center', border: true, bodyCls: 'grid-selector-buttons'\"></div>").appendTo(inLayout),
            inRightPanel = $("<div data-options=\"region: 'east', split: false, border: false\"></div>").width(width).appendTo(inLayout),
            accordion = accordionPanels.length > 0 ? $("<div />").appendTo(leftPanel) : null,
            dg1 = $("<div></div>").appendTo(inLeftPanel),
            dg2 = dg = $("<div class=\"grid-selector\"></div>").appendTo(inRightPanel),
            btn1 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-last" }).tooltip({ content: "选择全部" }).appendTo(inCenterPanel);
			if(opts.checkedBtn){
				var btn2 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-next" }).tooltip({ content: "選擇勾選" }).appendTo(inCenterPanel),
					btn3 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-prev" }).tooltip({ content: "取消勾選" }).appendTo(inCenterPanel);
			}
			var btn4 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-first" }).tooltip({ content: "取消全部" }).appendTo(inCenterPanel);
		inLayout.layout({ fit: true });
        container.layout({ fit: true });
        dia.extSelector = {
            accordion: accordion,
            datagrid1: dg1,
            datagrid2: dg2
        };

        $.util.delay(function () {
            var diaOpts = dia.dialog("options"), onResize = diaOpts.onResize, init = false, ceilds = [],jrows = {},pagin=false,
                extToolbar = self.checkToolbar(opts.extToolbar, datagridOptions.toolbar),
                toolbarObj = extToolbar ? self.getToolbar(datagridOptions.toolbar) : null,
                accordionOpts = accordionPanels.length > 0 ? $.extend({ animate: true }, accordionOptions, { fit: true, border: false }) : {};
			var dg2url,dg2Params = $.extend({},opts.queryParams);
			if(opts.remoteUrl){
				var dgfColumns = datagridOptions.frozenColumns?$.array.clone(datagridOptions.frozenColumns[0]):[],
					dgoColumns = $.array.clone(datagridOptions.columns[0]),
					dgColumns = $.array.merge(dgfColumns,dgoColumns),
					dglen = dgColumns.length,i=0,j=1,val,reg,rsfg;
				while(i<dglen){
					reg = /^[a-zA-Z_]+$/g;
					val = dgColumns[i++].field;
					rsfg = reg.test(val);
					if(val == opts.valueField){val =  'valuefield '+(rsfg?'(':'[') +val +(rsfg?')':']'); }
					else if(val == opts.textField){ val =  'textfield ' +(rsfg?'(':'[') +val +(rsfg?')':']'); }
					else{
						if(j>3){ val = "''"+' '+(rsfg?'(':'[')  + val +(rsfg?')':']'); }else{	val = 'colfield'+(j++)+' '+(rsfg?'(':'[')  + val +(rsfg?')':']');}
					}
					$.array.push(ceilds, val);
				}
				delete dg2Params.first;
				dg2url = datagridOptions.selectedUrl;
				dg2Params.fieldCols = ceilds;
				dg2Params.input_name = opts.iptname;
				opts.queryParams.input_name = opts.iptname;
				opts.queryParams.idField = datagridOptions.idField;
				pagin = true;
			}
            var dgOpts1 = $.extend({ striped: true }, datagridOptions, {
                title: "待選擇項", fit: true, border: false, doSize: true, queryParams: opts.queryParams,
                noheader: false, iconCls: null, collapsible: false, minimizable: false, maximizable: false, closable: false,
                toolbar: extToolbar ? toolbarObj.toolbar("toolbar") : null,
                onSelect: function (rowIndex, rowData) {
					if(!opts.checkedBtn){
						if ($.isFunction(datagridOptions.onSelect)) {
							datagridOptions.onSelect.apply(this, arguments);
						}
						if(opts.remoteUrl){
							var data=[];
							data[0] = rowData;
							jrows['data'] = data;
							jrows['input_name'] = opts.iptname;
							jrows['value_field'] = opts.valueField;
							jrows['text_field'] = opts.textField;
							$.util.postAjaxData(opts.remoteUrl+'/x_xinsCbSeltor',
													jrows,
													function(){
														dg1.datagrid("clearSelections");
														dg1.datagrid('reload');
														dg2.datagrid('reload');
													} );
						}else{
							self.selectRow(datagridOptions, rowData, dg2, refreshValue);
						}
					}
                },
                onUnselect: function (rowIndex, rowData) {
                    if ($.isFunction(datagridOptions.onUnselect)) {
                        datagridOptions.onUnselect.apply(this, arguments);
                    }
					if(!opts.remoteUrl && !opts.checkedBtn){
						self.unselectRow(datagridOptions, rowData, dg2, refreshValue);
					}
                },
                onLoadSuccess: function (data) {
                    if ($.isFunction(datagridOptions.onLoadSuccess)) {
                        datagridOptions.onLoadSuccess.apply(this, arguments);
                    }
					if(!opts.remoteUrl){
						if (!init) {
							self.selectRowOnFirst(dg1, tempData);
							init = true;
						}
						else {
							self.selectRowOnNotFirst(datagridOptions.idField, dg1, dg2);
						}
					}
                }
            }),
            dgOpts2 = $.extend({}, dgOpts1, {
                title: "已選擇項", url: dg2url, queryParams: dg2Params, remoteSort: false , pagination: pagin, toolbar: null,
                onSelect: function (rowIndex, rowData) {
					if(!opts.checkedBtn){
						if(opts.remoteUrl){
							var data=[];
							data[0] = rowData;
							jrows['data'] = data;
							jrows['input_name'] = opts.iptname;
							jrows['fields'] = ceilds;
							jrows['value_field'] = opts.valueField;
							$.util.postAjaxData(opts.remoteUrl+'/x_xdelCbSeltor',
													jrows,
													function(){
														dg1.datagrid("clearSelections");
														dg1.datagrid('reload');
														dg2.datagrid('reload');
													} );
						}else{
							self.removeRow(datagridOptions.idField, rowData, dg1, dg2, refreshValue);
						}
					}
                },
                onUnselect: function (rowIndex, rowData) { },
                onLoadSuccess: function (data) {  if(opts.remoteUrl){ refreshValue(); } }
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

            if (btn1) {
				btn1.click(function () {
					if(opts.remoteUrl){
						var rows = dg1.datagrid("getRows"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						jrows['text_field'] = opts.textField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xinsAllCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												} );
					}else{
						self.selectAllRow(datagridOptions.idField, dg1, refreshValue);
					}
				});
			}
			if (btn2) {
				btn2.click(function () {
					if(opts.remoteUrl){
						var rows = dg1.datagrid("getChecked"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						jrows['text_field'] = opts.textField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xinsCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid("clearChecked");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												} );
					}else{
						$.each(data, function (i, val) { self.selectRow(datagridOptions, val, dg2, refreshValue) });
					}
				});
			}
            if (btn3) {
				btn3.click(function () {
					if(opts.remoteUrl){
						var rows = dg2.datagrid("getChecked"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xdelCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid("clearChecked");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												});
					}else{
						$.each(data, function (i, val) { self.removeRow(datagridOptions.idField, val, dg1, dg2, refreshValue); });

					}
				});

			}
            if (btn4) {
				btn4.click(function () {
					if(opts.remoteUrl){
						var rows = dg2.datagrid("getRows"), data = $.array.clone(rows);
						jrows['data'] = data;
						jrows['input_name'] = opts.iptname;
						jrows['value_field'] = opts.valueField;
						$.util.postAjaxData(opts.remoteUrl+'/x_xclsCbSeltor',
												jrows,
												function(){
													dg1.datagrid("clearSelections");
													dg1.datagrid('reload');
													dg2.datagrid('reload');
												});
					}else{
						self.unselectAllRow(dg1, dg2, refreshValue);
					}
				});
			}

            //以下是先创建各panel的div，然后追加到accordion的div中，再一次性渲染accordion的方式。这种方式需要最后初始化accordion，记得。
            //采用这种方式是因为accordion的add方法存在一个“可能是bug”的现象，accordion在执行add之后，panel的content值并不是马上被渲染完成的，即add之后无法获取content中设定的对象。
            //而因为这个类似bug的存在，导致“tree在加载数据之前的打开遮罩层的位置始终无法定位到其父级的panel”，因为tree还未被及时渲染到panel中，无法通过tree对象获取到父级panel。
            //为了向panel的options中添加额外的属性queryable和queryType，只好创建panel的div时，以动态调整data-options的方式追加给panel的div。
            //            queryable：这是boolean值，表示该panel是否需要组织查询条件，值由“accordionPanels数组中每个元素的type”决定，当type为“tree”时，queryable为true；
            //                  该属性，在“绑定datagrid1的toolbar中的查询按钮事件”时，用于定位“accordion中当前选中的、并且有查询条件可组织的panel”。
            //            queryType：这是string值，继承于“accordionPanels数组中每个元素的type”的值；
            //                  该属性，用于定位panel之后，明确“从哪里获得组装查询条件的对象”。
            //以上描述的bug，在1.4.x版本中已经修复。但为了兼容1.3.x版本，仍旧采取原做法。
            $.each(accordionPanels, function (index, item) {
                var title = $.string.isNullOrWhiteSpace(item.panelTitle) ? "面板[" + index + "]" : item.panelTitle,
                    panelProp = { title: title };
                var accordionPanel = $("<div />").attr(panelProp).appendTo(accordion);
                switch (item.type) {
                    case undefined:
                    case "":
                    case "content":
                        accordionPanel.attr({ "data-options": "queryable: false, queryType: 'content'" }).html(String(item.typeOptions));
                        break;
                    case "tree":
                        accordionPanel.attr({ "data-options": "queryable: true, queryType: '" + item.type + "'" });
                        var treeOptions = item.typeOptions || {};
                        if (!$.isFunction(treeOptions.onSelectParamer)) { treeOptions.onSelectParamer = self.paramsBuilderForTree; }
                        if (!$.isFunction(treeOptions.onSelectQueryer)) { treeOptions.onSelectQueryer = self.dataQueryerForTree; }
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
								$.extend(params, opts.queryParams);
                                if (extToolbar) { $.extend(params, toolbarObj.toolbar("getValues")); }
								params.first = false;
                                treeOptions.onSelectQueryer.call(this, params, dg1);
                            },
                            onBeforeLoad: function (node, param) {
								opts.first = true;
                                var pass = true;
                                if ($.isFunction(treeOptions.onBeforeLoad)) { pass = treeOptions.onBeforeLoad.apply(this, arguments); }
                                if (pass) { $.easyui.loading({ locale: accordionPanel, msg: "數據加載中..." }); }
                                return pass;
                            },
                            onLoadSuccess: function (node, data) {
                                if ($.isFunction(treeOptions.onLoadSuccess)) { treeOptions.onLoadSuccess.apply(this, arguments); }
                                $.easyui.loaded(accordionPanel);
                            },
                            onLoadError: function (p1, p2, p3) {
                                if ($.isFunction(treeOptions.onLoadError)) {
                                    treeOptions.onLoadError.apply(this, arguments);
                                }
                                $.easyui.loaded(accordionPanel);
                            }
                        });
                        $("<ul/>").appendTo(accordionPanel).tree(treeOpts);
                        break;
                }
            });
            if (accordionPanels.length > 0) { accordion.accordion(accordionOpts); }

			dia.grid = dg1.datagrid(dgOpts1);
			if(datagridOptions.filterCol){
				dia.grid.datagrid('removeFilterRule');
				dia.grid.datagrid('enableFilter', datagridOptions.filterCol);
			}
            dia.valueGrid = dg2.datagrid(dgOpts2);

			if(datagridOptions.filterCol){
				dia.valueGrid.datagrid('removeFilterRule');
				dia.valueGrid.datagrid('enableFilter', datagridOptions.filterCol);
			}
            //dg1.datagrid(dgOpts1);
            //dg2.datagrid(dgOpts2);

            if (datagridOptions.pagination) { self.removePaginationMessage(dg1); }
			if(opts.remoteUrl) {
				self.removePaginationMessage(dg2);
			}else{
				self.loadSelectedData(tempData, datagridOptions.selectedUrl, datagridOptions.selectedMethod, datagridOptions.selectedFilter, dg2, refreshValue, opts.data, ceilds, opts.iptname);
			}

            diaOpts.onResize = function (w, h) {
                if ($.isFunction(onResize)) { onResize.apply(this, arguments); }
                $.util.delay(function () {
                    if (self.checkResizable(diaOpts, w, h)) {
                        var ww = (dia.panel("options").width - diaOpts.accordionRealWith - diaOpts.centerWidth) / 2;
						 inLeftPanel.panel("resize", { width: ww });
                        inRightPanel.panel("resize", { width: ww });
                        inLayout.layout("resize");
                    }
                });
            };
        });

        return dia;

	};

})(jQuery);