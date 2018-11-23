<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use lib\Rbac;
use PDO;
use PDOStatement;
use think\exception\PDOException;
class Pub extends Base {
	// 检查用户是否登录
//	public function _initialize(){
//		echo 'init function';
//	}
    public function top(Request $request)
    {
        
     $client_ip = $request->ip();
	 $this->assign('loginPcAddr',$client_ip);
	  $gId = $request->get('gId');
        if(empty($gId)){
            $gId = Session::get('gId');
        }else{
            session('gId',$gId);
        }
        //接入点
        $buModel = Db::name('TcUserDb',Null,'RBAC_DB_DSN');
        $userId = cookie(config('USER_AUTH_KEY'));
        $selMap['user_id'] = $userId;
        $selMap['db_type'] = 1;
          $firstCkd = $buModel->where($selMap)->value('db_id');//dump($firstCkd);
          $selMap2['user_id'] = $userId;
          $selecCkd = $buModel->where($selMap2)->order('db_id')->field('db_id')->select();//dump($selecCkd);
        //halt($selecCkd);
        //var_dump($buModel->getLastSql());
//      $d_sql = "SELECT db_id
//                FROM tc_user_db
//               WHERE user_id = $userId
//               ORDER BY db_id";
//	    $selecCkd = $buModel->query($d_sql);
		//var_dump($buModel->getLastSql());
        $this->assign('firstCkd',$firstCkd);
        $this->assign('selecCkd',$selecCkd);
		 //横向菜单
        $v_menu	=	Db::name("TcNode",Null,'RBAC_DB_DSN');
		$userStr = '';
        if($userId !=1 && !Session::has('administrator')){
			if($userId<>-1){
            $userStr = "and b.role_id in (select role_id from emesc.tc_role_user where user_lnk_id = $userId
										  union select brole_id
												  from (select *
														  from tc_role_lnk
														 where brole_id not in
															   (select role_id from tc_role_user where user_lnk_id = $userId))
												connect by prior brole_id = frole_id
												 start with frole_id in
															(select role_id from tc_role_user where user_lnk_id = $userId))";
			}else{
				 $userStr = "and b.role_id in (select role_id from emesc.tc_role_user where user_lnk_id = $userId)";
			}
        }
		$navSql = "select *
                          from (select distinct a.id, a.node_code, MLANG(A.NODE_NAME, '".$request->langset()."') title, a.node_sort,a.node_icon iconcls
                                  from emesc.tc_node a, emesc.tc_access b
                                 where a.id = b.node_id(+)
                                   and a.pid = 1
                                   and a.node_type = 1
                                   and a.node_level = 2
                                   and a.status = 1
                                   $userStr )
                         order by node_sort";
                         //halt($navSql);
		//dump($navSql);die();
        $list = $v_menu->query($navSql);//echo $list;
		$this->assign('navList',$list);
     return view();
        //return view();
  	}
	public function left()
    {
        //return $this->fetch();
        return view();
  	}
  	// 检查用户是否登录
	protected function checkUser() {
		//if(!isset(_SESSION[config('USER_AUTH_KEY')])) {
		  if(!cookie(config('USER_AUTH_KEY'))){
			$this->assign('jumpUrl','/home/Pub/login');
			$this->error('非法访问：请登录系统后操作！');
		}
	}
  		// 菜单页面
	public function menu(Request $request) {
	    $this->checkUser();
	    $dbflag = $request->get('dbId');
        if($dbflag!=''){
            session('DB_FLAG',null);
            session('DB_FLAG',$dbflag);
            //将用户切换当前数据ID存入数据库
            $userMap['id'] = cookie(config('USER_AUTH_KEY'));
            $saveDbFlag['login_area']   = $dbflag;
            Db::name('TcUser',Null,'RBAC_DB_DSN')->where($userMap)->save($saveDbFlag);
        }
        if(Cookie::has(config('USER_AUTH_KEY'))){
			//显示菜单项
			$menu = array ();
            $menuAtr = array();
			//读取数据库模块列表生成菜单项
			$node = Db::name( "TcNode",Null,'RBAC_DB_DSN');
            $pid = Cookie::has('_navId')?cookie('_navId'):1;//默认全部
            
             $nodeSql = "select a.id, a.pid, a.node_code , a.node_type , MLANG(A.NODE_NAME, '".$request->langset()."') NODE_NAME , a.node_icon, a.ismenu
                          from emesc.tc_node a
                         where a.status = 1
                           and a.node_type in (1, 2)
						   and isshow = 1
                        connect by prior a.id = a.pid
                         start with a.id = $pid
                         order by a.node_sort";//echo $nodeSql;//die();
            //dump($nodeSql);           
            $list = $node->query($nodeSql);
            $accessList = Session::get('_ACCESS_LIST'); 
           // halt($list);
			foreach ( $list as $key => $module ) {
				if (isset($accessList ['HOME'][strtoupper ( $module ['node_code'] )] ) || Session::has('administrator') || $module['ismenu'] == 1) {
				    if($module ['ismenu'] == 1){//如果是文件夹则底下子级必须要有权限才显示本级
				        $count = 0;unset($ckRst);$a='';
				        $ckPid['pid'] = $list[$key]['id'];//父ID
                        $ckPid['node_type'] = array('in','1,2');
                        $ckPid['isshow'] = array('in','1');
                        $ckPid['status'] = 1;
                        $ckRst = $node->where($ckPid)->select();
                        foreach($ckRst as $k => $v){
                            if (isset($accessList ['HOME'][strtoupper ( $v['node_code'] )] ) || Session::has('administrator') ) {
                              //if($v ['ismenu'] <> 1){
								$count+=1;
							  //}
                            }
                        }
						if($list[$key]['pid']>1) $state = 'closed';//第一級以上默認折疊，第一級默認展開
						else $state = 'open';
				    }else{
				        $count = 1;
						$state = null;//非菜單不許要狀態
				    }
					$menuNode = array();//初始化，此項必須，否則會有問題
                    if($count>0){
                        $menuNode ['id'] = $list[$key]['id'];//节点ID
                        $menuNode ['pid'] = $list[$key]['pid'];//父ID
                        $menuNode ['attributes'] = $list[$key];//加入自定义属性
                        $menuNode ['iconCls'] = $list[$key]['node_icon']?'icon-'.$list[$key]['node_icon']:'';//图标格式化
                        $menuNode ['title'] = $list[$key]['node_name'];//节点文本
                        $menuNode ['name'] = $list[$key]['node_code'];//节点文本
						if($state){//通過狀態menuNode數組是否存在state屬性
							$menuNode ['state'] = $state;
						}
                        $menu [] = $menuNode;
                    }
				}
			}
            $menuJson =  json_encode($menu);
		}
        //halt($menuJson);
        echo $menuJson;
	}
	public function center()
    {
        //return $this->fetch();
        $this->assign("v_refresh","刷新");
        return view();
  	}
		// 用户登录页面
	public function login() {
		
        if(!cookie(config('USER_AUTH_KEY'))){
    	       $loginPic = 'login_banner.jpg';
               $ipAddr		=	request()->ip();
    	   
            $arr = array('天','一','二','三','四','五','六');
            $wek = $arr[date('w',strtotime(date('Y-M-d')))];
            $skd = date('Y年m月d日 ').'&nbsp;&nbsp;&nbsp;&nbsp;星期'.$wek;
            //dump($_GET['_URL_']);die(11);
            session('ntrel', urlencode(input('param.nRel')));
			session('nttite', input('param.title'));
			session('lgDn',input('param.lgDn'));
			//dump($_SESSION);die();
            $this->assign('loginPic',$loginPic);
            $this->assign('ipAddr',$ipAddr);
            $this->assign('skd',$skd);
			//return view();
		}else{
			
			if(empty(session('ntrel'))){
				session('ntrel', urlencode(input('param.nRel')));
				Session::set('nttite',input('param.title')?input('param.title'):'查看-');
                if(input('param.lgDn')){//如果有傳入需要指定的事業部，則進行系統數據庫轉換
                    $dbfact = array('3074'=>'1','308'=>'2','3078'=>'3','306'=>'4','309'=>'6','304'=>'6');
                    $lgdn = $dbfact[input('param.lgDn')];
                    Session::set('DB_FLAG',null);
                    Session::set('DB_FLAG',$lgdn);//保存接入点到Session
                }
			}
			
			$this->redirect('/home/index/index');///tmpClear/'.$tempNums);
		}
		return view();
		//return $this->fetch();
	}
    // 登录检测
	public function checkLogin(Request $request){
		  	
		   Cookie::set('ckCookieSet',1);
	        if(Cookie::get('ckCookieSet')!=1){
	            return json($request->post(),'您的瀏覽器不支持Cookie，請確認是否開啟。',0);
	        }
			$map            =   array();
		    // 支持使用绑定帐号登录
		    $map['u_account']	= $request->post('account');
            $map['status']	=	array('gt',0);	
			$rbac = new Rbac();
	        $authInfo = $rbac->authenticate($map);
			
			//halt($authInfo);
			if(empty($authInfo)) {
				   
                    return $this->ajaxReturn($authInfo,'帐号不存在或已经被禁用',0);
            }else{
            	
            	if($authInfo['user_pwd'] != md5($request->post('password'))) {
            	 
            	   return $this->ajaxReturn($authInfo,'密码错误！',0);
               }
				if($request->param('UserPcAddr')){
                  $ip		=	trim($request->param('UserPcAddr'));
	            }else if(Cookie::has('loginPcAddr')){
	                $ip		=	cookie('loginPcAddr');
	            }else{
	                $ip		=	request()->ip();
	            }
	            if(!$ip || $ip=='0.0.0.0') return $this->ajaxReturn('','訪問地址['.$ip.']異常,拒絕登錄',0);
	              Cookie::set('loginPcAddr',$ip);
				    $buModel = Db::name('TcUserDb');
		            $User	 =	Db::name('TcUser',Null,'RBAC_DB_DSN');
		            $selMap['user_id'] = $authInfo['id'];
		            //获取用户事业部访问默认接入点
		            $firstCkd = $buModel->where($selMap)->order('db_type')->value('db_id');//echo $buModel->getLastSql();
		        
					if(!$firstCkd){
		                return $this->ajaxReturn($authInfo,'您沒有接入点访问權限，请联系管理员。',0);
		            }
					//获取用户上次访问接入点
		            $userMap['id'] = $authInfo['id'];
					$dbfact = array('3074'=>'1','308'=>'2','3078'=>'3','306'=>'4','309'=>'6','304'=>'6');
				  
					//halt($firstCkd);
					
					$lgdn = Session::has('lgDn')?$dbfact[Session::get('lgDn')]:'';
					
					Session::delete('lgDn');/*取出後，清除session*/
					//dump($_SESSION['ntrel']);
					//dump($_REQUEST);die();
		            $lastLoginDb = $User->where($userMap)->value('login_area');//dump($lastLoginDb);
					$lastLoginDb = $lgdn?$lgdn:$lastLoginDb;
					//halt($lastLoginDb);
		            if($lastLoginDb!=''){
		                $ckeDbMap['user_id'] =  $authInfo['id'];
		                $ckeDbMap['db_id'] = $lastLoginDb;
		                $countDb = $buModel->where($ckeDbMap)->count('db_id');
		                if($countDb>0){
		                    $logindb = $lastLoginDb;//转到上次登陆接入点
		                }else{
		                    $logindb = $firstCkd;
		                }
		            }else{
		                $logindb = $firstCkd;
		            }
				  
		            if(!$logindb){return $this->ajaxReturn($authInfo,'登录接入点发生异常。详细：Error Code:'.$firstCkd.'/'.$lastLoginDb.'/'.$countDb.'/',0);}
					$DeptMap['id'] =  $authInfo['dept_id'];
					$_DeptCost = Db::name('TcDept')->where($DeptMap)->value('cost_code',true);
		            session('DB_FLAG',null);
		            session('DB_FLAG',$logindb);//保存接入点到Session
		            cookie(config('USER_AUTH_KEY'),$authInfo['id']);//用戶ID
		            cookie('loginAccount',$authInfo['u_account']);//登錄帳號
		            cookie('loginEmail',$authInfo['user_email']);//Email
		            cookie('loginUserName',$authInfo['user_title']);//用戶名
		            cookie('lastLoginTime',$authInfo['last_login_time']);//最後登錄時間
		            cookie('login_count',$authInfo['login_count']);//登錄次數
		            cookie('costCode',$_DeptCost[0]);//成本中心
		            $adminModel = Db::name('TcRoleUser');
		            $adminMap['role_id'] =  1;
		            $_admin = $adminModel->where($adminMap)->column('user_lnk_id');
		            //$sql = Db::getLastSql();
		            $adminMap['role_id'] =  137;
		            $adminMap['user_lnk_id'] =  $authInfo['id'];
		            $_diskey = $adminModel->where($adminMap)->count(1);//獲取允許手輸入SN
		            $sponsorMap['role_id'] = array('in',array(17,51,123,124,125,126,127,128));
		            $sponsorMap['user_lnk_id'] =  $authInfo['id'];
		            $_sponsor = $adminModel->where($sponsorMap)->count(1);//專案主辦
		            $_SESSION['diskey']		=	$_diskey;
					cookie('sponsor',$_sponsor);//專案主辦
				$_admin = array_flip($_admin);
				//halt(array_key_exists($authInfo['id'],$_admin));	
			    
            if($authInfo['u_account']=='admin' || in_array($authInfo['id'],$_admin)) {
            	Session::set('administrator',true);
				//halt(Session::has(config('ADMIN_AUTH_KEY')));
            }
			
			if($authInfo['dept_id']=='52') {
            	Session::set('ITTEAM',true);
            }
			
           //保存登录信息
			$time	=	time();
            $data = array();
			$data['id']	=	$authInfo['id'];
			$data['last_login_time']	=	Db::raw('sysdate');
			$data['login_count']	=   Db::raw('nvl(login_count,0)+1');	
			$data['last_login_ip']	=	$ip;
            $data['login_area']	=	$logindb;
			$User->setField($data);
			//$User-> where('id',8)->setField($data);
			//halt(Db::getLastSql());
			// 缓存访问权限
            $rbac->saveAccessList();
            if(!Session::has('administrator') && cookie(config('USER_AUTH_KEY')) && !Session::has('_ACCESS_LIST')) {
                return $this->ajaxReturn($authInfo,'对不起，您没有权限访问系统。',0);
            }else{
				if(!empty($request->get('nRel'))){/*外部鏈接地址用*/
					Session::set('ntrel',$request->post('nRel'));
				    Session::set('nttite',$request->post('nTitle'));
				}
				$controller_name = $request->controller();
				
                userActionLog(1,$controller_name,'登錄');
               return  $this->ajaxReturn($authInfo,'登录成功！',1);
            }		
						
		   }//$authInfo is not null
		        
		        
		  
	}
    public function chageNavGroup(Request $request){
        $navId = $request->post('navId');
        Cookie::Set('_navId',$navId);
		//halt($navId);
		if(!Cookie::has(config('USER_AUTH_KEY'))){
			if (request()->isAjax()){
				header("HTTP/1.0 901 Not Log");
			} else {
				//跳转到认证网关
				redirect ( PHP_FILE . config( 'USER_AUTH_GATEWAY' ) );
			}
		}else{
			//halt($navId);
			if(Cookie::get('_navId')==$navId){
				$arr=[];
				return $this->ajaxReturn($arr,'切换菜单成功！',1);
			}else{
				$arr=[];
				return $this->ajaxReturn($arr,'切换菜单失败。',0);
			}
		}
	}
	// 后台首页 查看系统信息
    public function main() {
        $info = array(
            '开发单位'=>'新凯复材科技有限公司',
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            '开发框架'=>'ThinkPHP'.THINK_VERSION.'+jquery'.$_REQUEST['jqver'].'+EasyUI '.$_REQUEST['jeauiver'],
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '服务器IP地址'=>$_ENV["SERVER_ADDR"],
            '客户端IP地址'=>$_ENV["REMOTE_ADDR"],
            '剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
            );
        $this->assign('info1',$info);
        $this->display();
    }
    //切换用户
     public function login_dialog(){
            cookie(config('USER_AUTH_KEY'),null);
			session_destroy();
           return view();
    }
	 //锁定
	  public function cancelLock(){
        session('lockSystemSession', 'LOCK');
        return view();
    }
	   function doCancelLogin(Request $request) {//解锁操作
        $password = $request->post('pwd');
         $arr = [];
        if ($password) {
            $userName = Cookie::get('loginAccount');
            $userPass = pwdHash($password);
            $User	 =	Db::name('TcUser',Null,'RBAC_DB_DSN');
            $count = $User->where(array('u_account' => $userName, 'user_pwd' => $userPass))->count();
            //halt($User->getLastSql());
            if ($count) {
                session('lockSystemSession', null);
				
                return $this->ajaxReturn($arr,'解锁成功。',1);
            }else{
            	
               return  $this->ajaxReturn($arr,'解锁密码错误。',0);
            }
        }else{
            return $this->ajaxReturn($arr,'解锁密码不能为空。',0);
        }
    }
	   // 用户登出
    public function logout(Request $request){
    	$arr=[];
          if(cookie(config('USER_AUTH_KEY'))){
          	
			  $controller = $request->controller();
            userActionLog(1,$controller,'登出');
            cookie(null);
            cookie(config('USER_AUTH_KEY'),null);
			session_destroy();
            unset($_COOKIE);
             //$this->assign("jumpUrl",__URL__.'/login/');
             return $this->ajaxReturn($arr,'登出成功！',1);
        }else {
            return $this->ajaxReturn($arr,'已经登出！',1);
        }
    }
	  // 更换密码
    public function changePwd(Request $request)
    { 
		$arr = [];	
		$this->checkUser();
		$map	=	array();
        $map['user_pwd']= pwdHash($request->post('oldpassword'));
        if($request->has('account')) {
            $map['u_account']	 =	 $request->post('account');
        }//elseif(isset(_SESSION[C('USER_AUTH_KEY')])) {
         elseif(Cookie::has(config('USER_AUTH_KEY'))){
            $map['id']		= Cookie::get(config('USER_AUTH_KEY'));//	_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User    =   Db::name("TcUser",Null,'RBAC_DB_DSN');
        if(!$User->where($map)->field('id')->find()) {
           return  $this->ajaxReturn($arr,'旧密码不符合或密码输入有误！',0);
        }else {
			//$User->password	=	pwdHash($_POST['password']);
			$new_pwd    =	md5($request->post('password'));
			$id			=	Cookie::get(config('USER_AUTH_KEY'));
			//$User->update_type	=	'1';
			//$User->save();
			$User->where('id',$id)->update(['user_pwd' => $new_pwd]);
			//halt($User->getLastSql());
			return $this->ajaxReturn($arr,'密码修改成功，下次登录后生效！',1);
        }
    }
     public function password(){
		
		return view();
	}
	  //变更数据库
	  public function changeDB(Request $request){
        $dbflag = $request->post('dbId');
		$arr = [];
        if($dbflag!=''){
            session('DB_FLAG',null);
            session('DB_FLAG',$dbflag);
            //将用户切换当前数据ID存入数据库
            $userMap['id'] = cookie(config('USER_AUTH_KEY'));
            $saveDbFlag['login_area']   = $dbflag;
          Db::name('TcUser',Null,'RBAC_DB_DSN')->where($userMap)->update(['login_area' => $dbflag]);
		}
        if(Session::get('DB_FLAG')==$dbflag){
            userActionLog(1,$request->controller(),'切換到'.getDbName($dbflag));
            return $this->ajaxReturn($arr,'切换成功。',1);
        }else{
            return $this->ajaxReturn($arr,'切换失败',0);
        }
    }
	public function ccc(Request $request){
//			$sql = "SELECT role_name,remark FROM emesc.tc_role where role_name='KTGM'";
//			//$res = Db::query($sql);
//			$res = Db::query("SELECT role_name,remark FROM emesc.tc_role where role_name=?",['KTGM']);			
//		    dump($res);
       // $this->success('插入成功!',url('ok'));
     //dump(ROOT_PATH.'public'.DS.'static'.DS.'easyui'.DS.'themes'.DS.'icons');
			//halt($request->controller());
			halt(config());
			dump(__dir__);
			dump(__file__);
			dump(APP_PATH);
			dump($this->request->action());
			//$res = Db::connect();
			//$res->query("SELECT role_name,remark FROM emesc.tc_role where role_name=?",['KTGM']);			
		    //halt($res);
//		    halt(config());
//		    $uploadMonth = date('Ym');
//          $uploadNode = request()->action(); 
//		    dump(ROOT_PATH . 'public' . DS . 'uploads'.DS.$uploadMonth.DS.$uploadNode.DS);
//		    dump($request->langset());
//		    $arr = ['v_a'=>[1,2]];
//		    //$result = $res->execProcedure('pub_test1_kt',$arr);
//			$res =  execOci('pub_test1_kt',$arr);
//		    halt($res);
//          




      // 调用存储过程
       //$stmt -> execute ();
//	   $result = $res->getTableFields('TC_MT_MODEL');
//     dump($result);
//	   $type = $res->getFieldsType('TC_MT_MODEL');
//	   dump($type);
//	   $bind = $res->getFieldsBind('TC_MT_MODEL');
//	   dump($bind);


        //$res = Db::connect('DB_ATMESR');
		//$logModel = $res->name("TcUserActionLog");
		
		//$result = $logModel->query("select action_user,action_name,action_title from emesc.tc_user_action_log where action_user=?",[8]);
		//dump($result);
		//$this->redirect(url('ok'));
       // dump($request->controller());
        //$model =  config('USER_AUTH_MODEL');
        //使用给定的Map进行认证
        //$res = Db::name($model,Null,'RBAC_DB_DSN')->find();
		//$res = '/'.$request->module().'/'.$request->controller();
		//dump($res);
		//return view();
	}
	public function ok(Request $request){
		echo '<h2>成功</h2>';
		dump(config());
	}
	

}
?>