/**
* jQuery EasyUI 1.4.3
* Copyright (c) 2009-2015 www.jeasyui.com. All rights reserved.
*
* Licensed under the GPL license: http://www.gnu.org/licenses/gpl.txt
* To use it on other terms please contact us at info@jeasyui.com
* http://www.jeasyui.com/license_commercial.php
*
* jQuery EasyUI treegrid 扩展
* jeasyui.extensions.toolbar.js
* 开发 流云
* 由 落阳 整理
* 最近更新：2016-03-25
*
* 依赖项：
*   1、jquery.jdirk.js
*
* Copyright (c) 2016 ChenJianwei personal All rights reserved.
*/
(function ($) {

    function initialize(target) {
        var t = $(target), isDiv = /^(?:div)$/i.test(target.nodeName), cc = t.children(),
            toolbar = isDiv ? t : $("<div></div>").insertAfter(t).append(t);
        if (!isDiv) {
            toolbar.attr({ "class": t.attr("class"), "style": t.attr("style") }).removeClass("easyui-toolbar");
            t.children().each(function () { toolbar.append(this); });
            t.hide();
        }
        var state = $.data(target, "toolbar"), opts = state.options;
        state.toolbar = toolbar.addClass("dialog-toolbar toolbar");
        t.addClass("toolbar-f");

        state.wrapper = $("<table class='toolbar-wrapper' cellspacing='0' cellpadding='0' ></table>").appendTo(toolbar);
        appendItem(target, cc);

        setSize(target, { width: opts.width, height: opts.height });
        toolbar.bind("_resize", function () {
            setSize(target);
        });
    };

    function appendItem(target, item) {
        if (!item) { return; }
        if ($.array.likeArrayNotString(item)) {
            if (item.length) {
                $.each(item, function (i, n) { appendItem(target, n); });
            }
        } else if ($.isFunction(item)) {
            appendItem(target, item.call(target), false);
        } else {
            appendItemOption(target, item);
        }
    }

    function appendItemOption(target, item) {
        if (!item) { return; }
        var state = $.data(target, "toolbar"),
            tr = state.wrapper.find("tr:last");
        if (!tr.length) { tr = $("<tr class='toolbar-row'></tr>").appendTo(state.wrapper); }
        var container = $("<td class='toolbar-item-container'></td>").appendTo(tr);
        appendItemToContainer(target, container, item);
    };

    function appendItemToContainer(target, container, item) {
        var state = $.data(target, "toolbar"), opts = state.options;
        if ($.util.isDOM(item)) {
            //处理“以html的方式”初始化的toolbar
            var cell = $(item).addClass("toolbar-item").appendTo(container), text = cell.text();
            var isDivOrSpan = /^(?:div|span)$/i.test(cell[0].nodeName);
            if (isDivOrSpan && $.array.contains(["-", "—", "|"], text)) {
                cell.addClass("dialog-tool-separator").empty();
                $.data(cell[0], "toolbar-item-data", {
                    actions: opts.itemTypes.separator, target: cell, options: {}, type: "separator", container: container
                });
            } else {
                //在html中写的元素，不做options处理，权当 custom 类型元素
                $.data(cell[0], "toolbar-item-data", {
                    actions: null, target: cell, options: null, type: "custom", container: container
                });
            }
        } else if ($.array.contains(["string", "number", "date"], $.type(item))) {
            item = $.string.trim(item);
            if ($.array.contains(["-", "—", "|"], item)) {
                appendItemToContainer(target, container, { type: "separator" });
            } else if ($.array.contains(["\\t", "\\n"], item)) {
                //处理换行符 Add by cyp 2017-10-16
                var tr = $("<tr class='toolbar-row'></tr>").appendTo(state.wrapper); 
            } else if ($.string.isHtmlText(item)) {
                $(item).each(function () { appendItemToContainer(target, container, this); });
            } else {
                appendItemToContainer(target, container, { type: "label", options: { text: item } });
            }
        } else {
            var itemOpts = $.extend({}, opts.itemOptions, item || {}),
                actions = opts.itemTypes[itemOpts.type];
            if (!actions || !actions.init) { return; }
            var tItem = actions.init(container[0], $.extend({}, itemOpts, itemOpts.options || {})).addClass("toolbar-item");
            if (itemOpts.id) { tItem.attr("id", itemOpts.id); }
            if (itemOpts.name) { tItem.attr("name", itemOpts.name); }
            if (itemOpts.cls) { container.addClass(itemOpts.cls); }
            if (itemOpts.itemCls) { tItem.addClass(itemOpts.itemCls); }
            if (itemOpts.style) { container.css(itemOpts.style); }
            if (itemOpts.itemStyle) { tItem.css(itemOpts.itemStyle); }
            if (itemOpts.width) { container.css("width", itemOpts.width); }
            if (itemOpts.align) { container.css("text-align", itemOpts.align); }
            if (itemOpts.htmlAttr) { tItem.attr(itemOpts.htmlAttr); }
            $.data(tItem[0], "toolbar-item-data", {
                actions: actions, target: tItem, options: itemOpts.options, type: itemOpts.type, container: container
            });
        }
    };

    function setSize(target, size) {
        var t = $(target), state = $.data(target, "toolbar"),
            toolbar = state.toolbar, opts = state.options;
        size = $.extend({ width: opts.width, height: opts.height }, size || {});
        toolbar.css({
            width: size.width, height: size.height
        });
        $.extend(opts, size);
        $.util.delay(function () {
            setAlign(target, opts.align);
            setValign(target, opts.valign);
        });
        opts.onResize.call(target, $.isNumeric(size.width) ? size.width : toolbar.width(), $.isNumeric(size.height) ? size.height : toolbar.height());
    };

    function setAlign(target, align) {
        align = String(align);
        if (!$.array.contains(["left", "center", "right"], align.toLowerCase())) { return; }
        var t = $(target), state = $.data(target, "toolbar"),
            wrapper = state.wrapper, opts = state.options, left = 0;
        opts.align = align;
        wrapper.removeClass("toolbar-align-left toolbar-align-center toolbar-align-right").addClass("toolbar-align-" + align);
    };

    function setValign(target, valign) {
        valign = String(valign);
        if (!$.array.contains(["top", "middle", "bottom"], valign.toLowerCase())) { return; }
        var t = $(target), state = $.data(target, "toolbar"),
            toolbar = state.toolbar, wrapper = state.wrapper, opts = state.options,
            outerHeight = toolbar.height(), height = wrapper.height(), top;
        opts.valign = valign;
        wrapper.removeClass("toolbar-valign-top toolbar-valign-middle toolbar-valign-bottom").addClass("toolbar-valign-" + valign);
        switch (valign) {
            case "top": top = 0; break;
            case "middle": top = (outerHeight - height) / 2; break;
            case "bottom": top = (outerHeight - height); break;
        }
        wrapper.css("top", Math.max(top, 0));
    };

    function getItems(target) {
        var ret = [], t = $(target), wrapper = t.toolbar("wrapper");
        wrapper.find("tr:last>td.toolbar-item-container .toolbar-item").each(function () {
            var item = $.data(this, "toolbar-item-data");
            if (item) { ret.push(item); }
        });
        return ret;
    };

    function getValues(target, param) {
        return $(target).serializeObject(param);
    }

    function enable(target) {
        var items = getItems(target);
        $.each(items, function (i, item) {
            if (item.actions && $.isFunction(item.actions.enable)) {
                item.actions.enable(item.target[0]);
            }
        });
    };

    function disable(target) {
        var items = getItems(target);
        $.each(items, function (i, item) {
            if (item.actions && $.isFunction(item.actions.disable)) {
                item.actions.disable(item.target[0]);
            }
        });
    };


    function loadData(target, data) {
        var state = $.data(target, "toolbar"), opts = state.options;
        state.data = opts.loadFilter.call(target, data);
        state.wrapper.empty();
        appendItem(target, state.data);
        opts.onLoadSuccess.call(target, data);
    };

    function getData(target) {
        return $.data(target, "toolbar").data;
    };











    var itemOptions = {
        // 工具项id
        id: null,

        // 工具项name
        name: null,

        // 工具项类型
        type: "button",

        // 工具项options
        options: null,

        // 工具项容器样式名称
        cls: null,

        // 工具项容器样式内容
        style: null,

        // 工具项样式名称
        itemCls: null,

        // 工具项样式内容
        itemStyle: null,

        // 工具项容器宽度
        width: null,

        // 工具项容器水平对齐方式
        align: null,

        // 工具项html属性对象
        htmlAttr: null
    };

    var itemTypes = {

        //  分割符
        separator: {
            init: function (container) {
                return $("<div class='dialog-tool-separator'></div>").appendTo(container);
            }
        },

        //  标签
        //  额外支持属性：
        //      text: string
        //      disabled: boolean、function(t)
        label: {
            defaults: { text: " " },
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {});
                var label = $("<span class='toolbar-item-label'></span>").text(opts.text).appendTo(container);
                if (opts.disabled) {
                    if ($.isFunction(opts.disabled)) {
                        this[opts.disabled.call(label[0], $(container).closest("table.toolbar-wrapper")[0]) ? "disable" : "enable"](label[0]);
                    } else {
                        this[opts.disabled ? "disable" : "enable"](label[0]);
                    }
                }
                return label;
            },
            enable: function (target) {
                $(target).removeClass("toolbar-item-label-disabled");
            },
            disable: function (target) {
                $(target).addClass("toolbar-item-label-disabled");
            }
        },

        //  文本输入框
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      keydown: function(e,t)
        text: {
            defaults: { value: null, disabled: false, width: null },
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {});
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container);
                if (opts.value) { this.setValue(box[0], opts.value); }
                if (opts.disabled) {
                    if ($.isFunction(opts.disabled)) {
                        this[opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]) ? "disable" : "enable"](box[0]);
                    } else {
                        this[opts.disabled ? "disable" : "enable"](box[0]);
                    }
                }
                if (opts.width) { this.resize(box[0], opts.width); }
                var keydown = opts.keydown;
                if (keydown) { keydown = $.string.toFunction(keydown); }
                if ($.isFunction(keydown)) {
                    box.bind("keydown", function (event) {
                        keydown.call(this, event, $(container).closest("table.toolbar-wrapper")[0]);
                    });
                }
                return box;
            },
            setValue: function (target, value) {
                $(target).val(value);
            },
            getValue: function (target) {
                return $(target).val();
            },
            resize: function (target, width) {
                $(target)._outerWidth(width);
            },
            enable: function (target) {
                $(target).removeAttr("disabled");
            },
            disable: function (target) {
                $(target).attr("disabled", true);
            }
        },

        //  复选框
        //      text: string
        //      checked: boolean
        //      disabled: boolean、function(t)
        checkbox: {
            defaults: { checked: false, disabled: false, text: " " },
            init: function (container, options) {
                options = options || {};
                var opts = $.extend({}, this.defaults, $.util.isString(options) ? { text: options } : options);
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var label = $("<label class='toolbar-item-checkbox'></label>").appendTo(container),
                    box = $("<input type='checkbox' class='toolbar-item-checkbox-input' />").attr(prop).appendTo(label),
                    span = $("<span class='toolbar-item-checkbox-text'></span>").text(opts.text).appendTo(label);
                if (opts.checked) { this.setValue(box[0], opts.checked); }
                if (opts.disabled) {
                    if ($.isFunction(opts.disabled)) {
                        this[opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]) ? "disable" : "enable"](box[0]);
                    } else {
                        this[opts.disabled ? "disable" : "enable"](box[0]);
                    }
                }
                return box;
            },
            setValue: function (target, value) {
                $(target).attr("checked", value ? true : false);
            },
            getValue: function (target) {
                return $(target)[0].checked;
            },
            enable: function (target) {
                $(target).removeAttr("disabled").parent().find(">span.toolbar-item-checkbox-text").removeClass("toolbar-item-checkbox-disabled");
            },
            disable: function (target) {
                $(target).attr("disabled", true).parent().find(">span.toolbar-item-checkbox-text").addClass("toolbar-item-checkbox-disabled");
            }
        },

        //  按钮 easyui-linkbutton
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        button: {
            defaults: { plain: true, iconCls: "icon-ok" },
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {});
                var prop = {};
                if (opts.id) { prop.id = opts.id; }
                var handler = opts.onclick || opts.handler,
                    btn = $("<a class='toolbar-item-button'></a>").attr(prop).appendTo(container);
                if (handler) {
                    handler = $.string.toFunction(handler);
                }
                if (opts.disabled && $.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(btn[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                $.fn.toolbar.dealItemOptions(opts);
                return btn.linkbutton($.extend(opts, {
                    onClick: function () {
                        if ($.isFunction(handler)) {
                            handler.call(this, $(container).closest("table.toolbar-wrapper")[0]);
                        }
                    }
                }));
            },
            enable: function (target) {
                $(target).linkbutton("enable");
            },
            disable: function (target) {
                $(target).linkbutton("disable");
            }
        },

        //  验证框 easyui-validatebox
        //  额外支持属性：
        //      vlalue: string
        //      disabled: boolean、function(t)
        //      keydown: function(e,t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        validatebox: {
            defaults: { value: null, disabled: false, width: null },
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {});
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                $.fn.toolbar.dealItemOptions(opts);
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container).validatebox(opts);
                if (opts.value) { this.setValue(box[0], opts.value); }
                if ($.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                if (opts.disabled) { this.disable(box[0]); }
                if (opts.width) { this.resize(box[0], opts.width); }
                var keydown = opts.keydown;
                if (keydown) { keydown = $.string.toFunction(keydown); }
                if ($.isFunction(keydown)) {
                    box.bind("keydown", function (event) {
                        keydown.call(this, event, $(container).closest("table.toolbar-wrapper")[0]);
                    });
                }
                return box;
            },
            setValue: function (target, value) {
                $(target).val(value);
            },
            getValue: function (target) {
                return $(target).val();
            },
            resize: function (target, width) {
                $(target)._outerWidth(width);
            },
            enable: function (target) {
                $(target).removeAttr("disabled");
            },
            disable: function (target) {
                $(target).attr("disabled", true);
            }
        },

        //  文本框 easyui-textbox
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      keydown: function(e,t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        textbox: {
            defaults: { width: null, type: "text", multiline: false },
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {}, { type: options.options && options.options.type ? options.options.type : this.defaults.type });
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container);
                if ($.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                $.fn.toolbar.dealItemOptions(opts);
                box.textbox(opts);
                var keydown = opts.keydown;
                if (keydown) { keydown = $.string.toFunction(keydown); }
                if ($.isFunction(keydown)) {
                    var tb = box.textbox("textbox");
                    tb.bind("keydown", function (event) {
                        keydown.call(this, event, $(container).closest("table.toolbar-wrapper")[0]);
                    });
                }
                return box;
            },
            destroy: function (target) {
                $(target).textbox("destroy");
            },
            setValue: function (target, value) {
                $(target).textbox("setValue", value);
            },
            getValue: function (target) {
                return $(target).textbox("getValue");
            },
            resize: function (target, width) {
                $(target).textbox("resize", width);
            },
            enable: function (target) {
                $(target).textbox("enable");
            },
            disable: function (target) {
                $(target).textbox("disable");
            }
        },

        //  数字框 easyui-numberbox
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      keydown: function(e,t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        numberbox: {
            defaults: { width: null },
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {}, { type: "text" });
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container);
                if ($.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                $.fn.toolbar.dealItemOptions(opts);
                box.numberbox(opts);
                var keydown = opts.keydown;
                if (keydown) { keydown = $.string.toFunction(keydown); }
                if ($.isFunction(keydown)) {
                    var tb = box.numberbox("textbox");
                    tb.bind("keydown", function (event) {
                        keydown.call(this, event, $(container).closest("table.toolbar-wrapper")[0]);
                    });
                }
                return box;
            },
            destroy: function (target) {
                $(target).numberbox("destroy");
            },
            setValue: function (target, value) {
                $(target).numberbox("setValue", value);
            },
            getValue: function (target) {
                return $(target).numberbox("getValue");
            },
            resize: function (target, width) {
                $(target)._outerWidth(width);
            },
            enable: function (target) {
                $(target).numberbox("enable");
            },
            disable: function (target) {
                $(target).numberbox("disable");
            }
        },

        //  数字框 easyui-numberspinner
        numberspinner: {
            defaults: {},
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {}, { type: "text" });
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                $.fn.toolbar.dealItemOptions(opts);
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container).numberspinner(opts);
                return box;
            },
            destroy: function (target) {
                $(target).numberspinner("destroy");
            },
            setValue: function (target, value) {
                $(target).numberspinner("setValue", value);
            },
            getValue: function (target) {
                return $(target).numberspinner("getValue");
            },
            resize: function (target, width) {
                $(target).numberspinner("resize", width);
            },
            enable: function (target) {
                $(target).numberspinner("enable");
            },
            disable: function (target) {
                $(target).numberspinner("disable");
            }
        },

        //  日期框 easyui-datebox
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        datebox: {
            defaults: {},
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {}, { type: "text" });
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container);
                if ($.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                $.fn.toolbar.dealItemOptions(opts);
                box.datebox(opts);
                return box;
            },
            destroy: function (target) {
                $(target).datebox("destroy");
            },
            setValue: function (target, value) {
                $(target).datebox("setValue", value);
            },
            getValue: function (target) {
                return $(target).datebox("getValue");
            },
            resize: function (target, width) {
                $(target).datebox("resize", width);
            },
            enable: function (target) {
                $(target).datebox("enable");
            },
            disable: function (target) {
                $(target).datebox("disable");
            },
            setFocus: function (target) {
                $(target).datebox("textbox").focus();
            }
        },

        //  日期时间框 easyui-datetimebox
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        datetimebox: {
            defaults: {},
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {}, { type: "text" });
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container);
                if ($.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                $.fn.toolbar.dealItemOptions(opts);
                box.datetimebox(opts);
                return box;
            },
            destroy: function (target) {
                $(target).datetimebox("destroy");
            },
            setValue: function (target, value) {
                $(target).datetimebox("setValue", value);
            },
            getValue: function (target) {
                return $(target).datetimebox("getValue");
            },
            resize: function (target, width) {
                $(target).datetimebox("resize", width);
            },
            enable: function (target) {
                $(target).datetimebox("enable");
            },
            disable: function (target) {
                $(target).datetimebox("disable");
            },
            setFocus: function (target) {
                $(target).datetimebox("textbox").focus();
            }
        },

        //  时间框 easyui-timespinner
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        timespinner: {
            defaults: {},
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {}, { type: "text" });
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container);
                if ($.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                $.fn.toolbar.dealItemOptions(opts);
                box.timespinner(opts);
                return box;
            },
            destroy: function (target) {
                $(target).timespinner("destroy");
            },
            setValue: function (target, value) {
                $(target).timespinner("setValue", value);
            },
            getValue: function (target) {
                return $(target).timespinner("getValue");
            },
            resize: function (target, width) {
                $(target).timespinner("resize", width);
            },
            enable: function (target) {
                $(target).timespinner("enable");
            },
            disable: function (target) {
                $(target).timespinner("disable");
            }
        },

        //  下拉框 easyui-combo
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        combo: {
            defaults: {},
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {}, { type: "text" });
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container);
                if ($.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                $.fn.toolbar.dealItemOptions(opts);
                box.combo(opts);
                return box;
            },
            destroy: function (target) {
                $(target).combo("destroy");
            },
            setValue: function (target, value) {
                $(target).combo($.util.likeArrayNotString(value) ? "setValues" : "setValue", value);
            },
            getValue: function (target) {
                var combo = $(target), opts = combo.combo("options");
                return $(target).combo(opts.multiples ? "getValues" : "getValue");
            },
            resize: function (target, width) {
                $(target).combo("resize", width);
            },
            enable: function (target) {
                $(target).combo("enable");
            },
            disable: function (target) {
                $(target).combo("disable");
            },
            setFocus: function (target) {
                $(target).combo("textbox").focus();
            }
        },

        //  下拉框 easyui-combobox
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        combobox: {
            defaults: {},
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {}, { type: "text" });
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container);
                if ($.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                $.fn.toolbar.dealItemOptions(opts);
                box.combobox(opts);
                return box;
            },
            destroy: function (target) {
                $(target).combobox("destroy");
            },
            setValue: function (target, value) {
                $(target).combobox($.util.likeArrayNotString(value) ? "setValues" : "setValue", value);
            },
            getValue: function (target) {
                var combo = $(target), opts = combo.combobox("options");
                return $(target).combobox(opts.multiples ? "getValues" : "getValue");
            },
            resize: function (target, width) {
                $(target).combobox("resize", width);
            },
            enable: function (target) {
                $(target).combobox("enable");
            },
            disable: function (target) {
                $(target).combobox("disable");
            },
            setFocus: function (target) {
                $(target).combobox("textbox").focus();
            }
        },

        //  下拉树 easyui-combotree
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        combotree: {
            defaults: {},
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {}, { type: "text" });
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container);
                if ($.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                $.fn.toolbar.dealItemOptions(opts);
                box.combotree(opts);
                return box;
            },
            destroy: function (target) {
                $(target).combotree("destroy");
            },
            setValue: function (target, value) {
                $(target).combotree($.util.likeArrayNotString(value) ? "setValues" : "setValue", value);
            },
            getValue: function (target) {
                var combo = $(target), opts = combo.combotree("options");
                return $(target).combotree(opts.multiples ? "getValues" : "getValue");
            },
            resize: function (target, width) {
                $(target).combotree("resize", width);
            },
            enable: function (target) {
                $(target).combotree("enable");
            },
            disable: function (target) {
                $(target).combotree("disable");
            },
            setFocus: function (target) {
                $(target).combotree("textbox").focus();
            }
        },

        //  下拉列表 easyui-combogrid
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        combogrid: {
            defaults: {},
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {}, { type: "text" });
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container);
                if ($.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                //该属性若为null会影响combogrid的初始化
                if (opts.style == null) { opts.style = {}; }
                $.fn.toolbar.dealItemOptions(opts);
                box.combogrid(opts);
                return box;
            },
            destroy: function (target) {
                $(target).combogrid("destroy");
            },
            setValue: function (target, value) {
                $(target).combogrid($.util.likeArrayNotString(value) ? "setValues" : "setValue", value);
            },
            getValue: function (target) {
                var combo = $(target), opts = combo.combogrid("options");
                return $(target).combogrid(opts.multiples ? "getValues" : "getValue");
            },
            resize: function (target, width) {
                $(target).combogrid("resize", width);
            },
            enable: function (target) {
                $(target).combogrid("enable");
            },
            disable: function (target) {
                $(target).combogrid("disable");
            },
            setFocus: function (target) {
                $(target).combogrid("textbox").focus();
            }
        },

        //  搜索框 easyui-searchbox
        //  额外支持属性：
        //      disabled: boolean、function(t)
        //      说明：若 disabled 是回调函数，那么会初始化之前执行该回调并将结果赋值给 disabled 属性。这将覆盖。
        searchbox: {
            defaults: {},
            init: function (container, options) {
                var opts = $.extend({}, this.defaults, options || {}, { type: "text" });
                var prop = {};
                if (opts.id) { prop.id = opts.id; prop.name = opts.id; }
                if (opts.name) { prop.name = opts.name; }
                var box = $("<input type='text' class='toolbar-item-input' />").attr(prop).appendTo(container);
                if (opts.searcher) {
                    opts.searcher = $.string.toFunction(opts.searcher);
                }
                if ($.isFunction(opts.disabled)) {
                    opts.disabled = opts.disabled.call(box[0], $(container).closest("table.toolbar-wrapper")[0]);
                }
                $.fn.toolbar.dealItemOptions(opts);
                return box.searchbox(opts);
            },
            destroy: function (target) {
                $(target).searchbox("destroy");
            },
            setValue: function (target, value) {
                $(target).searchbox("setValue", value);
            },
            getValue: function (target) {
                return $(target).searchbox("getValue");
            },
            resize: function (target, width) {
                $(target).searchbox("resize", width);
            },
            enable: function (target) {
                $(target).searchbox("enable");
            },
            disable: function (target) {
                $(target).searchbox("disable");
            },
            setFocus: function (target) {
                $(target).searchbox("textbox").focus();
            }
        }
    };
    itemTypes.linkbutton = itemTypes.button;

    var loader = function (param, success, error) {
        var opts = $(this).toolbar("options");
        if (!opts.url) { return false; }
        $.ajax({
            type: opts.method, url: opts.url, data: param, dataType: "json",
            success: function (data) {
                success(data);
            }, error: function () {
                error.apply(this, arguments);
            }
        });
    }, loadFilter = function (data) {
        return $.array.likeArrayNotString(data) ? data : [];
    };



    $.fn.toolbar = function (options, param) {
        if (typeof options == "string") {
            return $.fn.toolbar.methods[options](this, param);
        }
        options = options || {};
        return this.each(function () {
            var state = $.data(this, "toolbar");
            if (state) {
                $.extend(state.options, options);
            } else {
                state = $.data(this, "toolbar", {
                    options: $.extend({}, $.fn.toolbar.defaults, $.fn.toolbar.parseOptions(this), options)
                });
            }
            initialize(this);
            if (state.options.data) {
                loadData(this, state.options.data);
            }
            //request(this);
        });
    };

    $.fn.toolbar.parseOptions = function (target) {
        return $.extend({}, $.parser.parseOptions(target, ["url", "data", "method", "width", "height", "align", "valign"]));
    };

    $.fn.toolbar.dealItemOptions = function (options) {
        delete options.options;
        delete options.itemCls;
        delete options.itemStyle;
    };

    $.fn.toolbar.methods = {

        //  获取当前 easyui-toolbar 控件的 options 参数对象；
        //  返回值：返回当前 easyui-toolbar 控件的 options 参数对象，为一个 JSON-Object。
        options: function (jq) { return $.data(jq[0], 'toolbar').options; },

        //  获取当前 easyui-toolbar 控件的工具栏包装器对象；
        //  返回值：返回当前 easyui-toolbar 控件的工具栏包装器对象；该方法返回一个包含 html-table 的 jQuery 对象。
        wrapper: function (jq) { return $.data(jq[0], "toolbar").wrapper; },

        //  获取当前 easyui-toolbar 控件的工具栏外框对象；
        //  返回值：返回当前 easyui-toolbar 控件的工具栏外框对象；该方法返回一个包含 html-div 的 jQuery 对象。
        toolbar: function (jq) { return $.data(jq[0], "toolbar").toolbar; },

        //  设置当前 easyui-toolbar 控件的尺寸大小；该方法的参数 size 为一个 JSON-Object，该参数定义如下属性：
        //      width : 表示工具栏的新宽度；如果设置为数值类型，则表示像素宽度；如果设置为 "auto"，则表示自适应最大宽度；
        //      height: 表示工具栏的新高度；如果设置为数值类型，则表示像素高度；如果设置为 "auto"，则表示自适应一行按钮的高度；
        //  返回值：返回表示当前 easyui-toolbar 控件的 jQuery 链式对象。
        resize: function (jq, size) { return jq.each(function () { setSize(this, size); }); },

        //  设置当前 easyui-toolbar 控件的工具栏项水平居中方式；该方法的参数 align 为一个 String 类型值，其可以被定义的值限定为如下范围：
        //      left  :
        //      right :
        //      center:
        //  返回值：返回表示当前 easyui-toolbar 控件的 jQuery 链式对象。
        align: function (jq, align) { return jq.each(function () { setAlign(this, align); }); },

        //  设置当前 easyui-toolbar 控件的工具栏项垂直居中方式；该方法的参数 valign 为一个 String 类型值，其可以被定义的值限定为如下范围：
        //      top   :
        //      middle:
        //      bottom:
        //  返回值：返回表示当前 easyui-toolbar 控件的 jQuery 链式对象。
        valign: function (jq, valign) { return jq.each(function () { setValign(this, valign); }); },

        //  获取当前 easyui-toolbar 控件的所有工具栏子项数据；
        //  返回值：返回表示当前 easyui-toolbar 控件的所有工具栏子项数据所构成的一个数组，数组的每个元素包含如下属性的 JSON-Object：
        //      actions:    表示该工具栏子项的初始化构造器；其值为 $.fn.toolbar.defaults.itemTypes 中的一个子项；
        //      options:    表示该工具栏子项初始化时的 options 参数数据；
        //      target :    表示该工具栏子项包含 "toolbar-item" 样式类的元素的 jQuery-DOM 对象；
        //      type   :    表示该工具栏子项的类型，如果是自定义加载的 html-DOM 对象则为 "custom"
        //      container:  表示该工具栏子项所在的 jQuery-DOM(html-td) 对象；
        getItems: function (jq) { return getItems(jq[0]); },

        //  获取当前 easyui-toolbar 控件中所有输入控件的输入值集合；
        //  返回值：返回一个 JSON-Object 对象，该对象中每个属性表示工具栏中一个具备输入值功能的子项控件，其值为输入控件当前的值；
        getValues: function (jq) { return getValues(jq[0]); },

        //  启用当前 easyui-toolbar 控件的所有工具栏子项；
        //  返回值：返回表示当前 easyui-toolbar 控件的 jQuery 链式对象。
        enable: function (jq) { return jq.each(function () { enable(this); }); },

        //  禁用当前 easyui-toolbar 控件的所有工具栏子项；
        //  返回值：返回表示当前 easyui-toolbar 控件的 jQuery 链式对象。
        disable: function (jq) { return jq.each(function () { disable(this); }); },

        //  获取当前 easyui-toolbar 控件加载的所有数据；仅在初始化该控件指定的 data 参数、通过 loadData 方法加载的数据和通过 url 远程加载的数据，才会被返回；
        //  返回值：返回一个数组对象，数组中的每一项都表示一个工具栏子项的数据格式(返回数据的格式参考 loadData 方法的参数 data 的数据格式)。
        getData: function (jq) { return getData(jq[0]); },

        //  以指定的数据重新加载当前 easyui-toolbar 控件的所有工具栏子项；该方法的参数 data 为一个数组，表示请求远程数据时发送的查询参数；数组中的每项都表示一个待加载的工具栏子项，其可以定义为如下类型：
        //      1、jQuery-DOM 对象：
        //      2、HTML-DOM 对象：
        //      3、String 类型：可以为以下类型：
        //          a："-"、"—"、"|"，表示分割线的 separator
        //          b："<" 开头和 ">" 结尾切字符串度大于等于3，表示 HTML 代码段；
        //          c："\\t"、"\\n"，表示换行 落阳注，无效
        //          d：其他长度大于 0 的字符串，表示 label。
        //      4、JSON-Object 对象：
        //          id      :
        //          name    :
        //          type    : $.fn.toolbar.defaults.itemTypes 中定义的工具栏项类型，例如 separator、label、button、textbox、checkbox、numberbox、validatebox、combobox、combotree、combogrid 等；
        //          options : 初始化该工具栏项的参数；
        //          style   :
        //          cls     :
        //          itemStyle:
        //          itemCls :
        //          width   :
        //          align   :
        //  返回值：返回表示当前 easyui-toolbar 控件的 jQuery 链式对象。
        //  备注：执行该方法会清空当前 easyui-toolbar 控件中原来的所有子项控件。
        loadData: function (jq, data) { return jq.each(function () { loadData(this, data); }); },
    };

    $.fn.toolbar.defaults = {

        //  表示 easyui-toolbar 控件的宽度，Number 类型数值；默认为 auto；
        width: "auto",

        //  表示 easyui-toolbar 控件的高度，Number 类型数值；默认为 auto；
        height: "auto",

        //  表示 easyui-toolbar 控件的横向对齐方式，可选的值为 "left"、"center" 或 "right"；默认为 "left"；
        align: "left",

        //  表示 easyui-toolbar 控件的纵向对齐方式，可选的值为 "top"、"middle" 或 "bottom"；默认为 "middle"；
        valign: "middle",

        //  表示 easyui-toolbar 控件在初始化时需要加载的内容数据；Array 类型，数组中的每项都表示一个待加载的工具栏子项，其可以定义为如下类型：
        //      1、jQuery-DOM 对象：
        //      2、HTML-DOM 对象：
        //      3、String 类型：可以为以下类型：
        //          a："-"、"—"、"|"，表示分割线的 separator
        //          b："<" 开头和 ">" 结尾切字符串度大于等于3，表示 HTML 代码段；
        //          c："\\t"、"\\n"，表示换行 落阳注，无效
        //          d：其他长度大于 0 的字符串，表示 label。
        //      4、JSON-Object 对象：
        //          id      :
        //          name    :
        //          type    : $.fn.toolbar.defaults.itemTypes 中定义的工具栏项类型，例如 separator、label、button、textbox、checkbox、numberbox、validatebox、combobox、combotree、combogrid 等；
        //          options : 初始化该工具栏项的参数；
        //          style   :
        //          cls     :
        //          itemStyle:
        //          itemCls :
        //          width   :
        //          align   :
        data: null,

        itemOptions: itemOptions,

        //  定义 easyui-toolbar 插件能够添加的工具栏项类型；
        //  开发人员可以通过扩展 $.fn.toolbar.defaults.itemTypes 属性来实现其自定义的 easyui-toolbar 工具栏项类型；
        //      就像扩展 $.fn.datagrid.defaults.editors 一样。
        //  已经内置的工具栏项类型有：
        //      separator   :
        //      label       :
        //      button      :
        //      linkbutton  :
        //      text        :
        //      textbox     :
        //      checkbox    :
        //      validatebox :
        //      numberbox   :
        //      numberspinner :
        //      datebox     :
        //      datetimebox :
        //      timespinner :
        //      combo       :
        //      combobox    :
        //      combotree   :
        //      combogrid   :
        //      searchbox   :
        itemTypes: itemTypes,

        //  表示 easyui-toolbar 的尺寸大小重置事件；当控件大小被调整后触发；该事件回调函数定义如下参数：
        //      width: 被设置的新的宽度；
        //      height: 被设置的新的告诉。
        //  回调函数中的 this 表示当前 easyui-toolbar 的 DOM 对象。
        onResize: function (width, height) { },

        //  表示 easyui-toolbar 的数据加载成功事件；
        onLoadSuccess: function (data) { },

        loadFilter: loadFilter
    };

    $.parser.plugins.push("toolbar");

})(jQuery);