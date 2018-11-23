<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use app\home\model\TpSo as so;
class TpSo extends Base {
	public function XxGetTableJson(Request $request){
			
		//halt(json_decode(stripslashes($this->request->param('filterRules')),true));
		$so = new so();
		$sql = $so->getSql();
		
		//echo $sql;die();
       return ($this->_listSql($sql));
    }
	public function add(Request $request){
		$controller = $request->controller();  
        $cacheName = $controller.'/AddItem_'.$this->getUserId().'_'.$request->ip();
		//$cache = S($cacheName,null); 
		return view();
	}
	public function edit(Request $request){
		
		return view();
	}
	public function XxwkItem(){
		return view('wkItem');
	}
	public function XxInsCache(Request $request){ 
        $cacheName = $request->controller().'/AddItem_'.$this->getUserId().'_'.$request->ip();
		$cache = S($cacheName); 
		$idx = $request->param('index');
		$flag =$request->param('flag');
		//echo $idx.'-'.$flag;die();
		if($flag=='D'){//刪除項目時，緩存數組移除 
			array_splice($cache['ln_code'],$idx,1);
			array_splice($cache['model_name'],$idx,1);
			array_splice($cache['cust_model'],$idx,1);
			array_splice($cache['target_qty'],$idx,1);
			array_splice($cache['dept_1'],$idx,1);
			array_splice($cache['freight_id'],$idx,1);
			array_splice($cache['pin_code'],$idx,1);
			array_splice($cache['prom_deli_date'],$idx,1);
			array_splice($cache['prom_ship_date'],$idx,1);
			array_splice($cache['remark'],$idx,1);
		}else if($flag=='U'){//修改項目時，修改緩存數組 
			array_splice($cache['ln_code'],$idx,1,$request->param('ln_code'));
			array_splice($cache['model_name'],$idx,1,$request->param('model_name'));
			array_splice($cache['cust_model'],$idx,1,$request->param('cust_model'));
			array_splice($cache['target_qty'],$idx,1,$request->param('target_qty'));
			array_splice($cache['dept_1'],$idx,1,$request->param('dept_1'));
			array_splice($cache['freight_id'],$idx,1,$request->param('freight_id'));
			array_splice($cache['pin_code'],$idx,1,$request->param('pin_code'));
			array_splice($cache['prom_deli_date'],$idx,1,$request->param('prom_deli_date'));
			array_splice($cache['prom_ship_date'],$idx,1,$request->param('prom_ship_date'));
			array_splice($cache['remark'],$idx,1,$request->param('remark'));
		}else{ 
			$cache['ln_code'][$idx] = $request->param('ln_code');
			$cache['model_name'][$idx] = $request->param('model_name');
			$cache['cust_model'][$idx] = $request->param('cust_model');
			$cache['target_qty'][$idx] = $request->param('target_qty');
			$cache['dept_1'][$idx] = $request->param('dept_1');
			$cache['freight_id'][$idx] = $request->param('freight_id');
			$cache['pin_code'][$idx] = $request->param('pin_code');
			$cache['prom_deli_date'][$idx] = $request->param('prom_deli_date');
			$cache['prom_ship_date'][$idx] = $request->param('prom_ship_date');
			$cache['remark'][$idx] = $request->param('remark');
		}
		//print_r($cache);die();
		S($cacheName,null); 
		S($cacheName,$cache);
		$arr = [];
        return $this->ajaxReturn($arr,'OK!',1);  
	}
	public function XxInsert(Request $request){
		$name = $request->action(); 
		$userId = $this->getUserId();
		$ip = $request->ip(); 
		$controller = $request->controller();
        $cacheName = $controller.'/AddItem_'.$userId.'_'.$ip; 
		//$rows = S($cacheName);
		//print_r($rows);die();
		$pubParam['i_so_number'] = $request->param('so_number'); 
		$pubParam['i_so_type'] = $request->param('so_type');
		$pubParam['i_po_no'] = $request->param('po_no');
		$pubParam['i_dept_1'] =  $request->param('dept_1');
		$pubParam['i_dept_2'] =  $request->param('dept_2');
		$pubParam['i_cust_code'] = $request->param('cust_code');
		$pubParam['i_freight_id'] = $request->param('freight_id');
		$pubParam['i_pin_code'] = $request->param('pin_code');
		$pubParam['i_plan_finish'] = $request->param('plan_finish');
		$pubParam['i_remark'] = $request->param('remark');
		$pubParam['i_ln_code'] = $rows['ln_code'];
		$pubParam['i_model_name'] = $rows['model_name'];
		$pubParam['i_cust_model'] = $rows['cust_model']; 
		$pubParam['i_starget_qty'] = $rows['target_qty']; 
		$pubParam['i_sdept1'] = $rows['dept_1'];
		$pubParam['i_sdept2'] = '';
		$pubParam['i_sfreight_id'] = $rows['freight_id'];
		$pubParam['i_spin_code'] = $rows['pin_code']; 
		$pubParam['i_prom_deli_date'] = $rows['prom_deli_date']; 
		$pubParam['i_prom_ship_date'] = $rows['prom_ship_date'];
		$pubParam['i_sremark'] = $rows['remark'];
		$pubParam['i_user_id'] = $this->getUserId();
		$pubName = "pd_so_bat_ins";
        $model = Db::connect();
		//print_r($pubParam);die();
		$result = $model->execProcedure($pubName,$pubParam); 
		return $this->ajaxReturn($result);
	}
	public function XxGetDetailData(Request $request){ 
        $so = new so();  
        $cacheName = $request->controller().'/AddItem_'.$this->getUserId().'_'.$request->ip();
		$sql = $so->getDetailSql(); 
		$voList = $so->query($sql); 
		$i=0;
		foreach($voList as $key=>$val){ 
			$cache['ln_code'][$i] = $val['ln_code'];
			$cache['model_name'][$i] = $val['model_name'];
			$cache['cust_model'][$i] = $val['cust_model'];
			$cache['target_qty'][$i] = $val['target_qty'];
			$cache['dept_1'][$i] = $val['dept_1'];
			$cache['freight_id'][$i] = $val['freight_id'];
			$cache['pin_code'][$i] = $val['pin_code'];
			$cache['prom_deli_date'][$i] = $val['prom_deli_date'];
			$cache['prom_ship_date'][$i] = $val['prom_ship_date'];
			$cache['remark'][$i] = $val['remark'];
			$i++;
		}
		//S($cacheName,$cache);
        if(empty($voList)){ $voList = array();}
		$count = count($voList);
        $list=json_encode($voList);//Json??
        if(empty($total)){ $total = array();}
        $footList = json_encode($total);
        if(!$count){$count=0;}
        $result = '{"total":'.$count.',"rows":' .$list. ',"footer":['.$footList.']}';
        echo ($result); //?出json?据   
		//echo $sql;
    } 
	public function XxUpdate(Request $request){		
		//跳轉頁面，變量傳入
		$name = $request->action(); 
		$controller = $request->controller();
		$userId = $this->getUserId();
		$ip = $request->ip(); 
        $cacheName = $controller.'/AddItem_'.$userId.'_'.$ip; 
		$rows = S($cacheName);
		$pubParam['i_so_number'] = $request->param('so_number');
		$pubParam['i_so_type'] = $request->param('so_type');
		$pubParam['i_po_no'] = $request->param('po_no');
		$pubParam['i_dept_1'] =  $request->param('dept_1');
		$pubParam['i_dept_2'] =  $request->param('dept_2');
		$pubParam['i_cust_code'] = $request->param('cust_code');
		$pubParam['i_freight_id'] = $request->param('freight_id');
		$pubParam['i_pin_code'] = $request->param('pin_code');
		$pubParam['i_plan_finish'] = $request->param('plan_finish');
		$pubParam['i_remark'] = $request->param('remark');
		$pubParam['i_ln_code'] = $rows['ln_code'];
		$pubParam['i_model_name'] = $rows['model_name'];
		$pubParam['i_cust_model'] = $rows['cust_model']; 
		$pubParam['i_starget_qty'] = $rows['target_qty']; 
		$pubParam['i_sdept1'] = $rows['dept_1'];
		$pubParam['i_sdept2'] = '';
		$pubParam['i_sfreight_id'] = $rows['freight_id'];
		$pubParam['i_spin_code'] = $rows['pin_code']; 
		$pubParam['i_prom_deli_date'] = $rows['prom_deli_date']; 
		$pubParam['i_prom_ship_date'] = $rows['prom_ship_date'];
		$pubParam['i_sremark'] = $rows['remark'];
		$pubParam['i_user_id'] = $this->getUserId();
		$pubName = "pd_so_bat_UPD";
        $model = Db::connect();
		$result = $model->execProcedure($pubName,$pubParam); 
		$this->resReturn($result);
	}
}
?>