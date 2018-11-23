<?php
// 用户模型
class TcDeptModel extends Model { 
	public $_auto		=	array( 
		array('update_time','sysdate',self::MODEL_UPDATE,'string'), 
        array('update_emp', 'getEmp',self::MODEL_BOTH,'callback'), 
		);
    public function getEmp(){ 
		$emp        =  cookie('loginAccount');
		return $emp;
	}
    function delRoleDepts($deptId,$roleIdList)
	{
		if(empty($roleIdList)) {
			return true;
		}
		$roleIdList	 =	 implode(',',$roleIdList);
		$where = 'dept_id ='.$deptId.' AND role_id in('.$roleIdList.')';
        $sql = 'delete from td_role_dept WHERE '.$where;//td_
		$rs = $this->execute($sql);
		//echo $sql;
        if($result===false) {
			return false;
		}else {
			return true;
		}
	}
    function setRoleDepts($deptId,$roleIdList)
	{
		if(empty($roleIdList)) {
			return true;
		}
		$roleIdList	 =	 implode(',',$roleIdList);
		$where = 'b.id ='.$deptId.' AND a.id in('.$roleIdList.')';
        $sql = 'INSERT INTO td_role_dept (role_id,dept_id) SELECT a.id, b.id FROM td_role a, tc_dept b WHERE '.$where;
		$rs = $this->execute($sql);
		//echo $sql;
        if($result===false) {
			return false;
		}else {
			return true;
		}
	}
}
?>