/**
* jQuery EasyUI 1.4.3
* Copyright (c) 2009-2015 www.jeasyui.com. All rights reserved.
*
* Licensed under the GPL license: http://www.gnu.org/licenses/gpl.txt
* To use it on other terms please contact us at info@jeasyui.com
* http://www.jeasyui.com/license_commercial.php
*
* jQuery EasyUI selector 扩展
* jeasyui.extensions.selector.dblDatagrid.js
* 开发 落阳
* 最近更新：2016-03-31
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

    $.util.namespace("$.easyui.showSelector.dblDatagrid");

    var self = $.easyui.showSelector.self.methods;
    $.extend(self, {

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
            }
            else { 
                datagrid2.datagrid("deleteRow", datagrid2.datagrid("getRowIndex", idValue));
                if ($.isFunction(refresh)) { refresh(); }
            }
        },

        //  已选datagrid加载已选数据
        loadSelectedData: function (selected, selectedUrl, selectedMethod, selectedFilter, datagrid2, refresh) { 
			//console.log(selectedUrl);
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
						console.log(data);
                        if ($.isFunction(selectedFilter)) { data = selectedFilter.call(datagrid2, data, $.util.isString(selected) ? selected.split(",") : selected); }
                        datagrid2.datagrid("loadData", data);
                        if ($.isFunction(refresh)) { refresh(); }
                    }
                });
            }
        }
    });



    function initialize(options) {
        var datagridOptions = options.datagridOptions ? options.datagridOptions : {};
        //计算 dialog 的最大和最小宽度
        var defaultCenterWith = 30, minDatagridWidth = self.getDataGridMinWidth(datagridOptions.pagination),
            diaMinWidth = (minDatagridWidth * 2) + defaultCenterWith,
            diaRealWidth = options.width ? (options.width < diaMinWidth ? diaMinWidth : options.width) : diaMinWidth + 50;
        var opts = $.extend({
            title: "選擇數據，" + (datagridOptions.singleSelect ? "單選" : "多選"),
        }, options, { width: diaRealWidth, minWidth: diaMinWidth, centerWidth: defaultCenterWith });
        delete opts.datagridOptions;

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
            width = (($.isNumeric(opts.width) ? opts.width : dia.outerWidth()) - opts.centerWidth) / 2,
            leftPanel = $("<div data-options=\"region: 'west', split: false, border: false\"></div>").width(width).appendTo(container),
            centerPanel = $("<div data-options=\"region: 'center', border: true, bodyCls: 'grid-selector-buttons'\"></div>").appendTo(container),
            rightPanel = $("<div data-options=\"region: 'east', split: false, border: false\"></div>").width(width).appendTo(container),
            dg1 = $("<div></div>").appendTo(leftPanel),
            dg2 = dg = $("<div class=\"grid-selector\"></div>").appendTo(rightPanel),
            btn1 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-last" }).tooltip({ content: "選擇全部" }).appendTo(centerPanel),
            btn2 = datagridOptions.singleSelect ? null : $("<a />").linkbutton({ plain: true, iconCls: "pagination-first" }).tooltip({ content: "取消全部" }).appendTo(centerPanel);

        container.layout({ fit: true });
        dia.selector = {
            datagrid1: dg1,
            datagrid2: dg2
        };

        $.util.delay(function () {
            var diaOpts = dia.dialog("options"), onResize = diaOpts.onResize, init = false;
            var dgOpts1 = $.extend({ striped: true, checkOnSelect: true, selectOnCheck: true, rownumbers: true }, datagridOptions, {
                title: "待選擇項", fit: true, border: false, doSize: true,
                noheader: false, iconCls: null, collapsible: false, minimizable: false, maximizable: false, closable: false,
                toolbar: self.checkToolbar(opts.extToolbar, datagridOptions.toolbar) ? self.getToolbarDiv(datagridOptions.toolbar) : null,
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
                title: "已選擇項", url: null, queryParams: {}, remoteSort: false , pagination: false, toolbar: null,
                onSelect: function (rowIndex, rowData) {
                    self.removeRow(datagridOptions.idField, rowData, dg1, dg2, refreshValue);
                },
                onUnselect: function (rowIndex, rowData) { },
                onLoadSuccess: function (data) {  }
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
					self.selectAllRow(datagridOptions.idField, dg1, refreshValue);
				}); 				
			}
            if (btn2) { btn2.click(function () { self.unselectAllRow(dg1, dg2, refreshValue); }); }

            dg1.datagrid(dgOpts1);
            dg2.datagrid(dgOpts2);
            if (datagridOptions.pagination) { self.removePaginationMessage(dg1); } 
			
            self.loadSelectedData(tempData, datagridOptions.selectedUrl, datagridOptions.selectedMethod, datagridOptions.selectedFilter, dg2, refreshValue);
		
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
    }

    //  增加自定义扩展方法 $.easyui.showSelector.dblDatagrid；该方法弹出一个包含两个 easyui-datagrid 控件的选择框窗口；
    $.easyui.showSelector.dblDatagrid = function (options) {
        options = options || {};
        var opts = $.extend({}, $.easyui.showSelector.dblDatagrid.defaults, options);

        return initialize(opts);
    };

    // 继承 $.easyui.showDialog 的属性，并增加了以下属性
    $.easyui.showSelector.dblDatagrid.defaults = $.extend({}, $.easyui.showDialog.defaults, {
        width: 750,
        height: 580,
        minHeight: 280,
        title: "數據選擇框",
        iconCls: "icon-hamburg-zoom",
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