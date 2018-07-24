<?php

namespace Addons\MemberCenter\Controller;
use Home\Controller\AddonsController;

class MemberCenterController extends AddonsController{
	protected $connection = 'DB_CONFIG1';
	function _initialize() {

		parent::_initialize ();
		$config = getAddonConfig ( 'WeiSite' );
		$this->assign ( 'config', $config );
		$public_info = get_token_appinfo ();
		$this->assign('site_title', '广羽人微官网');
		$this->assign ( 'normal_tips', addons_url ( 'WeiSite://WeiSite/index', array ('publicid' => $public_info ['id'])));
		$this->weisite_category   = M('Weisite_category'); //栏目表
		$this->custom_reply_news  = M('Custom_reply_news'); //文章表
		$this->goodsweixin        = M('Goodsweixin');
		$this->ordernew           = M('Ordernew');
		$this->cartwx             = M('Cartwx');
		$this->huiyuans           = M('Huiyuan','gz_',otherdb()); 
		$this->collection = M('Collection','gz_',otherdb());
		$this->dizhiwx    = M('Dizhi','gz_',otherdb());
		$this->assign ('goods_nav',$this->goods_class());
		
		//2017.01.16
		//获取网站配置
		$pall=getAddonConfig('Cpeizhi','gh_2777dd461b24');
		// print_r($pall);die;
		$this->assign ('pall',$pall);
		//dump($pall);exit;
		
	}
	function index() {
		//登录检测
		$login=json_decode($this->tologin(),true);
		return $login;
		// dump($login);
		//头像		GZ_USERAPITOKENURL.'/shearphoto_file/14793720385134_big.jpg'
	}
	function is_login(){
		$get_id=$this->huiyuans->where(array('id'=>$_COOKIE['user'],'loginsalt'=>$_COOKIE['lsalt']))->getField('id');
		return $get_id;
	}
	//修改会员资料
	function uinfocc(){
		//登录检测
		$login=$this->index();
		$url=GZ_USERAPITOKENURL.'index.php?g=Home&m=Infoucc&a=index';
		if($_POST){$_GET['mm']='b';}
		$datad=array(
			'id'=>$login['id'],
			'loginsalt'=>$_COOKIE['lsalt'],
			'mm'=>$_GET['mm']
		);

		//基本资料 $_POST['mm']=='b'
		if($_GET['mm']=='b'){
			$datab=array(
				'nicheng'=>$_POST['nicheng'],//昵称
				'name'=>$_POST['name'],//真实姓名
				'sex'=>$_POST['sex'],//性别
				'srn'=>$_POST['srn'],//生日年
				'sry'=>$_POST['sry'],//生日月
				'srr'=>$_POST['srr'],//生日日
				'szds'=>$_POST['szds'],//所在地省
				'szdsq'=>$_POST['szdsq'],//所在地市
				'szdq'=>$_POST['szdq'],//所在地区
				'jxs'=>$_POST['jxs'],//家乡省
				'jxss'=>$_POST['jxss'],//家乡市
				'jxq'=>$_POST['jxq']//家乡区
			);
			$data=array_merge($datad,$datab);
		}else if($_GET['mm']=='p' && $_GET['passold'] && $_GET['passnew']){
			//修改密码 $_POST['mm']=='p'
			$datap=array(
				'passold'=>$_GET['passold'],//旧密码
				'passnew'=>$_GET['passnew'] //新密码
			);
			$data=array_merge($datad,$datap);
		}
		//dump($data);
		if(!empty($data))  //echo $this->gzpostData($url,$data); // sok 则修改成功
		if($this->gzpostData($url,$data)=='sok'){
			if($_GET['mm']=='p'){
				$this->redirect('addon/Login/Login/index');
			}else{
				
				$this->redirect('addon/MemberCenter/MemberCenter/Personal_data');
				
			}
		}else{
			$this->redirect('addon/MemberCenter/MemberCenter/Personal_edit');
		}
		exit;
	}
	//修改会员资料2017011820
	function xiugazl(){
		$uid=$this->is_login();

		if($uid){
		$get_tel_id=$this->huiyuans->where(array('tel'=>$_POST['tel']))->getField('id');
		$get_email_id=$this->huiyuans->where(array('email'=>$_POST['email']))->getField('id');
		// 通过id 判断是否同一个用户
		if($get_tel_id!=$uid){$data['xgxx']=4;$this->ajaxReturn($data);return false;}        //号码检测有无相同
		if($get_email_id!=$uid){$data['xgxx']=5;$this->ajaxReturn($data);return false;}   //邮箱检测有无相同
			$datab=array(
					'nicheng'=>$_POST['nicheng'],                 //昵称
					'name'=>$_POST['name'],                      //真实姓名
					'tel'=>$_POST['tel'],                        //电话
					'email'=>$_POST['email'],                    //邮箱
					'sex'=>$_POST['sex'],                        //性别
					'srn'=>$_POST['srn'],                        //生日年
					'sry'=>$_POST['sry'],                        //生日月
					'srr'=>$_POST['srr'],                        //生日日
					'szds'=>$_POST['szds'],                      //所在地省
					'szdsq'=>$_POST['szdsq'],                    //所在地市
					'szdq'=>$_POST['szdq'],                      //所在地区
					'jxs'=>$_POST['jxs'],                        //家乡省
					'jxss'=>$_POST['jxss'],                      //家乡市
					'jxq'=>$_POST['jxq'],                        //家乡区
				);
			if($this->huiyuans->where(array('id'=>$uid))->save($datab)){
				$data['xgxx']=1;
			}else{
				$data['xgxx']=2;
			}
		}else{
			$data['xgxx']=3;
		}

		$this->ajaxReturn($data);
	}

	//常见问题
    function problem(){
    	$cate_id=I('get.cate_id')+0;
    	$condition['is_show']=array('eq',1);
    	$condition['id']=array('eq',$cate_id);
    	$get_lanmu=$this->weisite_category->where($condition)->field('id,title')->find();
    	$where['cate_id']=$get_lanmu['id'];
    	$this->assign('cj_content',$this->custom_reply_news->where ($where)->order ( 'sort asc, id desc' )->limit(10)->field('id,title,content')->select());
		$this->assign('get_lanmu',$get_lanmu);
		$this->assign('bodycss','padding-bottom:0.5rem;');
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/problem.html' );
	}
	//加入收藏
	function  addcollection(){
		$get_id=I('post.good_id')+0;
		$loginid=$this->is_login();
		if($loginid){
			$condition['uid']=array('eq',$loginid);
			$condition['sid']=array('eq',$get_id);
			$condition['type']=array('eq',3); //1,pc；
			if($this->collection->where($condition)->getField('id')){
				$data['addc']=3;
				unset($condition);
			}else{
				$addsc['uid']=$loginid;
				$addsc['sid']=$get_id;
				$addsc['type']=3; //1,pc； 2,手机；3微信；
				$addsc['ftime']=time();
				if($this->collection->add($addsc)){
					$data['addc']=1;
				}
			}
			
		}else{
			$data['addc']=2;
		}
		$this->ajaxReturn($data);
	}
	//我的收藏
	function collection(){
		$login=$this->index();
		$mycollection=$this->collection->where(array('uid'=>$login['id'],'type'=>3))->order('id desc')->select();
		foreach ($mycollection as $key => $value) {
			$mycollection[$key]['goods']=$this->goodsweixin->where(array('id'=>$value['sid']))->field('cover,id,title,price')->find();
		}
		$this->assign('mycollection',$mycollection);
		$this->assign('bodycss','padding-bottom:0.5rem; background-color:#F4F4F4;');
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/collection.html' );
	}
	// 删除收藏
	function delcollection(){
		$get_id=I('post.get_id');
		$loginid=$this->is_login();
		if($loginid){
			$condition['uid']=array('eq',$loginid);
			$condition['id']=array('eq',$get_id);
			if($this->collection->where($condition)->delete()){
				$data['del']=1;
			}	
		}else{
			$data['del']=2;
		}
		$this->ajaxReturn($data);
	}
	//收货地址
	function address(){
		$login=$this->index();
		
		$tihsdz=$this->dizhiwx->where(array('uid'=>$login['id'],'moren'=>1))->find();
		if($tihsdz){
			$tihsdz['xx']=$tihsdz['sf'].$tihsdz['shi'].$tihsdz['qy'].$tihsdz['xx'];
			$condition['id']=array('neq',$tihsdz['id']);
		}
		$condition['uid']=array('eq',$login['id']);
		$newdzwx=$this->dizhiwx->where($condition)->order('id desc')->select();
		unset($condition);
		foreach ($newdzwx as $key => $value) {
			$newdzwx[$key]['xx']=$value['sf'].$value['shi'].$value['qy'].$value['xx'];
		}
		$this->assign('user',$login);
		$this->assign('tihsdz',$tihsdz); 
		$this->assign('newdzwx',$newdzwx); 
		$this->assign('bodycss','padding-bottom:2.5rem; background-color:#F4F4F4;');
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/address.html' );
	}
	//新地址页面
	function newsaddress(){
		$get_wdzid=I('get.wdzid')+0;
		$login=$this->index();
		if($get_wdzid&&$login){
			$condition['id']=array('eq',$get_wdzid);
            $condition['uid']=array('eq',$login['id']);
            $this->assign('newdzxgxx',$this->dizhiwx->where($condition)->find()); 
            unset($condition);
		}
		$this->assign('bodycss','padding-bottom:50px; background-color:#F4F4F4;');
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/newsaddress.html' );
	}
	// 添加新地址
	function addwxdizhi(){
		$loginid=$this->is_login();
		if($loginid){
			if($this->dizhiwx->where(array('uid'=>$loginid))->count()<10) {
                $this->dizhiwx->where(array('uid'=>$loginid))->setField('moren',0);
                $dzwx['uid']=$loginid;
                $dzwx['tel']=I('post.tel')+0;        //电话
                $dzwx['xx']=I('post.xiangxidz');  //详细地址
                $dzwx['xm']=I('post.uname');      //收货人姓名
                $dzwx['yb']=I('post.youbian')+0;//邮编
                $szdq=I('post.szdq');
                $szdq=explode(' ',$szdq);
                $dzwx['sf']=$szdq[0]?$szdq[0]:'';
                $dzwx['shi']=$szdq[1]?$szdq[1]:'';
                $dzwx['qy']=$szdq[2]?$szdq[2]:'';
                $dzwx['moren']=1;
                if( $this->dizhiwx->add($dzwx)){
                    $data['xdzwx']=1;
                }else{
                    $data['xdzwx']=2;
                }
            }else{
                $data['xdzwx']=4;
             } 
        }else{
            $data['xdzwx']=3;
        }

		$this->ajaxReturn($data);
	}
	//删除收货地址
	function deldizhiwx(){
		$get_did=I('post.did')+0;
       	$loginid=$this->is_login();
        if($loginid){
            $condition['uid']=array('eq',$loginid);
            $condition['id']=array('eq',$get_did);
            if($this->dizhiwx->where($condition)->delete()){
                $data['dlwx']=1;

            }else{
                $data['dlwx']=2;
            } 
        }else{
            $data['dlwx']=3;
        }
       
        $this->ajaxReturn($data);
	}
	//修改收货地址
    public function xiugaidzwx(){
       	$loginid=$this->is_login();
        if($loginid){
            $this->dizhiwx->where(array('uid'=>$loginid))->setField('moren',0);
            $gid=I('post.gid')+0;
            $dz['tel']=I('post.tel');        //电话
            $dz['xx']=I('post.xiangxidz');  //详细地址
            $dz['xm']=I('post.uname');      //收货人姓名
            $dz['yb']=I('post.youbian')+0;  //邮编
            $szdq=I('post.szdq');
            $szdq=explode(' ',$szdq);
            $dz['sf']=$szdq[0]?$szdq[0]:'';
            $dz['shi']=$szdq[1]?$szdq[1]:'';
            $dz['qy']=$szdq[2]?$szdq[2]:'';
           
            $dz['moren']=1;
            $condition['uid']=array('eq',$loginid);
            $condition['id']=array('eq',$gid);
            if($this->dizhiwx->where($condition)->save($dz)){
                unset($condition);
                $date['is']=1;
            }else{
                $date['is']=2;
            } 
        }else{
           $date['is']=3; 
        }
        
        $this->ajaxReturn($date);
    }

    //地址列表
    function dizhilistwx(){
       	$login=$this->index();
        $dzwxlist=$this->dizhiwx->where(array('uid'=>$login['id']))->order('id desc')->select();
        foreach ($dzwxlist as $key => $value) {
        	$dzwxlist[$key]['xx']=$value['sf'].$value['sf'].$value['qy'].$value['xx'];
        }
        $this->assign('dzwxlist',$dzwxlist);
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/dizhilistwx.html' );
	}
	//更换地址
    public function ghdzwx(){
        $get_id=I('dids')+0;
        $loginid=$this->is_login();
        if($loginid){
            $this->dizhiwx->where(array('uid'=>$loginid))->setField('moren',0);
            if($this->dizhiwx->where(array('uid'=>$loginid,'id'=>$get_id))->setField('moren',1)){
                $date['gdzl']=1;
            }
        }else{
        	$date['gdzl']=2;
        }
        $this->ajaxReturn($date);
    }
	//充值
	function recharge(){
		$login=$this->index();
		$user=$this->huiyuans->where(array('id'=>$login['id']))->find();
		//记录
		$myjl=M('Hyxfjl','gz_',otherdb())->where(array('uid'=>$login['id']))->order('id desc')->select();
		foreach ($myjl as $key => $value) {
			$myjl[$key]['ctime']=date('Y-m-d',$value['ctime']);
		}
		$this->assign('myjl',$myjl);
		$this->assign('user',$user);
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/recharge.html' );
	}
	// 充值下一步
	function recharge_next(){
		$login=$this->index();
		$user=$this->huiyuans->where(array('id'=>$login['id']))->find();
		$this->assign('user',$user);
		$this->assign('bodycss',"padding-bottom:0.5rem; background-color:#F4F4F4;");
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/recharge_next.html' );
	}
	//提交充值
	function ajax_recharge(){
		session_start();
		$loginid=$this->is_login();
		if($loginid){
			$_SESSION['order_ok_wx_uid']=$loginid;
			$_SESSION['jine']=I('post.jine')+0; //充值金额
			if($_SESSION['order_ok_wx_uid']&&$_SESSION['jine']){
				$data['wxcz']=1;
			}else{
				$data['wxcz']=2;
			}
		}else{
			$data['wxcz']=3;
		}
		$this->ajaxReturn($data);
	}

	//加入购物车
	function cart_add(){
		$loginid=$this->is_login();
		$this->orderterm();
		if($loginid){
			$get_good_id=I('post.good_id')+0;
			$get_cm=I('post.cm');
			$get_ys=I('post.ys');
			$get_gkc=I('post.gkc')+0;
			$get_mun=I('post.get_mun');
			$get_good=$this->goodsweixin->where(array('id'=>$get_good_id))->find();
			$condition['product_id']= array('eq',$get_good_id);
			$condition['userid']	= array('eq',$loginid);
			$condition['type']	= array('eq',0);
			$condition['cm']	= array('eq',$get_cm);
			$condition['ys']	= array('eq',$get_ys);
			$condition['type']	= array('eq',0);
			$kus=$this->thisku($get_good['id'],$get_cm,$get_ys);
			//查询库存
			if($kus>=$get_mun){
				//判断购物车有没有相同数据
				if($this->cartwx->where($condition)->find()){
					//如果存在相同订单直接修改数量
					if($this->cartwx->where($condition)->setInc('number',$get_mun)){
						
						$vis=$this->kubd($get_ys,$get_cm,$get_good['kc'],$kus,$get_mun,$get_good_id,2);
						if($vis==1){
							$date['is']=1;
					}
					}else{
						$date['is']=2;
					}
				}else{
					if($get_good){
						$data['product_id']=$get_good['id'];
						$data['userid']=$loginid;
						$data['product_price']=$get_good['price'];
						$data['product_thumb']=$get_good['cover'];
						$data['product_name']=$get_good['title'];
						$data['number']=$get_mun;
						$data['cm']=$get_cm;
						$data['ys']=$get_ys;
						$data['add_time']=time();
						//订单号
						//$ddh=$userid.$get_id.$get_good['price'].time().rand(1000,9999);

						if($this->cartwx->add($data)){
							//获取库存数量
							$kus=$this->thisku($get_good['id'],$get_cm,$get_ys);
							$vis=$this->kubd($get_ys,$get_cm,$get_good['kc'],$kus,$get_mun,$get_good['id'],2);
							if($vis==1){
								$date['is']=1;
								$date['thiskcs']=$this->thisku($get_good_id,$get_cm,$get_ys);

							}
						}else{
							$date['is']=2;
						}
					}
				}
				}else{$date['is']=4; $date['thiskcs']=$kus;}

		}else{
			$date['is']=3;
		}
		$this->ajaxReturn($date);
	}
	//购物车
	function shopping(){
		$login=$this->index();
		$condition['userid']	= array('eq',$login['id']);
		$condition['type']	= array('eq',0);
		$get_carts=$this->cartwx->where($condition)->select();
		foreach ($get_carts as $key => $value) {
			$get_carts[$key]['kcs']=$this->thisku($value['product_id'],$value['cm'],$value['ys'])+$value['number'];
			$get_carts[$key]['gcprice']=$this->goodsweixin->where(array('id'=>$value['product_id']))->getField('cprice');
			$get_carts[$key]['gprice']=$this->goodsweixin->where(array('id'=>$value['product_id']))->getField('price');
	        $sum += ($get_carts[$key]['gprice'] * $value['number']);
	    }
	   	$this->assign('sum',sprintf("%.2f", $sum));
	    $this->assign('sumcount',count($get_carts));
	    $this->assign('bodycss','padding-bottom:0.5rem; background-color:#F4F4F4;');
		$this->assign('get_carts',$get_carts);
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/shopping.html' );
	}
	//删除购物车的商品
	function del_cart(){
		$get_id=I('post.get_id');
		$loginid=$this->is_login();
		if($loginid){
			$get_cart=$this->cartwx->where(array('id'=>$get_id))->find();
			$get_good=$this->goodsweixin->where(array('id'=>$get_cart['product_id']))->find();
			$condition['userid']= array('eq',$loginid);
			$condition['id']= array('eq',$get_id);
			//排除已经提交订单的，以免重复
			if($get_cart['type']!=2&&$get_cart['number']){
				$kus=$this->thisku($get_cart['product_id'],$get_cart['cm'],$get_cart['ys']);
				$vis=$this->kubd($get_cart['ys'],$get_cart['cm'],$get_good['kc'],$kus,$get_cart['number'],$get_good['id'],1);
				if($vis&&$this->cartwx->where($condition)->delete()){	
					$date['del']=1;
				}else{

					$date['del']=2;			
				}
			}else{
				if($this->cartwx->where($condition)->delete()){	
					$date['del']=1;
				}else{

					$date['del']=2;			
				}
			}
			
									
		}else{
			$date['del']=3;
		}
		
		$this->ajaxReturn($date);
	}
	//结算  提交至订单表
	function ajax_js(){
		$loginid=$this->is_login();		
		$this->orderterm();
		if($loginid){
			$get_id=I('post.get_id');
			$get_mun=I('post.get_mun');
			$str_id=explode(',',$get_id);
			$str_mun=explode(',',$get_mun);
			$sliang=count($str_id);
			if($sliang){
					$where['userid']=array('eq',$loginid);
					$where['type']=array('eq',1);
					$getype=$this->cartwx->where($where)->field('id')->select();
					if($getype){
						if($this->cartwx->where($where)->setField('type',0)){
							return true;
						}else{
							$date['isjs']=2;
							$this->ajaxReturn($date);
							return false;
						}
						unset($where);
					}
				for ($i=0; $i <$sliang ; $i++) { 
					$condition['userid']=array('eq',$loginid);
					$condition['id']=array('eq',$str_id[$i]);
					$data['number']=$str_mun[$i];
					$data['type']=1;
					$get_cartxx=$this->cartwx->where($condition)->find();
					$get_goodkc=$this->goodsweixin->where(array('id'=>$get_cartxx['product_id']))->getField('kc');
					$kus=$this->thisku($get_cartxx['product_id'],$get_cartxx['cm'],$get_cartxx['ys']);
					if($get_cartxx['number']>$str_mun[$i]){
						$thismun=$get_cartxx['number']-$str_mun[$i];
						$jjtype=1;
					}else{
						$thismun=$str_mun[$i]-$get_cartxx['number'];
						$jjtype=2;
					}
					$vis=$this->kubd($get_cartxx['ys'],$get_cartxx['cm'],$get_goodkc,$kus,$thismun,$get_cartxx['product_id'],$jjtype);
					$this->cartwx->where($condition)->save($data);
					// $date['isjs']=1;
				}
				unset($condition);

				$condition['type']=array('eq',1);
				$condition['userid']=array('eq',$loginid);
				$da_list=$this->cartwx->where($condition)->select();
				unset($condition);
			if($da_list){
				$condition['pay_status']=array('eq',1);
	        	$condition['userid']=array('eq',$loginid);
	        	if($this->ordernew->where($condition)->field('id')->select()){
	        		//过滤数据
	        		if($this->ordernew->where($condition)->setField('pay_status',0)<0){   
	        		
							$date['isjs']=2;  // 失败
							$this->ajaxReturn($date);
			       			return false;
					}
				  

	        	}
	        	  unset($condition);
				foreach ($da_list as $key => $value) {	
					$datadn['userid']=$loginid;
					// $datadn['wfjxx']=$get_fjxx;
					$datadn['shipping_id']=$value['product_id'];
					$datadn['add_time']=time();
					$datadn['shipping_name']=$value['product_name'];
					$datadn['sn']= date('YmdHis',time()).rand(10000,99999);  //订单号
					$get_price=$this->goodsweixin->where(array('id'=>$value['product_id']))->getField('price'); //获取商品价格
					$datadn['amount']=$get_price*$value['number']; //总价格
					$datadn['number']=$value['number']; //数量
					$datadn['ys']=$value['ys']; //商品颜色
					$datadn['cm']=$value['cm']; //商品尺码
					$datadn['pay_status']=1;
					// $datadn['jifen']=$get_jf;
					$alldata [] = $datadn;
					unset($datadn);
				}
				$condition['type']=array('eq',1);
				$condition['userid']=array('eq',$loginid);
				$xg['type']=2;    //已经提交到订单
				// $xg['fjxx']=$get_fjxx;   //附加信息 做记录
				if($this->cartwx->where($condition)->save($xg)&&$this->ordernew->addAll($alldata)){
					$date['isjs']=1;			
				}else{
					$date['isjs']=2;
				}
			
			}else{
				$date['isjs']=2;
			}
		}
		}else{
				$date['isjs']=3;
			}
		$this->ajaxReturn($date);
	}
	  //立即购买
    function buynowwx(){
		$loginid=$this->is_login();	
        if($loginid){
        	$this->orderterm();
        	$get_good_id=I('post.good_id')+0;
        	$where['userid']=array('eq',$loginid);
			$where['pay_status']=array('eq',1);
			$getype=$this->ordernew->where($where)->field('id')->select();
			if($getype){
				if($this->ordernew->where($where)->setField('pay_status',0)<0){
				
					$date['gm']=2;
					$this->ajaxReturn($date);
					return false;
				}
			}
			$get_good=$this->goodsweixin->where(array('id'=>$get_good_id))->find();
			$data['shipping_id']=$get_good['id'];
			$data['userid']=$loginid;
			$data['amount']=$get_good['price'];
			// $data['product_thumb']=$get_good['cover'];
			$data['shipping_name']=$get_good['title'];
			$data['number']=I('post.get_mun')+0;
			$data['cm']=I('post.cm');
			$data['ys']=I('post.ys');
			$data['pay_status']=1;
			$data['add_time']=time();
			$data['sn']= date('YmdHis',time()).rand(10000,99999);  //订单号
			//获取库存数量
			$kus=$this->thisku($get_good['id'],$data['cm'],$data['ys']);
			$vis=$this->kubd($data['ys'],$data['cm'],$get_good['kc'],$kus,$data['number'],$get_good['id'],2);		
			if($this->ordernew->add($data)){
				
				$date['gm']=1;
		
			}else{
				$date['gm']=2;
			}
	            
        	
        }else{
            $date['gm']=3;
        }
        $this->ajaxReturn($date);
    }
    //订单号
    function didanhao(){
        $sn=date('YmdHis',time()).rand(10000,99999);
        if($this->ordernew->where(array('sn'=>$sn))->getField('id')){
            $ddhh=date('YmdHis',time()).rand(10000,99999);
        }else{
               $ddhh=$sn;
            }  
        return $ddhh;
    }
	// 确认订单
	function qrdd(){
		$login=$this->index();
		$condition['pay_status']=array('eq',1);
		$condition['userid']=array('eq',$login['id']);
		$da_list=$this->ordernew->where($condition)->select();
		foreach ($da_list as $key => $value) {
			 $da_list[$key]['product_price']=$this->goodsweixin->where(array('id'=>$value['shipping_id']))->getField('price');
			 $da_list[$key]['product_thumb']=$this->goodsweixin->where(array('id'=>$value['shipping_id']))->getField('cover');
			 $sum += ($da_list[$key]['product_price'] * $value['number']);
		}
		$this->assign('da_list',$da_list);
		$this->assign('sum',$sum);
		$this->assign('user',$login);
		$ddzzwx=$this->dizhiwx->where(array('uid'=>$login['id'],'moren'=>1))->find();
		$ddzzwx['xx']=$ddzzwx['sf'].$ddzzwx['shi'].$ddzzwx['qy'].$ddzzwx['xx'];
		$this->assign('ddzzwx',$ddzzwx);
		$this->assign('bodycss','padding-bottom:0.5rem;');
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/qrdd.html' );
	}
	//成功提交订单
	function cgtjdd(){
		$pall=getAddonConfig('Cpeizhi',DEFAULT_TOKEN);
		$youfei=$pall['youfei']?$pall['youfei']:0;
		$get_tj=I('post.tj');  //做标识
		$loginid=$this->is_login();
		if($get_tj){
			$get_fjxx=I('post.fjxx');  // 备注信息
			$get_jf=I('jifen');  //获取积分
			if($get_jf){
				$get_jf=I('jifen');
			}else{
				$get_jf=0;
			}
			if($loginid){
				$condition['userid']=array('eq',$loginid);
				$condition['pay_status']=array('eq',1);	
				$cgtj['wfjxx']=$get_fjxx;
				$cgtj['jifen']=$get_jf;
				$cgtj['youfei']=$pall['youfei'];
				$cgtj['add_time']=time();
				if($this->ordernew->where($condition)->save($cgtj)){
					$date['is']=1;
				}
				
			}else{
				$date['is']=3;
				

			}
		$this->ajaxReturn($date);
		}
		$condition['userid']=array('eq',$loginid);
		$condition['pay_status']=array('eq',1);
        $news_dd_list=$this->ordernew->where($condition)->select();
        unset($condition);
        foreach ($news_dd_list as $k => $va) {
             $news_dd_list[$k]['gprice']=$this->goodsweixin->where(array('id'=>$va['shipping_id']))->getField('price');
             $sum += ($news_dd_list[$k]['gprice'] * $va['number']);
             $timeqxwx=$va['add_time'];
             $sjifen=$va['jifen'];
        }

        $jifenq=$sjifen?$sjifen/$pall['jifendikou']:0;
         // print_r($pall['jifendk']);die;
        $sum=$youfei+$sum-$jifenq;
        $qxsjwx=$timeqxwx+60*30;
        $this->assign('qxsjwx',$qxsjwx);
		$this->assign('sum',$sum);
		$this->assign('bodycss',"padding-bottom:0.5rem; background-color:#F4F4F4;");
		$ddzzwx=$this->dizhiwx->where(array('uid'=>$loginid,'moren'=>1))->find();
		$ddzzwx['xx']=$ddzzwx['sf'].$ddzzwx['shi'].$ddzzwx['qy'].$ddzzwx['xx'];
		$login=$this->index();
		$this->assign('users',$login);
		$this->assign('ddzzwx',$ddzzwx);
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/cgtjdd.html' );
	}
	//订单查询
	function orders_qry(){
		$login=$this->index();
		$condition['userid']=array('eq',$login['id']);
		$order_list=$this->ordernew->where($condition)->order('id desc')->select();
		foreach ($order_list as $key => $value) {
			$goodimg=$this->goodsweixin->where(array('id'=>$value['shipping_id']))->getField('cover');
			$order_list[$key]['goodimg']=$goodimg;
			$order_list[$key]['goodprice']=$this->goodsweixin->where(array('id'=>$value['shipping_id']))->getField('price');  //获取促销价格
			$order_list[$key]['yprice']=$this->goodsweixin->where(array('id'=>$value['shipping_id']))->getField('cprice');     //获取原价
		}
		$this->assign('bodycss',"padding-bottom:2.5rem; background-color:#F4F4F4;");
		$this->assign('orderlist',$order_list);
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/orders_qry.html' );
	}
	//取消订单
	function quxiaodd(){
		$get_id=I('post.get_id')+0;
		$loginid=$this->is_login();
		if($loginid){
			$condition['id']=array('eq',$get_id);
			$condition['userid']=array('eq',$loginid);
			$get_order=$this->ordernew->where($condition)->field('id,shipping_id,cm,ys,number,pay_status')->find(); //获取订单信息
			if($get_order['pay_status']!=2){
				$kus=$this->thisku($get_order['shipping_id'],$get_order['cm'],$get_order['ys']); //库存
				$get_kc=$this->goodsweixin->where(array('id'=>$get_order['shipping_id']))->getField('kc'); //获取对应商品库存信息
				$vis=$this->kubd($get_order['ys'],$get_order['cm'],$get_kc,$kus,$get_order['number'],$get_order['shipping_id'],1); //修改库存
				if($vis&&$this->ordernew->where($condition)->delete()){
					$date['del']=1;
				}else{
					$date['del']=2;
				}
				
			}else{
				if($this->ordernew->where($condition)->delete()){
					$date['del']=1;
				}else{
					$date['del']=2;
				}
			}
			unset($condition);
		}else{
			$date['del']=3;
		}
		
		$this->ajaxReturn($date);	
	}
	//支付
	function ajax_zhifu(){
		$zfid=I('post.zfid')+0;
		$loginid=$this->is_login();
		$_SESSION['order_ok_wx_uid']=$loginid;
		$_SESSION['jine']='';
		$login=$this->huiyuans->where(array('id'=>$loginid))->find();
		$pall=getAddonConfig('Cpeizhi',DEFAULT_TOKEN);
		$_SESSION['jfdkbeilu']=$pall['jifendikou'];
		if($loginid){
			//进行单个支付时更改pay_status状态 避免出现多个数据 以便实现单个支付
			if($zfid){
				$condition['pay_status']=array('eq',1);
				$condition['userid']=array('eq',$loginid);
				if($this->ordernew->where($condition)->field('id')->select()){
					if($this->ordernew->where($condition)->setField('pay_status',0)<0){
					
						$date['zf']=2;
						$this->ajaxReturn($date);
						return false;					
					}
				
				}
				unset($condition);
					$condition['userid']=array('eq',$loginid);
					$condition['id']=array('eq',$zfid);
					$getorder=$this->ordernew->where($condition)->find();
					$kus=$this->thisku($getorder['shipping_id'],$getorder['cm'],$getorder['ys']); //库存
					if($kus<$getorder['number']){
						$date['zf']=6;
						$this->ajaxReturn($date);
						return false;
					}
					if($this->ordernew->where($condition)->setField('pay_status',1)){
						$date['zf']=1;
					}else{
						$date['zf']=2;
						
					}
				 unset($condition);				
			}else{
				
					$ddzzwx=$this->dizhiwx->where(array('uid'=>$loginid,'moren'=>1))->find();
					$get_dz=$ddzzwx['sf'].$ddzzwx['shi'].$ddzzwx['qy'].$ddzzwx['xx'];
					$condition['pay_status']=array('eq',1);
					$condition['userid']=array('eq',$login['id']);
					$ordxg['addr']=$get_dz;	 //收货地址
					$get_tel=$ddzzwx['tel']?$ddzzwx['tel']:$login['tel'];
					$get_name=$ddzzwx['xm']?$ddzzwx['xm']:$login['name'];
					$ordxg['tel']=$get_tel;   //联系电话
					$ordxg['shrxm']=$get_name; //收货人姓名
					$ordxg['pay_time']=time(); //支付时间
					if($this->ordernew->where($condition)->save($ordxg)){
						$date['zf']=1;
					}else{
						$date['zf']=2;
					}
			}

		}else{
			$date['zf']=3;
		}
		unset($condition);
		$this->ajaxReturn($date);
	}
	//订单期限
	function orderterm(){
		$thistime=time()-30*60; //当前时间-30分钟
		$condition['add_time']=array('lt',$thistime);
		$condition['pay_status']=array('neq',2);
		$get_order=$this->ordernew->where($condition)->field('id,shipping_id,cm,ys,number')->select();//获取超时的订单信息
		$where['add_time']=array('lt',$thistime);
		$where['type']=array('neq',2);
		$get_cart=$this->cartwx->where($where)->field('id,product_id,ys,cm,number')->select();//获取超时的购物车数据
		//扫订单表
		if($get_order){
			foreach ($get_order as $key => $value) {
			$kus=$this->thisku($value['shipping_id'],$value['cm'],$value['ys']); //库存
			$get_kc=$this->goodsweixin->where(array('id'=>$value['shipping_id']))->getField('kc'); //获取对应商品库存信息
			$vis=$this->kubd($value['ys'],$value['cm'],$get_kc,$kus,$value['number'],$value['shipping_id'],1); //修改库存
			}
			$this->ordernew->where($condition)->delete();
			unset($condition);
		}
		
		// 扫购物车
		if($get_cart){
			foreach ($get_cart as $k => $val) {
			$kus_cart=$this->thisku($val['product_id'],$val['cm'],$val['ys']); //库存
			$get_kc_cart=$this->goodsweixin->where(array('id'=>$val['product_id']))->getField('kc'); //获取对应商品库存信息
			$vis=$this->kubd($val['ys'],$val['cm'],$get_kc_cart,$kus_cart,$val['number'],$val['product_id'],1); //修改库存
			}
			$this->cartwx->where($where)->setField('number',0);
       		unset($where);

		}

	}
	//积分
	function points(){
		$login=$this->index();
		
		$this->assign('ulist',$login);
		
		$jlist=M('jifenjilu','gz_',otherdb())->where(array('uid'=>$login['id']))->order('id desc')->select(); 
		//dump($jlist); //
		$this->assign('jlist',$jlist);
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/points.html' );
	}
	
	//个人资料
	function personal_data(){
		$login=$this->index();
		$this->assign('grzl',$login);
		$this->assign('bodycss','padding-bottom:0.5rem; background-color:#F4F4F4;');
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/personal_data.html' );
	}
	//修改个人资料
	function personal_edit(){
		$login=$this->index();
		$this->assign('grzl',$login);
		$this->assign('bodycss','padding-bottom:0.5rem; background-color:#F4F4F4;');
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/personal_edit.html' );
	}
	//产品与质量
	function cpyzl(){
		$cate_id=I('get.cate_id');
		$where['id']=array('eq',$cate_id);
		$this->assign('get_cpyzl_name',$this->weisite_category->where($where)->getField('title'));
		$condition['cate_id']=array('eq',$cate_id);
		$this->assign('cpzl_content',$this->custom_reply_news->where($condition)->order ( 'sort asc, id desc' )->limit(2)->select());
		$this->assign('bodycss','padding-bottom:0.5rem;');
		$this->display ( ONETHINK_ADDON_PATH . 'MemberCenter/View/default/MemberCenter/cpyzl.html' );
	}
	//单页内容
	function dynr($cate_id){
		$wo['cate_id']=$cate_id;
		M ( 'custom_reply_news' )->where ( $wo )->setInc('view_count');  //点击
		$co = M ( 'custom_reply_news' )->where ( $wo )->order ( 'sort asc, id desc' )->limit(1)->select ();
		return $co['0'];
	}
	//上下篇
	function sxp($id,$cate_id){
		$cs=M( 'custom_reply_news' )->where ( 'id<'.$id.' and cate_id='.$cate_id )->order('id desc')->limit(1)->select ();
		$cx=M( 'custom_reply_news' )->where ( 'id>'.$id.' and cate_id='.$cate_id )->limit(1)->select ();
		$c['0']=$cs['0'];
		$c['1']=$cx['0'];
		return $c;
	}
	//商品导航栏
	function goods_class(){
		$condition['fenlei']=array('eq',1);
		$condition['pid']=array('eq',0);
		$get_goods=$this->weisite_category->where ( $condition )->order ( 'sort asc, id desc' )->limit(5)->select ();
		foreach ($get_goods as $key => $value) {
			$where['pid']=array('eq',$value['id']);
			$get_subgoods=$this->weisite_category->where ($where)->order ( 'sort asc, id desc' )->limit(5)->select ();
			 if($get_subgoods){
                $get_goods[$key]['subclass'] = $get_subgoods;
            }else{
                unset($get_goods[$key]['subclass']);
            }
		}
		return $get_goods;
	}
	/**
	 * 库存变动
	 *$get_ys       颜色
	 *$get_cm       尺码
	 *$get_gkc      库存
	 *$gkc_number   库存数量
	 *$get_number   购买数量
	 *$get_id 		对应商品id
	 *$get_type     类型($get_type=1,加回;$get_type=2,减去)
	*/
	function kubd($get_ys,$get_cm,$get_gkc,$gkc_number,$get_number,$get_id,$get_type=2){
		//2016.12.01
		//原来颜色 尺寸 库存
		$old_kc=$get_ys.'lizhenqiu'.$get_cm.'lizhenqiulizhenqiu'.$gkc_number;
		//新库存
		if($get_type==2){
			$new_kc_num=($gkc_number-$get_number)+0;
		}else{
			$new_kc_num=($gkc_number+$get_number)+0;
		}
		if($new_kc_num<0) $new_kc_num=0;
		$new_kc=$get_ys.'lizhenqiu'.$get_cm.'lizhenqiulizhenqiu'.$new_kc_num;
		$kc_data['kc']=str_ireplace($old_kc,$new_kc,$get_gkc);
		//更新库存
		if($this->goodsweixin->where(array('id'=>$get_id))->save($kc_data)){
			return 1;
		}
		
		
		
	}
	/**
	 * 获取对应商品的库存  库存格式->白色lizhenqiuMlizhenqiulizhenqiu998
	 *$get_ys       颜色
	 *$get_cm       尺码
	 *$get_id 		对应商品id
	*/
	function thisku($get_id,$get_cm,$get_ys){
		$get_kc=$this->goodsweixin->where(array('id'=>$get_id))->getField('kc');
		$yanseee=explode('moweilin',$get_kc);
		 foreach($yanseee as $ks=>$vs){
		    $ysssaaa=explode('lizhenqiu',$vs);
		    $yansearr[]=$ysssaaa['0'];
		    $yuccmmck=$yuccmmck?$yuccmmck.'-'.$ysssaaa['0'].$ysssaaa['1'].'-'.$ysssaaa['3']:$ysssaaa['0'].$ysssaaa['1'].'-'.$ysssaaa['3'];
		  }
		  $yuccmmck=explode('-',$yuccmmck);
			foreach($yuccmmck as $kk=>$vv){
				$s[]=$vv;
			}
		  $yansearr=(array_unique($yansearr));
		  $str=$get_ys.$get_cm;

		  $tqjz=array_search($str,$s);
		  $k=$tqjz+1;
		  $ku=$s[$k];
		  return $ku;
	}
	//微信支付 2017.01.16
	function weixin_zf(){
		//echo 'dsafdas';exit;
		$uid=$this->is_login();
		//dump($_GET);
		//充值金额
		$jine=I('get.jine',0)+0;
		if(!$jine) exit;
		$_SESSION['jine']=$jine;
		$_SESSION['order_ok_wx_uid']=$uid;
		
		header('Location:/wx/example/jsapi.php');
		exit;
	}

	//余额支付
	function yezf(){
		$uid=$this->is_login();
		$pall=getAddonConfig('Cpeizhi',DEFAULT_TOKEN);
		//dump($pall);exit; jifendikou
		if($uid){
			$get_pricess=I('post.yeprices')+0;
			
			if(!$get_pricess) exit;
			$huiyuanxx=$this->huiyuans->where(array('id'=>$uid))->field('yue')->find();  
			$ssye=$huiyuanxx['yue'];  //获取余额 
			$ssjf=$huiyuanxx['jifen'];  //获取积分
			if($get_pricess>$ssye){
				$data['jq']=2;
			}else{
				$condition['pay_status']=array('eq',1);
				$condition['userid']=array('eq',$uid);
				$da_list=$this->ordernew->where($condition)->select();
				foreach ($da_list as $key => $value) {
					
					$get_price=$this->goodsweixin->where(array('id'=>$value['shipping_id']))->getField('price'); //获取商品价格
					$get_jifen=$this->goodsweixin->where(array('id'=>$value['shipping_id']))->getField('jifen'); //获取商品积分
					$all_price+=$get_price*$value['number']; //总价格
					$all_jifen+=$get_jifen*$value['number']; //总积分
					$orderjifne=$value['jifen']; //使用积分
					if($all_id) $all_id.=','.$value['id'];
					else $all_id=$value['id'];
				}
				
					// $jifen=0;//抵扣积分
					$sjifen=$all_jifen;//所有赠送积分
					// print_r($sjifen);die;
					if($orderjifne){
						//积分抵扣
						$sjifen=0;
						$dkje=number_format($orderjifne/$pall['jifendikou'],2); //抵扣金额
						$all_price=$all_price-$dkje;
					}
				// print_r($all_price);die;
				if($all_price<$ssye){
					//更新订单
					$all_w['id']=array('in',$all_id);
					$all_w['uid']=$uid;
					$all_pay_status_s['pay_status']=2;
					$all_pay_status_s['pay_time']=time();  //支付时间
					$uord=$this->ordernew->where($all_w)->save($all_pay_status_s);

					//更新余额
					$uhuiyuanw['id']=$uid;
					$uhuiyuans['yue']=array('exp','yue-'.$all_price);
					$uup=$this->huiyuans->where($uhuiyuanw)->save($uhuiyuans);
					
					//更新积分
					if($orderjifne){ 
						//说明使用积分
						$huiyuanzf['jifen']=array('exp','jifen-'.$orderjifne);
						$dajf['jtype']=2;
						$dajf['uid']=$uid;
						$dajf['ctime']=time();
						$dajf['jifen']=$orderjifne;
						$dajf['laiyuan']='积分抵扣';
					}else{  
						$dajf['jtype']=1;
						$dajf['uid']=$uid;
						$dajf['ctime']=time();
						$dajf['jifen']=$all_jifen;
						$dajf['laiyuan']='赠送';
						$huiyuanzf['jifen']=array('exp','jifen+'.$all_jifen);
					}
					$gxzf=$this->huiyuans->where($uhuiyuanw)->save($huiyuanzf);
					//更新消费记录
					M('jifenjilu','gz_',otherdb())->add($dajf);
					$xf['uid']=$uid;
					$xf['je']=$get_pricess;
					$xf['ctime']=time();
					$xf['gtype']=1;
					$xf['oid']=date('YmdHis',time()).rand(10000,99999);
					M('hyxfjl','gz_',otherdb())->add($xf);
					$data['jq']=1;

				
			}else{
				$data['jq']=4;
			}
		}
		}else{

			$data['jq']=3;
		}
		$this->ajaxReturn($data);
		
		
	}
	//第三方登录
	function dsfdl(){
		$login=$this->index();
        if(!$login){
            
           	$this->redirect('addon/Login/Login/index');
            exit;
        }
		$w['id']=$login['id'];
		$userinfoall=$this->huiyuans->where($w)->find();
		//dump($userinfoall);
		$this->assign('userinfoall',$userinfoall);
        $this->display();
    }
	//解除绑定qq
	function jcbdqq(){
		$uid=$this->index();
        if(!$uid['id']){
           	$this->redirect('addon/Login/Login/index');
            exit;
        }
		$w['id']=$uid['id'];
		$s['qquid']='';
		$s['qqtx']='';
		$s['qqname']='';
		$c=$this->huiyuans->where($w)->save($s);
		$this->redirect('addon/MemberCenter/MemberCenter/dsfdl');
		exit;
	}
	//解除绑定微博
	function jcbdwb(){
		$uid=$this->index();
        if(!$uid['id']){
           	$this->redirect('addon/Login/Login/index');
            exit;
        }
		$w['id']=$uid['id'];
		$s['qquid']='';
		$s['qqtx']='';
		$s['qqname']='';
		$c=$this->huiyuans->where($w)->save($s);
		$this->redirect('addon/MemberCenter/MemberCenter/dsfdl');
		exit;
	}
}
