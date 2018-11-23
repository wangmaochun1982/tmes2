<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use app\home\model\TcRole as role;
class TcRole extends Base{
	
	 //指派頁面
    public function prvlgSet(){
        $this->assign('roleId',$this->request->param('roleId'));
        return view();
    }    
	public function XxgetUserBu(){
		$name = $this->request->controller();
		$role = new role();
		$sql = $role->getUserBuSql(1); 
		return $this->_listSql($sql); 
	}
	public function XxgetUserAppoint(){
		$name = $this->request->controller();
		$role = new role();
		$sql = $role->getUserBuSql(0); 
		return $this->_listSql($sql); //dump($sql);
	}
	
	public function Xxgettablejson(){  
		$name = $this->request->controller();
		$role = new role();
		$sql = $role->getSql();  //dump($sql);

		 return  $this->_listSql($sql,'*','',false,'');
		
	}
	//繼承的角色的Datagrid數據
	public function XxgetExtendRole(){		 
		$name = $this->request->controller();
		$role = new role();
		$sql = $role->getUserBuSql(0); 
		//echo $sql;
		return $this->_listSql($sql); 
	}
	
	//被繼承的Datagrid數據
	public function XxgetReExtendRole(){ 
		$name = $this->request->controller();
		$role = new role();
		$sql = $role->getExtendRoleSql(1); 
		return $this->_listSql($sql); 		
	}
	//資源指派頁面中的treeGrid數據
	public function XxGetResource(){  
		$name = $this->request->controller();
		$role = new role();
		$sql = $role->getExtendRoleSql();//dump($sql);
		return $this->_listSql($sql); 		
	} 
	public function XxGetNodeGrid(){   
		$model =new role();
		$sql = $model->getNodeGridSql();
		//dump($sql); 	 
        $roleList = Db::connect()->query($sql);
       	$result = array();
        foreach($roleList as $key=>$data){  
            $node = array();
			$operation = array();
			$perm = explode(',',$data['permission']);
			for($i=0;$i<count($perm);$i++){
				$s = explode('|',$perm[$i]);
				$operation[$i]['id'] = $s[0];
				$operation[$i]['title'] = $s[1];
				$operation[$i]['okflag'] = $s[2];
			}
            $node['id'] = $data['id'];
            $node['title'] = $data['node_name'];
            $node['type'] = $data['node_type'];
            $node['ismenu'] = $data['ismenu'];
            $node['okflag'] = $data['okflag'];
            $node['operation'] = $operation;
            $node['parentid'] = $data['parentid']; 
            $count = $count + 1; 
            array_push($result,$node); 
        }
		$result = arrayToTree($result,'id','parentid','children');
		if($count>50){
			$results = '資源筆數超過50筆,請重新選擇'; 
		}else{
			$results = '{"total":'.$count.',"rows":[' .json_encode($result[1]). ']}'; 
		}       
		return  ($results);  
    }
	
/*----------------------------------------------------------------------------------------------
  -------- ----------------------------------      新增按鈕  -------------------------------------     
--------------------------------------------------------------------------------------------------------
*/	
	//新增角色
	public function XxInsert(){
		$roleType1 = 1 ;//功能/數據角色
		$roleType2 = $this->request->param('role_type2');//角色類別:基礎角色,應用角色
		$roleType3 = $this->request->param('role_type3');//基礎角色類別:生產,業務,品保
		$roleName  = $this->request->param('role_name');//角色名
		$deptId  = $this->request->param('dept_id');//部門id
		$status = $this->request->param('status');//狀態
		$remark = $this->request->param('remark');//備註
		$emp = $this->getUserId();
		$model = Db::name($this->request->controller(),Null,'DB_ATMESR'); 
		$pubParams = array($deptId,$roleName,$roleType1,$roleType2,$roleType3,$status,$emp,$remark,false); //存儲過程參數
		$pubName = "PD_ROLE_INS";//存儲過程名
		$result = $model->execProcedure($pubName,$pubParams); //執行存儲過程
		return $this->ajaxReturn($result,'ok',1);
	}
/*---------------------------------------------------------------------------------------------------------------------
 -------- ----------------------------------      編輯按鈕  ----------------------------------------------       
------------------------------------------------------------------------------------------------------------------
*/
	//修改角色
	public function XxUpdate(){
		$id=$this->request->param('id');
		$roleName = $this->request->param('role_name');
		$status = $this->request->param('status');
		$remark = $this->request->param('remark');
		$deptId = $this->request->param('dept_id');
		$roleType2 = $this->request->param('role_type2');
		$roleType3 = $this->request->param('role_type3');
		$sql = "update emesc.tc_role
				set role_name = '$roleName',
					status = '$status',
					remark = '$remark',
					role_type2 = '$roleType2',
					role_type3 = '$roleType3',
					DEPT_ID = '$deptId'
				where id= '$id'	";
	//	dump($sql);
		$model = Db::name($this->request->controller(),Null,'DB_ATMESR');
		$result = $model -> execute($sql);
		if($result !==false){
			return $this->ajaxReturn($result,'角色修改成功',1);
		}else{
			return $this->ajaxReturn($result,'角色修改失敗',0);
		}
		
	}  
	
	//取消繼承
	public function XxCancelExtend(){
		$frole = $this->request->param('frole');
		$brole = $this->request->param('brole');
		$sql= "delete from emesc.tc_role_lnk
				where frole_id = '$frole'
				and   brole_id = '$brole'
				";
		$model= Db::name($this->request->controller(),Null,'DB_ATMESR');
		$result = $model->execute($sql);
		if($result !==false){
			return $this->ajaxReturn($result,'取消成功!',1);
		}else{
			return $this->ajaxReturn($result,'取消失敗!',0);
		}
		
		//$this->ajaxReturn($_POST,$frole.'--'.$brole,1);
	}
	
	//角色繼承的存儲
	public function XxRoleInsert(){
		$frole = $this->request->param('roleId');//要繼承的角色id
		$role = $this->request->param('role');//被繼承的角色信息 
		$emp        =  $this->getUserId();
		$model = Db::name($this->request->controller(),Null,'DB_ATMESR');
		for($i=0;$i<count($role);$i++){
			$brole = $role[$i]['id'];
			$name = $role[$i]['role_name'];
			$remark = $role[$i]['remark'];//dump($name);
			$pubParam = array($frole,$brole,$remark,$emp,false);
			$pubName = "PD_ROLE_LNK_INS";
			$result = $model->execProcedure($pubName,$pubParam);
			$res = $result['res'];
			if($res =="OK"){
				$save .= $brole.'++'.$name.'--';
			}
		}
		
		return $this->ajaxReturn($result,'以下角色:-'.$save.'-繼承成功',1);	

	}
	
	//-----------------------------資源指派 ----------------------------------------------
	public function XxSaveResource(){
        //$this->ajaxReturn('','注意：为避免与旧系统冲突，授权已被禁止[LHP-2013年9月17日]。',2);
        //die();
        $roleId  = $this->request->param('roleId');
        $nodeId = $this->request->param('nodeId');
        $ckAction = $this->request->param('ckActionList');
        $nodeList = $this->request->param('nodeList');
		$ckdList  = implode('|',$ckAction);
		$nodeStr  = implode(',',$nodeList);
		//$this->ajaxReturn('',$roleId.'<br>'.$nodeStr.'<br>'.$ckdList,0);
		$emp = $this->getUserId();
		$M=M('TcNode',Null,'DB_ATMESR');  //默認指定到球拍數據庫
		// echo $roleId.'='.$nodeStr.'='.$ckdList.'='.$emp;die();
		$pubParm = array($roleId,$nodeStr,$ckdList,null,null,null,$emp,false);
         //   $this->ajaxReturn($POST,implode('-',$pubParm),0); 
		$pubName = "PD_ACCESS_INS";
		$result = $M->execProcedure($pubName,$pubParm);
		return $this->ajaxReturn($result,'ok',1);
    }
	//指派用戶
	public function XxSaveUser(){
        $checkUser = $this->request->param('chkUser');
        $roleId    = $this->request->param('roleId');
		//$nodeId	   = $this->request->param('nodeId');
		//$ckdUserList = implode(',',$checkUser);
		$emp = $this->getUserId();
		$model    =   M('TcRoleUser',Null,'DB_ATMESR');
		$suc=0;
		for($i=0;$i<count($checkUser);$i++){
			$uId=$checkUser[$i];
			$pubName = "PD_ROLE_USER_INS";//dump($roleId.','.$uId.','.$emp);
			$pubParam = array($roleId,$uId,null,$emp,false);
			$result = $model->execProcedure($pubName,$pubParam);
			$res = $result['res'];
			if($res == "OK"){
				$suc++;
			}
		}
		return $this->ajaxReturn($result,'成功指派'.$suc,0);
    } 
	//删除用户
	public function XxCancleUser(){
		$usersId = $this->request->param('usersId');//dump(2);die();
        $roleId = $this->request->param('roleId');
		$lnkModel = M('TcRoleUser',Null,'DB_ATMESR');
        $Map['role_id'] = $roleId;
        $Map['user_lnk_id'] = array('in',$usersId);
		$result =$lnkModel->where ( $Map )->delete ($Map); 
		return $this->ajaxReturn($result,'删除成功',1);
		
	}
}
?>