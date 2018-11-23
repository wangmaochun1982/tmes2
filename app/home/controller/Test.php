<?php
namespace app\home\controller;
use think\Controller;
use think\View;

class Test extends Controller
{
    
 public function index()
 {
//        //$result = db('emp')->select();
//	   //return  var_dump($result);
//	    //$result = model('Nav')->tabOne();
//		//return  var_dump($result);
//		//dump($_GET['lang']);
////dump(config());//dump($_SERVER['HTTP_ACCEPT_LANGUAGE']);
// //cookie('think_var','zh-tw');
//   //$view = new View();
//   //$view->name = 'thinkphp怎t';
//      halt("ttt");
//      return $this->fetch();
 	 $this->assign('test','hello hmewws');
return view();
//
//      
  }
    
	
	
}
