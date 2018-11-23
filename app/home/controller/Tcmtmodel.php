<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018 All rights reserved.
// +----------------------------------------------------------------------
// | ActionName:料號管理
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use app\home\model\TcMtModel as Modelname;
class Tcmtmodel extends Base {
	protected $beforeActionList = [
        '_before_edit'  =>  ['only'=>'edit'],
    ];
	public function add(){
		return  view();
	}
	public function _before_edit(Request $request){
		$atFlag = $request->post('atflag');
		if($atFlag){
			$model = new Modelname();
			$sql=$model->getTableSql();
			$rs = Db::connect()->query($sql);
			$this->assign('datas',json_encode($rs));
		}
	}
	public function edit(Request $request){
		
		$name = $request->action();
		$sql = model($name)->getRole();
		$result=Db::connect()->query($sql);//dump($result);
		$this->assign ('result', $result ); 
		return view();
	}
	function uploadFile(){
		return view();
	}
	function XxgetUpCode(){
		$upCode[0]["id"] = "cust_1";
		$upCode[0]["text"] = "電子配件文檔";
		$upCode[1]["id"] = "m_size";
		$upCode[1]["text"] = "Size";
		$upCode[2]["id"] = "m_1";
		$upCode[2]["text"] = "色漆";
		$upCode[3]["id"] = "cust_2,m_size";
		$upCode[3]["text"] = "SN規則+Size";
		return  json_encode($upCode);
	}
    public function XxgetTableJson(Request $request) {
        $model=model('TcMtModel');
		$sql=$model->getTableSql();//dump($sql);die();
		$userId = $this->getUserId();
		$ip = $request->ip();;
	
			S('sql_'.$userId.'_'.$ip,$sql,3600);
	
		return $this->_listSql($sql); //echo $sql;
	}
	public function XxInsert(Request $request){
		$Mod=Db::connect();
		$pubParam['i_model_name']= strtoupper(TRIM($request->post('model_name')));
		$pubParam['i_m_flag']= strtoupper(TRIM($request->post('m_flag')));
		$pubParam['i_model_serial']= strtoupper(TRIM($request->post('model_serial')));
		$pubParam['i_model_no']= strtoupper(TRIM($request->post('model_no')));
		$pubParam['i_model_desc']= TRIM($request->post('model_desc'));
		$pubParam['i_semi_product']=  strtoupper(TRIM($request->post('semi_product')));
		$pubParam['i_model_type1']= $request->post('model_type1');
		$pubParam['i_model_type2']= null;
		$pubParam['i_model_type3']= TRIM($request->post('cust_code3'));
		$pubParam['i_cust_code']= TRIM($request->post('cust_code'));
		$pubParam['i_cust_model']= strtoupper(TRIM($request->post('cust_model')));
		$pubParam['i_cust_desc']= TRIM($request->post('cust_desc'));
		$pubParam['i_pack_type']= $request->post('pack_type');
		$pubParam['i_cust_1']= TRIM($request->post('cust_1'));
		$pubParam['i_cust_2']= TRIM($request->post('cust_2'));
		$pubParam['i_cust_3']= null;
		$pubParam['i_ean_code']= TRIM($request->post('ean_code'));;
		$pubParam['i_route_code']= TRIM($request->post('route_code'));
		$pubParam['i_sn_rule']= null;
		$pubParam['i_unit']=  TRIM($request->post('unit'));
		$pubParam['i_erp_id']= TRIM($request->post('erp_id'));
		$pubParam['i_m_1']= TRIM($request->post('m_1'));
		$pubParam['i_m_2']= TRIM($request->post('m_2'));
		$pubParam['i_m_3']= null;
		$pubParam['i_m_size']= TRIM($request->post('m_size'));
		$pubParam['i_r_type']= TRIM($request->post('r_type'));
		$pubParam['i_m_status']='M';// TRIM($request->post('m_status']);
		//$pubParam['i_m_status']= TRIM($request->post('m_status']);
		$pubParam['i_remark']= TRIM($request->post('remark'));
		$pubParam['i_user_id']= $this->getUserId();;
		$pubParam['i_area']= null;
		$pubName = "PD_MODEL_INS";//存儲過程名字
		
		$result = $Mod->execProcedure($pubName,$pubParam);
		$this->resReturn($result);
	}
    //從ERP導入工單到MES
	public function XxErp2MesAdd(Request $request){
		$erpmodel="'".str_replace(',',"','",$request->post('erpmodel'))."'";
		$arr=[];
		if(empty($erpmodel)){
            return $this->ajaxReturn($arr,"您未輸入任何內容!",0);
		}
		$model = Db::connect();
		$pubParam['v_model']=$erpmodel;
		$pubName = "PD_ERP2MES_Model_AF";//存儲過程名字
		$result = $model->execProcedure($pubName,$pubParam);//dump($result);
	    return $this->ajaxReturn($result,"OK!",1);
	}

	public function Xxdelete() {
		$this->_foreverdelete('TcMtModel');
	}

	public function XxUpdate(){
		$Mod=Db::connect();
		$pubParam['i_model_name']= strtoupper(TRIM($request->post('model_name')))

;
		$pubParam['i_m_flag']= strtoupper(TRIM($request->post('m_flag')))

;
		$pubParam['i_model_serial']= strtoupper(TRIM($request->post('model_serial')))

;
		$pubParam['i_model_no']= strtoupper(TRIM($request->post('model_no')))

;
		$pubParam['i_model_desc']= TRIM($request->post('model_desc'));
		$pubParam['i_semi_product']=  strtoupper(TRIM($request->post('semi_product')))

;
		$pubParam['i_model_type1']= TRIM($request->post('model_type1'));
		$pubParam['i_model_type2']= TRIM($request->post('model_type2'));
		//$pubParam['i_model_type3']= TRIM($request->('model_type3'));
		$pubParam['i_cust_code']= TRIM($request->post('cust_code'));
		$pubParam['i_cust_model']= strtoupper(TRIM($request->post('cust_model')))

;
		$pubParam['i_cust_desc']= TRIM($request->post('cust_desc'));
		$pubParam['i_pack_type']= TRIM($request->post('pack_type'));
		$pubParam['i_cust_1']= TRIM($request->post('cust_1'));
		$pubParam['i_cust_2']= TRIM($request->post('cust_2'));
		$pubParam['i_cust_3']= TRIM($request->post('cust_3'));
		$pubParam['i_ean_code']= TRIM($request->post('ean_code'));;
		$pubParam['i_route_code']= TRIM($request->post('route_code'));
		$pubParam['i_model_type3']= TRIM($request->post('model_type3'));
		$pubParam['i_sn_rule']= $request->post('sn_rule');
		$pubParam['i_unit']=  TRIM($request->post('unit'));
		$pubParam['i_erp_id']= TRIM($request->post('erp_id'));
		$pubParam['i_m_1']= TRIM($request->post('m_1'));
		$pubParam['i_m_2']= TRIM($request->post('m_2'));
		$pubParam['i_m_3']= TRIM($request->post('m_3'));
		$pubParam['i_m_size']= TRIM($request->post('m_size'));
		$pubParam['i_r_type']= TRIM($request->post('r_type'));
		$pubParam['i_m_status']= TRIM($request->post('m_status'));
		$pubParam['i_remark']= TRIM($request->post('remark'));
		$pubParam['i_user_id']= $this->getUserId();;
		$pubParam['i_area']= TRIM($request->post('area'));
		$pubName = "PD_MODEL_UPD";//存儲過程名字
		$result = $Mod->execProcedure($pubName,$pubParam);
		return $this->ajaxReturn($result,"OK!",1);
	}
	public function XxcheckRoute(Request $request){
		$v_route=$request->param('v_route');
		//$this->ajaxReturn($_POST,$v_route,1);
		$sql="SELECT B.GROUP_DESC FIRST_GROUP ,C.GROUP_DESC END_GROUP
			  FROM EMESC.TC_ROUTE_DESC    A,
				   EMESC.TC_WS_GROUP_DESC B,
				   EMESC.TC_WS_GROUP_DESC C
			 WHERE A.ROUTE_CODE = $v_route
			 AND A.FIRST_GROUP_CODE=B.GROUP_CODE
			 AND A.END_GROUP_CODE=C.GROUP_CODE";
		$this->_listSql($sql,"*","FIRST_GROUP");
	}
	//料號選擇公用model
	function XxgetJsonList(Request $request){
		//$q =  isset($request->post('q')) ? $request->post('q') : '' ;
		if(!empty($request->post('q'))){
			$q = $request->post('q');
		}else{
			$q = '';
		} 
		$tp = $request->param('tp');
		$model = strtoupper($request->param('model'));
		if($q) $whereStr = " and (a.model_name like '%$q%' or a.model_serial like '%$q%')";
		if($model) $whereStr .= " and (a.model_name like '%$model%' or a.model_serial like '%$model%')";
		if($tp==1) $whereStr .= " and a.model_type1 = 1";
		elseif($tp==2) $whereStr .= " and a.model_type1 = 2";
		elseif($tp==3) $whereStr .= " and a.model_type1 = 3";
		elseif($tp==4) $whereStr .= " and a.model_type1 = 4";
		$fiter = $this->_filterSql('filterRules',true);
		$sql = "select * from (
		        select a.model_name, a.model_serial, a.cust_model, a.cust_desc, a.ean_code,b.model_no modelno, b.model_size,b.shape_rev,a.model_name id
				  from emesc.tc_mt_model a,emesc.tc_mt_model_shape_kt b
				 where a.m_status = 'M'
				   and a.model_name = b.model_name(+)
				 $whereStr)
				$fiter";
		$this->_listSql($sql,"*","model_serial");//dump($sql);
	}
	
	public function XxexportData(){
		$xlsName  = "料號";
        $xlsCell  = array(
            array('model_name','料號'),
			array('m_flag','塗裝別'),
			array('model_serial','型號開發別'),
			array('model_no','型號別'),
			array('semi_product','半成品'),
			array('model_type_name1','類別'),
			array('r_type_name','回報狀態'),
			array('cust_code','客戶'),
			array('cust_model','客戶品號'),
			array('cust_desc','品名'),
			array('ean_code','商品碼'),
			array('cust_1','電子配件名'),
			array('cust_2','客供標型號別'),
			array('m_1','色漆'),
			array('m_2','面漆'),
			array('m_size','Size'),
			array('route_desc','路由'),
			array('sn_rule','SN產生規則'),
			array('unit','單位'),
			array('pack_type','包裝方式'),
			array('status_name','狀態')
        );
		$userId = $this->getUserId();
		$ip = request()->ip();
		$sql = S(MODULE_NAME.'/sql_'.$userId.'_'.$ip);
		$xlsData = Db::name($name)->query($sql);
		$this -> delCacheFile(); // -----------刪除緩存文件
        $this->Export_Excel($xlsName,$xlsCell,$xlsData);
	}
}
?>