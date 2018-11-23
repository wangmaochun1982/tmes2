<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:63:"D:\xampp\htdocs\TMES-FINAL\public/../app/home\view\pub\top.html";i:1540275725;}*/ ?>
<div id="logo" style="background-image:url('/static/style/images/logo.png');position:absolute;left:10px;top:3px;">
	<img src="/static/style/images/logo.png" />
</div>

<div style="position: absolute; right: 0px;top:3px; bottom: 0px; ">
     <a href="javascript:void(0);" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-computer'"><span style="font-weight: bold;color:blue;"><?php echo $loginPcAddr; ?></span></a>
     <a href="javascript:void(0);" class="easyui-menubutton" data-options="menu:'#layout_north_cjMenu',iconCls:'icon-database'"><?php echo getDbName(\think\Session::get('DB_FLAG')); ?>(<?php echo \think\Lang::get('LANG_INDEXTOP_SWITCH'); ?>)</a>
     <a href="javascript:void(0);" class="easyui-menubutton" data-options="menu:'#layout_north_kzmbMenu',iconCls:'icon-settings'"><?php echo \think\Lang::get('LANG_INDEXTOP_CONTROLPANEL'); ?></a>
     <a href="javascript:void(0);" class="easyui-menubutton" data-options="menu:'#layout_north_zxMenu',iconCls:'icon-exit'"><?php echo \think\Lang::get('LANG_INDEXTOP_LOGOUT'); ?></a>
</div>
<div style="position: absolute; right: 0px;bottom: 0px; ">
     <?php if(is_array($navList) || $navList instanceof \think\Collection || $navList instanceof \think\Paginator): $i = 0; $__LIST__ = $navList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voNav): $mod = ($i % 2 );++$i;?>
        &nbsp;<a href="javascript:void(0);" class="easyui-linkbutton"  <?php if(!(empty($voNav['iconcls']) || (($voNav['iconcls'] instanceof \think\Collection || $voNav['iconcls'] instanceof \think\Paginator ) && $voNav['iconcls']->isEmpty()))): ?> data-options="iconCls:'icon-<?php echo $voNav['iconcls']; ?>'"<?php endif; ?> onclick="changeMenu( '<?php echo $voNav['id']; ?>','<?php echo $voNav['title']; ?>' );"><?php echo $voNav['title']; ?></a>
        
     <?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<div id="layout_north_cjMenu" style="width: 90px; display: none;">
    <?php if(is_array($selecCkd) || $selecCkd instanceof \think\Collection || $selecCkd instanceof \think\Paginator): $i = 0; $__LIST__ = $selecCkd;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc): $mod = ($i % 2 );++$i;if($voc['db_id'] == \think\Session::get('DB_FLAG')): ?>
            <div data-options="iconCls:'icon-tick'" style="color:blue;font-weight:bold;">@<?php echo getDbName($voc['db_id']); ?></div>
        <?php else: ?>
            <div onclick="changeDbConn('<?php echo $voc['db_id']; ?>');" data-options="iconCls:'icon-disconnect'"><?php echo getDbName($voc['db_id']); ?></div>
        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
</div>

<div id="layout_north_kzmbMenu" style="width: 120px; display: none;">
	<div onclick="userInfoFun();" data-options="iconCls:'icon-user'"><?php echo \think\Lang::get('LANG_INDEXTOP_CONTROLPANEL_USER'); ?></div>
	<?php if(cookie('loginAccount') != 'guest'): ?>
    <div onclick="changeUPwd();" data-options="iconCls:'icon-key'"><?php echo \think\Lang::get('LANG_INDEXTOP_CONTROLPANEL_PASS'); ?></div>
	<?php endif; ?>
	<div class="menu-sep"></div>
	<div data-options="iconCls:'icon-theme'">
		<span><?php echo \think\Lang::get('LANG_INDEXTOP_CONTROLPANEL_THEM'); ?></span>
		<div style="width: 100px;">
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('default');"><eq name="_COOKIE.easyuiThemeName" value="default">@</eq><?php echo \think\Lang::get('LANG_THEMES_DEFAU'); ?></div>
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('bootstrap');"><eq name="_COOKIE.easyuiThemeName" value="bootstrap">@</eq><?php echo \think\Lang::get('LANG_THEMES_GRAYS'); ?></div>
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('metro');"><eq name="_COOKIE.easyuiThemeName" value="metro">@</eq><?php echo \think\Lang::get('LANG_THEMES_METRO'); ?></div>
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('cupertino');"><eq name="_COOKIE.easyuiThemeName" value="cupertino">@</eq><?php echo \think\Lang::get('LANG_THEMES_CUPER'); ?></div>
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('dark-hive');"><eq name="_COOKIE.easyuiThemeName" value="dark-hive">@</eq><?php echo \think\Lang::get('LANG_THEMES_DARKH'); ?></div>
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('grinder');"><eq name="_COOKIE.easyuiThemeName" value="grinder">@</eq><?php echo \think\Lang::get('LANG_THEMES_PEPER'); ?></div>
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('sunny');"><eq name="_COOKIE.easyuiThemeName" value="sunny">@</eq><?php echo \think\Lang::get('LANG_THEMES_SUNNY'); ?></div>
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('green');"><eq name="_COOKIE.easyuiThemeName" value="green">@</eq><?php echo \think\Lang::get('LANG_THEMES_GREEN'); ?></div>
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('blue');"><eq name="_COOKIE.easyuiThemeName" value="blue">@</eq><?php echo \think\Lang::get('LANG_THEMES_BLUE'); ?></div>
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('orange');"><eq name="_COOKIE.easyuiThemeName" value="orange">@</eq><?php echo \think\Lang::get('LANG_THEMES_ORANGE'); ?></div>
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('material-teal');"><eq name="_COOKIE.easyuiThemeName" value="material-teal">@</eq>material-teal</div>
			<div data-options="iconCls:'icon-theme'" onclick="changeTheme('red');"><eq name="_COOKIE.easyuiThemeName" value="red">@</eq>red</div>
		</div>
	</div>
    <div class="menu-sep"></div>
	<div data-options="iconCls:'icon-font'">
		<span><?php echo \think\Lang::get('LANG_INDEXTOP_CONTROLPANEL_LANG'); ?></span>
		<div style="width: 100px;">
			<div data-options="iconCls:'icon-font'" onclick="changeLang('en-us');"><?php if(\think\Request::instance()->langset() == 'en-us'): ?>@<?php endif; ?>English</div>
			<!-- <div data-options="iconCls:'icon-font'" onclick="changeLang('zh-cn');"><?php if(\think\Request::instance()->langset() == 'zh-cn'): ?>@<?php endif; ?>简体</div> -->
			<div data-options="iconCls:'icon-font'" onclick="changeLang('zh-tw');"><?php if(\think\Request::instance()->langset() == 'zh-tw'): ?>@<?php endif; ?>繁體</div>
		</div>
	</div>
</div>

<div id="layout_north_zxMenu" style="width: 110px; display: none;">
	<div onclick="sysInfoFun();" data-options="iconCls:'icon-information'"><?php echo \think\Lang::get('LANG_INDEXTOP_LOGOUT_ABOUT'); ?></div>
	<div class="menu-sep"></div>
    <div onclick="changeLoginUser();" data-options="iconCls:'icon-user_go'"><?php echo \think\Lang::get('LANG_INDEXTOP_LOGOUT_SWUSER'); ?></div>
    <div onclick="systemLockFun();" data-options="iconCls:'icon-lock'"><?php echo \think\Lang::get('LANG_INDEXTOP_LOGOUT_LOCK'); ?></div>
	<div onclick="logoutFun(true);" data-options="iconCls:'icon-exit'"><?php echo \think\Lang::get('LANG_INDEXTOP_LOGOUT_LOGOFF'); ?></div>
</div>
<?php if(\think\Session::get('username')): ?>
<script>systemLockFun();</script>
<?php endif; ?>
<script id = "pub_top" src="/static/js/index/Pub/top.js" 
	tips="<?php echo \think\Lang::get('LANG_TIPS'); ?>" 
	swuser="<?php echo \think\Lang::get('LANG_INDEXTOP_LOGOUT_SWUSER'); ?>"
	btn="<?php echo \think\Lang::get('LANG_LOGIN_BTN_LOGIN'); ?>"
	logout_lock="<?php echo \think\Lang::get('LANG_INDEXTOP_LOGOUT_LOCK'); ?>"
	unlock="<?php echo \think\Lang::get('LANG_INDEXTOP_UNLOCK'); ?>"
	chgpass = "<?php echo \think\Lang::get('LANG_INDEXTOP_CONTROLPANEL_PASS'); ?>"
	topsave = "<?php echo \think\Lang::get('LANG_SAVE'); ?>"></script>

