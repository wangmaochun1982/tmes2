<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use app\home\model\TpCusInfo as info;
// 后台用户模块
class Tpcusinfo extends Base {
    function XxgetsearchC(){
        $result = array();
		$field = 'column_name id,column_desc text';
        $model = Db::name ('emesp.TpCgCus');
		$map="status=1";
        $count = $model->where($map)->field($field)->count('column_name');
		$list = $model->where($map)->field($field)->order("id asc")->select();
		return   json($list);
    }
    //共用模塊查詢客戶信息
    public function XxgetCusTableJson(){
		$emp_id=$this->getUserId();
		if($this->request->has('q')){
			$q = $this->request->post('q');
		}else{
			$q = '';
		} 
		if($q) $condition = "and  (a.cus_code like '%$q%' or a.cus_name like '%$q%' or a.cus_full_name like '%$q%')";
        $sql = "select a.cus_code, a.cus_name, a.cus_full_name
				  from emesp.tp_cus_info a 
				 where a.status=1   
                 $condition ";  //echo $sql;
		return $this->_listSql($sql);
    }
    //查詢
    public function XxgetTableJson(){
		$info = new info();
		$sql = $info->getSql(); 
        return  $this->_listSql($sql);
		//halt($t);
    }
    //插入數據
    public function Xxinsert(){ 
        $uploadfile    =$_FILES['cus_logo'];
        $src           =  $request->param('src');
        $organization  =  substr($src,-14);
		$cusBusi       =  $request->param('cus_busi');
        $cusCode       =  $request->param('cus_code');
        $cusFullName   =  $request->param('cus_full_name');
        $cusName       =  $request->param('cus_name');
        $country       =  $request->param('country');
        $cusAddress    =  $request->param('cus_address');
        $cusTrade      =  $request->param('cus_trade');
        $cusNature     =  $request->param('cus_nature');
        $cusType       =  $request->param('cus_type');
        $staffNum      =  $request->param('staff_num');
        $money         =  $request->param('money');
        $orderType     =  $request->param('order_type');
        $cusLogo       =  $_FILES['cus_logo']['name'];
        $website       =  $request->param('website');
        $production    =  $request->param('production');
        $tel           =  $request->param('tel');
        $fax           =  $request->param('fax');
        $describe      =  $request->param('describe');
        $chronicle     =  $request->param('chronicle');
        $status        =  $request->param('status');
       	$cusMaste      =  $request->param('cus_maste');
        $tradeTerms    =  $request->param('trade_terms');
        $payment    =  $request->param('payment');
        $emp           =  $this->getUserAccount();
        $emp_id=$this->getUserId();
		$chronicle     =  str_replace("\n","",$chronicle);
		$chronicle     =  str_replace("<br />","",$chronicle);
        //dump($cusLogo);
        if($cusLogo){
            $cusLogo=$this->uploadLogo();
        }
        if($organization){
        $this->doCropPhoto($organization);
        }
		$arr = '';
        if($emp_id){
		$model = Db::connect();
		$pubParm = array($cusBusi,$cusFullName,$cusName,$country,$cusAddress,$cusTrade,$cusNature,$cusType,$staffNum,
        $money,$orderType,$cusLogo,$website,$production,$tel,$fax,$describe,$chronicle,$status,$cusMaste,$emp_id,$organization,$tradeTerms,$payment,$cusCode,false);
		$pubName = "PD_CUS_INFO_INS_KT";//存儲過程名字 
		$result = $model->execpub($pubName,$pubParm); 
		$res=$result['res']; 
        $newCusCode=$result['i_cus_code'];  
		if($res== 'OK'){
            return $this->ajaxReturn($arr,'成功-'.$newCusCode,1);
        }else{
            return $this->ajaxReturn($arr,'失敗-'.lang($res),0);
        }
        }else{
           return $this->ajaxReturn('','賬號失效，請重新登錄！',0);  
        }
	}
    //更新数据
    public function Xxupdate() {
        $src           =  $request->param('src');
        $organization  =  substr($src,-14);
        $id            =  $request->param('id');
        $cusBusi       =  $request->param('cus_busi');
		$cusCode       =  $request->param('cus_code');
        $cusFullName   =  $request->param('cus_full_name');
        $cusName       =  $request->param('cus_name');
        $country       =  $request->param('country');
        $cusAddress    =  $request->param('cus_address');
        $cusTrade      =  $request->param('cus_trade');
        $cusNature     =  $request->param('cus_nature');
        $cusType       =  $request->param('cus_type');
        $staffNum      =  $request->param('staff_num');
        $money         =  $request->param('money');
        $orderType     =  $request->param('order_type');
        $cusLogo       =  $request->param('cus_logo');
        $website       =  $request->param('website');
        $production    =  $request->param('production');
        $tel           =  $request->param('tel');
        $fax           =  $request->param('fax');
        $describe      =  $request->param('describe');
        $chronicle     =  $request->param('chronicle');//dump($chronicle);
        $status        =  $request->param('status');
        $cusMaste      =  $request->param('cus_maste');
        $tradeTerms    =  $request->param('trade_terms');
        $payment       =  $request->param('payment');
        $emp           =  $this->getUserAccount();
        $emp_id=$this->getUserId();
		$chronicle     =  str_replace("\n","",$chronicle);
		$chronicle     =  str_replace("<br />","",$chronicle);
        $cusLogoModify       =  $_FILES['cus_logo_modify']['name'];//dump($cusLogoModify);
        if($cusLogoModify){
            $cusLogo=$this->uploadLogo();//dump($cusLogo);
        }else{
           $cusLogo='';   
        }
        if($src){
        $this->doCropPhoto($organization);
        }else{
        $organization='';  
        }
        if ($emp_id){
		$model = Db::connect(); 
		$pubParm = array($id,$cusBusi,$cusCode,$cusFullName,$cusName,$country,$cusAddress,$cusTrade,$cusNature,$cusType,$staffNum,
        $money,$orderType,$cusLogo,$website,$production,$tel,$fax,$describe,$chronicle,$status,$cusMaste,$organization,$tradeTerms,$payment,$emp_id,false);
		$pubName = "PD_CUS_INFO_UPD_KT";//存儲過程名字
		$result = $model->execpub($pubName,$pubParm); 
		$res=$result['res'];
		$arr = '';
		if($res== 'OK'){
            return $this->ajaxReturn($arr,'成功-'.$cusCode,1);
        }else{
            return $this->ajaxReturn($arr,'失敗-'.L($res),0);
        }
        }else{
            return $this->ajaxReturn($arr,'賬號失效，請重新登錄！',0);
        }
	}
    //删除数据 
    public function delete() {
			$arr = '';
		//删除指定记录 
		$model  =	 Db::name("Emesp.TpCusInfo");
        $this->_checkStatus($model);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $request->param($pk);
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					return $this->ajaxReturn($arr,'彻底删除数据成功！',1);
				} else {
					return $this->ajaxReturn($model->getError(),'數據删除失敗',0);
				}
			} else {
				return $this->ajaxReturn($_POST,'非法操作！',3);
			}
		}
		$this->forward ();
	}
	//查詢
	function XxgetCusTrade(){
        $result = array();
		$field = 'option_val id,option_name text,ord';
        $model = Db::name ('emesp.TpCgCusSub');
		$map="pid=1 and status=1";
        $count = $model->where($map)->field($field)->count('option_val');
		$list = $model->where($map)->field($field)->order("ord asc")->select();
		return   json($list);
    }
	//查詢
	function XxgetCusNature(){
        $result = array();
		$field = 'option_val id,option_name text,ord';
        $model = Db::name ('emesp.TpCgCusSub');
		$map="pid=2 and status=1";
        $count = $model->where($map)->field($field)->count('option_val');
		$list = $model->where($map)->field($field)->order("ord asc")->select();
		return   json($list);
    }
	//查詢
	function XxgetCusType(){
        $result = array();
		$field = 'option_val id,option_name text,ord';
        $model = Db::name ('emesp.TpCgCusSub');
		$map="pid=3 and status=1";
        $count = $model->where($map)->field($field)->count('option_val');
		$list = $model->where($map)->field($field)->order("ord asc")->select();
		echo  json_encode($list);
    }//查詢
	function XxgetOrderType(){
        $result = array();
		$field = 'option_val id,option_name text,ord';
        $model = Db::name ('emesp.TpCgCusSub');
		$map="pid=4 and status=1";
        $count = $model->where($map)->field($field)->count('option_val');
		$list = $model->where($map)->field($field)->order("ord asc")->select();
		return   json($list);
    } 
    //查詢事業部別
    function XxgetCusBusi(){
        $result = array();
		$field = 'option_val id,option_name text,ord';
        $model = Db::name ('emesp.TpCgCusSub');
		$map="pid=8 and status=1";
        $count = $model->where($map)->field($field)->count('option_val');
		$list = $model->where($map)->field($field)->order("ord asc")->select();
        //echo $model->getLastSql();
		return   json($list);
    }
    //查詢客戶別代碼
    function XxgetCusCode(){
        $result = array();
        $remark=$request->param('remark');
		$field = 'cus_code id,cus_name text';
        $model = Db::name ('emesp.TpCusInfo');
		$map="status=1 and cus_busi='$remark'";
        $count = $model->where($map)->field($field)->count('cus_code');
		$list = $model->where($map)->field($field)->order("cus_code asc")->select();
        //echo $model->getLastSql();
		return   json($list);
    }
    //查詢客戶優先級
    function XxgetCusMaste(){
        $result = array();
		$field = 'option_val id,option_name text,ord';
        $model = Db::name ('emesp.TpCgCusSub');
		$map="pid=7";
        $count = $model->where($map)->field($field)->count('option_val');
		$list = $model->where($map)->field($field)->order("ord asc")->select();
        //echo $model->getLastSql();
		return   json($list);
    }
   //查詢客戶貿易術語
   function XxgetTradeTerms(){
        $result = array();
		$field = 'option_val id,option_name text,ord';
        $model = Db::name ('emesp.TpCgCusSub');
		$map="pid=10";
        $count = $model->where($map)->field($field)->count('option_val');
		$list = $model->where($map)->field($field)->order("ord asc")->select();
        //echo $model->getLastSql();
		return   json($list);
   }
   //查詢客戶收款方式
   function XxgetPayment(){
        $result = array();
		$field = 'option_val id,option_name text,ord';
        $model = Db::name ('emesp.TpCgCusSub');
		$map="pid=11";
        $count = $model->where($map)->field($field)->count('option_val');
		$list = $model->where($map)->field($field)->order("ord asc")->select();
        //echo $model->getLastSql();
		return   json($list);
   }
    
    //查看頁面
	//<< 查看頁面2 begin >>
    public function Xxshow(){
        $id=$request->param("id"); //echo $id.'--';die();
        $model=Db::connect();
        $sql="select a.id,
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
                       a.organization,
                       a.trade_terms,
                       a.payment
                  from emesp.tp_cus_info a 
                 where   a.id='$id'";
          $cusList=$model->query($sql);
		  // dump($cusList);
          $this->assign('vo',$cusList);
		  $logo = explode(';',$cusList[0]["cus_logo"]);
		  $num = count($logo);
		  $cusCode = $cusList[0]["cus_code"];
		  // dump($cusList[0]["cus_logo"]);
		  // dump($logo);
		  // dump($num);
		  $this->assign('cusCode',$cusCode);
		  $this->assign('num',$num);
		  $this->assign('logo',$logo);
          $this->display('view');
    }
    //<< 查看頁面2 end >>
    public function XxshowEdit(){
       $id=$request->param("id"); //echo $id.'--';die();
       $model=Db::connect();
        $sql="select a.id,
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
                       a.organization,
                       a.trade_terms,
                       a.payment
                  from emesp.tp_cus_info a 
                 where    a.id='$id'";
        $cusList=$model->query($sql);//dump($cusList);
        $this->assign('vo',$cusList);
		$logo = explode(';',$cusList[0]["cus_logo"]);
		$num = count($logo);
		  // dump($cusList[0]["cus_logo"]);
		  // dump($logo[0]);
		  // dump($num);
		$this->assign('num',$num);
		$this->assign('logo',$logo);
        $this->display('edit');
    }
    
   
	
}
?>