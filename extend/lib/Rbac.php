<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Rbac.class.php 2601 2012-01-15 04:59:14Z liu21st $

/**
 +------------------------------------------------------------------------------
 * 基于角色的数据库方式验证类
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Rbac.class.php 2601 2012-01-15 04:59:14Z liu21st $
 +------------------------------------------------------------------------------
 */
namespace lib;

use think\Db;
use think\Config;
use think\Session;
use think\Request;
use think\Loader;
// 配置文件增加设置
// USER_AUTH_ON 是否需要认证
// USER_AUTH_TYPE 认证类型
// USER_AUTH_KEY 认证识别号
// REQUIRE_AUTH_MODULE  需要认证模块
// NOT_AUTH_MODULE 无需认证模块
// USER_AUTH_GATEWAY 认证网关
// Rbac_DB_DSN  数据库连接DSN
// Rbac_ROLE_TABLE 角色表名称
// Rbac_USER_TABLE 用户表名称
// Rbac_ACCESS_TABLE 权限表名称
// Rbac_NODE_TABLE 节点表名称
/*
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `think_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `think_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `pid` (`pid`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `think_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `think_role_user` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` char(32) DEFAULT NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
class Rbac {
    // 认证方法
    static public function authenticate($map,$model='')
    {
        if(empty($model)) $model =  config('USER_AUTH_MODEL');
        //使用给定的Map进行认证
        return Db::name($model,Null,'Rbac_DB_DSN')->where($map)->find();
    }

    //用于检测用户权限的方法,并保存到Session中
    static function saveAccessList($authId=null)
    {
        if(null===$authId)   $authId = cookie(config('USER_AUTH_KEY'));
        // 如果使用普通权限模式，保存当前用户的访问权限列表
        // 对管理员开发所有权限
        //halt(Session::get(config('ADMIN_AUTH_KEY')));
        if(config('USER_AUTH_TYPE') !=2 && !Session::get(config('ADMIN_AUTH_KEY')) ){
            Session::set('_ACCESS_LIST',Rbac::getAccessList($authId));
                    
        }           
        return ;
    }

	// 取得模块的所属记录访问权限列表 返回有权限的记录ID数组
	static function getRecordAccessList($authId=null,$module='') {
        if(null===$authId)   $authId = cookie(config('USER_AUTH_KEY'));//Session::get(config('USER_AUTH_KEY'));
        if(empty($module))  $module	=	request()->controller();
        //获取权限访问列表
        $accessList = Rbac::getModuleAccessList($authId,$module);
        return $accessList;
	}

    //检查当前操作是否需要认证
    static function checkAccess()
    {
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if( config('USER_AUTH_ON') ){
			$_module	=	array();
			$_action	=	array();
            if("" != config('REQUIRE_AUTH_MODULE')) {
                //需要认证的模块
                $_module['yes'] = explode(',',strtoupper(config('REQUIRE_AUTH_MODULE')));
            }else {
                //无需认证的模块
                $_module['no'] = explode(',',strtoupper(config('NOT_AUTH_MODULE')));
            }
            //检查当前模块是否需要认证
            if((!empty($_module['no']) && !in_array(strtoupper(request()->controller()),$_module['no'])) || (!empty($_module['yes']) && in_array(strtoupper(request()->controller()),$_module['yes']))) {
				if("" != config('REQUIRE_AUTH_ACTION')) {
					//需要认证的操作
					$_action['yes'] = explode(',',strtoupper(config('REQUIRE_AUTH_ACTION')));
				}else {
					//无需认证的操作
					$_action['no'] = explode(',',strtoupper(config('NOT_AUTH_ACTION')));
				}
				//检查当前操作是否需要认证
                $rLen = strlen(config('RBAC_NOTRY_PREFIX'));
                if(substr(request()->action(),0,$rLen)!=config('RBAC_NOTRY_PREFIX')){
                    if((!empty($_action['no']) && !in_array(strtoupper(request()->action()),$_action['no'])) || (!empty($_action['yes']) && in_array(strtoupper(request()->action()),$_action['yes']))) {
					   return true;
    				}else {
    					return false;
    				}
                }else{
                    return false;
                }				
            }else {
                return false;
            }
        }
        return false;
    }

	// 登录检查
	static public function checkLogin() {
        //检查当前操作是否需要认证
        if(Rbac::checkAccess()) {
            //检查认证识别号
            //if(!Session::get(config('USER_AUTH_KEY'))) {
              if(!cookie(config('USER_AUTH_KEY'))){
                if(config('GUEST_AUTH_ON')) {
                    // 开启游客授权访问
                    if(!Session::has('_ACCESS_LIST'))
                        // 保存游客权限
                        Rbac::saveAccessList(config('GUEST_AUTH_ID'));
                }else{
                    // 禁止游客访问跳转到认证网关
                    redirect(PHP_FILE.config('USER_AUTH_GATEWAY'));
                }
            }
        }
        return true;
	}

    //权限认证的过滤器方法
    static public function AccessDecision($appName=APP_NAME)
    {
        //检查是否需要认证
        if(Rbac::checkAccess()) {
            //存在认证识别号，则进行进一步的访问决策
            $accessGuid   =   md5($appName.request()->controller().request()->action());
            if(empty(Session::get(config('ADMIN_AUTH_KEY')))) {
                if(config('USER_AUTH_TYPE')==2) {
                    //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
                    //通过数据库进行访问检查
                    //$accessList = Rbac::getAccessList(Session::get(config('USER_AUTH_KEY')));
                      $accessList = Rbac::getAccessList(cookie(config('USER_AUTH_KEY')));
                }else {
                    // 如果是管理员或者当前操作已经认证过，无需再次认证
                    if( Session::get($accessGuid)) {
                        return true;
                    }
                    //登录验证模式，比较登录后保存的权限访问列表
                    $accessList = Session::get('_ACCESS_LIST');
                }
                //判断是否为组件化模式，如果是，验证其全模块名
                $module = defined('P_MODULE_NAME')?  P_MODULE_NAME   :   request()->controller();
                //echo strtoupper($appName).strtoupper($module).strtoupper(request()->action());  
                if(!isset($accessList[strtoupper($appName)][strtoupper($module)][strtoupper(request()->action())])) {
                    Session::set($accessGuid,false);
                    return false;
                }else {
                    Session::set($accessGuid,true);
                }
                
            }else{
                //管理员无需认证
				return true;
			}
        }
        return true;
    }

    /**
     +----------------------------------------------------------
     * 取得当前认证号的所有权限列表
     +----------------------------------------------------------
     * @param integer $authId 用户ID
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    static public function getAccessList($authId){
        // Db方式权限数据
        $db     =   Db::connect('RBAC_DB_DSN');
        $table = array('role'=>config('RBAC_ROLE_TABLE'),'user'=>config('RBAC_USER_TABLE'),'access'=>config('RBAC_ACCESS_TABLE'),'node'=>config('RBAC_NODE_TABLE'),'action'=>config('RBAC_ACTION_TABLE'),'natlnk'=>config('RBAC_NATLNK_TABLE'),'rolelnk'=>config('RBAC_ROLELNK_TABLE'));
        $sql    =   "select distinct asnode.id, asnode.node_code as \"name\"
						  from ". $table['access']." asaccess ,".
							   $table['node']." asnode,
							   (select role_id
									  from ".$table['user']."
									 where user_lnk_id = {$authId}
									union
									select brole_id
									  from (select brole_id,frole_id from ".$table['rolelnk']."
									  where brole_id not in (select role_id from ".$table['user']." where user_lnk_id = {$authId})
									  )
									connect by prior brole_id = frole_id
									 start with frole_id in
												(select role_id from ".$table['user']." where user_lnk_id = {$authId})) asuser,
							   ".$table['role']." asrole 
						 where asuser.role_id = asaccess.role_id
						   and asaccess.action_id = 0
						   and asaccess.node_id = asnode.id
						   and asuser.role_id = asrole.id
						   and asrole.status > 0
						   and asnode.id = 1
						   and asnode.status > 0";
						   
		$apps =   $db->query($sql);//echo $sql.';';exit; 
		
        $access =  array();
        foreach($apps as $key=>$app) {
            $appId	     =	$app['id'];
            $appName	 =	$app['name'];
            // 读取项目的模块权限
            $access[strtoupper($appName)]   =  array();
			$sql    =   "select asnode.id, asnode.node_code as \"name\"
							  from ". $table['access']." asaccess ,".
								   $table['node']." asnode,
								   (select role_id
									  from ".$table['user']."
									 where user_lnk_id = {$authId}
									union
									select brole_id
									  from (select brole_id,frole_id from ".$table['rolelnk']."
									  where brole_id not in (select role_id from ".$table['user']." where user_lnk_id = {$authId})
									  )
									connect by prior brole_id = frole_id
									 start with frole_id in
												(select role_id from ".$table['user']." where user_lnk_id = {$authId})) asuser,
								   ".$table['role']." asrole 
							 where asuser.role_id = asaccess.role_id
							   and asaccess.action_id = 0
							   and asaccess.node_id = asnode.id
							   and asuser.role_id = asrole.id
							   and asrole.status > 0 
							   and asnode.status > 0
							   group by asnode.id, asnode.node_code"; 
			$modules =   $db->query($sql);//echo $sql.';';die();
            // 判断是否存在公共模块的权限
            $publicAction  = array();
            foreach($modules as $key=>$module) {
                $moduleId	 =	 $module['id'];
                $moduleName = $module['name'];
                if('PUBLIC'== strtoupper($moduleName)) {
					$sql    =   "select asaction.id, asaction.action_code as \"name\"
							  from ". $table['access']." asaccess ,".
								   $table['node']." asnode,
								   (select role_id
									  from ".$table['user']."
									 where user_lnk_id = {$authId}
									union
									select brole_id
									  from (select brole_id,frole_id from ".$table['rolelnk']."
									  where brole_id not in (select role_id from ".$table['user']." where user_lnk_id = {$authId})
									  )
									connect by prior brole_id = frole_id
									 start with frole_id in
												(select role_id from ".$table['user']." where user_lnk_id = {$authId}) ) asuser,
								   ".$table['role']." asrole ,
								     ".$table['action']." asaction
							 where asuser.role_id = asaccess.role_id 
							   and asaccess.node_id = asnode.id
							   and asuser.role_id = asrole.id
							   and asrole.status > 0 
							   and asnode.status > 0
							   and asaction.status>0
							   and asaccess.action_id=asaction.id
							   and asnode.id={$moduleId}"; 
					$rs =   $db->query($sql);//echo $sql.';';die();
                    foreach ($rs as $a){
                        $publicAction[$a['name']]	 =	 $a['id'];
                    }
                    unset($modules[$key]);
                    break;
                }
            }//die();
            // 依次读取模块的操作权限
            foreach($modules as $key=>$module) {
                $moduleId	 =	 $module['id'];
                $moduleName = $module['name'];
				$sql    =   "select asnode.id, asaction.action_code as \"name\"
							  from ". $table['access']." asaccess ,".
								   $table['node']." asnode,
								   (select role_id
									  from ".$table['user']."
									 where user_lnk_id = {$authId}
									union
									select brole_id
									  from (select brole_id,frole_id from ".$table['rolelnk']."
									  where brole_id not in (select role_id from ".$table['user']." where user_lnk_id = {$authId})
									  )
									connect by prior brole_id = frole_id
									 start with frole_id in
												(select role_id from ".$table['user']." where user_lnk_id = {$authId}) ) asuser,
								   ".$table['role']." asrole ,
								     ".$table['action']." asaction
							 where asuser.role_id = asaccess.role_id 
							   and asaccess.node_id = asnode.id
							   and asuser.role_id = asrole.id
							   and asrole.status > 0 
							   and asnode.status > 0
							   and asaction.status>0
							   and asaccess.action_id=asaction.id
							   and asnode.id={$moduleId}
							   group by asnode.id, asaction.action_code"; 
							  //  echo $sql.';';die();
				$rs =   $db->query($sql); //dump( $sql);
                $action = array();
                foreach ($rs as $a){
                    $action[$a['name']]	 =	 $a['id'];
                }
                // 和公共模块的操作权限合并
                $action += $publicAction;
                $access[strtoupper($appName)][strtoupper($moduleName)]   =  array_change_key_case($action,CASE_UPPER);
                //$access[strtoupper($appName)][strtoupper('public')] = array_change_key_case($publicAction,CASE_UPPER);
            }
        } 
        //halt($access);
        return $access;
    }

	// 读取模块所属的记录访问权限
	static public function getModuleAccessList($authId,$module) {
        // Db方式
        $db     =   Db::getInstance(config('RBAC_DB_DSN'));
        $table = array('role'=>config('RBAC_ROLE_TABLE'),'user'=>config('RBAC_USER_TABLE'),'dept'=>config('RBAC_DEPT_TABLE'),'access'=>config('RBAC_ACCESS_TABLE'),'node'=>config('RBAC_NODE_TABLE'),'action'=>config('RBAC_ACTION_TABLE'),'natlnk'=>config('RBAC_NATLNK_TABLE'));
        $deptData = $db->query("select dpartm_id from emesc.td_user where id = {$authId} ");
        $deptId = $deptData[0]['dpartm_id'];
        $sql    =   "select asaccess.node_id from ".
                    $table['role']." asrole,".
                    $table['user']." asuser,".
                    $table['access']." asaccess ".
                    "where asuser.user_lnk_id='{$authId}' and asuser.role_id=asrole.id and ( asaccess.role_id=asrole.id  or (asaccess.role_id=asrole.pid and asrole.pid!=0 ) ) and asrole.status>0 and  asaccess.module='{$module}' and asaccess.status>0";
        echo $sql.';';return;
        $rs =   $db->query($sql);
        $access	=	array();
        foreach ($rs as $node){
            $access[]	=	$node['node_id'];
        }
		return $access;
	}
}