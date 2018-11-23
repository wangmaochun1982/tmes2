<?php
namespace app\home\model;
use think\Model;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
class TcRole extends  Model{
	public function getSql(){		
		$deptId = request()->param('id');//dump(request()->param('id']);
		$rId = request()->param('rId');
		//halt(request());
		$sql = '';
		$str = '';
		$filterMap = '';
		//dump(request()->param('id'));
		if($deptId !=''){  
			if($rId) $str .= " and a.id not in($rId)" ;
			$filterMap = action(request()->controller().'/_filterSql',array('filterRules',true));
			$sql = "select  * from (
					select  a.id,
							a.role_name,
							a.dept_id,
							b.dept_name,
							a.status,
							getoptionval('status',a.status,'".request()->langset()."') status_name, 
							a.remark,
							a.role_type2,
							getoptionval('role_type2',a.role_type2,'".request()->langset()."') type_name2,
							a.role_type3,
							getoptionval('role_type3',a.role_type3,'".request()->langset()."') type_name3
					from  emesc.tc_role a,
						  emesc.tc_dept b
					where role_type1 = 1
					and a.dept_id = b.id
					and a.dept_id in(select id
									  from emesc.tc_dept
									 start with id = $deptId
									connect by prior id = pid)
					$str ) $filterMap";
		} //dump($sql);
		//halt($sql);
		return $sql;
	}
	public function getExtendRoleSql($flag){
		$id = request()->param('roleId');
		if($flag){
			$BFstr = " and Brole_id = '$id'";
			$BFlnk = " a.Frole_id = b.id";
		}else{
			$BFstr = " and Frole_id = '$id'";
			$BFlnk = " a.Brole_id = b.id";
		}
		$sql="select b.role_name,
				   c.dept_name,
				   a.remark,
				   a.frole_id,
				   a.brole_id
			from emesc.tc_role_LNK a,
				emesc.tc_role  b,
				emesc.tc_dept c
			where $BFlnk
			and	  b.dept_id = c.id
			$BFstr 		";
		return $sql;
	}
	public function getUserBuSql($flag){	
		$buId = request()->param('id');
		$roleId = request()->param('rId'); 
		$where = action(request()->controller().'/_filterSql',array('filterRules',true));
		$buId = $buId?$buId:1;
		if($flag){
			$apFlag = " and b.id = c.user_lnk_id(+) and c.user_lnk_id is null";
		}else{
			$apFlag = " and b.id = c.user_lnk_id";		
		} 
		$sql = "select *
  from (select b.id,
               b.u_account,
               b.user_title,
               a.dept_name,
               case
                 when c.user_id is not null then
                  1
                 else
                  0
               end okflag
          from (select id, dept_name
                  from (select a.* from emesc.tc_dept a)
                 where status > 0
                connect by prior id = pid
                 start with id = $buId) a,
               emesc.tc_user b,
               (select * from emesc.tc_role_user where role_id = $roleId) c
         where a.id = b.dept_id
           and b.status > 0 $apFlag) $where";  //echo $sql;die();
		return $sql;
	}	
	public function getResourceSql(){
		$roleId = request()->param('rId');
        $startId = request()->param('nId');
		if($startId !=''){
			$sql="select * from 
						(select p.*,
						   case
							 when q.role_id is not null then
							  1
							 else
							  0
						   end okflag
					  from (select a.id, b.action_id, c.action_desc
							  from emesc.tc_node           a,
								   emesc.tc_node_action b,
								   emesc.tc_action    c
							 where a.id = b.node_id
							   and b.action_id = c.id
							 order by a.pid, a.id, c.id) p,
						   (select * from emesc.tc_access where role_id = $roleId) q
					 where p.id = q.node_id(+)
					   and p.action_id = q.action_id(+)
					   and p.id = $startId)";
		}
		return $sql;
	}
	function getNodeGridSql(){
        $roleId = request()->param('rId');  
        $startId = implode(',',request()->param('pid'));
		$lang =cookie('login_langset');
        $sql="select *
		  from (select A.ID,
					   A.NODE_CODE,
					   a.NODE_NAME,
					   zh_concat(distinct case
								   when b.action_id is not null then
									a.action_id || '|' || a.action_name || '|' || 1
								   else
									a.action_id || '|' || a.action_name || '|' || 0
								 end) permission,
					   A.PID,
					   A.NODE_LEVEL,
					   A.NODE_SORT,
					   A.NODE_TYPE,
					   A.ISMENU,
					   A.NODE_ICON,
					   A.REMARK,
					   a.parentid
				  from (select A.ID,
							   A.NODE_CODE,
							   MLANG(A.NODE_NAME, 'zh-tw') NODE_NAME,
							   b.action_id,
							   MLANG(c.action_name, 'zh-tw') action_name,
							   A.PID,
							   A.NODE_LEVEL,
							   A.NODE_SORT,
							   A.NODE_TYPE,
							   A.ISMENU,
							   A.NODE_ICON,
							   A.REMARK,
							   CASE
								 WHEN A.PID = 0 THEN
								  NULL
								 ELSE
								  A.PID
							   END parentid
						  from EMESC.TC_NODE           a,
							   emesc.tc_node_action b,
							   emesc.tc_action    c
						 WHERE a.node_type < 3
						   and a.status = 1
						   and a.id = b.node_id(+)
						   and b.action_id = c.id(+)
						 order by a.id, c.id) a,
					   (SELECT * FROM EMESC.Tc_ACCESS WHERE ROLE_ID = $roleId ) B
				 where a.id = b.node_id(+)
				   and a.action_id = b.action_id(+)
				 group by A.ID,
						  A.NODE_CODE,
						  a.NODE_NAME,
						  A.PID,
						  A.NODE_LEVEL,
						  A.NODE_SORT,
						  A.NODE_TYPE,
						  A.ISMENU,
						  A.NODE_ICON,
						  A.REMARK,
						  a.parentid
				connect by prior a.id = a.pid
				 start with id in ($startId)
				union
				select A.ID,
					   A.NODE_CODE,
					   a.NODE_NAME,
					   zh_concat(distinct case
								  when b.action_id is not null then
									a.action_id || '|' || a.action_name || '|' || 1
								   else
									a.action_id || '|' || a.action_name || '|' || 0
								 end) permission,
					   A.PID,
					   A.NODE_LEVEL,
					   A.NODE_SORT,
					   A.NODE_TYPE,
					   A.ISMENU,
					   A.NODE_ICON,
					   A.REMARK,
					   a.parentid
				  from (select A.ID,
							   A.NODE_CODE,
							   MLANG(A.NODE_NAME, 'zh-tw') NODE_NAME,
							   b.action_id,
							   MLANG(c.action_name, 'zh-tw') action_name,
							   A.PID,
							   A.NODE_LEVEL,
							   A.NODE_SORT,
							   A.NODE_TYPE,
							   A.ISMENU,
							   A.NODE_ICON,
							   A.REMARK,
							   CASE
								 WHEN A.PID = 0 THEN
								  NULL
								 ELSE
								  A.PID
							   END parentid
						  from EMESC.TC_NODE           a,
							   emesc.tc_node_action b,
							   emesc.tc_action    c
						 WHERE a.node_type < 3
						   and a.status = 1
						   and a.id = b.node_id(+)
						   and b.action_id = c.id(+)
						 order by a.id, c.id) a,
					   (SELECT * FROM EMESC.Tc_ACCESS WHERE ROLE_ID = $roleId ) B
				 where a.id = b.node_id(+)
				   and a.action_id = b.action_id(+)
				 group by A.ID,
						  A.NODE_CODE,
						  a.NODE_NAME,
						  A.PID,
						  A.NODE_LEVEL,
						  A.NODE_SORT,
						  A.NODE_TYPE,
						  A.ISMENU,
						  A.NODE_ICON,
						  A.REMARK,
						  a.parentid
				connect by prior a.pid = a.id
				 start with id in ($startId))"; //echo $sql;
		return $sql;
	}
}
?>