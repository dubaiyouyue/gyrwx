<?php

namespace Addons\Login\Controller;
use Home\Controller\AddonsController;

class LoginController extends AddonsController{
	function index(){
		
		$this->display ( ONETHINK_ADDON_PATH . 'Login/View/default/Login/index.html' );
	}
	function bdsian(){
		
		$this->display ( ONETHINK_ADDON_PATH . 'Login/View/default/Login/bdsian.html' );
	}	
	function bdqq(){
		$this->display ( ONETHINK_ADDON_PATH . 'Login/View/default/Login/bdqq.html' );
	}
	function zhao(){
		$this->display ( ONETHINK_ADDON_PATH . 'Login/View/default/Login/zhao.html' );
	}
	function zhaochasck(){
		$this->huiyuan = M('huiyuan','gz_',otherdb());
		$tel=I('get.tel')+0;
		$code=I('get.code');
		if(!$tel || !$code) exit;
		if($_SESSION['check_pic']!=$code) exit('e1');
		$tl=$this->huiyuan->where(array('tel'=>$tel))->find();
		if(!$tl['id']) exit('e2');
		
			$get_pass=$this->get_passwordssss(8);
			
			$_SESSION['dxyzmtel']=$tel;
			$_SESSION['dxyzmssss']=$get_pass;
			$_SESSION['verifysms']=$_SESSION['check_pic'];
			$_SESSION['verify']=$_SESSION['check_pic'];
			$_SESSION['dxyzmtelzhuimim']=1;
			
            $xgmm['salt'] = $this->get_passwordssss(8);
            $xgmm['pass'] = md5(md5($get_pass).$xgmm['salt']); 
			$this->huiyuan->where(array('tel'=>$tel))->save($xgmm);
		
		
	}
    function get_passwordssss( $length = 8 ){
        $str = substr(md5(time()), 0, 6);
        return $str;
    }
	function chasck(){
		//检测是否已经注册
		$tel=I('get.tel')+0;
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
