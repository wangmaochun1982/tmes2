<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use app\home\model\TcDept as dept;
class Tcdept extends Base {
	protected $beforeActionList = [
        '_before_add'    =>  ['only'=>'add'],
        '_before_patch'  =>  ['only'=>'patch'],
        '_before_edit'   =>  ['only'=>'edit'],
        
    ];	
	//左边数菜单显示
    public function XxgetDeptTree(){
        
		if(!empty($this->request->param('id'))){
			$pId = $this->request->param('id');
		}else{
			$pId = 0;
		}
        $nodeModel = Db::name('TcDept');
		$psql = "";
		if($this->request->has('flag')){
			$dbFlag = Session::get('DB_FLAG');
			switch($dbFlag){
				case '1' :
					$startId = 25;
					$pId = empty($pId)?25:$pId;
					$psql = "";
					break;
				case '2' :
					$startId = 6;
					$pId = empty($pId)?6:$pId;
					$psql = "";
					break;
				case '3' :
					$startId = 31;
					$pId = empty($pId)?31:$pId;
					$psql = "";
					break;
				case '4' :
					$startId = 20;
					$pId = empty($pId)?20:$pId;
					$psql = "";
					break;
				case '5' :
					$startId = 14;
					$pId = empty($pId)?14:$pId;
					$psql = "";
					break;
				case '6' :
					$startId = 24;
					$psId = empty($pId)?169:$pId;
					$pjId = empty($pId)?14:$pId;
					$pId = empty($pId)?24:$pId;
					$psql = "union 
					        SELECT ltrim(sys_connect_by_path(id, '-'), '-') pathid,
								   id,
								   pid,
								   dept_code,
								   MLANG(dept_name, '".$this->request->langset()."') dept_name,
								   cost_code
							  FROM emesc.tc_dept
							 where status > 0
							   and pid = $pjId
							 START WITH id = 14
							CONNECT BY PRIOR id = pid
							union 
					        SELECT ltrim(sys_connect_by_path(id, '-'), '-') pathid,
								   id,
								   pid,
								   dept_code,
								   MLANG(dept_name, '".$this->request->langset()."') dept_name,
								   cost_code
							  FROM emesc.tc_dept
							 where status > 0
							   and pid = $psId
							 START WITH id = 169
							CONNECT BY PRIOR id = pid";
					break;
				case '7' :
					$startId = 892;
					$pId = empty($pId)?892:$pId;
					$psql = "";
					break;
				default :
					$startId = 1;
			}
		}else{
			$startId = 1;
		}
		$sql = "SELECT ltrim(sys_connect_by_path(id, '-'), '-') pathid,
					   id,
					   pid,
					   dept_code,
					   MLANG(dept_name, '".$this->request->langset()."') dept_name,
					   cost_code
				  FROM emesc.tc_dept
				 where status > 0
				   and pid = $pId
				 START WITH id = $startId
				CONNECT BY PRIOR id = pid
				$psql
				order by dept_name asc";//dump($sql);die();
		$nodeList = $nodeModel->query($sql);
		//$nodeList = $this->Xxquery2arr($nodeList);
		//halt($nodeList);
		$nodePIds = $nodeModel->column('pid');
	    $nodePIds = array_flip($nodePIds);
		$result = array();$node = array();$i = 0;
        foreach($nodeList as $key=>$data){
            $node[$i]['id'] = $data['id'];
            $node[$i]['text'] = $data['dept_name'];
            $node[$i]['state'] = in_array($data['id'],$nodePIds) ? 'closed' : 'open';
            $node[$i]['pid'] = $data['pid'];
            $node[$i]['pathid'] = $data['pathid'];
            $node[$i]['iconCls'] = in_array($data['id'],$nodePIds) ? '' : 'icon-application_xp';//图标
            $i++;
        }
        $result = ComTreeFormat($node,$pId);
		if($psql){
			$result = array_merge(ComTreeFormat($node,$psId),$result);
			$result = array_merge(ComTreeFormat($node,$pjId),$result);
		}
		//搜索框 (全部)
		$filter = $this->request->param('filter');
		if($filter==1){
			$comList[0]['id'] = '';
            $comList[0]['text'] = '全部';
			$comList[0]['state'] = '';
			$comList[0]['pid'] = '';
			$comList[0]['pathid'] = '';
			$comList[0]['iconCls'] = '';
			$result = array_merge($comList,$result);
		}
		return json($result);
		//echo $sql;
		//echo $this->Xxdepts();
	}
	public function Xxdepts(){
		$sql = "SELECT id,pid,
				   dept_code,
				   MLANG(dept_name, '".$this->request->langset()."') dept_name,
				    MLANG(dept_name, '".$this->request->langset()."')text,
				   cost_code,
				   case when level = 1 or CONNECT_BY_ISLEAF=1 then 'open' else 'closed' end state,
				   ''iconCls,
				   ltrim(sys_connect_by_path(id, '-'), '-') pathid,
				   level
			  FROM emesc.tc_dept
			 where status = 1
			 start with id = 1
			connect by nocycle prior id = pid
			 order by level,dept_name asc";
		$nodeModel = Db::name('TcDept');
		$nodeList = $nodeModel->query($sql);
		$nodeList = $this->Xxquery2arr($nodeList);
        $result = ComTreeFormat($nodeList,0);
	    return json($result);
	}
	public function Xxlist2() {

		if($this->request->has('pid')){
			$pid = $this->request->param('pid');
		}else{
		  $pid = 0;
		}
		$pid = $this->request->has('id')? $this->request->post('id'):0;
		$page = $this->request->has('page') ? intval($this->request->post('page')) : 1;
		$rows = $this->request->has('rows') ? intval($this->request->post('rows')) : 10;
		$offset = ($page-1)*$rows;
		$node = Db::name('TcDept');
           $result = array();
		if ($pid == null){
			$pid = 0;
		}
		$condition['pid'] = $pid;
	    $condition['status'] = array('gt',0);
		$result = array();
		$field = "id,dept_code,MLANG(dept_name, '".$this->request->langset()."') dept_name,cost_code,pid,status,remark,dept_lnk1,dept_level";
		$model = Db::name('TcDept');
		$count = $model->where($condition)->field($field)->count('id');
		$result['total'] = $count;
		$list ='';
		$list = $node->where($condition)->field($field)->limit($offset . ',' . $rows)->select();//dump($model->getlastsql());
		$result['rows'] = $list;
        if($count<1){
        	$result['total'] = 0;
        	$result['rows'] = '';

        }
		return json($result);
	}
	function XxgetCost(){
		$dbflag=Session::get('DB_FLAG');//echo '====='.$dbflag;
		$str.="  status=1";
		if($this->request->param('searDb')){
			//$searDb=$this->request->param('searDb');
			$str.=" and bu_id=$dbflag";
		}
		if($this->request->param('searchKeyWords')){
            $searchKeyWords = trim($this->request->param('searchKeyWords'));
            $str .= " and ( cost_code like '$searchKeyWords%'
                                or cost_name like '%$searchKeyWords%')";
		}
		$model = Db::name('TcCost');
		$field="cost_code, cost_name,cost_code||cost_name cc_cost, cost_code id";
		$count = $model->field($field)->where($str)->count('cost_code');
		$list = $model->field($field)->where($str)->order("cost_code asc")->select();
        //echo $model->getLastSql();
		return json($list);
	}

	public function XxInsert(){
		$deptCode  = $this->request->param('dept_code');
		$deptName  = $this->request->param('dept_name');
		$pid = $this->request->param('pid');
		$status   = $this->request->param('status');
		$userId   =  $this->getUserId();
		$costCode  = $this->request->param('cost_code');
		$deptLnk1  = '';//$this->request->param('dept_lnk1');
		$deptLevel  = '';//$this->request->param('dept_level');
		$remark  = '';//$this->request->param('remark');
		/* if(empty($lang)){
			$lang='zh-tw';
		} */
		$lang=$this->request->langset();
		$updateType=1;//update_type=1 atmesr觸發器才會啟動
		$name = $this->request->controller();
		$M =Db::connect('DB_ATMESR');
		//dump($langCode.','.$langFlag.','.$langText.','.$modelLnk.','.$langDesc.','.$status.','.$emp);
		$pubParam = array($deptCode,$deptName,$pid,$costCode,$deptLnk1,$deptLevel,$status,$deptName,$userId,$lang,$updateType,false);
		$pubName = "PD_DEPT_INS";
		$result = $M->execProcedure($pubName,$pubParam);
		return $this->ajaxReturn($result,'ok',1);
	}
	public function XxUpdate(){
		$id= $this->request->param('id');
		$deptCode  = $this->request->param('dept_code');
		$deptName  = $this->request->param('dept_name');
		$pid = $this->request->param('pid');
		$status   = $this->request->param('status');
		$userId   =  $this->getUserId();
		$costCode  = $this->request->param('cost_code');
		$deptLnk1  = $this->request->post('dept_lnk1');//$this->request->param('dept_lnk1');
		$deptLevel  = $this->request->post('dept_level');//$_REQUEST['dept_level');
		$remark  = '';//$_REQUEST['remark');
		$lang=$this->request->langset();
		$updateType=1;//update_type=1 atmesr觸發器才會啟動
		$name = $this->getActionName();
		$M =Db::connect('DB_ATMESR');
		 //dump($id.','.$deptCode.','.$deptName.','.$pid.','.$costCode.','.$deptLnk1.','.$deptLevel.','.$status.','.$deptName.','.$userId.','.$lang);die();
		$pubParam = array($id,$deptCode,$deptName,$pid,$costCode,$deptLnk1,$deptLevel,$status,$deptName,$userId,$lang,$updateType,false);
		$pubName = "PD_DEPT_UPD";
		$result = $M->execProcedure($pubName,$pubParam);
		return $this->ajaxReturn($result,'ok',1);
    }

	public function setUser(){
        $this->assign('roleId',$this->request->param('roleId'));
        $this->display();
    }
    public function XxGetUserData(){
        $roleId = $this->request->param('rId'); //echo $roleId;
        if($this->request->param('searchKeyWords')){
            $searchKeyWords = $this->request->param('searchKeyWords');
            $str = " and ( a.account like '%$searchKeyWords%' or a.nickname like '%$searchKeyWords%' or c.partname like '%$searchKeyWords%')";
        }
        $sql = "select *
  from (select a.id,
               a.u_account,
               a.user_title,
               a.user_sex,
               MLANG(c.dept_name, '".$this->request->langset()."') dept_name,
               case
                 when b.u_account is not null then
                  1
                 else
                  0
               end okflag,
               f.rolenames
          from emesc.tc_user a,
               (select * from emesc.tc_user where dept_id = $roleId) b,
               emesc.tc_dept c,
               (select d.user_id, wm_concat(e.role_name) rolenames
                  from emesc.td_role_user d, emesc.td_role e
                 where d.role_id = e.id
                 group by d.user_id) f
         where a.id = b.id(+)
           and a.dept_id = c.id
           and a.id = f.user_id $str)"; //echo $sql;
        return $this->_listSql($sql);
    }
  //指派用戶
    public function XxSetUser(){
        $allUser   = $this->request->param('allUser');
        $checkUser = $this->request->param('chkUser');
        $deptId    = $this->request->param('roleId');
        $group    =   Db::name('TcUser',Null,'DB_ATMESR');
        $mapUser['id'] = array('in',$allUser);
        $result1   = $group->where($mapUser)->setField('dept_id','');//dump($group->getlastsql());
        $arr = [];
        if($result1){
            $mapUser['id'] = array('in',$checkUser);
            $result2   = $group->where($mapUser)->setField('dept_id',$deptId);
            if($result2){
                return $this->ajaxReturn($arr,'员工指派成功！',1);
            }else{
                return $this->ajaxReturn($arr,'员工指派失败！',0);
            }
        }else{
            return $this->ajaxReturn($arr,'指派失败！',0);
        }
    }


    //顯示當前部门角色。
    public function setRole(){
        $this->assign('deptId',$this->request->param('deptId'));
        return view();
    }
    public function XxGetRole(){
        $deptId = $this->request->param('deptId');
        $roleM = Db::name('TdRole');
        $sql = "select a.id,
                       a.role_name,
                       a.status,
                       a.remark,
                       b.dept_id,
                       case
                         when b.dept_id is not null then
                          1
                         else
                          0
                       end okflag
                  from emesc.td_role a,
                       (select * from emesc.td_role_dept where dept_id = $deptId) b
                 where a.id = b.role_id(+)
                   and a.status > 0";
        return $this->_listSql($sql);
    }

    public function XxsetRole(){
        $allRole   = $this->request->param('pageId');
        $checkRole = explode('_',$this->request->param('ids'));
        $userId    = $this->request->param('userId');

        $group     =   Db::name('TcDept',Null,'DB_ATMESR');
		$arr = [];
        $result1   = $group->delRoleDepts($userId,$allRole);
        if($result1){
            $result2   = $group->setRoleDepts($userId,$checkRole);
            if($result2){
                return $this->ajaxReturn($arr,"部门角色指派成功。",1);
            }else{
                return $this->ajaxReturn($arr,"部门角色指派失败。",0);
            }
        }else{
            return $this->ajaxReturn($arr,"角色修改失败。",0);
        }

    }

	// 获取配置类型
	public function _before_add() {
		$organization	=	Db::name("Organization");
		$organization->getById(Session::get('currentNodeId'));
        $this->assign('pid',$organization->id);
	}

    public function _before_patch() {
		$model	=	Db::name("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
		$node	=	Db::name("Node");
		$node->getById(Session::get('currentNodeId'));
        $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
    }
	public function _before_edit() {
		$model	=	Db::name("Group");
		$list	=	$model->where('status=1')->select();
		$this->assign('list',$list);
	}


}
?>