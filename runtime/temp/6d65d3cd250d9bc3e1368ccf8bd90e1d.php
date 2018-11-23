<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:65:"D:\xampp\htdocs\TMES-FINAL\public/../app/home\view\pub\login.html";i:1539584275;}*/ ?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo \think\Config::get('sitename'); ?></title>
<meta http-equiv="X-UA-Compatible" contchangeLangent="IE=edge" />
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="keywords" content="" />
<meta http-equiv="description" content="<?php echo \think\Config::get('sitename'); ?>" />
<link rel="icon" href="/static/style/images/mes_white_icon2.ico" type="image/x-icon" />
<link id="easyuiTheme" rel="stylesheet" type="text/css" href="/static/easyui/themes/<?php echo (isset($_COOKIE['easyuiThemeName']) && ($_COOKIE['easyuiThemeName'] !== '')?$_COOKIE['easyuiThemeName']:'default'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="/static/easyui/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="/static/style/mesCss.css" />
<link href="/static/style/login.css" rel="stylesheet" type="text/css" />
<script src="/static/easyui/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/static/easyui/jquery.easyui.min.js" type="text/javascript" charset="utf-8"></script>
<script id="easyuiLang" type="text/javascript" src="/static/easyui/locale/easyui-lang-<?php echo (isset($_COOKIE['easyuiLangType']) && ($_COOKIE['easyuiLangType'] !== '')?$_COOKIE['easyuiLangType']:'en'); ?>.js" charset="utf-8"></script>
<script type="text/javascript" src="/static/easyui/jquery.cookie.js" charset="utf-8"></script>
<script type="text/javascript" src="/static/js/mesUtil.js" charset="utf-8"></script>

</head>
<body>
<div id="login">
	<div id="login_header">
		<h1 class="login_logo">
			<a href=""><img src="/static/style/images/login_logo.gif" /></a>
		</h1>
		<div class="login_headerContent">
			<div class="navList">
				<ul>
					<li><a href="javascript:void(0)" onclick="changeLang('en-us');">English&nbsp;</a></li>
					<li><a href="javascript:void(0)" onclick="changeLang('zh-cn');">简体&nbsp;</a></li>
					<li><a href="javascript:void(0)" onclick="changeLang('zh-tw');">繁體&nbsp;</a></li>
				</ul>
			</div>
			<h1 class="login_title" style="text-align: left;"><?php echo $skd; ?><!--<img src="/static/style/images/login_title.png" />--></h1>
		</div>
	</div>
	<div id="login_content">
		<div class="loginForm">
			<form method="post" id="loginForm">
				<p>
					<label><?php echo \think\Lang::get('LANG_LOGIN_USERNAME'); ?>:</label>
					<input type="text" name="account" id="account" class="easyui-validatebox" data-options="required:true" />
				</p>
				<p>
					<label><?php echo \think\Lang::get('LANG_LOGIN_USERPASS'); ?>:</label>
					<input type="password" id="password" name="password" class="easyui-validatebox" data-options="required:true" />
				</p>
               
                <p>&nbsp;</p>
                <p>
				<div class="login_bar">
                    <a href="#" class="easyui-linkbutton" iconCls="icon-user_go" onclick="loginUser()"> <?php echo \think\Lang::get('LANG_LOGIN_BTN_LOGIN'); ?> </a>&nbsp;&nbsp;
                    <a href="#" class="easyui-linkbutton" iconCls="icon-account" onclick="$.messager.alert('提示','申请通道尚未开通...');"> <?php echo \think\Lang::get('LANG_LOGIN_BTN_REGER'); ?> </a>
				</div>
                </p>
                <p>&nbsp;</p>
                <p style="text-align: center;">
                    <a href="#" class="easyui-linkbutton" onclick="loginUser('guest','123456');" data-options="plain:true,iconCls:'icon-user_red'">游客身份直接访问>></a>
                </p>
                <p>&nbsp;</p>
                <p style="text-align: center;">
                <span id="showPcName" style="color:blue;font-weight:blod;"><notempty name="ipAddr">IP地址：<?php echo $ipAddr; ?></notempty></span>
                <input type="hidden" name="UserPcAddr" id="UserPcAddr" value="<?php echo $ipAddr; ?>" />
                </p>
			</form>
		</div>
		<div class="login_banner"><img src="/static/style/images/<?php echo $loginPic; ?>" /></div>
		<div class="login_main">
			<div class="login_inner">
            <p>V1.0.0 Alpha</p>
            <p><font color=red>温馨提示:首次登陆后请修改密码，帐号为您的员工卡号，初始密码：123456。</font></p>
			</div>
		</div>
	</div>
	<div id="login_footer">
		Copyright &copy; 2013 <?php echo \think\Config::get('COMPANY'); ?> All Rights Reserved.
	</div>
</div>
<script src="/static/js/index/Pub/login.js"></script>
</body>

</html>