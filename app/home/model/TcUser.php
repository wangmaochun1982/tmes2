<?php
namespace app\home\model;
use think\Model;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
// 用户模型
class TcUser extends Model {  
	
    public function getSql(){
    	$whereStr = ''; 
		$noInId = cookie(config('USER_AUTH_KEY'));
        $ruModel = Db::name('TcRoleUser');
		$whereStr .= " and a.id not IN (1)";
	//halt(Session::has('administrator'));
       if($noInId>1 and !Session::has('administrator')){  
            if(request()->has('dpartm_id')){
				$deptid = request()->param('dpartm_id');
            }else{ 
				$deptid =  getTableColVal($noInId,'TcUser','DEPT_ID');
            }
            $dpsql = "select id
                  from emesc.tc_dept
                 start with id = $deptid
                connect by prior id = pid"; //echo $dpsql;//die();
			//查找$deptid所屬ID和子級ID  eg:$deptid=6
			//dump($dpsql);
			$res = Db::connect()->query($dpsql);
			//dump($res);
            $dplist = arr2to1($res,null,'id');
            $dpArrs = rtrim(ltrim(implode(',',$dplist),','),',');
            $whereStr.=" and a.DEPT_ID in ($dpArrs)";
        }//dump(2);die();
        $action_name = request()->controller();
		//halt($action_name);
        $filterMap = action($action_name.'/_filterSql',array('filterRules',true));
        //halt($filterMap);
        $sql = "select *
  from (select a.id,
               a.u_account,
               a.user_title,
               a.user_pwd,
               a.emp_no,
               a.dept_id,
               a.lang_sets,
               a.manger_code,
               a.post_code,
               a.user_sex,
               a.status,
			   getoptionval('status',a.status,'".request()->langset()."') status_name,
               a.user_email,
               a.user_phone,
               a.user_mobile,
               a.online_status,
               a.session_id,
               a.last_login_ip,
               a.login_count,
               a.photo_url,
               a.login_area,
               a.remark,
               a.update_id,
               b.dept_name,
               c.dis_name bu_name,
               to_char(a.last_login_time, 'yyyy/mm/dd hh24:mi:ss') last_login_time,
               to_char(a.update_time, 'yyyy/mm-dd hh24:mi:ss') update_time
          from emesc.tc_user a, emesc.tc_dept b, emesc.tc_db_con C
         where a.dept_id = b.id(+)
           and a.login_area = c.id(+) $whereStr) $filterMap"; //echo $sql;
		return $sql;
	}
	public function getGroupSql(){ 
		$empId = request()->param('empId');
		
		if(request()->has('lnkFlag')){
            $lnkFlag = request()->param('lnkFlag');
			if($lnkFlag==2){
				$not=" not ";
			}
			$str=" and b.group_code is $not null";
		}	 
		$filterMap = action(request()->controller().'/_filterSql',array('filterRules',false));
        $sql = "select *
				  from (select a.group_code id,
							   a.group_code,
							   a.group_name,
							   a.group_type,
							   a.group_desc,
							   c.section_name,
							   c.section_desc,
							   case
								 when b.group_code is not null then
								  1
								 else
								  0
							   end okflag
						  from emesc.tc_ws_group_desc a,
							   (select *
								  from emesc.tc_scrt_emp_group_rel
								 where emp_no = '$empId') b,
								emesc.tc_ws_section_desc c
						 where a.group_code = b.group_code(+)
						   and a.section_code = c.section_code
						   and a.status > 0
						   and a.group_type <> 1
						   and a.group_code <> 0 $str)
				 where id is not null $filterMap";//echo $sql;
		return $sql;
	}
	function getRoleSql(){
		$uId = request()->param('userId');
		$deptId = request()->param('id'); 
		$filterMap = action(request()->controller().'/_filterSql',array('filterRules',true)); 
		if(request()->has('lnkFlag')){
            $lnkFlag = request()->param('lnkFlag');
			if($lnkFlag==2){
				$not=" not ";
			}
			$str=" and b.role_id is $not null";
		}	 
		if($deptId){
			//$str .= " and a.dept_id = '$deptId'";
			$str .= " and a.dept_id in(select id
									  from emesc.tc_dept
									 start with id = $deptId
									connect by prior id = pid)";
		}
		$sql = "select * from (select a.id, a.role_name, a.remark, c.dept_name
			  from emesc.tc_role a, (select * from emesc.tc_role_user where user_lnk_id=$uId) b, emesc.tc_dept c
			 where a.dept_id = c.id
			   and a.id = b.role_id(+)
			   and a.status = 1
			   $str) $filterMap"; //dump($sql);
		return $sql;
	}  
	
	function delGroupUsers($empId,$groupIdList){
		if(empty($groupIdList)) {
			return true;
		}        
		$groupIdList	 =	 implode(',',$groupIdList);
		$where = "emp_no = (select account from emesc.tc_user where id = $empId) AND group_code in(".$groupIdList.")";
        $sql = 'delete from TC_SCRT_EMP_GROUP_REL WHERE '.$where;
		$rs = $this->execute($sql);
		//echo $sql;
        if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function setGroupUsers($empId,$groupIdList)
	{
		if(empty($groupIdList)) {
			return true;
		}        
		$groupIdList	 =	 implode(',',$groupIdList);
		$where = "a.id ='".$empId."' AND b.group_code in(".$groupIdList.")";
        $sql = 'INSERT INTO TC_SCRT_EMP_GROUP_REL (emp_no,group_code,id) SELECT a.account emp_no, b.group_code,Tc_Scrt_Emp_Group_Rel_Id_Seq.Nextval FROM tc_user a, Tc_Ws_Group_Desc b WHERE '.$where;
		//return $sql;
		$rs = $this->execute($sql); 
        if($result===false) {
			return false;
		}else {
			return true;
		}
	}
	  
}
?>