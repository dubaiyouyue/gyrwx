<?php

namespace Addons\Login\Controller;
use Home\Controller\AddonsController;

class LoginController extends AddonsController{
	function index(){
		
		$this->display ( ONETHINK_ADDON_PATH . 'Login/View/default/Login/index.html' );
	}
	function chasck(){
		//检测是否已经注册
		$tel=I('get.tel');
		$pass=I('get.pass');
		$code=I('get.code');
		if(!$tel || !$pass || !$code) exit;
		if($_SESSION['check_pic']!=$code) exit('e1');
		
			$url = GZ_USERAPITOKENURL.'index.php?g=Home&m=Userapilogin&a=check';//POST指向的链接      
			$data = array(      
				'tel'=>$tel,
				'pass'=>$pass
			); 
			$json_data = $this->gzpostData($url, $data);
			$loginc=json_decode($json_data,true);
			//dump($loginc);
			if($loginc['id']){
				session_start();
				$_SESSION['order_ok_wx_uid']=$loginc['id'];
				setcookie("user", $loginc['id'], time()+3600000,'/');
				setcookie("lsalt", $loginc['loginsalt'], time()+3600000,'/');
				exit('lok');
			}
			echo $json_data;
	}
}
;
				setcookie("lsalt", $loginc['loginsalt'], time()+3600000,'/');
				exit('lok');
			}
			echo $json_data;
	}
}
