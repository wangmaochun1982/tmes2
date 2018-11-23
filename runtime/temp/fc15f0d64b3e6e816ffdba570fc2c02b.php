<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:67:"D:\xampp\htdocs\TMES-FINAL\public/../app/home\view\Index\index.html";i:1541036327;}*/ ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
<title><?php echo \think\Config::get('sitename'); ?></title>
<link rel="icon" href="/static/style/images/mes_white_icon2.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/static/style/images/mes_white_icon2.ico" type="image/x-icon" />
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="keywords" content="" />
<meta http-equiv="description" content="<?php echo \think\Config::get('sitename'); ?>" />



		<script src="/static/easyui/jquery.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/static/easyui/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
		<script src="/static/easyui/jquery.easyui.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/static/easyui/datagrid-filter.js" type="text/javascript" charset="utf-8"></script>
		<link id="easyuiTheme" rel="stylesheet" type="text/css" href="/static/easyui/themes/default/easyui.css">
		        <link rel="stylesheet" type="text/css" href="/static/easyui/themes/icon.css">
		<script id="easyuiLang" type="text/javascript" src="/static/easyui/locale/easyui-lang-<?php echo (\think\Request::instance()->langset() ?: 'en-us'); ?>.js" charset="utf-8"></script>
		<script src="/static/js/mesUtil.js" type="text/javascript" charset="utf-8"></script>
	    
	    <link rel="stylesheet" type="text/css" href="/static/style/mesCss.css" />
	    <script src="/static/js/ext/jquery.jdirk.js" type="text/javascript"></script>
	<script src="/static/js/ext/jeasyui.extensions.toolbar.js" type="text/javascript"></script>
	<script src="/static/js/ext/jeasyui.extensions.base.isEasyUI.js" type="text/javascript"></script>
	<script src="/static/js/ext/jeasyui.extensions.base.current.js" type="text/javascript"></script>
	<script src="/static/js/ext/jeasyui.extensions.base.loading.js" type="text/javascript"></script>
	<link href="/static/js/ext/jeasyui.extensions.css" rel="stylesheet" type="text/css" />
	<link href="/static/js/ext/jeasyui.extensions.selector.css" rel="stylesheet" type="text/css" />
	<script src="/static/js/ext/jeasyui.extensions.js" type="text/javascript"></script>
	<script src="/static/js/ext/jeasyui.extensions.gridselector1.6.js" type="text/javascript"></script>
	<script src="/static/js/ext/jquery.comboselector1.6.js" type="text/javascript"></script>
	<script src="/static/js/ext/datagrid-dnd.js" type="text/javascript"></script>
	<script src="/static/js/componentMethodUtil.js" type="text/javascript" charset="utf-8"></script>
	    
		<title><?php echo lang('button1'); ?></title>
	</head>

		

 
<body class="easyui-layout">
	<div data-options="region:'north',href:'/home/Pub/top',border : false," id="top_image" style="height:58px;overflow:hidden;">
		
	</div>
	
	
	<div id="layoutMenu" data-options="iconCls:'icon-user',region:'west',title:'<?php echo \think\Lang::get('LANG_INDEX_LOGIN_USER'); ?>:<font color=red><?php echo \think\Cookie::get('loginUserName'); ?></font>',href:'/home/Pub/left',hideCollapsedContent:false" style="width:210px;overflow: hidden;">
		
	</div>
	
	<div data-options="region:'center',title:'',href:'/home/Pub/center'" style="overflow: hidden;">
		
	</div>

</body>
</html>