<?php
namespace app\home\model;
use think\Model;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
class TpSo extends Model { 
	public function getSql(){
		
		$controller = request()->controller();
		//halt($controller);			 
		$where = action($controller.'/_filterSql',['filterRules',true]);  

        $sql = "select *
				  from (select a.SO_NUMBER,
							   a.so_type,
							   getOptionVal('so_type', a.SO_TYPE, 'zh-tw') so_desc,
							   a.PO_NO,
							   a.TARGET_QTY,
							   a.FINISH_QTY,
							   a.DEPT_1,
							   getOptionVal('area', a.DEPT_1, 'zh-tw') dept1_desc,
							   a.DEPT_2,
							   a.CUST_CODE,
							   b.cus_name,
							   a.FREIGHT_ID,
							   getOptionVal('freight_id', a.FREIGHT_ID, 'zh-tw') freight_desc,
							   a.PIN_CODE,
							   getOptionVal('pin_code', a.PIN_CODE, 'zh-tw') pin_desc,
							   to_char(a.PLAN_FINISH,'yyyy-mm-dd') PLAN_FINISH,
							   to_char(a.ACTU_FINISH,'yyyy-mm-dd') ACTU_FINISH,
							   a.SO_STATUS,
							   to_char(a.CREA_DATE,'yyyy-mm-dd') CREA_DATE,
							   a.REMARK,
							   getUser(a.USER_ID, 'USER_TITLE') USER_ID,
							   a.UPDATE_TIME
						  from emesp.tp_so a, emesp.tp_cus_info b
						 where a.cust_code = b.cus_code) x $where";// echo $sql;
		return $sql;
	}
	public function getDetailSql(){
		$so_number = request()->param('so_number');
		$so_type = request()->param('so_type');
		$sql = "select ln_code,
					   model_name,
					   cust_model,
					   target_qty,
					   dept_1,
					   getoptionval('area', dept_1, 'zh-tw') dept1_desc,
					   freight_id,
					   getoptionval('freight_id', freight_id, 'zh-tw') freight_desc,
					   pin_code,
					   getoptionval('pin_code', pin_code, 'zh-tw') pin_desc,
					   to_char(prom_deli_date, 'yyyy-mm-dd') prom_deli_date,
					   to_char(prom_ship_date, 'yyyy-mm-dd') prom_ship_date,
					   remark
				  from emesp.tp_so_sub
				 where so_number = '$so_number'
				   and so_type = '$so_type'
				   and so_status <> -1";
		 return $sql;
	}
}
?>