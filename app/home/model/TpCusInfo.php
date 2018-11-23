<?php
namespace app\home\model;
use think\Model;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
// 角色模型
class TpCusInfo extends Model {  
	function getSql(){
		$table = '';
		$whereStr = '';
		$condition = '';
        $emp_id=cookie(config('USER_AUTH_KEY'));//dump(1);
		$emp           =  cookie('loginAccount');
         if(request()->param('searchKeyWords')){
            $keyWords = strtoupper(request()->param('searchKeyWords'));
            $whereStr = " and ( upper(a.cus_code) like '%$keyWords%' 
                                or upper(a.cus_name) like '%$keyWords%'
                                or upper(a.cus_name) like '%$keyWords%'
                                or upper(a.country) like '%$keyWords%')";
		} 
        if(request()->param('searchC')){
            $searchCWords=request()->param('searchCWords');
            $searchC=request()->param('searchC'); 
            $table=',emesp.tp_cg_cus c,emesp.tp_cg_cus_sub d';
            $condition="and c.id=d.pid and d.option_val=a.".$searchC." and d.option_name like '%".$searchCWords."%' and c.column_name='".$searchC."'";
        } 
        $sql="select table1.id,
                     table1.cus_code,
                     table1.cus_full_name,
                     table2.option_name   cus_busi_desc,
                     table1.cus_busi,
                     table1.cus_name,
                     table1.country,
                     table1.cus_address,
                     table3.option_name   cus_trade_desc,
                     table1.cus_trade,
                     table4.option_name   cus_nature_desc,
                     table1.cus_nature,
                     table5.option_name   cus_type_desc,
                     table1.cus_type,
                     table1.staff_num,
                     table1.money,
                     table6.option_name   order_type_desc,
                     table1.order_type,
                     table1.cus_logo,
                     table1.website,
                     table1.production,
                     table1.tel,
                     table1.fax,
                     table1.describe,
                     table1.chronicle,
                     table1.status,
                     table7.option_name   cus_maste_desc,
                     table1.cus_maste,
                     table1.trade_terms,
                     table1.payment
                from (select a.id,
                             a.cus_code, 
                             a.cus_full_name,
                             a.cus_busi,
                             a.cus_name,
                             a.country,
                             a.cus_address,
                             a.cus_trade,
                             a.cus_nature,
                             a.cus_type,
                             a.staff_num,
                             a.money,
                             a.order_type,
                             a.cus_logo,
                             a.website,
                             a.production,
                             a.tel,
                             a.fax,
                             a.describe,
                             a.chronicle,
                             a.status,
                             a.cus_maste,
                             a.trade_terms,
                             a.payment
                        from emesp.tp_cus_info a  $table
                        where a.status = 1   
                          $whereStr $condition) table1,
                     (select t.table_name, t.column_name, t1.option_name, t1.option_val
                        from emesp.tp_cg_cus t, emesp.tp_cg_cus_sub t1
                        where t.id = t1.pid
                          and t.table_name = 'TpCusInfo'
                          and t.column_name = 'CUS_BUSI') table2,
                     (select t.table_name, t.column_name, t1.option_name, t1.option_val
                        from emesp.tp_cg_cus t, emesp.tp_cg_cus_sub t1
                       where t.id = t1.pid
                        and t.table_name = 'TpCusInfo'
                        and t.column_name = 'CUS_TRADE') table3,
                     (select t.table_name, t.column_name, t1.option_name, t1.option_val
                        from emesp.tp_cg_cus t, emesp.tp_cg_cus_sub t1
                        where t.id = t1.pid
                          and t.table_name = 'TpCusInfo'
                          and t.column_name = 'CUS_NATURE') table4,
                     (select t.table_name, t.column_name, t1.option_name, t1.option_val
                        from emesp.tp_cg_cus t, emesp.tp_cg_cus_sub t1
                        where t.id = t1.pid
                          and t.table_name = 'TpCusInfo'
                          and t.column_name = 'CUS_TYPE') table5,
                     (select t.table_name, t.column_name, t1.option_name, t1.option_val
                        from emesp.tp_cg_cus t, emesp.tp_cg_cus_sub t1
                        where t.id = t1.pid
                          and t.table_name = 'TpCusInfo'
                          and t.column_name = 'ORDER_TYPE') table6,
                     (select t.table_name, t.column_name, t1.option_name, t1.option_val
                        from emesp.tp_cg_cus t, emesp.tp_cg_cus_sub t1
                        where t.id = t1.pid
                          and t.table_name = 'TpCusInfo'
                          and t.column_name = 'CUS_MASTE') table7
               where table1.cus_busi = table2.option_val
                 and table1.cus_trade = table3.option_val(+)
                 and table1.cus_nature = table4.option_val(+)
                 and table1.cus_type = table5.option_val(+)
                 and table1.order_type = table6.option_val(+)
                 and table1.cus_maste = table7.option_val(+) ";    // echo $sql;die();
		return $sql;
	}
}
?>