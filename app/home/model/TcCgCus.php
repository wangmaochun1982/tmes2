<?php
namespace app\home\model;
use think\Model;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
// 用户模型
class TcCgCus extends Model {  
    protected $connection = 'DB_ATMESR';
	function getSql($parm_pid){ 
		$snode_id = $parm_pid ? $parm_pid :request()->param('id');
		 //dump($pid);
        if(!empty($snode_id)){
			$str=" where snode_id=$snode_id and status=1"; // 禁用狀態不進行獲取
		} 
		$filter = action(request()->controller().'/_filterSql',array('filterRules',true)); 
		$sql ="select * from(select id,id idx,
			   cus_code,
			   cus_name,
			   mlang(cus_name, '".request()->langset()."') lang_value,
			   cg_type,
			   remark,
			   status,
			   snode_id,
			   fnode_id, 
			   user_id,
			   to_char(update_time, 'yyyy/mm/dd hh24:mi') update_time,
			   getOptionVal('cg_type',cg_type, '".request()->langset()."') type_desc
		  from emesc.tc_cg_cus
		  $str)x $filter";//dump($sql);
		return $sql;
	}
	function getSubSql(){ 
		 $pid= $_REQUEST['pid'];
		 $filterMap = action(request()->controller().'/_filterSql',array('filterRules',false));
		 $lang =cookie('login_langset');
		 $sql ="select * from (select b.id,
					  b.pid,
					  a.cus_code,
					  b.option_name,
					  mlang(b.option_name, '".request()->langset()."') lang_value,
					  b.option_val,
					  b.sg_desc,
					  b.bg_color, 
					  b.update_emp,
					  to_char(b.update_time,'yyyy/mm/dd hh24:mi') update_time,
					  b.status,
					  b.ord
				from emesc.tc_cg_cus a,
					 emesc.tc_cg_cus_sub b
				where a.id= b.pid
				and b.pid=$pid)
				where id is not null $filterMap";//dump($sql);
		return $sql;
	}
	function getOpValSql(){
		$cusCode = strtolower(request()->param('cusCode'));
		$type =request()->param('type');
		if($type==0){
			$str = "";
		}else if($type==1 && $cusCode=='pro_section'){
			$str = "and b.option_val = 0";
		}else if($type==2 && $cusCode=='pro_section'){
			$str = "and b.option_val != 0";
		}
		$sql="select b.option_val id,c.lang_text text,c.lang_text||'['||b.id||']' idtext
			  from emesc.tc_cg_cus     a,
				   emesc.tc_cg_cus_sub b,
				   emesc.tc_cg_langtext   c 
			 where a.id = b.pid 
			   and a.cus_code = '$cusCode'
			   and b.option_name = c.lang_code
			   and c.lang_flag = '".request()->langset()."'
			   and a.status = 1
			   and b.status = 1
			   and c.status = 1
			   $str
			   order by b.pid,b.ord";  //echo $sql;
		return $sql;
	}
	function getOpArrySql($cusCode){ 
		$sql="select b.option_val, mlang(b.option_name, 'zh-tw') option_name, b.ord
			  from emesc.tc_cg_cus     a,
				   emesc.tc_cg_cus_sub b 
			 where a.id = b.pid 
			   and a.cus_code = '$cusCode' 
			   and a.status = 1
			   and b.status = 1 
			   order by b.ord"; 
		return $sql;
	}
}   
?>