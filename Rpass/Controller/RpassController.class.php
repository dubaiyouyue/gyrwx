<?php

namespace Addons\Rpass\Controller;
use Home\Controller\AddonsController;

class RpassController extends AddonsController{
	function _initialize() {
		parent::_initialize ();
		$this->huiyuans   = M('Huiyuan','gz_',otherdb()); 
	}
	function index(){
		$login=json_decode($this->tologin(),true);
		$this->assign('users',$login);
		$this->display ( ONETHINK_ADDON_PATH . 'Rpass/View/default/Rpass/index.html' );
	}
	function is_login(){
		$get_id=$this->huiyuans->where(array('id'=>$_COOKIE['user'],'loginsalt'=>$_COOKIE['lsalt']))->getField('id');
		return $get_id;
	}
	function zhanghao(){
			
			$get_haoma=I('get.tel')+0;
			if($this->huiyuans->where(array('tel'=>$get_haoma))->getField('id')){
				
				$this->dxyz();die;
			}else{
				$this->error ('账号不匹配，请重新输入');
				$this->idnex();die;
			}
	}
	//密码找回
	function mmyz(){
		$get_tel=I('get.zhanghao')+0;
		$this->assign('zhanghao',$get_tel);
		$this->display ( ONETHINK_ADDON_PATH . 'Rpass/View/default/Rpass/mmyz.html' );
	}
	// 手机短信找回
	function dxyz(){
		$get_tel=I('get.zhanghao')+0;
		$this->assign('zhanghao',$get_tel);
		$this->display ( ONETHINK_ADDON_PATH . 'Rpass/View/default/Rpass/dxyz.html' );
	}
	function newmm(){
		$this->display ( ONETHINK_ADDON_PATH . 'Rpass/View/default/Rpass/newmm.html' );
	}
	 function get_passwordssss( $length = 8 ){
        $str = substr(md5(time()), 0, 6);
        return $str;
    }
	function edit_qb(){
		//  "passold=" + passold+"&passnew="+passnew+"&tel="+tel,
		$get_tel=I('post.tel')+0;
		$get_passold=I('post.passold')+0;
		$get_passnew=I('post.passnew')+0;

		 if($get_passold&&$get_tel&&$get_passnew){
		 	$r=$this->huiyuans->where(array('tel'=>$get_tel))->find();
            $salt=$r['salt'];
            $ylpass=$r['pass'];
            $rpass=md5(md5($get_passold).$salt);
           if($ylpass==$rpass){
           		$xgmm['salt'] = $this->get_passwordssss(8);
	            $xgmm['pass'] = md5(md5($get_passnew).$xgmm['salt']); 
	            if($this->huiyuans->where(array('tel'=>$get_tel))->save($xgmm)){
	                $data['xg']=1;
	            }else{
	                $data['xg']=2;
	            }
           }else{
           		$data['xg']=3;
           }
           
            $this->ajaxReturn($data);   
        }
		
	}
	//修改密码
	function xgmima(){
		$this->display ( ONETHINK_ADDON_PATH . 'Rpass/View/default/Rpass/xgmima.html' );
	}
	function edit_mima(){
		$uid=$this->is_login();
		$get_passnew2=I('post.passnew2')+0;
		$get_passold=I('post.passold')+0;
		$get_passnew=I('post.passnew')+0;
		if($get_passnew2!=$get_passnew){
			$data['xg']=5;    //检测密码是否一致
			$this->ajaxReturn($data);   
			return false;
		}
		if(!$uid){$data['xg']=4; $this->ajaxReturn($data);   return false;} //未登录
		 if($get_passold&&$uid&&$get_passnew){
		 	$r=$this->huiyuans->where(array('id'=>$uid))->find();
            $salt=$r['salt'];
            $ylpass=$r['pass'];
            $rpass=md5(md5($get_passold).$salt);
           if($ylpass==$rpass){

           		$xgmm['salt'] = $this->get_passwordssss(8);
	            $xgmm['pass'] = md5(md5($get_passnew).$xgmm['salt']); 
	            if($ylpass== $xgmm['pass']){$data['xg']=7; $this->ajaxReturn($data);   return false;}
	            if($_SESSION['login_app_token']){
					if($_SESSION['sinauid']){
						$xgmm['sinauid']=$_SESSION['sinauid'];
						$xgmm['sinatx']=$_SESSION['sinatx'];
						$xgmm['sinaname']=$_SESSION['sinaname'];
					}else if($_SESSION['qquid']){
						$xgmm['qquid']=$_SESSION['qquid'];
						$xgmm['qqname']=$_SESSION['qqname'];
						$xgmm['qqtx']=$_SESSION['qqtx'];
					}
				}
	            if($this->huiyuans->where(array('id'=>$uid))->save($xgmm)){
	                $data['xg']=1;
	            }else{
	                $data['xg']=2;
	            }
           }else{
           		$data['xg']=3;     //旧密码错误
           }
           
            $this->ajaxReturn($data);   
        }
		
	}
}
