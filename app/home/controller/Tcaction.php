<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use app\home\model\TcAction as act;
// +----------------------------------------------------------------------
// | Copyright (c) 2012 All rights reserved.
// +----------------------------------------------------------------------
// | ActionName:單價管理
// +----------------------------------------------------------------------
// | Author: zhm <zhm@keentech-xm.com> at 2014-01
// +----------------------------------------------------------------------
class TcAction extends Base {
	public function XxgetTableJson(){
		$userId = $this->getUserId();
		$ip = $this->request->ip();
		$act = new act();
		$sql = $act->getSql();//dump($sql); die();
		return $this->_listsql($sql);
	}
	public function XxInsert(){
		$actionCode  = $this->request->param('action_code');
		$actionName  = $this->request->param('action_name');
		$actionType = $this->request->param('action_type');
		$icon  = $this->request->param('icon');
		$remark = $this->request->param('remark');
		$status   = $this->request->param('status');
		$emp        =  $this->getUserAccount();
		$empId   =  $this->getUserId(); /*獲取登錄帳號id*/
		$name = $this->request->controller();
		$M =Db::name($name,Null,'DB_ATMESR');
		$pubParam['i_action_code']= $actionCode;
		$pubParam['i_action_name']= $actionName;
		$pubParam['i_icon']= $icon;
		$pubParam['i_action_type']= $actionType;
		$pubParam['i_status']= $status;
		$pubParam['i_remark']= $remark;
		$pubParam['i_user_id']= $empId;
		$pubParam['i_lang_flag']= $this->request->langset() ;

		//$pubParam = array($actionCode,$actionType,$icon,$actionDesc,$status,false);
		$pubName = "pd_action_ins";
		$result = $M->execProcedure($pubName,$pubParam);
		return $this->ajaxReturn($result,'ok',1);
	}

	public function XxUpdate(){
		$id = $this->request->param('id');//列表名ID
		$actionCode  = $this->request->param('action_code');
		$langValue  = $this->request->param('lang_value');
		$actionType = $this->request->param('action_type');
		$icon  = $this->request->param('icon');
		$remark = $this->request->param('remark');
		$status   = $this->request->param('status');
		$emp        =  $this->getUserAccount();
		$empId   =  $this->getUserId(); /*獲取登錄帳號id*/
		$name = $this->request->controller();
		$M =Db::name($name,Null,'DB_ATMESR');
		$pubParam['i_id']= $id;
		$pubParam['i_action_code']= $actionCode;
		$pubParam['i_action_name']= $langValue;
		$pubParam['i_icon']= $icon;
		$pubParam['i_action_type']= $actionType;
		$pubParam['i_status']= $status;
		$pubParam['i_remark']= $remark;
		$pubParam['i_user_id']= $empId;
		$pubParam['i_lang_flag']= $this->request->langset() ;
		//dump($actionCode.','.$actionType.','.$icon.','.$actionDesc.','.$status);
		//$pubParam = array($id,$actionCode,$actionType,$icon,$actionDesc,$status,false);
		$pubName = "PD_ACTION_upd";
		$result = $M->execProcedure($pubName,$pubParam);
		return $this->ajaxReturn($result,'ok',1);
	}
	
}

?>