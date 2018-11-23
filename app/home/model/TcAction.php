<?php
namespace app\home\model;
use think\Model;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
class TcAction extends Model {
	protected $connection = 'DB_ATMESR';
	function getSql(){
		$str = '';
		if(request()->param('sear_code')){
			$searCode = trim(request()->param('sear_code'));
			$str .= " and (a.action_code like '$searCode%' or action_desc like '%$searCode%')";
		}
		if(!empty(request()->param('action_type'))){
			$actionType = trim(request()->param('action_type'));
			$str .=" and a.action_type ='$actionType'";
			
		}
		if(!empty(request()->param('node_lnk'))){
			$nodeLnk = trim(request()->param('node_lnk'));
			$nodeId = trim(request()->param('node_id'));
			if($nodeLnk==1){
				$not=' not ';
			}
			$str .=" and status=1 and id $not in(select action_id from emesc.TC_NODE_ACTION where node_id=$nodeId)";
		}
		
		$filterMap = action(request()->controller().'/_filterSql',array('filterRules',false));
		
		$lang =cookie('login_langset');
		$sql = "select * from (select id,
							   action_code,
							   action_name,
							   mlang(action_name, '$lang') lang_value,
							   icon,
							   action_type,
							   getOptionVal('action_type', action_type, '$lang') list_value,
							   REMARK,
							   status
						  from emesc.TC_ACTION a where id is not null $str)
						 where id is not null  $filterMap";//if(empty($_REQUEST['node_lnk'))){echo $sql;}
		return $sql;
	}
}
?>