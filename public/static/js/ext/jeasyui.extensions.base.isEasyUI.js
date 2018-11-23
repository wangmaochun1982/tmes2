/**
* jQuery EasyUI 1.4.3
* Copyright (c) 2009-2015 www.jeasyui.com. All rights reserved.
*
* Licensed under the GPL license: http://www.gnu.org/licenses/gpl.txt
* To use it on other terms please contact us at info@jeasyui.com
* http://www.jeasyui.com/license_commercial.php
*
* jQuery EasyUI 通用插件基础库 扩展
* jeasyui.extensions.base.isEasyUI.js
* 开发 流云
* 由 落阳 整理
* 最近更新：2015-10-16
*
* 依赖项：
*   1、jquery.jdirk.js
*
* Copyright (c) 2015 ChenJianwei personal All rights reserved.
*/
(function ($) {

    $.util.namespace("$.easyui");

    // 判断传入的 jQuery 对象或 HTML DOM 对象是否为已初始化的 jQuery EasyUI 组件；该方法定义如下参数：
    //      selector: jQuery 对象选择器，或者 DOM 对象，或者 jQuery 对象均可；
    //      plugin  : 要判断的插件名称，例如 "panel"、"dialog"、"datagrid" 等；
    //      ignoreValid: 是否忽略检查 $.parser.plugins 数组中存在 plugin 指定的插件名；boolean 类型值；该参数可选，默认为 false；
    // 返回值：
    //      如果 selector 所表示的 jQuery 对象中的第一个 DOM 元素为 plugin 参数所示的 easyui 插件且已经被初始化，则返回 true，否则返回 false；
    //      如果 selector 或 plugin 中任意一参数为 null/undefined，或 selector 所示的 jQuery 选择器不包含任意 HTML DOM 对象，则直接返回 false；
    //      如果 plugin 所示的名称不是一个 jQuery EasyUI 组件（没有包含在 $.parser.plugins 数组中），则直接抛出异常。
    $.easyui.isComponent = $.easyui.isEasyUiComponent = $.easyui.isEasyUI = function (selector, plugin, ignoreValid) {
        if (!selector || !plugin)
            return false;

        ignoreValid = ignoreValid == null || ignoreValid == undefined ? false : !!ignoreValid;
        if (!ignoreValid && !$.array.contains($.parser.plugins, plugin))
        { alert($.string.format($.easyui.isComponent.defaults.errorMessage, plugin)); }

        var t = $(selector);
        if (!t.length)
            return false;

        var state = $.data(t[0], plugin);
        return state && state.options ? true : false;
    };

    // $.easyui.isComponent 方法的默认参数格式
    $.easyui.isComponent.defaults = {
        errorMessage: "传入的参数 plugin: {0} 不是 easyui 插件名。"
    };



    $.extend({

        // 判断传入的 jQuery 对象或 HTML DOM 对象是否为已初始化的 jQuery EasyUI 组件；该方法定义如下参数：
        //      selector: jQuery 对象选择器，或者 DOM 对象，或者 jQuery 对象均可；
        //      plugin  : 要判断的插件名称，例如 "panel"、"dialog"、"datagrid" 等；
        //      ignoreValid: 是否忽略检查 $.parser.plugins 数组中存在 plugin 指定的插件名；boolean 类型值；该参数可选，默认为 false；
        // 返回值：
        //      如果 selector 所表示的 jQuery 对象中的第一个 DOM 元素为 plugin 参数所示的 easyui 插件且已经被初始化，则返回 true，否则返回 false；
        //      如果 selector 或 plugin 中任意一参数为 null/undefined，或 selector 所示的 jQuery 选择器不包含任意 HTML DOM 对象，则直接返回 false；
        //      如果 plugin 所示的名称不是一个 jQuery EasyUI 组件（没有包含在 $.parser.plugins 数组中），则直接抛出异常。
        isEasyUiComponent: function (selector, plugin, ignoreValid) {
            return $.easyui.isComponent(selector, plugin, ignoreValid);
        }
    });


    $.fn.extend({

        // 判断当前的 jQuery 对象中第一个 DOM 对象是否为已初始化的 jQuery EasyUI 组件；该方法定义如下参数：
        //      plugin  : 要判断的插件名称，例如 "panel"、"dialog"、"datagrid" 等；
        //      ignoreValid: 是否忽略检查 $.parser.plugins 数组中存在 plugin 指定的插件名；boolean 类型值；该参数可选，默认为 false；
        // 返回值：
        //      如果 jQuery 对象中的第一个 DOM 元素为 plugin 参数所示的 easyui 插件且已经被初始化，则返回 true，否则返回 false；
        //      如果 plugin 为 null/undefined，或 jQuery 选择器不包含任意 HTML DOM 对象，则直接返回 false；
        //      如果 plugin 所示的名称不是一个 jQuery EasyUI 组件（没有包含在 $.parser.plugins 数组中），则直接抛出异常。
        isEasyUiComponent: function (plugin, ignoreValid) {
            return $.easyui.isComponent(this, plugin, ignoreValid);
        }
    });

})(jQuery);