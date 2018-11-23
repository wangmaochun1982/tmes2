<?php
namespace app\home\model;
use think\Model;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
class TcMtModel  extends Model { 
	public function getTableSql(){
			
        $controller = request()->controller();
		$whereStr = action($controller.'/_filterSql',array('filterRules',false));
        if(!stripos($whereStr,'m_status')){
           // $whereStr .= " and M_STATUS in( 'M','O','0','P')";  lmj 2018-1-16 16:04:45默認顯示生效的
            $whereStr .= " and M_STATUS in ('M','P','0')";//P是採購件
        }
    	if( request()->param('model')!=''){
    		$model = strtoupper(trim(request()->param('model')));
    		$whereStr.=" and (model_serial like '%$model%' or model_name like '%$model%')";
    	}
		if( request()->param('model_name')!=''){
    		$model_name = strtoupper(trim(request()->param('model_name')));
    		$whereStr.=" and model_name = '$model_name'";
    	}
    	if( request()->param('color_beg')!=''){
    		$colorBeg = strtoupper(request()->param('color_beg'));
			$colorEnd = strtoupper(request()->param('color_beg'));
			if($colorEnd==''){
    			$colorEnd=$colorBeg;
    		}
    		$whereStr.=" and substr(model_name,10,2) between '$colorBeg' and '$colorEnd'";
    	}
    	if( request()->param('law')!=''){
    		$law = strtoupper(trim(request()->param('law')));
    		$whereStr.=" and substr(model_name,12,1) ='$law'";
    	}
    	if( request()->param('special')!=''){
    		$special=strtoupper(trim(request()->param('special')));
    		$whereStr.=" and substr(model_name,15,1) ='$special'";
    	}
    	if( request()->param('model_type1')!=''){
    		$modelType1=strtoupper(trim(request()->param('model_type1')));
			if(substr_count($modelType1,',')){
				$modelType1 = str_replace(",","','",$modelType1);
				$whereStr.=" and model_type1 in('$modelType1')";
			}else{
				$whereStr.=" and model_type1 = '$modelType1'";
			}
    	}
		if( request()->param('m_status')!=''){
    		$mStatus=strtoupper(trim(request()->param('m_status')));
			if(substr_count($mStatus,',')){
				$mStatus = str_replace(",","','",$mStatus);
				$whereStr.=" and m_status in('$mStatus')";
			}else{
				$whereStr.=" and m_status = '$mStatus'";
			}
    	}
		$ftable = '';
		if(request()->param('useType')=='comboselector'|| request()->param('useType')=='combosltor'){
			$modName = request()->param('mod_name');
			$iptName = request()->param('input_name');
			$userId = cookie(config('USER_AUTH_KEY'));
			$pcAddr = request()->ip();
			$ftable = ",(select valuefield
											  from emesc.tc_selector
											 where module_name = '$modName'
											   and user_id = $userId
											   and pc_addr = '$pcAddr'
											   and input_name = '$iptName'
											   and flag>0) f";
			$whereStr .= " and a.model_name = f.valuefield(+) and f.valuefield is null";
		} //echo $whereStr;
        if(!empty(request()->param('q'))){
       		$q = request()->param('q');
        	$whereStr.=" and (model_serial like '%$q%' or model_name like '%$q%')";
        }
		$sql ="SELECT a.MODEL_NAME,
						a.MODEL_SERIAL,
						a.MODEL_NO,
						a.MODEL_DESC,
						a.SEMI_PRODUCT,
						a.M_FLAG,
						a.MODEL_TYPE1,
						getoptionval('model_type1',a.model_type1,'".request()->langset()."') model_type_name1,
						a.MODEL_TYPE2,
						a.MODEL_TYPE3,
						a.CUST_CODE,
						cus.cus_name ,
						a.CUST_MODEL,
						a.CUST_DESC,
						getoptionval('pack_type',a.pack_type,'".request()->langset()."') PACK_TYPE,
						a.CUST_1,
						a.CUST_2,
						a.CUST_3,
						a.EAN_CODE,
						b.route_desc,
						a.route_code,
						a.SN_RULE,
						a.UNIT,
						a.ERP_ID,
						a.M_1,
						a.M_2,
						a.M_3,a.m_size,
						a.r_type,
						getoptionval('r_type',a.r_type,'".request()->langset()."') R_TYPE_name,
						a.M_STATUS,
						getoptionval('m_status',a.M_STATUS,'".request()->langset()."') status_name,
						a.REMARK,
						a.USER_ID,
						a.UPDATE_TIME,
						a.ID
		 FROM EMESC.TC_MT_MODEL a, EMESC.TC_ROUTE_DESC b,emesp.tp_cus_info cus  $ftable
		 where a.route_code=b.route_code(+)
		 and a.cust_code = cus.cus_code(+)
		 $whereStr"; //echo $sql;die();
		return $this->getSqlInit($sql);
	}
	public function getRole(){
		$userId = cookie(config('USER_AUTH_KEY'));
		//role_id=170是品名說明權限
		$sql="select count(1) qty
		  from emesc.tc_role_user
		 where user_lnk_id = '$userId'
		   and role_id in(1,170)";//echo $sql;die();
		   return $sql;
	}

}
?>