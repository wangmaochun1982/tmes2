/**
* jQuery EasyUI 1.4.3
* Copyright (c) 2009-2015 www.jeasyui.com. All rights reserved.
*
* Licensed under the GPL license: http://www.gnu.org/licenses/gpl.txt
* To use it on other terms please contact us at info@jeasyui.com
* http://www.jeasyui.com/license_commercial.php
*
* jQuery EasyUI 通用插件基础库 扩展
* jeasyui.extensions.base.current.js
* 开发 流云
* 由 落阳 整理
* 最近更新：2015-12-29
*
* 依赖项：
*   1、jquery.jdirk.js
*   2、jeasyui.extensions.base.isEasyUI.js
*
* Copyright (c) 2015 ChenJianwei personal All rights reserved.
*/
(function ($) {

    $.fn.extend({

        // 获取当前对象所在的 easyui-layout 对象
        currentLayout: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("layout", true);
            });
        },

        // 获取当前对象所在的 easyui-layout-region 面板对象
        currentRegionPanel: function () {
            return $.util.closest(this, function () {
                if (!this.isEasyUiComponent("panel", true) || !this.is(".layout-body")) {
                    return false;
                }
                var panel = this.panel("panel");
                if (!panel.is(".layout-panel")) {
                    return false;
                }
                var layout = panel.parent();
                return layout.isEasyUiComponent("layout", true);
            });
        },

        // 获取当前对象所在的 easyui-layout-region 面板对象的 region 名称（north、west、center、east、south）
        currentPanelRegion: function () {
            var p = this.currentRegionPanel();
            if (!p.length) {
                return null;
            }
            var panel = p.panel("panel");
            if (panel.is(".layout-panel-north")) {
                return "north";
            }
            if (panel.is(".layout-panel-south")) {
                return "south";
            }
            if (panel.is(".layout-panel-west")) {
                return "west";
            }
            if (panel.is(".layout-panel-east")) {
                return "east";
            }
            if (panel.is(".layout-panel-center")) {
                return "center";
            }
            return null;
        },

        // 获取当前对象所在的 easyui-panel 对象
        currentPanel: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("panel", true);
            });
        },

        // 获取当前对象所在的 easyui-tabs 对象
        currentTabs: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("tabs", true);
            });
        },

        // 获取当前对象所在的 easyui-tabs-tab 面板对象
        currentTabPanel: function () {
            return $.util.closest(this, function () {
                if (!this.isEasyUiComponent("panel", true)) {
                    return false;
                }
                var panel = this.panel("panel"), panels = panel.parent(), container = panels.parent();
                return panels.is(".tabs-panels") && container.is(".tabs-container");
            });
        },

        // 获取当前对象所在的 easyui-tabs 对象中的 tab 索引号
        currentTabIndex: function () {
            var p = this.currentTabPanel();
            return p.length ? p.panel("panel").index() : -1;
        },

        // 获取当前对象所在的 easyui-accordion 对象
        currentAccordion: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("accordion", true);
            });
        },

        // 获取当前对象所在的 easyui-accordion-panel 面板对象
        currentAccPanel: function () {
            return $.util.closest(this, function () {
                if (!this.isEasyUiComponent("panel", true) || !this.is(".accordion-body")) {
                    return false;
                }
                var panel = this.panel("panel"), accordion = panel.parent();
                return accordion.isEasyUiComponent("accordion", true);
            });
        },

        // 获取当前对象所在的 easyui-accordion 对象中的 panel 索引号
        currentAccIndex: function () {
            var p = this.currentAccPanel();
            return p.length ? p.panel("panel").index() : -1;
        },

        // 获取当前对象所在的 easyui-window 对象
        currentWindow: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("window", true);
            });
        },

        // 获取当前对象所在的 easyui-dialog 对象
        currentDialog: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("dialog", true);
            });
        },

        // 获取当前对象所在的 easyui-form 对象
        currentForm: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("form", true);
            });
        },

        // 获取当前对象所在的 easyui-tree 对象
        currentTree: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("tree", true);
            });
        },

        // 获取当前对象所在的 easyui-datagrid 对象
        currentDatagrid: function () {
			//return $.util.closest(this,'.grid-selector-container').find('.grid-unselector');
            var grid = null,
                p = $.util.closest(this, function () { 
                    if (!this.isEasyUiComponent("panel", true) || !this.is(".datagrid-wrap")) {
                        return false;
                    }
					//console.log(this.parent().find(".grid-selector-container"))
                    grid = this.find(">.datagrid-view>:eq(2)"); 
                    return grid.isEasyUiComponent("datagrid", true);
                }); 
            return p.length && grid && grid.length ? grid : p;
        },

        // 获取当前对象所在的 easyui-treegrid 对象
        currentTreegrid: function () { 
            var grid = null,
                p = $.util.closest(this, function () {
                    if (!this.isEasyUiComponent("panel", true) || !this.is(".datagrid-wrap")) {
                        return false;
                    }
                    grid = this.find(">.datagrid-view>:eq(2)");
                    return grid.isEasyUiComponent("treegrid", true);
                });
            return p.length && grid && grid.length ? grid : p;
        },

        // 获取当前对象所在的 easyui-propertygrid 对象
        currentPropertygrid: function () {
            var grid = null,
                p = $.util.closest(this, function () {
                    if (!this.isEasyUiComponent("panel", true) || !this.is(".datagrid-wrap")) {
                        return false;
                    } 
                    grid = this.find(">.datagrid-view>:eq(2)");
                    return grid.isEasyUiComponent("propertygrid", true);
                });
            return p.length && grid && grid.length ? grid : p;
        },

        // 获取当前对象所在的 easyui-linkbutton 对象
        currentLinkbutton: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("linkbutton", true);
            });
        },

        // 获取当前对象所在的 easyui-menubutton 对象
        currentMenubutton: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("menubutton", true);
            });
        },

        // 获取当前对象所在的 easyui-splitbutton 对象
        currentSplitbutton: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("splitbutton", true);
            });
        },

        // 获取当前对象所在的 easyui-calendar 对象
        currentCalendar: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("calendar", true);
            });
        },

        // 获取当前对象所在的 easyui-tooltip 对象
        currentTooltip: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("tooltip", true);
            });
        },

        // 获取当前对象所在的 easyui-progressbar 对象
        currentProgressbar: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("progressbar", true);
            });
        },

        currentDraggable: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("draggable", true);
            });
        },

        currentDroppable: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("droppable", true);
            });
        },

        currentResizable: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("resizable", true);
            });
        },

        currentPagination: function () {
            return $.util.closest(this, function () {
                return this.isEasyUiComponent("pagination", true);
            });
        }
    });


})(jQuery);