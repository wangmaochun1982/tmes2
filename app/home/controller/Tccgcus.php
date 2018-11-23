<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use app\home\model\TcCgCus as cg;
class TcCgCus extends Base{

	public function XxgetTableJson($parm_pid = null){
		
		$cg = new cg();
		$sql = $cg->getSql($parm_pid);
		//dump($sql);
		$this->_listSql($sql);
	}
	//獲取列表名對應的列表值
	function XxgetSubJson(){
		$cg = new cg();
		$sql = $cg->getSubSql();
		//dump($sql);
		$this->_listSql($sql);
	}
	
	//获取列表值
	public function Xxgetoptionval(){
		
		$model =new cg();
		$sql = $model->getOpValSql();
		$list = Db::connect()->query($sql);
		//dump($list);
		$filter = $this->request->param('filter');
		if($filter==1){
			if($this->request->langset()=='en-us') $val = 'default';
			else $val = '默認';
			$comList[0]['text'] = $val;
            $comList[0]['id'] = '';
			$comList[0]['idtext'] = $val;
			$list = array_merge($comList,$list);
		}
		return json($list);
	}
	//获取列表值
	public function XxGetOptionArry($custCode){
		
		$model =new cg();
		$sql = $model->getOpArrySql($custCode);
		$list = Db::connect()->query($sql);
		return $list;
	}
}
?>
