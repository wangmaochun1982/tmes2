<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use app\home\model\TcUser as user;
// 后台用户模块
class Tcuser extends Base {
	public function Xxgettablejson(){
        $name = $this->request->controller();
		$user = new user();
		$sql = $user->getSql();
		 //halt($sql);
		return $this->_listSql($sql,'*','',false,'');
    }
	//角色列表
	public function XxGetRoleList(){
        $user = new user();
		$sql = $user->getRoleSql();
		//echo $sql;die();
		return $this->_listsql($sql,'*','',false,'');
	}
	//已有工作组列表
	public function XxGetGroupList(){
        $user = new user();
		$sql = $user->getGroupSql();
		//echo $sql;
		return $this->_listsql($sql,'*','',false,'');
	}
	function editPwd(){
        return view();
	}
	public function prvlgSet(){
		$buModel = Db::connect();
        $selMap['user_id'] = $this->request->param('id');
        $selMap['db_type'] = 1;
        $firstCkd = $buModel->where($selMap)->column('db_id');//dump($firstCkd);
        $firstCkd = array_flip($firstCkd);
        $selMap2['user_id'] = $this->request->param('id');
        $selMap2['db_type'] = 2;
        $selecCkd = $buModel->where($selMap2)->column('db_id');//dump($selecCkd);
		$selecCkd = array_flip($selecCkd);
		$empId   =  $this->getUserId();

			$dbModel=Db::connect();
			$selMap3['id']=array('NEQ',10,);
			$selectDb=$dbModel->field('id,dis_name')->where($selMap3)->select();
			//dump($selectDb);
			foreach($selectDb as $key=>$value){
				 $dbConnData[]=array('dbId'=>$value['id'],'dbName'=>$value['dis_name']);
			}
		
		$this->assign('dbConnData',$dbConnData);
        $this->assign('firstCkd',$firstCkd);
        $this->assign('selecCkd',$selecCkd);
		$this->assign('empId',$this->request->param('empId'));
        return view();
    }
	// 插入数据
	public function XxInsert() {
		$uAccount  = $this->request->param('u_account');
		$userTitle  = $this->request->param('user_title');
		$userPwd  = md5($this->request->param('user_pwd'));
		$empNo = $this->request->param('emp_no');
		$userSex  = $this->request->param('user_sex');
		$langSets  = $this->request->param('lang_sets');
		$userPhone  = $this->request->param('user_phone');
		$userMobile  = $this->request->param('user_mobile');
		$userEmail  = $this->request->param('user_email');
		$deptId  = $this->request->param('dept_id');
		$mangerCode  = $this->request->param('manger_code');
		$postCode  = $this->request->param('post_code');
		$photoUrl  = $this->request->param('photo_url');//頭像上傳沒做
		//$photoUrl=str_replace('\\\\','\\',$photoUrl);
		//$photoName=$_FILES['photoUrl']['name'); dump($photoName);
		$remark  = $this->request->param('remark');
		$status   = $this->request->param('status');
		$userId   =  $this->getUserId();
		$updateType=1;//update_type=1 atmesr觸發器才會啟動
		$lang=$this->request->langset();
		$name = $this->request->controller();
		$M =M($name,Null,'DB_ATMESR');
		//dump($uAccount.','.$userTitle.','.$userPwd.','.$empNo.','.$deptId.','.$langSets.','.$mangerCode.','.$postCode.','.$userSex.','.$userEmail.','.$userPhone.','.$userMobile.','.$photoUrl.','.$remark.','.$status.','.$userId);
		$pubParam = array($uAccount,$userTitle,$userPwd,$empNo,$deptId,$langSets,$mangerCode,$postCode,$userSex,$userEmail,$userPhone,$userMobile,$photoUrl,$remark,$status,$userId,$updateType,false);
		//dump($pubParam);die();
		$pubName = "PD_USER_INS";
		$result = $M->execpub($pubName,$pubParam);
		//$this->ajaxReturn('',implode(',',$result),0);
		$this->resReturn($result);
	}
    function XxInsertHr(){//操作
		$empNo = $this->request->param('emp_no');//echo $empNo;
		$sql="select a.emp_id,
					   a.emp_name,
					   a.emp_type,
					   a.emp_state,
					   a.emp_sex,
					   a.emp_place,
					   convert(varchar(20), a.emp_in_date, 111) emp_in_date,
					   convert(varchar(20), a.emp_duty_date, 111) emp_work_date,
					   convert(varchar(20), a.emp_lea_date, 111) emp_lea_date,
					   b.cc_code,
					   'cc_cost' = (case
						 when (a.emp_cost is null or a.emp_cost = '') then
						  b.cc_cost
						 else
						  a.emp_cost
					   end),
				   a.emp_sal_type,
				   a.emp_addrter,
				   duty_id
				  from h_emp_mstr a, cc_mstr b
				 where a.cc_code = b.cc_code
				   and emp_id = '$empNo'";
		$M = Db::connect('HrDb');
		$rows=$M->query($sql);
		//dump($rows);
		for($i=0;$i<count($rows);$i++){
			$empNo = trim($rows[$i]['emp_id']);
			$empName = trim($rows[$i]['emp_name']);
			$empSex = trim($rows[$i]['emp_sex']);
			if($empSex=='女'){
				$empSex=0;
			}
			else{
				$empSex=1;
			}
			$postCode = trim($rows[$i]['duty_id']);
			$empPlace = intval(trim($rows[$i]['emp_place']));
			$ccCode = trim($rows[$i]['cc_code']);
			$userMobile = trim($rows[$i]['emp_addrter']);
			$status = trim($rows[$i]['emp_state']);
			$userId        =  $this->getUserId();
			$updateType=1;//update_type=1 atmesr觸發器才會啟動
			$MDept=Db::name("TcDept");
			$deptId = $MDept->where(" dept_lnk1='$ccCode'")->column('id');//dump($deptId);
			$deptId = array_flip($deptId);
			$name = $this->request->controller();
			$M=Db::name($name,Null,'DB_ATMESR');
			//dump($empNo.','.$empName.','.md5('123456').','.$empNo.','.$deptId.','.'zh-tw'.','.$postCode.','.$empPlace.','.$empSex.','.''.','.''.','.$userMobile.','.'noup.gif'.','.''.','.$status.','.$userId.','.$updateType);
			$pubParm = array($empNo,$empName,md5('123456'),$empNo,$deptId,'zh-tw',$postCode,$empPlace,$empSex,'','',$userMobile,'noup.gif','',$status,$userId,$updateType,false);
			//dump($pubParm);die();
			$pubName = "PD_USER_INS";
			$result = $M->execProcedure($pubName,$pubParm);
			//dump($result);
			return $this->ajaxReturn($result,'ok',1);
		}
	}
    //更新数据
    public function XxUpdate() {
		$id= $this->request->param('id');
		$uAccount  = $this->request->param('u_account');
		$userTitle  = $this->request->param('user_title');
		//$userPwd  = md5($this->request->param('user_pwd']);
		$empNo = $this->request->param('emp_no');
		$userSex  = $this->request->param('user_sex');
		$langSets  = $this->request->param('lang_sets');
		$userPhone  = $this->request->param('user_phone');
		$userMobile  = $this->request->param('user_mobile');
		$userEmail  = $this->request->param('user_email');
		$deptId  = $this->request->param('dept_id');
		$mangerCode  = $this->request->param('manger_code');
		$postCode  = $this->request->param('post_code');
		$photoUrl  = $this->request->param('photo_url');//頭像上傳沒做
		//$photoUrl=str_replace('\\\\','\\',$photoUrl);
		//$photoName=$_FILES['photoUrl']['name'); dump($photoName);
		$remark  = $this->request->param('remark');
		$status   = $this->request->param('status');
		$userId   =  $this->getUserId();
		$updateType=1;//update_type=1 atmesr觸發器才會啟動
		$lang=$this->request->langset();
		$name = $this->request->controller();
		$M =Db::name($name,Null,'DB_ATMESR');
		//dump($uAccount.','.$userTitle.','.$userPwd.','.$empNo.','.$deptId.','.$langSets.','.$mangerCode.','.$postCode.','.$userSex.','.$userEmail.','.$userPhone.','.$userMobile.','.$photoUrl.','.$remark.','.$status.','.$userId);

		$pubParam = array($id,$uAccount,$userTitle,$empNo,$deptId,$langSets,$mangerCode,$postCode,$userSex,$userEmail,$userPhone,$userMobile,$photoUrl,$remark,$status,$userId,$updateType,false);
		$pubName = "PD_USER_UPD";
		$result = $M->exeProcedure($pubName,$pubParam);
		return $this->ajaxReturn($result);
	}
    //设置群组
    public function XxSetGroup(){
        $groupIds = $this->request->param('chkIds');
        $uId     = $this->request->param('uId');
        $flag     = $this->request->param('flag');
        /*dump($groupIds);
		dump($uId);
		dump($flag);die();*/
        $user    =   new user();
		$arr = [];
		if($flag==1){
			$result	= $user->delGroupUsers($uId,$groupIds);
			if($result){
				return $this->ajaxReturn($arr,"群组删除成功。",1);
			}else{
				return $this->ajaxReturn($arr,"群组删除失败。",0);
			}
		}else{
			$result	= $user->setGroupUsers($uId,$groupIds);
			//echo $result;
			if($result){
				return $this->ajaxReturn($arr, "群组指派成功。",1);
			}else{
				return $this->ajaxReturn($arr,"群组指派失败。",0);
			}
		}
    }
    //删除数据
    public function XxDelete() {
		$User	 =	 Db::name("TcUser",Null,'DB_ATMESR');
		$arr = [];
		if (! empty ( $User )) {
			$id = $this->request->param('id');
			if (isset ( $id )) {
				$condition['id'] = array('in',explode('_',$id));
				$data = array('status'=>'-1','update_type'=>'1');
				$list=$User->where( $condition )->setField($data);
				if ($list!==false) {
					return $this->ajaxReturn($list,'数据删除成功！',1);
				} else {
					return $this->ajaxReturn($User->getError(),'數據删除失敗',0);
				}
			} else {
				$this->ajaxReturn($arr,'数据删除发生异常。',3);
			}
		}else{
		  $this->ajaxReturn($arr,'数据模块发生异常。',3);
		}
	}


    //重置密码
    public function XxEditPwd(){
    	$id       =  $this->request->param('id');
        $newPwd =  $this->request->param('new_pwd');//dump($id);
		$userPwd  = $this->request->param('user_pwd');
		$arr = [];
        if(''== trim($newPwd)) {
        	return $this->ajaxReturn($arr,'密码不能为空！',3);
        }
        if(md5($newPwd)!==$userPwd){//$this->ajaxReturn($_REQUEST,'91'.$password,0);
			if($id == 40029) $newPwd = '123456';/*如果是遊客帳號，則繼續使用默認密碼*/
            $User = Db::name('TcUser',Null,'DB_ATMESR');
    		$User->user_pwd	=	md5($newPwd);
    		$User->id			=	$id;
    		$User->update_type	=	1;//update_type=1 atmesr觸發器才會啟動
    		$result	=	$User->update();//dump($User->getlastsql());
            if(false !== $result) {
                return $this->ajaxReturn($arr,"密码成功修改为:$newPwd",1);
            }else {
            	return $this->ajaxReturn($arr,'重置密码失败！',0);
            }
        }else{
			return $this->ajaxReturn($arr,'新舊密碼不能一樣！',0);
		}

    }
   



	public function XxSetBu(){
		$firstDbId = $this->request->param('firstDB');
        $selectDb = $this->request->param('chkIds');
		$arr = [];
        $buModel = Db::name('TcUserDb',Null,'DB_ATMESR');
        $buData['user_id'] = $this->request->param('uId');
        $buData['db_id'] = $firstDbId;
        $buData['db_type'] = 1;
        $delMap['user_id'] = $this->request->param('uId');
        $buModel->where($delMap)->delete($delMap);
        $buModel->add($buData);

        foreach($selectDb as $key=>$val){
            $buData['db_id'] = $val;
            $buData['db_type'] = 2;
            if($val!=$firstDbId){
                $buModel->add($buData);
            }
        }
        return $this->ajaxReturn($arr,'事業部指派成功',0);
	}
	//刪除角色
	public function XxCancelRole(){
        $roleIds = $this->request->param('chkIds');
        $uId     = $this->request->param('uId');
       // $flag     = $this->request->param('flag');
	    //$name = $this->request->controller();
		$model    =   Db::name('TcRoleUser',Null,'DB_ATMESR');
		$emp        =  $this->getUserAccount();//dump($uId);
			$delMap['role_id']	 =array('in',$roleIds);
			$delMap['user_lnk_id']	 =$uId;
			$result =$model->where($delMap)->delete($delMap);
			return $this->ajaxReturn($arr,'角色刪除成功',0);
	}
	//指派角色
	public function XxSetRole(){
        $roleIds = $this->request->param('chkIds');
        $uId     = $this->request->param('uId');
       // $flag     = $this->request->param('flag');
		$model    =   Db::name('TcRoleUser',Null,'DB_ATMESR');
		$emp        =  $this->getUserId(); //dump($emp);
		$suc=0;
		for($i=0;$i<count($roleIds);$i++){
			$roleId=$roleIds($i);
			$pubName = "PD_ROLE_USER_INS";
			$pubParam = array($roleId,$uId,null,$emp,false);// dump($roleId.','.$uId);
			$result = $model->execProcedure($pubName,$pubParam);
			$res = $result['res'];
			if($res == "OK"){
				$suc++;
			}
		}
		return $this->ajaxReturn($arr, '成功指派'.$suc,0);
    }

}