<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use app\home\model\TcNode as node;
class Tcnode extends Base {
	
	function add(){
		return view();
	}
	function edit(){
		return view();
	}
	function delete(){
		$this->_delete();
	}
	 //指派頁面
    public function prvlgset(){
		$node_id=$this->request->param('node_id');
		//halt($this->request->param('nodeType'));
		$sql ="select distinct b.remark,b.id
			  from emesc.tc_access a, emesc.tc_action b
			 where a.action_id = b.id
			   and a.node_id = $node_id
			   order by b.id";//echo $sql;
		$name = $this->request->controller();
		$model =Db::connect();
		$column =$model->query($sql);
		$column = $this->Xxquery2arr($column);
		$this->assign('columnList',$column);
        $this->assign('node_id',$this->request->param('node_id'));
        return view();
    }
    //获取easyui下目录的所有图标文件
    function XxgetIconJson(){
        $dir = ROOT_PATH.'public'.DS.'static'.DS.'easyui'.DS.'themes'.DS.'icons'.DS;
        //halt(opendir($dir));
        if (is_dir($dir)){
        	//halt(opendir($dir));
            if ($dh = opendir($dir)){
                $fileArrs = array();
                while (($file = readdir($dh)) !== false){
                $type='';
				//dump($file);
				$i = 0;
                $type = filetype($dir.$file);
                    if($type!='dir' and $file!='..' and $file!='.'){
                        $file=trim(iconv('big5','utf-8',$file));
                        $fileArr['value'] = str_replace(strrchr($file, "."),"",$file);
                        $fileArr['text'] = str_replace(strrchr($file, "."),"",$file);
                        $fileArrs[] = $fileArr;
                        $i++;
                    }
                }
            closedir($dh);
            }
        }
        $sortArr = multi_array_sort($fileArrs,'value');
        return json($sortArr);
    }

	function XxInsert() {
		$pid = $this->request->param('pid');
		$nodeCode = $this->request->param('node_code');
		$nodeName = $this->request->param('node_name');
		$node_sort = $this->request->param('node_sort');
		$node_icon = $this->request->param('node_icon');
		$nodeType = $this->request->param('node_type');
		$status = $this->request->param('status');
		$ismenu = $this->request->param('ismenu');
		$isshow = $this->request->param('isshow');
		$remark = $this->request->param('remark');
		$levelId = $this->request->param('node_level');
		$emp     =  $this->getUserAccount();
		$empId   =  $this->getUserId(); /*獲取登錄帳號id*/
		$name = $this->request->controller();
		$model =Db::name($name,Null,'DB_ATMESR');
		//dump($pid.'-'.$nodeCode.'-'.$node_sort.'-'.$node_icon.'-'.$nodeType.'-'. $status.'-'. $ismenu.'-'. $remark.'-'. $levelId.'-'.$emp);
		$pubParam['i_node_code']=$nodeCode ;
		$pubParam['i_node_name']= $nodeName;
		$pubParam['i_node_sort']= $node_sort;
		$pubParam['i_node_type']= $nodeType;
		$pubParam['i_pid']= $pid ;
		$pubParam['i_node_level']= $levelId;
		$pubParam['i_node_icon']= $node_icon ;
		$pubParam['i_ismenu']=  $ismenu;
		$pubParam['i_isshow']=  $isshow;
		$pubParam['i_status']= $status;
		$pubParam['i_remark']= $remark;
		$pubParam['i_user_id']= $empId;
		$pubParam['i_lang_flag']= $this->request->langset();
		$pubName = "pd_node_ins";//new
		//$pubParam = array($pid,$nodeCode,$node_sort,$node_icon,$nodeType,$status,$ismenu,$isshow,$remark,$levelId,$emp,false);
		$result = $model->execProcedure($pubName,$pubParam);
//		if($result['res']=='OK'){
//			/*節點創建OK，自動新建該節點的index和 Lib Action&Model*/
//			if($ismenu<>'1'){
//				$this->buildHtml($nodeCode."/index",APP_PATH.DS.'home/view','format:index');
//				$this->_bulidAM($nodeCode,$nodeName);
//			}
//		}
		return $this->ajaxReturn($result,'ok',1);
	}

    public function XxgetTableJson(){
        $pId = $this->request->param('pid');
        //IF($pId){$pidStr = " AND A.PID = $pId";}
        if(empty($pId)) $pId = 0; 
		$pidStr = " AND A.PID = $pId";
		if(empty($lang)){
			$lang='zh-tw';
		}
        $sql = "SELECT *
          FROM (SELECT A.ID,
					   A.NODE_CODE,
					   MLANG(A.NODE_NAME, '".$this->request->langset()."') NODE_NAME,
					   A.NODE_NAME LANG_CODE,
					   A.PID,
					   A.NODE_LEVEL node_level,
					   A.NODE_SORT node_sort,
					   getOptionVal('node_type', A.NODE_TYPE, '".$this->request->langset()."') action_lang,
					   A.NODE_TYPE,
					   getOptionVal('ismenu', A.ISMENU, '".$this->request->langset()."') ismenu_lang,
					   getOptionVal('ismenu', A.isshow, '".$this->request->langset()."') isshow_lang,
					   A.ISMENU,
					   A.isshow,
					   A.STATUS,
					   A.NODE_ICON node_icon,
					   A.REMARK,
					   A.USER_ID user_id,
					   A.UPDATE_TIME,
					   MLANG(B.NODE_NAME, '".$this->request->langset()."') PNAME
				  FROM EMESC.TC_NODE A, EMESC.TC_NODE B
                 WHERE A.PID = B.ID(+)
                    AND A.node_type IN (1,2)
                   $pidStr
                   )";
		//echo $sql;die();
		//halt($sql);
		//halt($this->_listSql($sql));
        return $this->_listSql($sql);
    }


	 public function Xxgetnodetree(){
    	
    	if($this->request->has('id')){
    		$pId = $this->request->param('id');
    	}else{
    		$pId = 0;
    	}
		$nodeModel = Db::name('TcNode');
		$lang =cookie('login_langset');
		$nodeMap['status'] = array('gt',0);
        $nodeMap['pid']    = $pId;//dump($this->request->param('node_type']);
        //$nodeMap['node_type']   = array('in','1');
		//halt($this->request->param('node_type'));
		if($this->request->param('node_type')){
			$nodeMap['node_type']   = array('in',$this->request->param('node_type'));
		}
		else{
			$nodeMap['node_type']   = array('neq',3);
		}
        $nodeList = $nodeModel->where($nodeMap)->field('id,node_code, MLANG(node_name, \''.$this->request->langset().'\') NODE_NAME,status,node_sort,pid,node_icon,node_level,ismenu')->order('node_sort asc')->select();
        //if($pId>0)  die($nodeModel->getLastSql());
		//halt($nodeModel->getLastSql());
		$result = array();
        $nodePIds = $nodeModel->where('ismenu=1')->column('pid');//菜單節點
        $nodePIds = array_flip($nodePIds);
        //dump($nodeList);
		//halt($nodePIds);
        foreach($nodeList as $key=>$data){
            $node = array();
            $node['id'] = $data['id'];
            $node['text'] = $data['node_name'];
            $node['state'] = in_array($data['id'],$nodePIds) ? 'closed' : 'open';
            $node['attributes'] = $data;
            $node['iconCls'] = $data['node_icon']?'icon-'.$data['node_icon']:'';//图标格式化
			$node['ismenu'] =$data['ismenu'];
            array_push($result,$node);
        }
       return json($result);
	}

	public function XxUpdate(){
		$id = $this->request->param('id');
		$pid = $this->request->param('pid');
		$nodeCode = $this->request->param('node_code');
		$nodeName = $this->request->param('node_name');
		$node_sort = $this->request->param('node_sort');
		$node_icon = $this->request->param('node_icon');
		$nodeType = $this->request->param('node_type');
		$status = $this->request->param('status');
		$ismenu = $this->request->param('ismenu');
		$isshow = $this->request->param('isshow');
		$remark = $this->request->param('remark');
		$levelId = $this->request->param('node_level');
		$empId   =  $this->getUserId(); /*獲取登錄帳號id*/
		$name = $this->request->controller();
		$model =Db::name($name,Null,'DB_ATMESR');
		$pubParam['i_id']= $id;
		$pubParam['i_node_code']= $nodeCode;
		$pubParam['i_node_name']= $nodeName;
		$pubParam['i_node_sort']= $node_sort;
		$pubParam['i_node_type']= $nodeType;
		$pubParam['i_pid']= $pid ;
		$pubParam['i_node_level']= $levelId;
		$pubParam['i_node_icon']= $node_icon;
		$pubParam['i_ismenu']= $ismenu;
		$pubParam['i_isshow']= $isshow;
		$pubParam['i_status']= $status;
		$pubParam['i_remark']= $remark;
		$pubParam['i_user_id']= $empId;
		$pubParam['i_lang_flag']= $this->request->langset();
		$pubName = "pd_node_upd";//new
		$result = $model->execProcedure($pubName,$pubParam);
		return $this->ajaxReturn($result,'ok',1);
	}

    //获取Action名称 by lhp 2013-09-26
    function getAName($input, $start, $end) {
        //$substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));
        //$substr = str_replace('Action','',$substr);
        $substr = $this->request->action();
        return trim($substr);

        //preg_match("/$start(.*)$end/i",$input,$arr);
        //return $arr[1);
    }
    //获取权限验证的Function by lhp 2013-09-26
    function getFName($input,$spt){
        $reArr = explode($spt,$input);
        $zimu = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        $fnNameArr = array();
        foreach($reArr as $key=>$data){
            $tempArr = '';$tempName='';
            $tempArr = explode('(',$data);
            $tempName = trim($tempArr[0]);
            if(!in_array($tempName,$fnNameArr) && in_array(strtolower(substr($tempName,0,1)),$zimu) &&substr($tempName,0,2)!='Xx'){
                $fnNameArr[] = $tempName;
            }
        }
        sort($fnNameArr);
        return $fnNameArr;
    }
    //自动计算层级
    public function XxAutoLevelId(){
        $this->_AutoLevelId(0,0);
        return  '计算完成';
    }
    //计算层级
    public function _AutoLevelId($pid=0,$level=0){
        $nodeModel = Db::name('TcNode',Null,'DB_ATMESR');
        $nodelMap['pid'] = $pid;
        $nodeResult = $nodeModel->where($nodelMap)->select();
        if($nodeResult){
            $saveData['node_level'] = $level + 1;
            $saveMap['pid'] = $pid;
            $nodeModel->where($saveMap)->update($saveData);
            foreach($nodeResult as $key=>$data){
                $this->_AutoLevelId($data['id'],$data['node_level']);
            }
        }
    }


	public function XxRoleActionList(){
		$node = new node();
		$sql = $node->getAccessRoleSql();
		return $this->_listSql($sql);
		//dump($sql);
	}
	public function XxgetPartRole(){
		$node = new node();
		$sql = $node->getPartRoleSql();
		//echo $sql;die();
		return $this->_listSql($sql);
	}
	public function XxgetRoleAppoint(){
		$nodeId =$this->request->param('id');
		$actionList =$this->request->param('actionList');
		$actionStr=implode($actionList,',');
		$actionNum=count($actionList);
		$sql="select a.id,a.org_code,a.role_name name, b.dept_name, a.remark
			  from emesc.tc_role a,
				   emesc.tc_dept b,
				   (select role_id
					  from emesc.tc_access a
					 where node_id = $nodeId
					 group by role_id
					having sum(case when action_id in ($actionStr) then 1 else - 1 end) = $actionNum
					  and count(action_id) = $actionNum) c
			 where a.org_code = b.id
			   and a.id = c.role_id";
		//echo $sql;
		return $this->_listSql($sql);
		//echo json_encode($result);
	}
	public function XxNodeRoleInserts(){
		$actionData = $this->request->param('actionData');
		$roleData = $this->request->param('roleData');
		$nodeId = $this->request->param('nodeId');
		$emp =  $this->getUserAccount();
		//$name = $this->getActionName();
		$model = Db::connect('DB_ATMESR');
		$OK=0;
		$pubName = "pd_node_role_ins";
		//echo $nodeId.'-'.$actionData.'-'.$roleData;die();
		$pubParam = array($nodeId,$actionData,$roleData,$emp,false);
		$result = $model->execProcedure($pubName,$pubParam);
		$res = $result['res'];
		return $this->ajaxReturn($result,$res,0);
	}

	public function XxNodeActionInsert(){
		$actionData = $this->request->param('actionData');
		$nodeId = $this->request->param('nodeId');
		//$actionArr=explode('_',$actionData);
		$emp        =  $this->getUserAccount();
		$empId   =  $this->getUserId(); /*獲取登錄帳號id*/
		$name = $this->getActionName();
		$model =Db::connect('DB_ATMESR');
		$sql ="delete from emesc.TC_NODE_ACTION
				where node_id = '$nodeId'";
		$result = $model->execute($sql);
		$OK=0;
		
		$pubParam['i_action_str']= $actionData;
		$pubParam['i_node_id']= $nodeId;
		$pubParam['i_update_id']= $empId;
		$pubName = "PD_NODE_ACTION_INS";
		//$pubParam = array($nodeId,$actionId,$emp,false);
		$result = $model->execProcedure($pubName,$pubParam);
		$this->resReturn($result);
	}
	//取消
	public function XxCancelAppoint(){
		$role_id = $this->request->param('role_id');
		$node_id = $this->request->param('node_id');
		$sql ="delete from emesc.tc_access_kt
				where role_id='$role_id'
				and node_id = '$node_id'";
		$name = $this->getActionName();
		//$model =M($name,Null,'DB_ATMESR');
		$model =Db::connect('DB_ATMESR');
		$result = $model->execute($sql);
		if($result !==false){
			return $this->ajaxReturn($result,'取消成功!',1);
		}else{
			return $this->ajaxReturn($result,'取消失敗!',0);
		}

	}
	
}
?>