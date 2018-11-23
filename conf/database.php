<?php
use think\Db;
use think\Config;
use think\Session;
use think\Request;
use think\Loader;
// +----------------------------------------------------------------------
    // | 数据库设置
    // +----------------------------------------------------------------------
/*輪一DB*/
$DbConfB1 = [
  'type'=>'\think\oracle\Connection',
  'hostname'=>'10.2.1.190',
  'database'=>'mesb',
  'username'=>'emesc',
  'password'=>'emesc',
  'hostport'=>'1521',
  'prefix'=>'',
  ];
/*安全DB*/
$DbConfHM = [
'type'=>'\think\oracle\Connection',
'hostname'=>'10.2.1.189',
'database'=>'mesh',
'username'=>'emesc',
'password'=>'emesc',
'hostport'=>'1521',
'prefix'=>'',];
/*輪二DB*/
$DbConfB2 = [
'type'=>'\think\oracle\Connection',
'hostname'=>'10.2.1.37',
'database'=>'atmesb',
'username'=>'emesc',
'password'=>'emesc',
'hostport'=>'1521',
'prefix'=>'',
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
];
/*供料DB*/
$DbConfHY = ['type'=>'\think\oracle\Connection',
'hostname'=>'10.2.1.36',
'database'=>'atmesa',
'username'=>'emesc',
'password'=>'emesc',
'hostport'=>'1521',
'prefix'=>''
];
/*測試*/
$DbConfTS = [
'type'=>'\think\oracle\Connection',
'hostname'=>'10.2.1.128',
'database'=>'test',
'username'=>'emesc',
'password'=>'emesc',
'hostport'=>'1521',
'prefix'=>''
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
];

$config =  [
        // 数据库类型
        //'type'            => 'oracle',
//      'type'  =>  '\think\oracle\Connection',
//      // 数据库连接DSN配置
//      'dsn'             => '',
//      // 服务器地址
//      'hostname'        => '10.2.1.189',
//      // 数据库名
//      'database'        => 'mesh',
//      // 数据库用户名
//      'username'        => 'emesc',
//      // 数据库密码
//      'password'        => 'emesc',
//      // 数据库连接端口
//      'hostport'        => '1521',
        // 数据库连接参数
        //'params'          => [],
        'params' => [PDO::ATTR_CASE => PDO::CASE_LOWER, PDO::ATTR_EMULATE_PREPARES => true,],
        // 数据库编码默认采用utf8
        'charset'         => 'utf8',
        // 数据库表前缀
        'prefix'          => '',
        // 数据库调试模式
        'debug'           => false,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'          => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'     => false,
        // 读写分离后 主服务器数量
        'master_num'      => 1,
        // 指定从服务器序号
        'slave_no'        => '',
        // 是否严格检查字段是否存在
        'fields_strict'   => true,
        // 数据集返回类型
        'resultset_type'  => 'array',
        // 自动写入时间戳字段
        'auto_timestamp'  => false,
        // 时间字段取出后的默认时间格式
        'datetime_format' => 'Y-m-d H:i:s',
        // 是否需要进行SQL性能分析
        'sql_explain'     => false,
        'RBAC_DB_DSN' => 'oracle://emesc:emesc@10.2.1.38:1521/atmesr_new',//球拍
        'DB_ATMESR' => 'oracle://emesc:emesc@10.2.1.38:1521/atmesr_new',
        'DB_ATMESA' => 'oracle://emesc:emesc@10.2.1.36:1521/atmesa_new',
        'DB_ATMESB' => 'oracle://emesc:emesc@10.2.1.37:1521/atmesb_new',
        
        'DB_MESH' => 'oracle://emesc:emesc@10.2.1.189:1521/mesh',
        'DB_HR' => 'mssql://kt:kt@hr:1433/hr_xk_new',
        'DB_HR_TEST' => 'mssql://kt:kt@hr:1433/hr_xk_test',
        'DB_WMS' => 'oracle://EMESW:EMESW@10.2.1.36:1521/atmesa_new',
    ];
	 $dbflag =  Session::get('DB_FLAG');
	if($dbflag=='1'){
        return array_merge($DbConfB1,$config);
    }else if($dbflag=='2'){
        return array_merge($DbConfHM,$config);
    }else if($dbflag=='3' or $dbflag=='9'){
        return array_merge($DbConfB2,$config);
    }else if($dbflag=='4'){
        return array_merge($DbConfRT,$config);
    }else if($dbflag=='5' or $dbflag=='6' or $dbflag=='7'){
        return array_merge($DbConfHY,$config);
    }else if($dbflag=='8'){
        return array_merge($DbConfTS,$config);
    }else{
        return array_merge($DbConfB1,$config);
    }