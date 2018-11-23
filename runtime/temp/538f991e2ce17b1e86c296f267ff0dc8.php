<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:58:"D:\xampp\htdocs\TMES\public/../app/home\view\pub\left.html";i:1540174214;}*/ ?>
<div id="leftMenuPanel" class="easyui-accordion" data-options="fit:true,border:false">
    <div id="menuTreeTitle" title="<?php echo \think\Lang::get('LANG_INDEXLEFT_MENUNAME'); ?>" data-options="iconCls:'icon-house',
     tools : [ {
		 iconCls : 'icon-reload',
		 handler : function() {
			$('#layout_west_tree').tree('reload');
			}
		}, {
     iconCls : 'icon-redo',
     handler : function() {
		 openNode();
     }
    }, {
     iconCls : 'icon-undo',
     handler : function() {
		 closedNode();
     }
    }  ]">
        <ul id="layout_west_tree">
        </ul>
    </div>
	<div id="myFavorite"  title="<?php echo \think\Lang::get('LANG_INDEXLEFT_FAVORITES'); ?>" data-options="iconCls:'icon-heart'" style="padding:5px;" >
       <ul>
            <li>
                <div onclick="layout_center_addTab('/index/index/index','用户管理');"><img src="/static/images/patrol/Employee.jpg" width="16" height="16"/>用户管理</div>
            </li>
         </ul>
    </div>
</div>
<div id="layout_leftTree_tabsMenu" class="easyui-menu" style="width: 120px;display:none;">
	<div data-options="iconCls:'icon-heart_add'" type="addFavorite">我的最爱</div>
	<div class="menu-sep"></div>
	<div data-options="iconCls:'icon-reload'" type="reLoad">刷新</div>
	<div data-options="iconCls:'icon-undo'" type="collapse">收缩</div>
	<div data-options="iconCls:'icon-redo'" type="expand">展开</div>
</div>
<script src="/static/js/index/Pub/left.js"></script>