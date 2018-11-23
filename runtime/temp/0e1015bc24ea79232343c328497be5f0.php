<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"D:\xampp\htdocs\TMES-FINAL\public/../app/home\view\pub\center.html";i:1540174978;}*/ ?>
<div id="layout_center_tabs" style="overflow: hidden;" >
	<div title="<?php echo \think\Lang::get('LANG_INDEXCENTER_HOME'); ?>" data-options="fit:true,border:false,iconCls:'icon-house'">
      <div data-options="region:'center',title:'<?php echo \think\Lang::get('LANG_INDEXCENTER_HOME'); ?>'" style="overflow: hidden;padding: 5px;">
	   <?php echo \think\Lang::get('LANG_REFRESH'); ?>
	   
   
		 
       </div>
	</div>
</div>
<div id="layout_center_tabsMenu" style="width: 120px;display:none;">
	<div type="refresh" data-options="iconCls:'icon-reload'">刷新</div>
	<div class="menu-sep"></div>
	<div type="close" data-options="iconCls:'icon-cross'">关闭</div>
	<div type="closeOther" data-options="iconCls:'icon-cross'">关闭其他</div>
	<div type="closeAll" data-options="iconCls:'icon-cross'">关闭所有</div>
</div>
<script id = "PubCenter" src="/static/js/index/Pub/center.js" refreshlang="<?php echo \think\Lang::get('LANG_REFRESH'); ?>" closelang="<?php echo \think\Lang::get('LANG_CLOSE'); ?>"></script>