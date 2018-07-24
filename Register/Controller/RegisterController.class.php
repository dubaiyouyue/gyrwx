<?php

namespace Addons\Register\Controller;
use Home\Controller\AddonsController;

class RegisterController extends AddonsController{
	function index() {
		$pall=getAddonConfig('Register');
		$this->assign ( 'pall', $pall );
		
			/*$url = GZ_USERAPITOKENURL.'index.php?g=Home&m=Userapi&a=index';//POST指向的链接      
			$data = array(      
				'access_token'=>'thekeyvalue',
				'tel'=>'13607875450'
			);      
			//$this->tokengzuserapi();
			$json_data = $this->gzpostData($url, $data);      
			//$array = json_decode($json_data,true);    
			//$json_data;
			//print_r($array);      

      
		$this->assign ( 'json_data', $json_data );*/
		
		$this->display();
	}
	function regg(){
		//注册
		$tel=$_SESSION['dxyzmtel'];
		//I('get.tel'); //验证手机号是否被非法修改发送短信手机号
		$email=I('get.email');
		$code=I('get.code');
		$dxyzm=I('get.dxyzm');
		$pass=I('get.pass');
		if($_SESSION['check_pic']!=$code || !$_SESSION['check_pic']) exit('e1'); //验证码错误
		if($_SESSION['dxyzm']!=$dxyzm || !$_SESSION['dxyzm']) exit('e2'); //短信验证码错误
		$url = GZ_USERAPITOKENURL.'index.php?g=Home&m=Userapi&a=regiser';//POST指向的链接  
		$loginsalt=md5(md5($this->get_passwordssss(18)).$tel);

		$data = array(      
			'tel'=>$tel,
			'email'=>$email,
			'pass'=>$pass,
			'loginsalt'=>$loginsalt
		);

		//清空默认token cookie
		//setcookie("token", '', time()-3600000,'/');
		
		echo $json_data = $this->gzpostData($url, $data);
		//注册成功
		if($json_data){
			$User = M("huiyuan");
			$lw['id']=$json_data;
			echo $s=$User->where($lw)->limit(1)->select();
			
				setcookie("user", $lw['id'], time()+3600000,'/');
				setcookie("lsalt", $loginsalt, time()+3600000,'/');
		}
		else exit('e3');//已经注册 或者非法错误
		exit;
	}
	function chasck(){
		//检测是否已经注册
		$tel=I('get.tel');
		$email=I('get.email');
		$code=I('get.code');
		if(!$tel || !$email || !$code) exit;
		if($_SESSION['check_pic']!=$code) exit('e1');
			$url = GZ_USERAPITOKENURL.'index.php?g=Home&m=Userapi&a=check';//POST指向的链接      
			$data = array(      
				'tel'=>$tel,
				'email'=>$email
			); 
			//echo $json_data = $this->gzpostData($url, $data);
			
			$dxyzm=rand(100000,999999);
			$_SESSION['dxyzm']=$dxyzm;
			//发送短信手机号
			$_SESSION['dxyzmtel']=$tel;
			//验证手机号、邮箱是否被非法修改
			
			//发送短信手机号
			$_SESSION['dxyzmtelzhuimim']=0;
			$_SESSION['verifysms']=$_SESSION['check_pic'];
			$_SESSION['verify']=$_SESSION['check_pic'];
			$_SESSION['dxyzmtel']=$tel;//2016.11.18
			$_SESSION['dxyzmssss']=$dxyzm;
			
			
			
			//if(!$json_data) exit("$dxyzm");//短信验证码
		exit;
	}

}
