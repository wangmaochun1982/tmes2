<?php
use think\Controller;
use think\Request;
/*輪一DB*/
$DbConfB1 = [
  'type'=>'\think\oracle\Connection',
  'hostname'=>'10.2.1.190',
  'database'=>'mesb',
  'username'=>'emesc',
  'password'=>'emesc',
  'hostport'=>'1521',
  'prefix'=>'',
   'params' => [PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_EMULATE_PREPARES => true,],
  ];
/*安全DB*/
$DbConfHM = [
'type'=>'\think\oracle\Connection',
'hostname'=>'10.2.1.189',
'database'=>'mesh',
'username'=>'emesc',
'password'=>'emesc',
'hostport'=>'1521',
'prefix'=>'',
 'params' => [PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_EMULATE_PREPARES => true,],
];
/*輪二DB*/
$DbConfB2 = [
'type'=>'\think\oracle\Connection',
'hostname'=>'10.2.1.37',
'database'=>'atmesb',
'username'=>'emesc',
'password'=>'emesc',
'hostport'=>'1521',
'prefix'=>'',
 'params' => [PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_EMULATE_PREPARES => true,],
];
/*球拍DB*/
$DbConfRT = [
'type'=>'\think\oracle\Connection',
'hostname'=>'10.2.1.38',
'database'=>'atmesr',
'username'=>'emesc',
'password'=>'emesc',
'hostport'=>'1521',
'prefix'=>'',
 'params' => [PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_EMULATE_PREPARES => true,],
];
/*供料DB*/
$DbConfHY = ['type'=>'\think\oracle\Connection',
'hostname'=>'10.2.1.36',
'database'=>'atmesa',
'username'=>'emesc',
'password'=>'emesc',
'hostport'=>'1521',
'prefix'=>'',
 'params' => [PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_EMULATE_PREPARES => true,],
];
/*測試*/
$DbConfTS = [
'type'=>'\think\oracle\Connection',
'hostname'=>'10.2.1.128',
'database'=>'test',
'username'=>'emesc',
'password'=>'emesc',
'hostport'=>'1521',
'prefix'=>'',
 'params' => [PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_EMULATE_PREPARES => true,],
];
/*WMSDB*/
$DbConfWS = [
'type'=>'\think\oracle\Connection',
'hostname'=>'10.2.1.36',
'database'=>'atmesa',
'username'=>'EMESW',
'password'=>'EMESW',
'hostport'=>'1521',
'prefix'=>'',
 'params' => [PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_EMULATE_PREPARES => true,],
];
    $request = Request::instance();
	return [
	'app_debug'              => true,
	'app_email' => 'ken@keentech-xm.com',
	'lang_switch_on' => true,
	//语言列表
    'lang_list' => ['zh-cn','en-us','zh-tw'],
    'USER_AUTH_ON'=>true,
	'USER_AUTH_TYPE'=>1,		// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_KEY'=>'authId',	// 用户认证SESSION标记
    'ADMIN_AUTH_KEY'=>'administrator',//超级管理员
	'USER_AUTH_MODEL'=>'TcUser',	// 默认验证数据表模型
	'AUTH_PWD_ENCODER'=>'md5',	// 用户认证密码加密方式
	'USER_AUTH_GATEWAY'=>'/home/Pub/login',	// 默认认证网关
    'USER_AUTH_GATEWAYDIG'=>'/home/Pub/login_timeout',	// 默认认证网关
	'NOT_AUTH_MODULE'=>'Pub',		// 默认无需认证模块
	'REQUIRE_AUTH_MODULE'=>'',		// 默认需要认证模块
	'NOT_AUTH_ACTION'=>'',		// 默认无需认证操作
	'REQUIRE_AUTH_ACTION'=>'',		// 默认需要认证操作
    'GUEST_AUTH_ON'=>true,    // 是否开启游客授权访问
    'GUEST_AUTH_ID'=>25,     // 游客的用户ID
    'URL_HTML_SUFFIX'=>'html|shmtl|xml',//伪静态设置
     'URL_CASE_INSENSITIVE' => true ,
    'RBAC_NOTRY_PREFIX'=>'Xx',//设置不验证的方法前缀,必须配置，不可随意变更
    'RBAC_ROLE_TABLE'=>'tc_role',
	'RBAC_USER_TABLE'=>'tc_role_user', 
	'RBAC_ACCESS_TABLE'=>'tc_access',
	'RBAC_NODE_TABLE'=>'tc_node',
	'RBAC_ACTION_TABLE'=>'tc_action',
	'RBAC_NATLNK_TABLE'=>'tc_node_action',
	'RBAC_ROLELNK_TABLE'=>'tc_role_lnk',
	 //PDO::ATTR_CASE => PDO::CASE_NATURAL,
	 // 关闭URL中控制器和操作名的自动转换
  //'url_convert'    =>  false,
    // auth配置
	'auth'  => [
	    'auth_on'           => 1, // 权限开关
	    'auth_type'         => 1, // 认证方式，1为实时认证；2为登录认证。
	    'auth_group'        => 'auth_group', // 用户组数据不带前缀表名
	    'auth_group_access' => 'auth_group_access', // 用户-用户组关系不带前缀表名
	    'auth_rule'         => 'auth_rule', // 权限规则不带前缀表名
	    'auth_user'         => 'member', // 用户信息不带前缀表名
	],
	// 视图输出字符串内容替换
    'view_replace_str'       => [
       
    ],
     'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],
        'RBAC_DB_DSN' => $DbConfRT,//球拍
        'DB_ATMESR' => $DbConfRT,
        'DB_ATMESA' => $DbConfHY,
        'DB_ATMESB' => $DbConfB2,
        'DB_MESB'   => $DbConfB1,
        'DB_MESH'   => $DbConfHM,
        'DB_HR' => 'mssql://kt:kt@hr:1433/hr_xk_new',
        'DB_HR_TEST' => 'mssql://kt:kt@hr:1433/hr_xk_test',
        'DB_WMS' => 'oracle://EMESW:EMESW@10.2.1.36:1521/atmesa_new',
	];
	 