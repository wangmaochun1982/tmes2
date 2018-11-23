<?php
namespace app\home\controller;
use think\Controller;
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
						//redirect(PHP_FILE.config('USER_AUTH_GATEWAYDIG'));//bylhp20130917此处无奈用ThinkPHP调用页面方式来制作超时登录，后续考虑纳入easyui来实现
                        header("HTTP/1.0 901 Not Log");
                        //$this->ajaxReturn(true, "You haven't log on yet or your session has time out, please log on again.", 901);
					} else {
						//跳转到认证网关
						redirect ( config( 'USER_AUTH_GATEWAY' ) );
					}
				}else{
					// 没有权限 抛出错误
					if (config( 'RBAC_ERROR_PAGE' )) {
						// 定义权限错误页面
						redirect ( config( 'RBAC_ERROR_PAGE' ) );
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
		$empId =  $this->getUserId(); /*獲取登錄帳號id*/
		if(empty(Session::has(config('ADMIN_AUTH_KEY'))) && $empId!='55') {
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
					   and c.node_code = '$action'
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
					   and c.node_code = '$action'
					   and a.id <> 0
					 order by a.act_sort";
		}//echo $sql;
		$actList = Db::name($action)->query($sql);
        foreach($actList as $key=>$act) {
			/*注釋(如下字符拼接必須換行，否則JS不認識)
				text : action體現的名稱
				iconCls:action體現的圖標
				handler:action調用的函數
			*/
			if($toolBar){
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
		$filter = json_decode(stripslashes(Request::post($rqName)),true);//添加反斜杠并将JSON字符串转化为数组
		//dump($filter);
        if(!empty(Request::post($rqName))){
           foreach($filter as $key=>$data){
            $exp = '';
            $op  = $data['op'];
            $val = '';
            if($op!='nofilter' && $data['value']!=''){
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

        $name = $this->request->action();
        if (empty($model)){
            $model= Db::name($name);
        }
		if (!empty ( Request::post('sort') )) {
			$order = Request::post('sort');
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//dump($order);
		if (Request::has('order')) {
			$sort = Request::has('order') ? Request::post('order') : 'desc';
		} else {
			$sort = $asc ? 'desc' : 'asc';
		}
		$sql = str_replace('\\','',$sql);
		if (!Request::has('page') ) {
			$limitsql = 'SELECT '.$field.' FROM (SELECT A.*, ROWNUM RN FROM ('.$sql.' order by '.$order.' '.$sort.') A )';
			//dump($limitsql);
			$voList = $model->query($limitsql);
			if(!$voList) $voList = array();
			$count = count($voList);  //echo $limitsql;
		}else{
			$countSql = 'select count(1) rowCount from ('.$sql.')';
			// 調用緩存判斷函數
			$countArr = $model->query($countSql);//dump($model);die();
			$count = $countArr[0]['rowcount'];  //echo $countSql.'<br>'.$count;die();
			if ($count > 0) {
				if (! empty ( Request::post ('rows') )) {
					$listRows = Request::post ('rows') ;
				} else {
					$listRows = 20;//默认20笔
				}
				if (! empty ( Request::post('page') )) {
					$listPage = Request::post('page');
				} else {
					$listPage = 1;
				}
				$startRows = ($listPage-1)*$listRows;
				$endRows   = $listPage * $listRows;
				$limitsql = 'SELECT '.$field.' FROM (SELECT A.*, ROWNUM RN FROM ('.$sql.' order by '.$order.' '.$sort.') A WHERE ROWNUM <= '.$endRows.') WHERE RN > '.$startRows;
				//echo $limitsql;die();
				$voList = $model->query($limitsql);

			} // dump($model->getlastsql());die();
			if(!$voList) $voList = array();
		}
		$result['total'] = $count?$count:0;
		$result['rows'] = $voList;//dump((json_encode($result)));
		echo  json_encode($result);
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
	public function ajaxReturn($arr,$message,$status){
	 $arr['message'] = $message;
	 $arr['status'] = $status;
	 //halt(json($arr));
	 return json($arr);
   }	
}
	