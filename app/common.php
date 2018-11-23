<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use lib\Rbac;
// 应用公共文件
//用户操作记录方法
function userActionLog($type,$name,$title){
	$request = Request::instance();
	$logModel = Db::connect('DB_ATMESR');
	//$result = Db::query("SELECT role_name,remark FROM emesc.tc_role where role_name=?",['KTGM']);				
    //$result = Db::query("select action_user,action_name,action_title from emesc.tc_user_action_log where action_user=?",[8]);
	//$result = $db->query("select station_number,station_name from emesc.tc_ws_station where station_number=?",[177]);
//dump($result);
    //$logModel = $db->name("TcUserActionLog");
	
  $action_user   = cookie(config('USER_AUTH_KEY'));
  $action_type   = $type;//操作类型
  $action_name   = $name;//操作action名称
  $action_title  = $title;//操作描述
  $addr = cookie('loginPcAddr');
  $client_ip = $request->ip();
  $action_pcaddr = $addr?$addr:$client_ip;
	//dump($logData['action_user'].'-'.$logData['action_type'].'-'.$logData['action_name']);
	
//	$sql = "insert into emesc.tc_user_action_log(action_user,action_type,action_name,action_title,action_time,action_pcaddr)
//values(?,?,?,?,?,?)";
    $sql = "insert into emesc.tc_user_action_log(action_user,action_type,action_name,action_title,action_time,action_pcaddr) values('$action_user','$action_type','$action_name','$action_title',sysdate,'$action_pcaddr')";
    //halt($sql);
    $result = $logModel->execute($sql);
}
function getDbName($dbid){
	
    switch($dbid){
        case 1: $showText = lang('LANG_DB_B1');  break;
        case 2: $showText = lang('LANG_DB_HM');  break;
        case 3: $showText = lang('LANG_DB_B2');  break;
        case 4: $showText = lang('LANG_DB_RT');  break;
        case 5: $showText = lang('LANG_DB_GL');  break;
        case 6: $showText = lang('LANG_DB_HY');  break;
        case 7: $showText = lang('LANG_DB_TK');  break;
        case 8: $showText = lang('LANG_DB_TS');  break;
        case 9: $showText = lang('LANG_DB_TKB');  break;
        default:
        $showText = 'Unknow';
    }
    return $showText;
}
function pwdHash($password, $type = 'md5') {
	return hash ( $type, $password );
}
function execOci($pName,$pValue){
		    $pValue = array_change_key_case($pValue, CASE_UPPER);
		    $user = config('database.username');
		    $pass = config('database.password');
			$dsn =  config('database.hostname').':'.config('database.hostport').'/'.config('database.database');
		    
		    $conn = oci_connect($user,$pass,$dsn) or die;
			
			$argSql = "SELECT ARGUMENT_NAME,IN_OUT,DATA_TYPE,type_name FROM USER_ARGUMENTS WHERE OBJECT_NAME = '".strtoupper($pName)."' and ARGUMENT_NAME is not null ORDER BY SEQUENCE";        
			$s = oci_parse($conn, $argSql);
			oci_execute($s);
			$ParStr = '';
            $x = 0;  
			$nrows = oci_fetch_all($s, $argRow, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
		    //halt($argRow);
			foreach ($argRow as $key=>$value) {
				if($x==0){
                    $ParStr .= ":".$value["ARGUMENT_NAME"];                    
	            }else{
	                $ParStr .= ",:".$value["ARGUMENT_NAME"];                    
	            } 
				
	            $x++;
			}
			//halt($ParStr);
			$sql = "BEGIN $pName($ParStr); END;";
			$bindRes = oci_parse($conn, $sql);
			
			foreach ($argRow as $key=>$val) {
				if($val['IN_OUT'] == 'OUT'){
            	     oci_bind_by_name($bindRes, ":".$val['ARGUMENT_NAME'], $pValue[$val['ARGUMENT_NAME']], 4000);
	                
				}else{
					if($val['DATA_TYPE'] == 'VARRAY' OR $val['DATA_TYPE'] == 'TABLE'){
						$arg = $val['ARGUMENT_NAME'];	
						$$arg = oci_new_collection($conn,strtoupper($val['TYPE_NAME']));
						
						for ($i = 0; $i < count($pValue[$arg]); ++$i) {
							$$arg->append($pValue[$arg][$i]);
						}
						
						oci_bind_by_name($bindRes, ":".$val['ARGUMENT_NAME'], $$arg,-1,OCI_B_NTY); 
					}else{
						
	            	 oci_bind_by_name($bindRes, ":".$val['ARGUMENT_NAME'], $pValue[$val['ARGUMENT_NAME']],1024);
	            
					}		
				}
				
				
			}
			
						
			// Execute the statement but do not commit
			oci_execute($bindRes, OCI_DEFAULT);
			oci_close($conn);
			//halt($pValue)
			return $pValue;
         
	}
	//二维数组排序

function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
    if(is_array($multi_array)){
    foreach ($multi_array as $row_array){
    if(is_array($row_array)){
    $key_array[] = $row_array[$sort_key];
    }else{
    return false;
    }
    }
    }else{
    return false;
    }
    array_multisort($key_array,$sort,$multi_array);
    return $multi_array;
}
//将数组转化为ComTree树形数组 
function ComTreeFormat($data,$pid){
	$tree = array();
	foreach($data as $k => $v){
		if($v['pid'] == $pid){
			$v['children'] = ComTreeFormat($data,$v['id']);
			$tree[] = $v;
		}
	}
	return $tree;
}
function getColVal($id,$tab,$col,$wol="id",$sel){
    if(empty($tab) or empty($col) or empty($id)){return false;}
    $tabModel = Db::name($tab);
    $where[$wol] = $id;
    $where['column_name'] =$sel;
	$where['_logic'] = 'and';
    $list = $tabModel->where($where)->column($wol.','.$col);
    $list = array_flip($list);
    //echo $tabModel->getLastSql();
    //dump($list);
    $name = $list[$id];
    return $name;
}
//二維數組轉一維數組
function arr2to1($list,$default='',$k=''){
 $tmp='';
 if(array($list)){
  if(array($default)){
   $tmp[$default[0]]=$default[1]; 
  }
  foreach ($list as $k1=>$v1){
   $tmp[$k1+1]=$v1[$k];
     } 
 }
 return $tmp;
}
