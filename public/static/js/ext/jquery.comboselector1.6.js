/**
* jQuery EasyUI 1.3.6
* Copyright (c) 2009-2014 www.jeasyui.com. All rights reserved.
*
* Licensed under the GPL or commercial licenses
* To use it on other terms please contact author: info@jeasyui.com
* http://www.gnu.org/licenses/gpl.txt
* http://www.jeasyui.com/license_commercial.php
*
* jQuery EasyUI combosltor Plugin Extensions 1.0 beta
* jQuery EasyUI combosltor 插件扩展
* jquery.combosltor1.6.js
* 二次开发 CYP
* 最近更新：2014-09-09
*
* 依赖项：
*   1、jquery.jdirk.js
*   2、jeasyui.extensions.dialog.sealed.sample.js （快速灵活打开dialog，需要该扩展支持）
*   3、jeasyui.extensions.toolbar.js、jeasyui.extensions.toolbar.css [可灵活依赖] （当需要额外工具条功能时，需要依赖）
*   4、jeasyui.extensions.base.isEasyUI.js、jeasyui.extensions.base.current.js [可灵活依赖] （当额外工具条需要按钮查询功能时，需要依赖）
*   5、jeasyui.extensions.gridselector1.6.js
*   6、jeasyui.extensions.base.loading.js、jeasyui.extensions.base.css （tree加载的遮罩层，需要该扩展支持）

*
* Copyright (c) 2013-2014 ChenJianwei personal All rights reserved.
* http://www.chenjianwei.org
*/
(function ($, undefined) {

    function create(target) {
        var state = $.data(target, "combosltor"), opts = state.options,jiptname = $(target).attr('name'),
            dgOpts = opts.datagridOptions?opts.datagridOptions:opts.treeOptions,
			idField = state.valueField = dgOpts.idField  = dgOpts.idField || state.valueField || opts.valueField,jrows = {},
            valueField = state.valueField = dgOpts.idField || idField;
		jrows['flag'] = 0;
		jrows['input_name'] = jiptname;
        var localdia = $(target).currentLayout();
		//創建清除臨時資料
		//if(opts.remoteUrl){ $.util.requestAjaxBoolean(opts.remoteUrl+'/x_xclsCbSeltor',jrows); }
		if(opts.datagridOptions){
			dgOpts.singleSelect = opts.multiple ? false : true;
		}else{
			dgOpts.checkbox = opts.multiple ? true : false;
		}
		 var t = $(target).addClass("combosltor-f").combo($.extend({}, opts, {
				 icons: [{
						iconCls:'icon-application-form-magnify',
						handler: function(e){
							var grid = opts.selectorTypes[opts.selector];
							if(!state.dialog){
								state.dialog = grid.method($.extend({}, opts, {
									width: $.isNumeric(opts.panelWidth) ? opts.panelWidth : grid.width,
									height: $.isNumeric(opts.panelHeight) ? opts.panelHeight : grid.height,
									selected: t.combo(opts.multiple ? "getValues" : "getValue"),
									checkedBtn : opts.checkedBtn ? opts.checkedBtn : false,
                                    locale : localdia,
									url: opts.url, data: dgOpts.data, iptname:jiptname,
									buttons: [
										{
											index: 101.5, text: "清除", iconCls: "icon-cancel",hidden: opts.enableClearButton ? false : true,
											handler: function (d) {
												if(opts.remoteUrl){
													jrows['flag'] = 0;
													jrows['time'] = (new Date()).valueOf();
													$.util.requestAjaxBoolean(opts.remoteUrl+'/x_xclsCbSeltor',jrows);
												}
												t.combo("clear").combo("setValue",'').combo("setText",'');
												state.dialog.dialog("destroy");
												state.dialog = null;
											}
										},
										{
											index: 102, text: "取消", iconCls: "icon-no", handler: function () {
												//取消刪除已指派資料
												if(opts.remoteUrl){
													jrows['flag'] = 1;
													$.util.requestAjaxBoolean(opts.remoteUrl+'/x_xclsCbSeltor',jrows);
												}
												state.dialog.dialog("close");
											}
										}
									],
									onEnter: function (val) {
										//確認已指派資料
										if(opts.remoteUrl){
											$.util.requestAjaxBoolean(opts.remoteUrl+'/x_xcmdCbSeltor',jrows);
										}
										if (val) {
											var isArray = $.util.likeArrayNotString(val),
												values = isArray ? $.array.map(val, function (value) { return value[idField]; }) : [val[idField]];
											state.data = opts.data = val;
											setValues(target, values);
										} else {
											t.combo("clear");
										}
										state.dialog.dialog("close");
									},
									onClose: function () {
										//取消刪除已指派資料
										//var state = $.data(target, "combo");
										//if (state && state.options) { t.combo("hidePanel"); }
										if(opts.autoDestroy){
											state.dialog.dialog("destroy");
											state.dialog = null;
										}
									} ,
									onLoadSuccess: function (data) {
										state.data = data ? ($.util.likeArrayNotString(data) ? data : data.rows) : [];
										 if ($.isFunction(opts.onLoadSuccess)) {
											opts.onLoadSuccess.apply(this, arguments);
										}
									},
									onLoadError: function () {
										$.fn.datagrid.defaults.onLoadError.apply(this, arguments);
										if ($.isFunction(opts.onLoadError)) {
											opts.onLoadError.apply(this, arguments);
										}
									},
									onBeforeLoad: function (param) {
										$.fn.datagrid.defaults.onBeforeLoad.apply(this, arguments);
										if ($.isFunction(opts.onBeforeLoad)) {
											opts.onBeforeLoad.apply(this, arguments);
										}
									}
								}));
							}else{
								state.dialog.dialog('open');
							}
							var dopts = state.dialog.dialog("options");
							if ($.isFunction(opts.onShowPanel)) { opts.onShowPanel.apply(this, arguments); }
						}
					} ],
                onDestroy: function () {
                    if (state.dialog) {
                        state.dialog.dialog("destroy");
                        state.dialog = null;
                    }
                    if ($.isFunction(opts.onDestroy)) { opts.onDestroy.apply(this, arguments); }
                },
                onChange: function (newValue, oldValue) {
                    if ($.isFunction(opts.onChange)) {
                        opts.onChange.apply(this, arguments);
                    }
                }
            }));
        state.data = opts.data ? opts.data : [];
        opts.originalValue = opts.value;
        opts.originalText = opts.text;
        if (opts.lazyLoad) {
            initValue();
        } else {
            $[opts.method](opts.url, $.extend({}, opts.queryParams || {}, { page: opts.pageNumber || $.fn.datagrid.defaults.pageNumber, rows: opts.rows || $.fn.datagrid.defaults.pageSize }), function (data) {
                state.data = data ? ($.util.likeArrayNotString(data) ? data : data.rows) : [];
                initValue();
            });
        }
        t.combo("validate");

        function initValue() {
            if (state.data && state.data.length) {
                if (opts.value) {
                    setValues(target, $.util.likeArrayNotString(opts.value) ? opts.value : [opts.value]);
                }
            } else {
                if (opts.text) {
                    setText(target, opts.text);
                } else {
                    if (opts.value) {
                        setValues(target, $.util.likeArrayNotString(opts.value) ? opts.value : [opts.value]);
                    }
                }
            }
        };
    };


    function setValues(target, values) {
        var t = $(target), state = $.data(target, "combosltor"), opts = state.options,
            array = $.util.likeArrayNotString(values) ? values : [values],
            getText = function (value) {
                var row = $.array.first(state.data, function (val) { return val[state.valueField] == value; });
				return row ? row[opts.textField] : null;
            },
            hasTextField = $.util.likeArrayNotString(state.data) && opts.textField,
			hasobjText = $.util.isObject(state.data)&&opts.textField,
            text = hasTextField ? $.array.map(array, function (val) { return getText(val); }).join(opts.separator) : (hasobjText?state.data[opts.textField]:$.array.join(array, opts.separator));

			if(array.length>1){
				var j_others = ',...';
			}else{
				var j_others = '';
			}
        t.combo("setValues", array).combo("setText", (text || array.join(opts.separator))+j_others);
    };

    function setText(target, text) {
        $(target).combo("setText", text).combosltor("options").text = text;
    };

    function reset(target) {
        var t = $(target), opts = t.combosltor("options");
        t.combosltor("setValue", opts.originalValue).combosltor("setText", opts.originalText);
    };


    function loadData(target, data) {
        var t = $(target), state = $.data(target, "combosltor");
        if (state.dialog) {
            state.dialog.find(".grid-selector").datagrid("loadData", data);
        } else {
            state.data = data;
        }
    };




    var selectorTypes = {
        grid: { method: $.easyui.showGridSelector, width: 580, height: 360 },
        dblgrid: { method: $.easyui.showDblGridSelector, width: 800, height: 500 },
        tree: { method: $.easyui.showTreeSelector, width: 300, height: 260 },
        treedblgrid: { method: $.easyui.showTreeDblGridSelector, width: 680, height: 500 },
        accordiondblgrid: { method: $.easyui.showAccordionDblGridSelector, width: 680, height: 500 }
    };




    $.fn.combosltor = function (options, param) {
        if (typeof options == "string") {
            var method = $.fn.combosltor.methods[options];
            if (method) {
                return method(this, param);
            } else {
                return this.combo(options, param);
            }
        }
        options = options || {};
        return this.each(function () {
            var state = $.data(this, "combosltor");
            if (state) {
                $.extend(state.options, options);
            } else {
                $.data(this, "combosltor", { options: $.extend({}, $.fn.combosltor.defaults, $.fn.combosltor.parseOptions(this), options) });
                create(this);
            }
        });
    };

    $.fn.combosltor.parseOptions = function (target) {
        return $.extend({},
            $.fn.combo.parseOptions(target),
            $.fn.datagrid.parseOptions(target),
            $.parser.parseOptions(target, ["text", "selector", "iconCls", { extToolbar: "boolean" }])
            );
    };

    $.fn.combosltor.methods = {
        options: function (jq) {
            var opts = jq.combo("options"), copts = $.data(jq[0], 'combosltor').options;
            return $.extend(copts, {
                originalValue: opts.originalValue, originalText: opts.originalText, disabled: opts.disabled, readonly: opts.readonly
            });
        },
		/*destroy : function(jq){ 
			var opts = jq.combo("options");
			opts.onDestroy.call(jq);
			jq.combo("destroy");
		},*/
        setValues: function (jq, values) { return jq.each(function () { setValues(this, values); }); },

        setValue: function (jq, value) { return jq.each(function () { setValues(this, [value]); }); },

        setText: function (jq, text) { return jq.each(function () { setText(this, text); }); },

        loadData: function (jq, data) { return jq.each(function () { loadData(this, data); }); },

        getData: function (jq) { return $.data(jq[0], "combosltor").data; },

        reset: function (jq) { return jq.each(function () { reset(this); }); }
    };

    $.fn.combosltor.defaults = $.extend({}, $.fn.combo.defaults, {

        //  表示弹出的表格选择框的类型；这是一个 String 类型值，可选的值有：
        //      grid    :
        //      dblgrid :
        //      tree    :
        //      treedblgrid:
        //      accordiondblgrid:
        selector: "dblgrid",
        text: null,
        extToolbar: false,
		topMost: false,
		method: 'post',
		hasDownArrow: false,
        panelWidth: "auto",
        panelHeight: "auto",
		autoDestroy: false,
        autoShowPanel: false,

        //  表示 grid-selector 中的数据是否为懒加载；
        //  如果该值设置为 true，则是在鼠标点击打开 grid-selector 后，表格中的数据才实际被加载；否则将是在控件被初始化时即刻加载数据；
        //  boolean 类型值，默认为 true。
        lazyLoad: true,
		enableSaveButton: true,
		enableClearButton: true,
        url: null,

        data: null,

        selectorTypes: selectorTypes,
        onLoadSuccess: function (data) { },

        onLoadError: function () { },

        onBeforeLoad: function (param) { }
    });


    if ($.fn.datagrid) {
        $.extend($.fn.datagrid.defaults.editors, {
            combosltor: {
                init: function (container, options) {
                    var box = $("<input type=\"text\"></input>").appendTo(container).combosltor(options);
                    box.combosltor("textbox").addClass("datagrid-editable-input");
                    return box;
                },
                destroy: function (target) {
                    $(target).combosltor("destroy");
                },
                getValue: function (target) {
                    var t = $(target), opts = t.combosltor("options");
                    return t.combosltor(opts.multiple ? "getValues" : "getValue");
                },
                setValue: function (target, value) {
                    var t = $(target), opts = t.combosltor("options");
                    if (value) {
                        if (opts.multiple) {
                            if ($.util.likeArrayNotString(value)) {
                                t.combosltor("setValues", value);
                            } else if (typeof value == "string") {
                                t.combosltor("setValues", value.split(opts.separator));
                            } else {
                                t.combosltor("setValue", value);
                            }
                        } else {
                            t.combosltor("setValue", value);
                        }
                    } else {
                        t.combosltor("clear");
                    }
                },
                resize: function (target, width) {
                    $(target).combosltor("resize", width);
                },
                setFocus: function (target) {
                    $(target).combosltor("textbox").focus();
                }
            }
        });
    }


    $.parser.plugins.push("combosltor");

    if ($.fn.form && $.isArray($.fn.form.comboList)) {
        $.fn.form.comboList.push("combosltor");
        //$.array.insert($.fn.form.comboList, 0, "combosltor");
    }

})(jQuery);