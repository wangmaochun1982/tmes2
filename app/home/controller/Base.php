<?php
namespace app\home\controller;
use think\Controller;
use think\Model;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use lib\Rbac;
class Base extends Controller
{
      protected $request;
	  public function _initialize(){
	  	// 初始化request
	  	 $this->request = Request::instance();
          if (config( 'USER_AUTH_ON' ) && !in_array($this->request->controller(),explode(',',config('NOT_AUTH_MODULE')))) {
		  	 if (! Rbac::AccessDecision ()) {
		  	 	   // 提示错误信息
                /*登錄無權限清除session返回,驗證失敗不清除session*/
                  if(Cookie::has(config('USER_AUTH_KEY')) && !Session::has ('_ACCESS_LIST')) {
                    cookie(null);
                    Cookie::set(config('USER_AUTH_KEY'),null);
                    session(null);
                    session_destroy();
                   }
				//检查认证识别号
                  if(!cookie(config('USER_AUTH_KEY'))){
					if ($this->request->isAjax()){
                        header("HTTP/1.0 901 Not Log");

					} else {
						//跳转到认证网关
						//halt(config( 'USER_AUTH_GATEWAY' ));
						$this->redirect( config( 'USER_AUTH_GATEWAY' ) );
					}
				}else{
					// 没有权限 抛出错误
					if (config( 'RBAC_ERROR_PAGE' )) {
						// 定义权限错误页面
						$this->redirect( config( 'RBAC_ERROR_PAGE' ) );
					}else {
						if (config( 'GUEST_AUTH_ON' )) {
							$this->assign ( 'jumpUrl', config( 'USER_AUTH_GATEWAY' ) );
						}
						if ($this->request->isAjax()){
							//$this->assign("jumpUrl",__URL__.'/login/');
							header("HTTP/1.1 902 Not Right");
							//$this->ajaxReturn(true,L ( '_VALID_ACCESS_' ),302);
							//$this->error( L ( '_VALID_ACCESS_' ) );
						}else{
							echo lang ( '_VALID_ACCESS_' );
						}
					}
				}
			 }
             
		  }
      }
	public function  index($controller = null, $action = null) {
		$controller = 	$controller?$controller:$this->request->controller();
		$action = 	$action?$action:$this->request->action();
		$v_url = '/'.$controller.'/'.$action;
		//halt($v_url);
		$empId =  $this->getUserId()?$this->getUserId():0; /*獲取登錄帳號id*/
		//dump(Session::has(config('ADMIN_AUTH_KEY')));
		if(!Session::has(config('ADMIN_AUTH_KEY')) && $empId!='55') {
			$sql = "select distinct  mlang(a.action_name, '".$this->request->langset()."') langValue, a.icon, a.action_fun,a.act_sort
					  from tc_action a, tc_access b, tc_node c, (select role_id
																  from emesc.tc_role_user
																 where user_lnk_id = $empId
																union
																select brole_id
																  from (select *
																		  from tc_role_lnk
																		 where brole_id not in
																			   (select role_id
																				  from tc_role_user
																				 where user_lnk_id = $empId))
																connect by prior brole_id = frole_id
																 start with frole_id in (select role_id
																						   from tc_role_user
																						  where user_lnk_id = $empId)) d
					 where a.id = b.action_id
					   and b.node_id = c.id
					   and lower(c.node_code) = lower('$controller')
					   and a.status = 1
					   and c.status = 1
					   and a.id not in (22)
					   and b.role_id = d.role_id
					   and b.action_id > 0
				    order by a.act_sort";
		}else{//管理員獲取全部操作權限
			$sql = "select distinct mlang(action_name, '".$this->request->langset()."') langValue,
						   a.icon ,
						   a.action_fun,a.act_sort
					  from tc_action a, tc_node_action b, emesc.tc_node c
					 where a.id = b.action_id
					   and b.node_id = c.id
					   and a.id not in (22)
					  and lower(c.node_code) = lower('$controller')
					   and a.id <> 0
					 order by a.act_sort";
		}//echo $sql;
		$actList = Db::name($action)->query($sql);
		//halt($actList);
        foreach($actList as $key=>$act) {
			/*注釋(如下字符拼接必須換行，否則JS不認識)
				text : action體現的名稱
				iconCls:action體現的圖標
				handler:action調用的函數
			*/
			if(isset($toolBar)){
				$toolBar .= ",{
						text : '".$act['langvalue']."',
						iconCls : 'icon-".$act['icon']."',
						handler : function() {
							".$act['action_fun'].";
						}
					},'-'";
			}else{
				$toolBar = "{
						text : '".$act['langvalue']."',
						iconCls : 'icon-".$act['icon']."',
						handler : function() {
							".$act['action_fun'].";
						}
					},'-'";
			}
		}
		if(empty($toolBar)) $toolBar = "'-'";
		$this->assign('toolBar',$toolBar);
		return view($v_url);
	}
	/**
     +----------------------------------------------------------
	 * 根据easyui过滤器传入生成ThinkPHP查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $rqName easyui datagrid-filter 对象名称
     +----------------------------------------------------------
	 * @return HashMap
     +----------------------------------------------------------
     +----------------------------------------------------------
	 */
	protected function _filterMap($rqName = 'filterRules') {
		$filter = json_decode(stripslashes(Request::post($rqName)),true);//添加反斜杠并将JSON字符串转化为数组
        if(!empty(Request::post($rqName))){
           foreach($filter as $key=>$data){
            $exp = '';
            $op  = $data['op'];
            $val = '';
            if($data['type']=='datebox'){$data['value']  = toUnix($data['value']);}
			if($data['type']=='datebox'){$data['value']  = toUnix($data['value']);}
            if($op!='nofilter' && $data['value']!=''){
                    if($op=='contains'){        $exp = 'like';      $val='%'.$data['value'].'%';}//包含
                elseif($op=='notcontains'){     $exp = 'notlike';   $val=$data['value'];}//不包含
                elseif($op=='equal'){           $exp = 'eq';        $val=$data['value'];}//等于
                elseif($op=='notequal'){        $exp = 'neq';       $val=$data['value'];}//不等于
                elseif($op=='beginwith'){       $exp = 'like';      $val=$data['value'].'%';}//开始
                elseif($op=='endwith'){         $exp = 'like';      $val='%'.$data['value'];}//结束
                elseif($op=='less'){            $exp = 'lt';        $val=$data['value'];}//小于
                elseif($op=='lessorequal'){     $exp = 'elt';       $val=$data['value'];}//小于等于
                elseif($op=='greater'){         $exp = 'gt';        $val=$data['value'];}//大于
                elseif($op=='greaterorequal'){  $exp = 'egt';       $val=$data['value'];}//大于等于
                $map[$data['field']] = array($exp,$val);
            }
         }
      }
		return $map;
	}
    /**
     +----------------------------------------------------------
	 * 根据easyui过滤器传入生成原生查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $rqName easyui datagrid-filter 对象名称
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
     +----------------------------------------------------------
	 */
	function _filterSql($rqName = 'filterRules',$where=false) {
		$filter = json_decode(stripslashes($this->request->param($rqName)),true);//添加反斜杠并将JSON字符串转化为数组
		$map = '';
        if(!empty($this->request->param($rqName))){
           foreach($filter as $key=>$data){
            $exp = '';
            $op  = $data['op'];
            $val = '';
            if($op!='nofilter' && $data['value']!=''){
				if(isset($data['type'])){
				  if($data['type']=='datebox' or $data['type']=='datetimebox' ){
					if($op=='equal')			 {	$exp = ' = ';		$val="'".$data['value']."'";}//等于
					elseif($op=='notequal') 	 {	$exp = ' <> ';		$val="'".$data['value']."'";}//不等于
					elseif($op=='less')			 {	$exp = ' < ';		$val="'".$data['value']."'";}//小于
					elseif($op=='lessorequal')	 {	$exp = ' <= ';		$val="'".$data['value']."'";}//小于等于
					elseif($op=='greater')		 {	$exp = ' > ';		$val="'".$data['value']."'";}//大于
					elseif($op=='greaterorequal'){  $exp = ' >= ';		$val="'".$data['value']."'";}//大于等于
				  }else{
					if($op=='contains')			 {	$exp = ' like ';		$val="'%".$data['value']."%'";}//包含
					elseif($op=='notcontains')	 {	$exp = ' not like ';	$val="'".$data['value']."'";}//不包含
					elseif($op=='equal')		 {	$exp = ' = ';			$val="'".$data['value']."'";}//等于
					elseif($op=='notequal') 	 {	$exp = ' <> ';			$val="'".$data['value']."'";}//不等于
					elseif($op=='beginwith')	 {	$exp = ' like ';		$val="'".$data['value']."%'";}//开始
					elseif($op=='endwith')		 {	$exp = ' like ';		$val="'%".$op.$data['value']."'";}//结束
					elseif($op=='less')			 {	$exp = ' < ';			$val="'".$data['value']."'";}//小于
					elseif($op=='lessorequal')	 {	$exp = ' <= ';			$val="'".$data['value']."'";}//小于等于
					elseif($op=='greater')		 {	$exp = ' > ';			$val="'".$data['value']."'";}//大于
					elseif($op=='greaterorequal'){  $exp = ' >= ';			$val="'".$data['value']."'";}//大于等于
				}
				}else{
					if($op=='contains')			 {	$exp = ' like ';		$val="'%".$data['value']."%'";}//包含
					elseif($op=='notcontains')	 {	$exp = ' not like ';	$val="'".$data['value']."'";}//不包含
					elseif($op=='equal')		 {	$exp = ' = ';			$val="'".$data['value']."'";}//等于
					elseif($op=='notequal') 	 {	$exp = ' <> ';			$val="'".$data['value']."'";}//不等于
					elseif($op=='beginwith')	 {	$exp = ' like ';		$val="'".$data['value']."%'";}//开始
					elseif($op=='endwith')		 {	$exp = ' like ';		$val="'%".$op.$data['value']."'";}//结束
					elseif($op=='less')			 {	$exp = ' < ';			$val="'".$data['value']."'";}//小于
					elseif($op=='lessorequal')	 {	$exp = ' <= ';			$val="'".$data['value']."'";}//小于等于
					elseif($op=='greater')		 {	$exp = ' > ';			$val="'".$data['value']."'";}//大于
					elseif($op=='greaterorequal'){  $exp = ' >= ';			$val="'".$data['value']."'";}//大于等于
				}
                if($map=='' && $where==true){
                    $map .= ' where '.$data['field'].$exp .$val;
                }else{
                    $map .= ' and '.$data['field'].$exp .$val;
                }
            }
         }
      }
		return $map;
	}
     protected function _listSql($sql, $field = '*',$sortBy = '', $asc = false,$model='') {
        	
        
        $name = $this->request->controller();
		    //halt($name);
        $model= Db::name($name);
		//halt($model->getPk());
		if(empty($sql)){
			$result['total'] = 0;
		  $result['rows'] = [];//dump((json_encode($result)));
		  return  json($result);
		}
		if (!empty ( $this->request->param('sort') )) {
			$order = $this->request->param('sort');
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk();
		}
		if ($this->request->has('order')) {
			$sort = $this->request->has('order') ? $this->request->param('order') : 'desc';
		} else {
			$sort = $asc ? 'desc' : 'asc';
		}
		$sql = str_replace('\\','',$sql);
		if(empty($order)) $order = 1;
		//halt($this->request->has('page'));
		if (!$this->request->has('page') ) {
			$limitsql = 'SELECT '.$field.' FROM (SELECT A.*, ROWNUM RN FROM ('.$sql.' order by '.$order.' '.$sort.') A )';
			//halt($limitsql);
			//dump($limitsql);
			$voList = array();
			$voList = $model->query($limitsql);
			$count = count($voList);  //echo $limitsql;
		}else{
			$countSql = 'select count(1) rowCount from ('.$sql.')';
	
			// 調用緩存判斷函數
			$countArr = $model->query($countSql);//dump($model);die();
			$count = $countArr[0]['rowcount'];  //echo $countSql.'<br>'.$count;die();
			if ($count > 0) {
				if (! empty ( $this->request->param ('rows') )) {
					$listRows = $this->request->param ('rows') ;
				} else {
					$listRows = 20;//默认20笔
				}
				if (! empty ( $this->request->param('page') )) {
					$listPage = $this->request->param('page');
				} else {
					$listPage = 1;
				}
				$startRows = ($listPage-1)*$listRows;
				$endRows   = $listPage * $listRows;
				$limitsql = 'SELECT '.$field.' FROM (SELECT A.*, ROWNUM RN FROM ('.$sql.' order by '.$order.' '.$sort.') A WHERE ROWNUM <= '.$endRows.') WHERE RN > '.$startRows;
				//echo $limitsql;die();
				$voList = $model->query($limitsql);

			} // dump($model->getlastsql());die();
			if(!isset($voList)) $voList = array();
		}
		$result['total'] = $count?$count:0;
		$result['rows'] = $voList;//dump((json_encode($result)));
		//halt($result);
		return  json($result);
	}
	//用戶ID獲取共用model
	public function getUserId(){
		return  cookie(config('USER_AUTH_KEY'));
	}
    public function getUserAccount() {
        return Cookie::has('loginAccount')?Cookie::get('loginAccount'):'';/*登錄帳號*/
    }
    public function getUserName() {
        return Cookie::has('loginUserName')?Cookie::get('loginUserName'):'';/*用戶名*/
    }
    public function getUserCostCode() {
        return Cookie::has('costCode')?Cookie::get('costCode'):'';/*用戶成本中心*/
    }
	//下拉列表共用選擇model
	public function XxgetOption(){
		return action('Tccgcus/Xxgetoptionval');
	}
	//料號選擇共用model
	public function XxgetModelJson(){
		return action('TcMtModel/XxgetTableJson');
		//return action('TcMtModelDesc/XxgetTableJson');
	}
	//料號選擇共用model
	public function XxgetModelJson1(){
		return action('TcMtModel/XxgetJsonList');
	}
	//不良現象共用選擇model
	public function XxgetSymptomJson(){
		return action('TcSymptomCode/XxgetTableJson');
	}
	//段組站共用選擇model
	public function XxgetLnGpSt(){
		return action('TcWsSection/XxgetFLSGSJson');
	}
	//段組站共用選擇model
	public function XxgetScGpSt(){
		return action('TcWsSection/XxgetLSGSJson');
	}
	//下拉列表共用選擇model
	public function XxgetOptionArry($custCode){
		return action('Tccgcus/XxGetOptionArry',array($custCode));
	}
	//部門樹共用選擇model
	public function XxgetDeptTree(){
		return action('Tcdept/XxgetDeptTree');
	}
	//廠別共用選擇model
	public function XxgetFactory(){
		return action('Tcwsfactory/XxgetFactory');
	}
	//線別共用選擇model
	public function XxgetLine(){
		return action('Tcwsline/XxgetLine');
	}
	//成本中心共用選擇model
	public function XxgetCost(){
		return action('Tcdept/XxgetCost');
	}
	public function XxgetRbpPdJson(){
		return action('Tprbppdemp/XxgetRbpPdJson');
	}
	
	//單據號選擇共用model
	public function XxgetOrderNo(){
		return action('TpRbpOrder/XxgetOrderNo');
	}
	//新增單據
	function XxInsertNo(){
		return action('Tprbporder/x_xInsert');
	}		
	public function ajaxReturn($arr,$message,$status){
	 $arr['message'] = $message;
	 $arr['status'] = $status;
	 //halt(json($arr));
	 return json($arr);
   }	
   public function resGet($res,$pubName='PD_get_res') {
   	
		$model = Db::name('EMESP.TP_RES_REC');
		$pubParams = array($res,$request->langset(),false,false,false); //存儲過程參數
		$pubRes = $model->execpub($pubName,$pubParams); //執行存儲過程
        return $pubRes['o_msg'];
	}
   public function resReturn($data,$type='JSON',$info='',$status=2,$pubName='PD_get_res') {
        $result  =  array();
        $result['navTabId']  =  $request->param('navTabId');
        $result['rel']  =  $request->param('rel'); 
        $result['callbackType']  =  $request->param('callbackType'); 
        $returl['forward'] = $request->param('forward'); 
        $result['forwardUrl']  =  $request->param('forwardUrl');
        $result['confirmMsg']  =  $request->param('confirmMsg');

		$model = Db::name('EMESP.TP_RES_REC'); //var_dump($data);
		if(is_array($data)){
			$pubParams = array($data['res'],$request->langset(),false,false,false); //存儲過程參數
		}else{
			$pubParams = array($data,$request->langset(),false,false,false); //存儲過程參數
		}
		$pubRes = $model->execProcedure($pubName,$pubParams); //執行存儲過程
		//dump($data);
		//dump($pubRes);
		$result['status']  =  $pubRes['o_res_flag'];
        $result['statusCode']  =  $pubRes['o_res_flag'];
        $result['message'] = $pubRes['o_msg'];
        $result['info'] =  $pubRes['o_msg'];
        $result['sound'] =  $pubRes['o_res_sound'];
        $result['data'] = $data;
        //扩展ajax返回数据, 在Action中定义function ajaxAssign(&$result){} 方法 扩展ajax返回数据。
     
        if(empty($type)) $type  =   config('DEFAULT_AJAX_RETURN');
        if(strtoupper($type)=='JSON') {
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:text/html; charset=utf-8');
            return (json_encode($result));
        }elseif(strtoupper($type)=='XML'){
            // 返回xml格式数据
            header('Content-Type:text/xml; charset=utf-8');
            return (xml_encode($result));
        }elseif(strtoupper($type)=='EVAL'){
            // 返回可执行的js脚本
            header('Content-Type:text/html; charset=utf-8');
            return ($result);
        }elseif(strtoupper($type)=='RETURN'){
			//單獨返回結果
            return($result);
        }else{
            // TODO 增加其它格式
        }
    }
   public function XxSysUploadFile($o_id,$photoDesc,$filename){

      // 获取表单上传文件 例如上传了001.jpg
    $file = request()->file('$filename');
	   if(empty($file)) {  
            $this->error('请选择上传文件');  
     } 
	   $size = 8*1024*1024 ;// 设置附件上传大小
	   $allow_upload_ext = ['xls', 'txt', 'doc', 'ppt', 'pdf', 'csv',
									'xlsx', 'docx', 'pptx','png','lbl','out',
									'jpg','jpeg','gif','bmp','msg','eml','html']; 
    $uploadMonth = date('Ym');
    $uploadNode = request()->action(); 
	  $uploadpath = ROOT_PATH . 'public' . DS . 'uploads'.DS.$uploadMonth.DS.$uploadNode.DS;
    $ip   =  request()->ip();
	  $saveRule = 'time';
    $emp_id=cookie(config('USER_AUTH_KEY'));
    if(!file_exists($uploadpath)){
        mkdir($uploadpath,0777,true);
    }
    // 移动到框架应用根目录/public/uploads/ 目录下
    $info = $file->validate(['size'=>$size,'ext'=>$allow_upload_ext])->move($uploadpath);
    $arr = [];
    if($info){
        // 成功上传后 获取上传信息
        // 输出 jpg
        echo $info->getExtension();
        // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
        echo $info->getSaveName();
        // 输出 42a79759f284b767dfcb2a0197904287.jpg
        echo $info->getFilename(); 
		         for($i=0;$i<count($file);$i++){
                    $pubParmUp = array($o_id,$uploadClass,$uploadNode,$info->getFilename(), $info->getSaveName(),$info->getSize(),$info->getExtension(),$uploadpath,$photoDesc[$i],$ip,$emp_id,false,false);
                     //var_dump($pubParmUp);die();
                    $pubNameUp = "PD_UPLOADS_INS";
                    $resultUp = Db::connect()->execProcedure($pubNameUp,$pubParmUp);
                    if($resultUp['res']<>'OK'){
                        $ngcountUp++;
                        unlink($uploadpath.DS.$info->getSaveName());//刪除原档。
                        unlink($uploadpath.DS.$info->getSaveName());//刪除原档。
                    }
                    $res .= $photoDesc[$i].'['.$resultUp['res'].']';
               }
       return $this->ajaxReturn($arr,'OK',1);
     }else{
        // 上传失败获取错误信息
        
		   return $this->ajaxReturn($arr,$file->getError(),0);
    }
    }
   //tp5 query二维转一维
	public function Xxquery2arr($arr){
		 $result = array();  // 新数组
			foreach ($arr as $vo) { // $arr是原数组
			    foreach ($vo as $k => $v) {
			        
					  $result[$k]=$v;
			    }
			}
			return $result;
	}
  
}
	