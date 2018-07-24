<?php

namespace Addons\Loginapptoken\Controller;
use Home\Controller\AddonsController;

class LoginapptokenController extends AddonsController{
    
    public function index(){
        $hurl='http://guangyuren.com/libweibo-master/';
		$appqqurl=$_GET['appqqurl'];
		if($appqqurl=='qq'){
			$hurl='http://guangyuren.com/Connect/example/oauth/index.php';
		}
        $now_token_sina=$this->get_passwordssss(18);
		$now_token_sina=$now_token_sina.md5(time().rand(1,9));
		$hurl=$hurl.'?loginapp=weixin&loginapptoken='.$now_token_sina;
		$_SESSION['login_app_token']=$now_token_sina;
		header('Location:'.$hurl);
		exit;
    }
	public function sinahave(){
		$w['msinatoken']=$_SESSION['login_app_token'];
		$fuid=M('Huiyuan','gz_',otherdb())->where($w)->find();
		$uid=$fuid['id'];
		if($uid){
			//登录成功
			$ldata['loginsalt']=md5(md5($this->get_passwordssss(18)).$uid);
			$lw['id']=$uid;
			$lok=M('Huiyuan','gz_',otherdb())->where($lw)->limit(1)->save($ldata);
		
			setcookie("user", $uid, time()+3600000,'/');
			setcookie("lsalt", $ldata['loginsalt'], time()+3600000,'/');
				header('Location:/index.php?s=/addon/WeiSite/WeiSite/Meber_service/cate_id/38.html');
				exit();
		}
	}
	public function sinano(){
		//echo 'dasfasdfd';exit;
		$appid=$_GET['appid']+0;
		$w['id']=$appid;
		if($appid){

			$f=M('loginapptoken','gz_',otherdb())->where($w)->find();
			if($f['token']==$_SESSION['login_app_token']){
				if($f['sinauid']){
				
					$_SESSION['sinauid']=$f['sinauid'];
					$_SESSION['sinatx']=$f['sinatx'];
					$_SESSION['sinaname']=$f['sinaname'];
					$url='/index.php?s=/addon/Login/Login/bdsian.html';
					
						$xgmm['sinauid']=$f['sinauid'];
						$xgmm['sinatx']=$f['sinatx'];
						$xgmm['sinaname']=$f['sinaname'];
					
				}else if($f['qquid']){
					
					$_SESSION['qquid']=$f['qquid'];
					$_SESSION['qqname']=$f['qqname'];
					$_SESSION['qqtx']=$f['qqtx'];
					$url='/index.php?s=/addon/Login/Login/bdqq.html';
					
					
						$xgmm['qquid']=$f['qquid'];
						$xgmm['qqname']=$f['qqname'];
						$xgmm['qqtx']=$f['qqtx'];
					
				}
				$uid=$_SESSION['order_ok_wx_uid'];
				if($uid){
					$lw['id']=$uid;
					$lok=M('Huiyuan','gz_',otherdb())->where($lw)->limit(1)->save($xgmm);
					$url='/index.php?s=/addon/Login/Login/bdqq.html';
					
				}
				
				/*echo $url;
				exit;*/
				header('Location:'.$url);
				exit();
			}
		}
	}
	//随机字符串
	public function get_passwordssss( $length = 8 ){
		$str = substr(md5(time()), 0, 6);
		return $str;
	}
}
