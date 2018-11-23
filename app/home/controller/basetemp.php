public function actionindex($module = null, $parm_name = null) {
		/**
		 ** index 主頁 action 操作按鈕獲取
		 ** @Parma $module 		調用的模塊頁面
		 ** @Parma $parm_name 	調用的 ActionName，顯示指派的操作配置
		 ** add by cyp 2014-05-27
		**/
        $empId =  $this->getUserId(); /*獲取登錄帳號id*/
		$name = $parm_name?$parm_name:$this->getActionName();
		if(empty(Session::has(config('ADMIN_AUTH_KEY'))) && $empId!='55') {
			$sql = "select distinct  mlang(a.action_name, '".$request->langset()."') langValue, a.icon, a.action_fun,a.act_sort
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
					   and c.node_code = '$name'
					   and a.status = 1
					   and c.status = 1
					   and a.id not in (22)
					   and b.role_id = d.role_id
					   and b.action_id > 0
				    order by a.act_sort";
		}else{//管理員獲取全部操作權限
			$sql = "select distinct mlang(action_name, '".$request->langset()."') langValue,
						   a.icon ,
						   a.action_fun,a.act_sort
					  from tc_action a, tc_node_action b, emesc.tc_node c
					 where a.id = b.action_id
					   and b.node_id = c.id
					   and a.id not in (22)
					   and c.node_code = '$name'
					   and a.id <> 0
					 order by a.act_sort";
		}//echo $sql;
		$actList = Db::name($name)->query($sql);
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
		return view($module);
		//$this->display ($module);	// 模塊頁面調用（V 層）
		//return;
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

        $name = $this->getActionName();
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