<?php
namespace app\home\model;
use think\Model;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
// 节点模型
class TcNode extends Model {
    protected $connection = 'DB_ATMESR';
	//protected $emp        =  cookie('loginAccount');
	protected $_validate	=	array(
		array('name','checkNode','節點已經存在',0,'callback',self::MODEL_INSERT),
        //array('name','','節點已經存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
        //array('id','','ID重複,請刷新',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
       // array('id','','ID重複,請刷新',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
	);
	//array('验证字段','验证规则','错误提示','验证条件','附加规则','验证时间') 

    protected $_auto		=	array(
        array('id','getSeqId',self::MODEL_INSERT,'callback'),
        array('node_level','getLevelId',self::MODEL_BOTH,'callback'),
       	//array('create_time','sysdate',self::MODEL_INSERT,'string'),
        array('user_id', 'getEmp',self::MODEL_BOTH,'callback'),
		array('update_time','sysdate',self::MODEL_BOTH,'string'),
	);
    public function getEmp(){ 
		$emp        =  cookie('loginAccount');
		return $emp;
	}
	
    public function getSeqId(){ 
		$sql = "select TD_NODE_ID_SEQ.nextval id from sys.dual";
		$list = $this->query($sql); 
		$id = $list[0]['id'];
		return $id;
	}
    
    public function getLevelId($pid=''){
        $pid = $pid?$pid:request()->param('pid');
        if($pid){
            $pidMap['id'] = $pid;
            $pidLevel = $this->where($pidMap)->getField('node_level');
            $pidLevel = $pidLevel?$pidLevel:0;
            $pidLevelNext = $pidLevel+1;
        }
        return $pidLevelNext;
    }

	public function checkNode() {
		$map['name']	 =	 request()->post('name');
		$map['pid']	=	isset(request()->post('pid'))?request()->post('pid'):0;
        $map['status'] = 1;
        if(!empty(request()->post('id'))) {
			$map['id']	=	array('neq',request()->post('id'));
        }
		$result	=	$this->where($map)->field('id')->find();
        if($result) {
        	return false;
        }else{
			return true;
		}
	}
	public function getAccessRoleSql(){ 
		$nodeId = request()->param('node_id');
		$sql ="select distinct b.remark, b.id
				  from emesc.tc_access a, emesc.Tc_ACTION b
				 where a.action_id = b.id
				   and a.node_id = $nodeId
				 order by id";//echo $sql;
		$column =$this->query($sql);
		for ($i=0;$i<count($column);$i++){
			$sqlstr.=',max(decode(b.id, '.$column[$i]['id'].', \'Y\')) '.$column[$i]['remark'];
		}
		$sql ="select c.role_name".$sqlstr."
			  from emesc.tc_access a, emesc.Tc_ACTION b, emesc.tc_role c
			 where a.action_id = b.id
			   and a.role_id = c.id
			   and a.node_id = $nodeId
			 group by role_name";
		return $sql;
	}
	public function getPartRoleSql(){
		$nodeId =request()->param('id');
		$orgCode = request()->param('orgCode');
		$orgCode = $orgCode?$orgCode:1;
		$actionList =request()->param('actionList');
		$actionNum=count($actionList);
		$actionStr=implode($actionList,',');
		$actionStr=$actionNum?$actionStr:'null';
		$sql = "select a.id, a.dept_id, a.role_name name, b.dept_name, a.remark
				  from emesc.tc_role a,
					   emesc.tc_dept b,
					   (select role_id
						  from emesc.tc_access a
						 where node_id = $nodeId
						 group by role_id
						having sum(case when action_id in ($actionStr) then 1 else - 1 end) = $actionNum  and count(action_id) = $actionNum ) c
				 where a.dept_id = b.id
				   and a.id = c.role_id(+)
				   and c.role_id is  null
				   and a.dept_id = $orgCode";
		return $sql;
	}
	
}
?>