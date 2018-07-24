<?php

namespace Addons\WeiSite\Controller;
use Addons\WeiSite\Controller\BaseController;

class WeiSiteController extends BaseController {
	function _initialize() {

		parent::_initialize ();
		$public_info   = get_token_appinfo ();
		$normal_tips   = addons_url ( 'WeiSite://WeiSite/index', array ('publicid' => $public_info ['id']));
		$this->assign ( 'normal_tips', $normal_tips );
		$this->assign('site_title', '广羽人微官网');
		$this->weisite_category   = M('weisite_category');
		$this->custom_reply_news  = M('custom_reply_news'); //文章表
		$this->weisite_slideshow  = M('Weisite_slideshow');
		$this->goodsweixin        = M('goodsweixin');
		$this->weiliy             = M('weiliy');
		$this->ordernew           = M('Ordernew');
		$this->cartwx             = M('Cartwx');
		$this->assign ('goods_nav',$this->goods_class());
		$this->huiyuans           = M('Huiyuan','gz_',otherdb()); 
		
		
		
	}
	function is_login(){
		$get_id=$this->huiyuans->where(array('id'=>$_COOKIE['user'],'loginsalt'=>$_COOKIE['lsalt']))->getField('id');
		return $get_id;
	}
	function config() {
		$public_info = get_token_appinfo ();
		$normal_tips = '在微信里回复“微官网”即可以查看效果,也可以点击：<a href="' . addons_url ( 'WeiSite://WeiSite/index', array (
				'publicid' => $public_info ['id'] 
		) ) . '">预览</a>， <a id="copyLink" data-clipboard-text="' . addons_url ( 'WeiSite://WeiSite/index', array (
				'publicid' => $public_info ['id'] 
		) ) . '">复制链接</a><script type="application/javascript">$.WeiPHP.initCopyBtn("copyLink");</script>';
		$this->assign ( 'normal_tips', $normal_tips );
		
		$config = D ( 'Common/AddonConfig' )->get ( _ADDONS );
		// dump($config);die;
		if (IS_POST) {
			$_POST ['config'] ['background'] = implode ( ',', $_POST ['background'] );
			// $config = array_merge ( ( array ) $config, ( array ) $_POST ['config'] );
			$flag = D ( 'Common/AddonConfig' )->set ( _ADDONS, $_POST ['config'] );
			if ($flag !== false) {
				if ($_GET ['from'] == 'preview') {
					$url = U ( 'preview' );
				} else {
					$url = Cookie ( '__forward__' );
				}
				$this->success ( '保存成功', $url );
			} else {
				$this->error ( '保存失败' );
			}
			exit ();
		}
		$config ['background_arr'] = explode ( ',', $config ['background'] );
		$config ['background'] = $config ['background_arr'] [0];
		$this->assign ( 'data', $config );
		$this->display ();
	}
	// 首页

	function index() {
		
		// $this->assign('site_title', '首页');
		// add_credit ( 'weisite', 86400 );
		if (file_exists ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_index'] . '.html' )) {
			$this->pigcms_index ();
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_index'] . '.html' );
		} else {
			$map1 ['token'] = $map ['token'] = get_token ();
			$map1 ['is_show'] = $map ['is_show'] = 1;
			$map ['pid'] = 0; // 获取一级分类           
			// 分类
			$category = M ( 'weisite_category' )->where ( $map )->order ( 'sort asc, id desc' )->select ();
			foreach ( $category as &$vo ) {
				$vo ['icon'] = get_cover_url ( $vo ['icon'] );
				empty ( $vo ['url'] ) && $vo ['url'] = addons_url ( 'WeiSite://WeiSite/lists', array (
						'cate_id' => $vo ['id'] 
				) );
			}
	
			$this->assign ( 'category', $category );
			// dump($category);
			// 幻灯片
			$slideimg['cate_id']=array('eq',0);
			$slideimg['is_show']=array('eq',1);
			$slideshow =$this->weisite_slideshow->where ($slideimg)->order ( 'sort asc, id desc' )->select ();
			// dump($slideshow);die;
			unset($slideimg);
			foreach ( $slideshow as &$vo ) {
				$vo ['img'] = get_cover_url ( $vo ['img'] );
			}
			
			foreach ( $slideshow as &$data ) {
				foreach ( $category as $cate ) {
					if ($data ['cate_id'] == $cate ['id'] && empty ( $data ['url'] )) {
						$data ['url'] = $cate ['url'];
					}
				}
			}
			$this->assign('slideshow',$slideshow);
		
			
			// dump($category);
			$map2 ['token'] = $map ['token'];
			$public_info = get_token_appinfo ( $map2 ['token'] );
			$this->assign ( 'publicid', $public_info ['id'] );
			
			$this->assign ( 'manager_id', $this->mid );
			
			//园区介绍
			$yqjssss=$this->dynr('2');
			//dump($yqjssss);
			$this->assign ( 'yqjssss', $yqjssss );
			//园区优势
			$yyssdynrlb=$this->dynrlb(3,0,6);
			$this->assign ( 'yyssdynrlb', $yyssdynrlb );
			//园区新闻
			$yqxxssnrlb=$this->dynrlb(8,0,6);
			$this->assign ( 'yqxxssnrlb', $yqxxssnrlb );
			$this->_footer ();
			// $backgroundimg=ONETHINK_ADDON_PATH.'WeiSite/View/default/TemplateIndex/'.$this->config['template_index'].'/icon.png';
			if ($this->config ['show_background'] == 0) {
				$this->config ['background'] = '';
				
			}

			// 特惠活动
    		$condition['is_show']=array('eq',1);
    		$condition['id']=array('eq',43);
    		$get_lanmus=$this->weisite_category->where($condition)->field('id,title')->find();
    		unset($condition);
			$where['cate_id']=array('eq',$get_lanmus['id']);
			$where['status']=array('eq',1);
    		$this->assign('hd_content', M ( 'custom_reply_news' )->where ($where)->order ( 'sort asc, id desc' )->limit(3)->field('id,title,intro,cover')->select());
			unset($where);
			$this->assign('get_lanmu',$get_lanmus);
			//热销单品
			// $this->assign('hot_goods', $this->product->where (array('status'=>1))->order ( 'hits desc, id desc' )->limit(6)->select());
			$this->assign('hot_goods',$this->goodsweixin->order('view_count desc')->limit(6)->select());
			$this->assign('bodycss','background-color:#F0F0F0;');
			$html ='ColorV1'; //empty ( $this->config ['template_index'] ) ? 'ColorV1' : $this->config ['template_index'];
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/' . $html . '/index.html' );
		}
	}
	//内页头部
	function htoubu(){
	
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/htoubu.html' );
	}
	//底部
	function fdibu(){
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/fdibu.html' );
	}

	//2016.11.22 获取商品分类导航
	function shopfenlei(){
		//获取商品一级分类
		$w['fenlei']=1;
		$r=M ( 'weisite_category' )->where ($w)->order ( 'sort asc, id desc' )->select();
		//dump($r);
	}
	
	
	//关于我们
	function about(){
		$cate_id=I('get.cate_id');
		$getid=I('get.post_id');
		$get_zlm= $this->dqhuozlm($cate_id,3);
		$getid=$getid?I('get.post_id'):$get_zlm[0]['id'];
		$posts=$this->dynr($getid);
		$this->assign ('cate_id', $cate_id);
		$this->assign ('getid', $getid);
		$this->assign ('dynr', $get_zlm);
		$this->assign ('posts',$posts);
		//2016.11.24 获取门店分布
			if($posts['cate_id']==37){
				$url=GZ_USERAPITOKENURL.'index.php?g=Home&m=Mdianfenbuapi&a=index';
				$Mdianfenbuapi=$this->gzpostData($url,$data);
				$this->assign ('Mdianfenbuapi',json_decode($Mdianfenbuapi,true));
			}
		//end
		$this->assign('iconimg',$this->weisite_category->where(array('id'=>31))->getField('icon'));
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/about.html' );
	}
	//会员服务
	function meber_service(){
		$cate_id=I('get.cate_id');
		$mebers='meber_service';
		$uid=$this->is_login();
		$meber_nav=$this->dqhuozlm($cate_id,12);
		foreach ($meber_nav as $key => $value) {
			if($value['url']=='register'){
				if($uid){
					$meber_nav[$key]['url']= addons_url ( 'MemberCenter://MemberCenter/dsfdl'); //路径拼接
				}else{
					$meber_nav[$key]['url']= addons_url ( 'Login://Login/index'); //路径拼接
				}
				
			}else{
				$meber_nav[$key]['url']= addons_url ( 'MemberCenter://MemberCenter/'.$value['url'], array ('cate_id' => $value ['id'])); //路径拼接
			}
			if($value['url']=='activity'){
				$meber_nav[$key]['url']=addons_url ( 'WeiSite://WeiSite/'.$value['url'], array ('cate_id' => $value ['id'])); //路径拼接
			}
		}
		$this->assign('meber_nav',$meber_nav);
		$this->assign('mebers',$mebers);
		$this->assign('iconimg',$this->weisite_category->where(array('id'=>38))->getField('icon'));
		$this->assign('bodycss','padding-bottom:3rem; background:#FFF;');
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/meber_service.html' );
	}
	//最新咨询
	function contact(){
		$cate_id=I('get.cate_id');
		$getid=I('get.post_id');
		$get_zx= $this->dqhuozlm($cate_id,2);
		$getid=$getid?I('get.post_id'):$get_zx[0]['id'];
		$posts=$this->dynrlb($getid,0,5);
		$this->assign('get_max_id',$posts[ count($posts) -1]['id']);
		$this->assign ('cate_id', $cate_id);
		$this->assign ('getid', $getid);
		$this->assign ('dynr', $get_zx);
		$this->assign ('posts',$posts);
		$this->assign('iconimg',$this->weisite_category->where(array('id'=>32))->getField('icon'));
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/contact.html' );
	}
	//ajax更多资询
	public function get_zx_more(){
        $get_zx_id=I('post.zx_id');
        $get_cate_id=I('post.cate_id');
        $get_post_id=I('post.post_id');
        $condition['id']=array('lt',$get_zx_id);
        $condition['cate_id']=array('eq',$get_post_id);
        $date['lists'] = M ( 'custom_reply_news' )->where ( $condition )->order ( 'sort asc, id desc' )->limit(5)->field('id,title,intro,cover,cTime')->select ();
     	if(count($date['lists'])>0){
     		$date['len']=1;
     	}else{
     		$date['len']=0;
     	}
	    foreach ($date['lists'] as $key => $value) {
	        $date['lists'][$key]['cover']=get_cover_url($value['cover']);
	        $date['lists'][$key]['cTime']=time_format($value['cTime'],'Y-m-d');
	        $date['lists'][$key]['urls']=addons_url ( 'WeiSite://WeiSite/Contact_detail',array ('cate_id' =>$get_cate_id,'post_id'=>$get_post_id,'post'=>$value['id']));
	        $date['lists'][$key]['intro']=msubstr($value['intro'],0,24);
	    }
	    $date['max_zx_id']= $date['lists'][count( $date['lists'])-1]['id'];
 
        $this->ajaxReturn($date);
    }
	//详情咨询
	function contact_detail(){
		$cate_id=I('get.cate_id');
		$getid=I('get.post_id');
		$getpost=I('get.post');
		$get_zx= $this->dqhuozlm($cate_id,2);
		$wo['id']=array('eq',$getpost);
		$wo['cate_id']=array('eq',$getid);
		$posts=M ( 'custom_reply_news' )->where ( $wo )->order ( 'sort asc, id desc' )->find();
		$this->assign('sxp',$this->sxp($posts['id'],$posts['cate_id']));
		$this->assign ('cate_id', $cate_id);
		$this->assign ('getid', $getid);
		$this->assign ('dynr', $get_zx);
		$this->assign ('posts',$posts);
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/contact_detail.html' );
	}
	//客服中心
	function customer(){
		$login=$this->login=json_decode($this->tologin(),true);
		$cate_id=I('get.cate_id');
		$khzx=$this->weisite_category->where(array('pid'=>68,'is_show'=>1))->limit(3)->order('sort,id desc')->select();
		$cate_id=$cate_id?$cate_id:$khzx[0]['id'];
		$this->assign('khzx',$khzx);
		$this->assign('user',$login);
		$this->assign('cate_id',$cate_id);
		
		//获取网站配置
		$pall=getAddonConfig('Cpeizhi','gh_2777dd461b24');
		// print_r($pall);die;
		$this->assign ('pall',$pall);
		if($cate_id==69){
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/onlinekf.html' );
		}else{

			$this->assign('iconimg',$this->weisite_category->where(array('id'=>68))->getField('icon'));
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/customer.html' );
		}
		
	}
	//微留言
	function weily(){
		$lognid=$this->is_login();
		$get['lxr']=$_POST['name'];
		$get['dh']=$_POST['tel'];
		$get['nr']=$_POST['content'];
		$get['ctime']=time();
		$get['uid']=$lognid;
		$get['bt']=$_POST['title'];
		if($get['uid']){
			$get_time=$this->weiliy->where(array('uid'=>$get['uid']))->order('id desc')->getField('ctime'); // 获取最后一条留言的时间戳
			$daysjc=86400;  //一天的时间戳
			$dqsj=time()-$get_time;
			if($dqsj>$daysjc){  //限制留言提交一天一次
				if($this->weiliy->add($get)){
					$data['isly']=1;
				}else{
					$data['isly']=2;
				}
			}else{
				$data['isly']=3;
			}		
		}else{
			$data['isly']=4;
		}
		
		$this->ajaxReturn($data);
		
	}
	//商品分类--->导航
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
	// 商品列表
	function goods_list(){
		$wdhot=I('get.wdhot');
		$get_cate_id=I('get.id')+0;
		$get_order=I('get.order');
		$get_dxps=I('get.dxps');
		if($wdhot=='hot'){
			$order='view_count desc';
		}
		if($wdhot!='hot'&&$wdhot){
			$get_cate_id=$wdhot;
		}
		if($get_order){
            $ddqqppxx=$get_order;
                // 商品排序 2016.11.29
                switch ($ddqqppxx){
                    case 'xl':
                        if($get_dxps=='desc'){
                            $ddqorder='zxl desc,view_count desc,id desc';  $dxps='asc'; //总销量排序
                        }else { 
                            $ddqorder='zxl asc,view_count asc,id asc'; $dxps='desc';
                        }
                        break;
                    case 'jg':
                        if($get_dxps=='desc'){
                            $ddqorder='price desc,view_count desc,id desc'; $dxps='asc'; //价格排序
                        }else { 
                            $ddqorder='price asc,view_count asc,id asc'; $dxps='desc';
                        }
                        break;
                    case 'zx':
                        if($get_dxps=='desc') {
                            $ddqorder='id desc,price desc,view_count desc'; $dxps='asc'; //新品排序
                        }else{
                            $ddqorder='id asc,price asc,view_count asc'; $dxps='desc';
                        }
                        break;
                        default:
                        $ddqorder='view_count desc,price asc,id desc';  $dxps='desc';//综合排序
                }
                    $ddqorder=' '.$ddqorder.',';
        }
		$order=$order?$order:"id desc";
		$dxps=$dxps?$dxps:'desc';
		$condition['cover']=array('neq','');
		if($get_cate_id){
			$get_types=$this->weisite_category->where(array('pid'=>$get_cate_id))->getField('id',true);
			if($get_types){
				$strid=array($get_cate_id);
           		$get_types=array_merge($strid,$get_types);
				$condition['cate_id']=array('in',$get_types);
			}else{
				$condition['cate_id']=array('eq',$get_cate_id);
			}
			
		}
		$get_list=$this->goodsweixin->where($condition)->order($ddqorder.$order)->limit(6)->field('id,title,cover,price')->select();
		unset($condition);
		$this->assign('goods',$get_list);
		$this->assign('get_dxps',$get_dxps);
		$this->assign('dxps',$dxps);
		$this->assign('catetype',$get_cate_id);
        $this->assign('get_order',$get_order);
		$this->assign('bodycss','padding-bottom:0.5rem;');
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/goods_list.html' );
	}
	function ajax_goods_list(){
		$pyh=I('post.pyh')+0;
		$get_order=I('post.order');
		$get_dxps=I('post.dxps');
		$get_catetype=I('post.catetype')+0;
		if($get_catetype){
			$get_types=$this->weisite_category->where(array('pid'=>$get_catetype))->getField('id',true);
			if($get_types){
				$strid=array($get_catetype);
           		$get_types=array_merge($strid,$get_types);
				$condition['cate_id']=array('in',$get_types);
			}else{
				$condition['cate_id']=array('eq',$get_catetype);
			}
		}
		$condition['cover']=array('neq','');
        $counts=$this->goodsweixin->where($condition)->count(); //总数
        $number=6;
        $pages=ceil($counts/$number);
        if($pyh>$pages){$pyh=$pages;}
        if($pyh<0){$pyh=0;}
        $p=$pyh*$number;
        	if($get_order){
            $ddqqppxx=$get_order;
                // 商品排序 2016.11.29
                switch ($ddqqppxx){
                    case 'xl':
                        if($get_dxps=='desc'){
                            $ddqorder='zxl desc,view_count desc,id desc';  $dxps='asc'; //总销量排序
                        }else { 
                            $ddqorder='zxl asc,view_count asc,id asc'; $dxps='desc';
                        }
                        break;
                    case 'jg':
                        if($get_dxps=='desc'){
                            $ddqorder='price desc,view_count desc,id desc'; $dxps='asc'; //价格排序
                        }else { 
                            $ddqorder='price asc,view_count asc,id asc'; $dxps='desc';
                        }
                        break;
                    case 'zx':
                        if($get_dxps=='desc') {
                            $ddqorder='id desc,price desc,view_count desc'; $dxps='asc'; //新品排序
                        }else{
                            $ddqorder='id asc,price asc,view_count asc'; $dxps='desc';
                        }
                        break;
                        default:
                        $ddqorder='hits desc,price asc,id desc';  $dxps='desc';//综合排序
                }
                    $ddqorder=' '.$ddqorder.',';
        }

       	$date['lists'] = $this->goodsweixin->where ( $condition )->order ($ddqorder.'id desc')->limit($p,$number)->field('id,title,cover,price')->select ();
	     unset($condition);	
     	if(count($date['lists'])>0){
     		$date['len']=1;
     	}else{
     		$date['len']=0;
     	}
	    foreach ($date['lists'] as $key => $value) {
	        $date['lists'][$key]['thumb']=get_cover_url($value['cover']);
	        $date['lists'][$key]['urls']=addons_url ( 'WeiSite://WeiSite/Goods_detail',array ('id'=>$value['id'])); 
	    }
	    $date['pyh']= $pyh+1; 
		$this->ajaxReturn($date);
	}
	//订单期限 处理超时订单
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
	// 产品详情
	function goods_detail(){
		$get_id=I('get.id');
		$this->orderterm();
		$condition['id']=array('eq',$get_id);
		$this->goodsweixin->where ( $condition )->setInc('view_count');  //点击量
		$good=$this->goodsweixin->where ($condition)->find();
		if($good['pic']){
			$strimg=explode(',',$good['pic']);
	        foreach ($strimg as $key => $value) {
	        	$good['strimg'][$key]=get_cover_url($value);
	        }
		}
     	// print_r($good);die;
        $this->assign('good',$good);
        $this->assign('bodycss','padding-bottom:2.5rem;');
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/goods_detail.html' );
	}
	//个人资料
	function personals(){
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/personals.html' );
	}
	//购物流程
	function gwlc(){
		$cate_id=I('get.cate_id');
		$this->assign('biaoti',$this->weisite_category->where(array('id'=>$cate_id))->getField('title'));
		$where['cate_id']=$cate_id;
		$this->assign('gwlc_content',$this->custom_reply_news->where ($where)->order ( 'sort asc, id desc' )->find());
		$this->assign('bodycss','padding-bottom:0.5rem;');
		$this->assign('cateid',$cate_id);
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/gwlc.html' );
	}
	//优惠活动
	function activity(){
		$cate_id=I('get.cate_id');
    	$condition['is_show']=array('eq',1);
    	$condition['id']=array('eq',$cate_id);
    	$get_lanmu=$this->weisite_category->where($condition)->field('id,title')->find();
		$where['cate_id']=$get_lanmu['id'];
		$hd_content=$this->custom_reply_news->where ($where)->order ( 'sort asc, id desc' )->limit(4)->field('id,title,intro,cover')->select();
		$this->assign('get_max_id',$hd_content[ count($hd_content) -1]['id']);
    	$this->assign('hd_content',$hd_content);
		$this->assign('get_lanmu',$get_lanmu);
		$this->assign('bodycss','padding-bottom:0.5rem; background-color:#F4F4F4;');
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/activity.html' );
	}
	function ajax_activity(){
		$get_hd_id=I('post.max_hu_id');
		$get_cate_id=$this->custom_reply_news->where(array('id'=>$get_hd_id))->getField('cate_id');
		$condition['id']=array('lt',$get_hd_id);
		$condition['cate_id']=array('eq',$get_cate_id);
		$date['lists']=$this->custom_reply_news->where ($condition)->order ( 'sort asc, id desc' )->limit(4)->field('id,title,intro,cover')->select();
		if(count($date['lists'])>0){
     		$date['len']=1;
     	}else{
     		$date['len']=0;
     	}
		foreach ($date['lists'] as $key => $value) {
	        $date['lists'][$key]['cover']=get_cover_url($value['cover']);
	        $date['lists'][$key]['urls']=addons_url ( 'WeiSite://WeiSite/Activity_details',array ('cate_id' =>$get_cate_id,'post_id'=>$value['id'])); 
	    }
	    $date['max_zx_id']= $date['lists'][count( $date['lists'])-1]['id'];
		$this->ajaxReturn($date);
	}
	//详情页
	function activity_details(){
		$get_id=I('get.post_id');
		$cate_id=I('get.cate_id');
		$where['id']=array('eq',$cate_id);
		$this->assign('get_lanmu_name',$this->weisite_category->where($where)->getField('title'));
		$condition['cate_id']=array('eq',$cate_id);
		$condition['id']=array('eq',$get_id);
		$hd_details=$this->custom_reply_news->where($condition)->order ( 'sort asc, id desc' )->find();
		$this->assign('sxpian',$this->sxp($hd_details['id'],$hd_details['cate_id']));
		$this->custom_reply_news->where ($condition)->setInc('view_count');  //点击
		$this->assign('cate_id',$hd_details['cate_id']);
		$this->assign('hd_details',$hd_details);
		$this->assign('bodycss',"padding-bottom:0.5rem; background-color:fff;");
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/activity_details.html' );
	}
	//获取当前栏目的子栏目
	function dqhuozlm($pid,$limit=1){
		$wt['pid']=$pid;
		$ct=M ( 'weisite_category' )->where ( $wt )->order ( 'sort asc, id desc' )->limit($limit)->select ();
		return $ct;
	}
	//获取子栏目
	function huozlm($id){
		$wo['id']=$id;
		$co = M ( 'weisite_category' )->where ( $wo )->order ( 'sort asc, id desc' )->limit(1)->select ();
		$copid=$co['0']['pid'];
		$wt['pid']=$copid;
		$ct=M ( 'weisite_category' )->where ( $wt )->order ( 'sort asc, id desc' )->select ();
		return $ct;
	}
	//当前栏目seo
	function dzlm($id){
		$wo['id']=$id;
		$co = M ( 'weisite_category' )->where ( $wo )->order ( 'sort asc, id desc' )->limit(1)->select ();
		return $copid=$co['0'];
	}
	//单页内容
	function dynr($cate_id){
		$wo['cate_id']=$cate_id;
		M ( 'custom_reply_news' )->where ( $wo )->setInc('view_count');  //点击
		$co = M ( 'custom_reply_news' )->where ( $wo )->order ( 'sort asc, id desc' )->limit(1)->select ();
		return $co['0'];
	}
	//列表页内容
	function dynrlb($cate_id,$p,$m){
		$wo['cate_id']=$cate_id;
		$co = M ( 'custom_reply_news' )->where ( $wo )->order ( 'sort asc, id desc' )->limit(($p*$m),$m)->select ();

		return $co;
	}
	//获取更多
	function hqgddd(){
		$p=I('get.p');
		$m=6;
		$th=I('get.th');
		$cate_id=I('get.cate_id');
		$list=$this->dynrlb($cate_id,$p,$m);
		$this->assign('list', $list);
		//dump($list);
		if(empty($list)) exit('no');
		else $this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/'.$th.'.html' );
	}
	//内容
	function dynrid($id){
		$wo['id']=$id;
		$co = M ( 'custom_reply_news' )->where ( $wo )->order ( 'sort asc, id desc' )->limit(1)->select ();
		return $co['0'];
	}

	// 搜索
	function searchs(){
		$get_name=I('get.wds');
		$condition['title']=array('like','%'.$get_name.'%');
        $counts=$this->goodsweixin->where($condition)->count();
        $seach_list=$this->goodsweixin->where($condition)->order('id desc')->limit(10)->select();
        $this->assign('sname',$get_name);
        $this->assign('goodlist',$seach_list);
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/searchs.html' );
	}
	function moresearch(){
		$get_name=I('post.name');
		$get_p=I('post.pse')+0;
		$pagep=10;
        $condition['title']=array('like','%'.$get_name.'%');
        $counts=$this->goodsweixin->where($condition)->count();
        $pages=ceil($counts/$pagep);
        if($get_p>$pages){$listpss=$pages;}
        if($get_p<0){$get_p=0;}
        $p=$get_p*$pagep;
        $data['lists']=$this->goodsweixin->where($condition)->order('id desc')->limit($p,$pagep)->select();
        foreach ($data['lists'] as $key => $value) {
        	$data['lists'][$key]['thumb']=get_cover_url($value['cover']);
	        $data['lists'][$key]['urls']=addons_url ( 'WeiSite://WeiSite/Goods_detail',array ('id'=>$value['id'])); 
        }
        if(count($data['lists'])>0){$data['len']=1;}
        unset($condition);
        $data['listpss']=$get_p+1;
        $this->ajaxReturn($data);
	}
	//概况
	function Survey(){
		$cate_id = I ( 'cate_id', 0, 'intval' );
		$this->assign('cate_id', $cate_id);
		$zlmcategory=$this->huozlm($cate_id);
		$dzlm=$this->dzlm($cate_id);
		$this->assign('dzlm', $dzlm);
		$dynr=$this->dynr($cate_id);
		$this->assign ( 'dynr', $dynr );
		//dump($category);
		$this->assign ( 'zlmcategory', $zlmcategory );
		$this->assign ( 'config', $this->config );
		//seo
		$this->assign('site_title', $dzlm['title']);
		//end
		
		$this->htoubu();
		if($cate_id==3 || $cate_id==26){
			$p=0;//当前页
			$m=6;//每页显示
			$this->assign('dynrlb', $this->dynrlb($cate_id,$p,$m));
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/Surveyys.html' );
		}
		else $this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/Survey.html' );
		$this->fdibu();
	}
	//园区动态
	function dynamic(){
		$cate_id = I ( 'cate_id', 0, 'intval' );
		$this->assign('cate_id', $cate_id);
		$zlmcategory=$this->huozlm($cate_id);
		$dzlm=$this->dzlm($cate_id);
		$this->assign('dzlm', $dzlm);
		$dynr=$this->dynr($cate_id);
		$this->assign ( 'dynr', $dynr );
		//dump($category);
		$this->assign ( 'zlmcategory', $zlmcategory );
		$this->assign ( 'config', $this->config );
		//seo
		$this->assign('site_title', $dzlm['title']);
		//end
		
		$p=0;//当前页
		$m=6;//每页显示
		$this->assign('dynrlb', $this->dynrlb($cate_id,$p,$m));
		$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/dynamic.html' );
		$this->fdibu();
		
	}
	//注册
	function register(){
		
		$cate_id = I ( 'cate_id', 0, 'intval' );
		$this->assign('cate_id', $cate_id);
		$zlmcategory=$this->huozlm($cate_id);
		$dzlm=$this->dzlm($cate_id);
		$this->assign('dzlm', $dzlm);
		$dynr=$this->dynr($cate_id);
		$this->assign ( 'dynr', $dynr );
		//dump($category);
		//短信验证码
		$dxyzm=$this->NoRandxxx();
		$_SESSION['dxyzm']=$dxyzm;
		$this->assign ( 'dxyzm', $dxyzm );
		
		$this->assign ( 'zlmcategory', $zlmcategory );
		$this->assign ( 'config', $this->config );
		//seo
		$this->assign('site_title', '注册');
		//end
		$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/register.html' );
		$this->fdibu();
	}
	function rpost(){
		//dump($_POST);
		$r['sj']=I('post.sj');
		$r['yx']=I('post.yx');
		$yzm=I('post.yzm');
		$dxyzm=I('post.dxyzm');
		$r['salt']=$this->NoRandxxx();
		$r['pass']=md5(I('post.pass').$r['salt']);
		$this->assign('site_title', '注册');
		$e='';
		//是否注册
		$cosj = M ( 'huiyuan' )->where ( 'sj='.'\''.$r['sj'].'\'' )->limit(1)->select ();
		if($cosj['0']['id']) $e='手机号已经注册';
		$coyx = M ( 'huiyuan' )->where ( 'yx='.'\''.$r['yx'].'\'' )->limit(1)->select ();
		if($coyx['0']['id']) $e='邮箱已经注册';
		if($_SESSION['check_pic']!=$yzm) $e='验证码错误';
		if($_SESSION['dxyzm']!=$dxyzm) $e='短信验证码错误';
		if(!$e){
			$er=M ( 'huiyuan' )->add ($r);
		}
		if($er) {
			$e='成功注册！';
			setcookie("uid", $er, time()+360000000);
			$lsalt=md5($er.time());
			setcookie("lsalt", $lsalt, time()+360000000);
			$dl['lsalt']=$lsalt;
			$dw['id']=$er;
			$sok=M ( 'huiyuan' )->where($dw)-> save($dl);
			if($sok){
				header('Location:/index.php?s=/addon/WeiSite/WeiSite/grzx/ok/zc.html');
				exit;
			}
		}
		$this->assign ( 'e', $e );
		$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/rpost.html' );
		$this->fdibu();
	}
	//判断是否登录
	function sfdl(){
		$id=$_COOKIE['uid']+0;
		$_COOKIE['lsalt'];
		if(!$id) return false;
		$s=M ( 'huiyuan' )->where( 'id='.'\''.$id.'\'' )->limit(1)-> select();
		
		if($s['0']['lsalt']==$_COOKIE['lsalt']) return $s['0']['id'];
		else return false;
	}
	//随机数
	function NoRandxxx($begin=0,$end=9,$limit=5){ 
		$rand_array=range($begin,$end); 
		shuffle($rand_array);//调用现成的数组随机排列函数 
		$nn=array_slice($rand_array,0,$limit);//截取前$limit个 
		foreach($nn as $k=>$v){
			if($k<4) $vv=$vv.$v;
		}
		return $vv;
	}
	function dlpost(){
		$sj=I('get.sj')+0;
		$yzm=I('get.yzm');
		//$pass=md5(I('get.pass').$r['salt']);
		if($_SESSION['check_pic']!=$yzm) exit('e1'); //$e='验证码错误';
		
		$s=M ( 'huiyuan' )->where( 'sj='.'\''.$sj.'\'' )->limit(1)-> select();
		if(!$s['0']['id']) exit('e2');//手机号没有注册
		else{
			$pass=md5(I('get.pass').$s['0']['salt']);
			if($pass==$s['0']['pass']) {
				setcookie("uid", $s['0']['id'], time()+360000000);
				$lsalt=md5($s['0']['id'].time());
				setcookie("lsalt", $lsalt, time()+360000000);
				$dl['lsalt']=$lsalt;
				$dw['id']=$s['0']['id'];
				$sok=M ( 'huiyuan' )->where($dw)-> save($dl);
				if($sok) exit('ok'); //登录成功
				else exit('e4');//服务器繁忙
			}
			else exit('e3');//密码错误
		}
		//dump($_POST);
	}
	//忘记密码
	function wjmm(){
		$this->assign('site_title', '忘记密码');
		//end
		$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/wjmm.html' );
		$this->fdibu();
	}
	function wjmmt(){
		$sj=I('post.sj');
		$this->assign('site_title', '忘记密码');
		//end
		//echo $sj;
		if($sj){
			//新密码
			$pass=$this->NoRandxxx();
			$r['salt']=$this->NoRandxxx();
			$r['pass']=md5($pass.$r['salt']);
			
			$sjx=$this->check_emailsss($sj);
			if($sjx){
				
					$coyx = M ( 'huiyuan' )->where ( 'yx='.'\''.$sj.'\'' )->limit(1)->select ();
					if(!$coyx['0']['id']){
						header('Location:/index.php?s=/addon/WeiSite/WeiSite/wjmm/ok/yx.html');
						exit;
					}
				
				//发送邮件
				$dw['yx']=$sj;
				$sok=M ( 'huiyuan' )->where($dw)-> save($r);
				if($sok){
					
					
					//echo $pass;
					$url = "http://gbwx.test.resonance.net.cn/email.php?v".time();
					// 参数数组
					$post_data = array (
						't' => '【中国供销-桂北农产品电商园】新密码成功修改',
						'c' => '新密码： '.$pass.' 请及时登录修改密码',
						'e' => $sj
					);
					$this->postData($url,$post_data);
					
					header('Location:/index.php?s=/addon/WeiSite/WeiSite/Signin/ok/ok.html');
					exit;
				}
			}
			else{
					$coyx = M ( 'huiyuan' )->where ( 'sj='.'\''.$sj.'\'' )->limit(1)->select ();
					if(!$coyx['0']['id']){
						header('Location:/index.php?s=/addon/WeiSite/WeiSite/wjmm/ok/sj.html');
						exit;
					}
				//发送验证码
			}
		}
		/*$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/wjmmt.html' );
		$this->fdibu();*/
	}
    function postData($url, $data){      
        $ch = curl_init();      
        $timeout = 300;       
        curl_setopt($ch, CURLOPT_URL, $url);     
        curl_setopt($ch, CURLOPT_REFERER, "http://resonance.com.cn/");   //构造来路    
        curl_setopt($ch, CURLOPT_POST, true);      
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);      
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);      
        $handles = curl_exec($ch);      
        curl_close($ch);      
        return $handles;      
    } 
	//封装邮箱验证函数
	function check_emailsss($email){
		if (ereg('^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+',$email)){
			return true;
		}else{
			return false;
		}
	}
	
	
	//登录
	function Signin(){
		$cate_id = I ( 'cate_id', 0, 'intval' );
		$this->assign('cate_id', $cate_id);
		$zlmcategory=$this->huozlm($cate_id);
		$dzlm=$this->dzlm($cate_id);
		$this->assign('dzlm', $dzlm);
		$dynr=$this->dynr($cate_id);
		$this->assign ( 'dynr', $dynr );
		//dump($category);
		$this->assign ( 'zlmcategory', $zlmcategory );
		$this->assign ( 'config', $this->config );
		//seo
		$this->assign('site_title', '登录');
		//end
		$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/Signin.html' );
		$this->fdibu();
	}
	//个人中心
	function grzx(){
		$pd=$this->sfdl();
		if(!$pd){
			header('Location:/index.php?s=/addon/WeiSite/WeiSite/register.html');
			exit;
		}
		//获取会员信息
		$s=M ( 'huiyuan' )->where( 'id='.'\''.$pd.'\'' )->limit(1)-> select();
		$this->assign('s', $s['0']);
		
		$this->assign('site_title', '个人中心');
		$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/grzx.html' );
		$this->fdibu();
	}
	//修改个人资料
	function zlxg(){
		$pd=$this->sfdl();
		if(!$pd){
			header('Location:/index.php?s=/addon/WeiSite/WeiSite/register.html');
			exit;
		}
		$r['nic']=I('post.nic');
		$r['xm']=I('post.xm');
		$r['xb']=I('post.xb');
		//dump($r);
		$si=M ( 'huiyuan' )->where( 'id='.'\''.$pd.'\'' )->save($r);
		//if($si){
			header('Location:/index.php?s=/addon/WeiSite/WeiSite/grzx/ok/ok.html');
			exit;
		//}
	}

	function duiccc(){
		setcookie("uid", '', time()-360000000);
		setcookie("lsalt", '', time()-360000000);
		header('Location:/index.php?s=/addon/WeiSite/WeiSite/index.html');
	}
	function xgmmposit(){
			$pd=$this->sfdl();
			if(!$pd){
				header('Location:/index.php?s=/addon/WeiSite/WeiSite/register.html');
				exit;
			}
			//新密码
			$pass=I('post.pass');
			$r['salt']=$this->NoRandxxx();
			$r['pass']=md5($pass.$r['salt']);
			$dw['id']=$pd;
			$sok=M ( 'huiyuan' )->where($dw)-> save($r);
			
				//setcookie("uid", '', time()-360000000);
				//setcookie("lsalt", '', time()-360000000);
			
			header('Location:/index.php?s=/addon/WeiSite/WeiSite/xgmm/ok/ok.html');
			exit;
	}
	//修改密码
	function xgmm(){
		$pd=$this->sfdl();
		if(!$pd){
			header('Location:/index.php?s=/addon/WeiSite/WeiSite/register.html');
			exit;
		}
		
		$this->assign('site_title', '修改密码');
		//dump($this->peizhihq());exit;
		$this->assign('peizhihq', $this->peizhihq());
		
		$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/xgmm.html' );
		$this->fdibu();
		
	}
	//获取网站配置
	function peizhihq(){
		return $pall=getAddonConfig('Cpeizhi');
	}
	//联系我们
	function lxwm(){
		$this->assign('site_title', '联系我们');
		//dump($this->peizhihq());exit;
		$this->assign('peizhihq', $this->peizhihq());
		
		$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/lxwm.html' );
		$this->fdibu();
	}
	//微留言
	function wly(){
		$this->assign('site_title', '微留言');
		$this->assign('wlyimg','images/banner-6_s1.jpg');
		$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/wly.html' );
		$this->fdibu();
	}
	function lypost(){
		//dump($_POST);
		$r['bt']=I('post.bt');
		$r['lxr']=I('post.lxr');
		$r['dh']=I('post.dh');
		$r['nr']=I('post.nr');
		$r['ctime']=time();
		$si=M ( 'Weiliy' )->add($r);
		if($si){
			header('Location:/index.php?s=/addon/WeiSite/WeiSite/wly/ok/ok.html');
			exit;
		}
		
	}
	//服务中心
	function service(){
		$cate_id = I ( 'cate_id', 0, 'intval' );
		$this->assign('cate_id', $cate_id);
		$zlmcategory=$this->huozlm($cate_id);
		$dzlm=$this->dzlm($cate_id);
		$this->assign('dzlm', $dzlm);
		$dynr=$this->dynr($cate_id);
		$this->assign ( 'dynr', $dynr );
		//dump($category);
		$this->assign ( 'zlmcategory', $zlmcategory );
		$this->assign ( 'config', $this->config );
		//seo
		$this->assign('site_title', $dzlm['title']);
		//end
		
		$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/Survey.html' );
		$this->fdibu();
	}
	//招商营销
	function investment (){
		$cate_id = I ( 'cate_id', 0, 'intval' );
		$this->assign('cate_id', $cate_id);
		$zlmcategory=$this->huozlm($cate_id);
		$dzlm=$this->dzlm($cate_id);
		$this->assign('dzlm', $dzlm);
		$dynr=$this->dynr($cate_id);
		$this->assign ( 'dynr', $dynr );
		//dump($category);
		$this->assign ( 'zlmcategory', $zlmcategory );
		$this->assign ( 'config', $this->config );
		//seo
		$this->assign('site_title', $dzlm['title']);
		//end
		
		$p=0;//当前页
		$m=6;//每页显示
		$this->assign('dynrlb', $this->dynrlb($cate_id,$p,$m));
		$this->htoubu();
		if($cate_id==17){
			$this->assign('peizhihq', $this->peizhihq());
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/zxyy.html' );
		}
		else $this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/investment.html' );
		
		$this->fdibu();
		
	}
	function zxyyrpost(){
		//dump($_POST);
		//dump($_POST);
		$r['bt']=I('post.bt');
		$r['lxr']=I('post.lxr');
		$r['dh']=I('post.dh');
		$r['nr']=I('post.nr');
		$r['yx']=I('post.yx');
		$r['ctime']=time();
		$si=M ( 'Zxyy' )->add($r);
		if($si){
			header('Location:/index.php?s=/addon/WeiSite/WeiSite/investment/cate_id/17/ok/ok.html');
			exit;
		}
		
		
		
	}
	//上下篇
	function sxp($id,$cate_id){
		$cs=M( 'custom_reply_news' )->where ( 'id<'.$id.' and cate_id='.$cate_id )->order('id desc')->limit(1)->select ();
		$cx=M( 'custom_reply_news' )->where ( 'id>'.$id.' and cate_id='.$cate_id )->limit(1)->select ();
		$c['0']=$cs['0'];
		$c['1']=$cx['0'];
		return $c;
	}
	//详情页
	function yqymnry(){
		$cate_id = I ( 'cate_id', 0, 'intval' );
		$id = I ( 'id', 0, 'intval' );
		$this->assign('cate_id', $cate_id);
		$zlmcategory=$this->huozlm($cate_id);
		$dzlm=$this->dzlm($cate_id);
		$this->assign('dzlm', $dzlm);
		$dynr=$this->dynr($cate_id);
		$this->assign ( 'dynr', $dynr );
		
		$this->assign ( 'dynrid', $this->dynrid($id) );
		//dump($category);
		$this->assign ( 'zlmcategory', $zlmcategory );
		$this->assign ( 'config', $this->config );
		//seo
		$this->assign('site_title', $dzlm['title']);
		$this->assign('sxp', $this->sxp($id,$cate_id));
		
		//end
		$this->htoubu();
		$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateIndex/ColorV1/yqymnry.html' );
		$this->fdibu();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// 分类列表
	function lists() {
		$cate_id = I ( 'cate_id', 0, 'intval' );
		empty ( $cate_id ) && $cate_id = I ( 'classid', 0, 'intval' );
		if (file_exists ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_lists'] . '.html' )) {
			
			$this->pigcms_lists ( $cate_id );
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_lists'] . '.html' );
		} else {
			$map ['token'] = get_token ();
			if ($cate_id) {
				$map ['cate_id'] = $cate_id;
				$cate = M ( 'weisite_category' )->where ( 'id = ' . $map ['cate_id'] )->find ();
				$this->assign ( 'cate', $cate );
				// 二级分类
				$category = M ( 'weisite_category' )->where ( 'pid = ' . $map ['cate_id'] )->order ( 'sort asc, id desc' )->select ();
			}
			if (! empty ( $category )) {
				foreach ( $category as &$vo ) {
					$vo ['icon'] = get_cover_url ( $vo ['icon'] );
					empty ( $vo ['url'] ) && $vo ['url'] = addons_url ( 'WeiSite://WeiSite/lists', array (
							'cate_id' => $vo ['id'] 
					) );
				}
				$this->assign ( 'category', $category );
				// 幻灯片
				
				$slideshow = M ( 'weisite_slideshow' )->where ( $map )->order ( 'sort asc, id desc' )->select ();
				foreach ( $slideshow as &$vo ) {
					$vo ['img'] = get_cover_url ( $vo ['img'] );
				}
				
				foreach ( $slideshow as &$data ) {
					foreach ( $category as $c ) {
						if ($data ['cate_id'] == $c ['id']) {
							$data ['url'] = $c ['url'];
						}
					}
				}
				$this->assign ( 'slideshow', $slideshow );
				
				$this->_footer ();
				if ($this->config ['template_subcate'] == 'default') {
					// code...
					$htmlstr = 'cate.html';
				} else {
					$htmlstr = 'index.html';
				}
				if (! $cate ['template']) {
					$cate ['template'] = $this->config ['template_subcate'];
				}
				$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateSubcate/' . $cate ['template'] . '/' . $htmlstr );
			} else {
				
				$page = I ( 'p', 1, 'intval' );
				$row = isset ( $_REQUEST ['list_row'] ) ? intval ( $_REQUEST ['list_row'] ) : 20;
				
				$data = M ( 'custom_reply_news' )->where ( $map )->order ( 'sort asc, id DESC' )->page ( $page, $row )->select ();
				if (empty ( $data )) {
					$cmap ['id'] = $map ['cate_id'] = intval ( $cate_id );
					$cate = M ( 'weisite_category' )->where ( $cmap )->find ();
					if (! empty ( $cate ['url'] )) {
						redirect ( $cate ['url'] );
						die ();
					}
				}
				/* 查询记录总数 */
				$count = M ( 'custom_reply_news' )->where ( $map )->count ();
				$list_data ['list_data'] = $data;
				
				// 分页
				if ($count > $row) {
					$page = new \Think\Page ( $count, $row );
					$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
					$list_data ['_page'] = $page->show ();
				}
				
				foreach ( $list_data ['list_data'] as $k => $li ) {
					if ($li ['jump_url'] && empty ( $li ['content'] )) {
						$li ['url'] = $li ['jump_url'];
					} else {
						$li ['url'] = U ( 'detail', array (
								'id' => $li ['id'] 
						) );
					}
					$showType = explode ( ',', $li ['show_type'] );
					if (in_array ( 1, $showType )) {
						$slideData [] = $li;
					}
					if (in_array ( 0, $showType )) {
						// unset($list_data['list_data'][$k]);
						$lists [] = $li;
					}
				}
				$this->assign ( 'slide_data', $slideData );
				$this->assign ( 'lists', $lists );
				$this->assign ( $list_data );
				$this->_footer ();
				$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateLists/' . $this->config ['template_lists'] . '/lists.html' );
			}
		}
	}
	// 详情
	function detail() {
		if (file_exists ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_detail'] . '.html' )) {
			$this->pigcms_detail ();
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/Index_' . $this->config ['template_detail'] . '.html' );
		} else {
			$map ['id'] = I ( 'get.id', 0, 'intval' );
			$info = M ( 'custom_reply_news' )->where ( $map )->find ();
			// dump($info);exit;
			if ($info ['is_show'] == '0') {
				unset ( $info ['cover'] );
			}
			// dump($info);exit;
			$this->assign ( 'info', $info );
			
			// dump($info);exit;
			M ( 'custom_reply_news' )->where ( $map )->setInc ( 'view_count' );
			
			$this->_footer ();
			$this->display ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateDetail/' . $this->config ['template_detail'] . '/detail.html' );
		}
	}
	
	// 3G页面底部导航
	function _footer($temp_type = 'weiphp') {
		if ($temp_type == 'pigcms') {
			$param ['token'] = $token = get_token ();
			$param ['temp'] = $this->config ['template_footer'];
			$url = U ( 'Home/Index/getFooterHtml', $param );
			$html = wp_file_get_contents ( $url );
			// dump ( $url );
			// dump ( $html );
			$file = RUNTIME_PATH . $token . '_' . $this->config ['template_footer'] . '.html';
			if (! file_exists ( $file ) || true) {
				file_put_contents ( $file, $html );
			}
			
			$this->assign ( 'cateMenuFileName', $file );
		} else {
			$list = D ( 'Addons://WeiSite/Footer' )->get_list ();
			
			foreach ( $list as $k => $vo ) {
				if ($vo ['pid'] != 0)
					continue;
				
				$one_arr [$vo ['id']] = $vo;
				unset ( $list [$k] );
			}
			
			foreach ( $one_arr as &$p ) {
				$two_arr = array ();
				foreach ( $list as $key => $l ) {
					if ($l ['pid'] != $p ['id'])
						continue;
					
					$two_arr [] = $l;
					unset ( $list [$key] );
				}
				
				$p ['child'] = $two_arr;
			}
			$this->assign ( 'footer', $one_arr );
			if (empty ( $this->config ['template_footer'] )) {
				$this->config ['template_footer'] = 'V1';
			}
			$html = $this->fetch ( ONETHINK_ADDON_PATH . 'WeiSite/View/default/TemplateFooter/' . $this->config ['template_footer'] . '/footer.html' );
			$this->assign ( 'footer_html', $html );
		}
	}
	function _deal_footer_data($vo, $k) {
		$arr = array (
				'id' => $vo ['id'],
				'fid' => $vo ['pid'],
				'token' => $vo ['token'],
				'name' => $vo ['title'],
				'orderss' => 0,
				'picurl' => get_cover_url ( $vo ['icon'] ),
				'url' => $vo ['url'],
				'status' => "1",
				'RadioGroup1' => "0",
				'vo' => array (),
				'k' => $k 
		);
		return $arr;
	}
	function coming_soom() {
		$this->display ();
	}
	function tvs1_video() {
		$this->display ();
	}
	
	/*
	 * 兼容小猪CMS模板
	 *
	 * 移植方法：
	 * 1、把 tpl\static\tpl 目录下的所有文档复制到 weiphp的 Addons\WeiSite\View\default\pigcms 目录下
	 * 2、把 tpl\Wap\default 目录下的所有文档也复制到 weiphp的 Addons\WeiSite\View\default\pigcms 目录下
	 * 3、把 tpl\User\default\common\ 目录下的所有图片文件复制到 weiphp的 Addons\WeiSite\View\default\pigcms 目录下
	 * 4、把 PigCms\Lib\ORG\index.Tpl.php 文件复制到 weiphp的 Addons\WeiSite\View\default\pigcms 目录下
	 * 5、把pigcms 目录下所有文档代码里的 Wap/Index/lists 替换成 Home/Addons/execute?_addons=WeiSite&_controller=WeiSite&_action=lists
	 * 6、把pigcms 目录下所有文档代码里的 Wap/Index/index 替换成 Home/Addons/execute?_addons=WeiSite&_controller=WeiSite&_action=index
	 */
	function pigcms_init() {
		// dump ( 'pigcms_init' );
		C ( 'TMPL_L_DELIM', '{pigcms:' );
		// C ( 'TMPL_FILE_DEPR', '_' );
		
		define ( 'RES', ONETHINK_ADDON_PATH . 'WeiSite/View/default/pigcms/common' );
		
		$public_info = get_token_appinfo ();
		$manager = get_userinfo ( $public_info ['uid'] );
		
		// 站点配置
		$data ['f_logo'] = get_cover_url ( C ( 'SYSTEM_LOGO' ) );
		$data ['f_siteName'] = C ( 'WEB_SITE_TITLE' );
		$data ['f_siteTitle'] = C ( 'WEB_SITE_TITLE' );
		$data ['f_metaKeyword'] = C ( 'WEB_SITE_KEYWORD' );
		$data ['f_metaDes'] = C ( 'WEB_SITE_DESCRIPTION' );
		$data ['f_siteUrl'] = SITE_URL;
		$data ['f_qq'] = '';
		$data ['f_qrcode'] = '';
		$data ['f_ipc'] = C ( 'WEB_SITE_ICP' );
		$data ['reg_validDays'] = 30;
		
		// 用户信息
		$data ['user'] = array (
				'id' => $GLOBALS ['myinfo'] ['uid'],
				'openid' => get_openid (),
				'username' => $GLOBALS ['myinfo'] ['nickname'],
				'mp' => $public_info ['token'],
				'password' => $GLOBALS ['myinfo'] ['password'],
				'email' => $GLOBALS ['myinfo'] ['email'],
				'createtime' => $GLOBALS ['myinfo'] ['reg_time'],
				'lasttime' => $GLOBALS ['myinfo'] ['last_login_time'],
				'status' => 1,
				'createip' => $GLOBALS ['myinfo'] ['reg_ip'],
				'lastip' => $GLOBALS ['myinfo'] ['last_login_ip'],
				'smscount' => 0,
				'inviter' => 1,
				'gid' => 5,
				'diynum' => 0,
				'activitynum' => 0,
				'card_num' => 0,
				'card_create_status' => 0,
				'money' => 0,
				'moneybalance' => 0,
				'spend' => 0,
				'viptime' => $GLOBALS ['myinfo'] ['last_login_time'] + 86400,
				'connectnum' => 0,
				'lastloginmonth' => 0,
				'attachmentsize' => 0,
				'wechat_card_num' => 0,
				'serviceUserNum' => 0,
				'invitecode' => '',
				'remark' => '' 
		);
		
		// 微网站配置信息
		$data ['homeInfo'] = array (
				'id' => $manager ['uid'],
				'token' => $public_info ['token'],
				'title' => $this->config ['title'],
				'picurl' => get_cover_url ( $this->config ['cover'] ),
				// 'apiurl' => "",
				// 'homeurl' => "",
				'info' => $this->config ['info'],
				// 'musicurl' => "",
				// 'plugmenucolor' => "#5CFF8D",
				'copyright' => $manager ['copy_right'],
				// 'radiogroup' => "12",
				// 'advancetpl' => "0"
				'logo' => get_cover_url ( $this->config ['cover'] ) 
		);
		
		// 背景图
		$bgarr = $this->config ['background_arr'];
		$data ['flashbgcount'] = count ( $bgarr );
		foreach ( $bgarr as $bg ) {
			$data ['flashbg'] [] = array (
					'id' => $bg,
					'token' => $public_info ['token'],
					'img' => get_cover_url ( $bg ),
					'url' => "javascript:void(0)",
					'info' => "背景图片",
					'tip' => '2' 
			);
		}
		// $data ['flashbg'] [0] = array (
		// 'id' => $this->config ['background_id'],
		// 'token' => $public_info ['token'],
		// 'img' => $this->config ['background'],
		// 'url' => "javascript:void(0)",
		// 'info' => "背景图片",
		// 'tip' => '2'
		// );
		$data ['flashbgcount'] = count ( $data ['flashbg'] );
		$map ['token'] = get_token ();
		$map ['is_show'] = 1;
		// 幻灯片
		$slideshow = M ( 'weisite_slideshow' )->where ( $map )->order ( 'sort asc, id desc' )->select ();
		foreach ( $slideshow as $vo ) {
			$data ['flash'] [] = array (
					'id' => $vo ['id'],
					'token' => $vo ['token'],
					'img' => get_cover_url ( $vo ['img'] ),
					'url' => $vo ['url'],
					'info' => $vo ['title'],
					'tip' => '1' 
			);
		}
		$data ['num'] = count ( $data ['flash'] );
		
		// 底部栏
		$this->_footer ( 'pigcms' );
		
		// 设置版权信息
		$data ["iscopyright"] = 0;
		$data ["copyright"] = $data ["siteCopyright"] = empty ( $manager ['copy_right'] ) ? C ( 'COPYRIGHT' ) : $manager ['copy_right'];
		// 分享
		$data ['shareScript'] = '';
		
		$data ['token'] = $public_info ['token'];
		$data ['wecha_id'] = $public_info ['wechat'];
		
		$this->assign ( $data );
		
		// 模板信息
		if (file_exists ( ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/index.Tpl.php' )) {
			$pigcms_temps = require_once ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/index.Tpl.php';
			foreach ( $pigcms_temps as $k => $vo ) {
				$temps [$vo ['tpltypename']] = $vo;
			}
		}
		
		if (file_exists ( ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/cont.Tpl.php' )) {
			$pigcms_temps = require_once ONETHINK_ADDON_PATH . _ADDONS . '/View/default/pigcms/cont.Tpl.php';
			foreach ( $pigcms_temps as $k => $vo ) {
				$temps [$vo ['tpltypename']] = $vo;
			}
		}
		$tpl = array (
				'id' => $public_info ['id'],
				'routerid' => "",
				'uid' => $public_info ['uid'],
				'wxname' => $public_info ['public_name'],
				'winxintype' => $public_info ['type'],
				'appid' => $public_info ['appid'],
				'appsecret' => $public_info ['secret'],
				'wxid' => $public_info ['id'],
				'weixin' => $public_info ['wechat'],
				'headerpic' => get_cover_url ( $GLOBALS ['myinfo'] ['headface_url'] ),
				'token' => $public_info ['token'],
				'pigsecret' => $public_info ['token'],
				'province' => $GLOBALS ['myinfo'] ['province'],
				'city' => $GLOBALS ['myinfo'] ['city'],
				'qq' => $GLOBALS ['myinfo'] ['qq'],
				// 'wxfans' => "0",
				// 'typeid' => "8",
				// 'typename' => "服务",
				// 'tongji' => "",
				// 'allcardnum' => "0",
				// 'cardisok' => "0",
				// 'yetcardnum' => "0",
				// 'totalcardnum' => "0",
				// 'createtime' => "1440150418",
				// 'updatetime' => "1440150418",
				// 'transfer_customer_service' => "0",
				// 'openphotoprint' => "0",
				// 'freephotocount' => "3",
				// 'oauth' => "0",
				'color_id' => 0,
				
				'tpltypeid' => $temps [$this->config ['template_index']] ['tpltypeid'],
				'tpltypename' => $this->config ['template_index'],
				
				'tpllistid' => $temps [$this->config ['template_lists']] ['tpltypeid'],
				'tpllistname' => $this->config ['template_lists'],
				
				'tplcontentid' => $temps [$this->config ['template_detail']] ['tpltypeid'],
				'tplcontentname' => $this->config ['template_detail'] 
		);
		$this->assign ( 'tpl', $tpl );
		$this->assign ( 'wxuser', $tpl );
	}
	function pigcms_index() {
		$this->pigcms_init ();
		
		$cate = $this->_pigcms_cate ( 0 );
		$this->assign ( 'info', $cate );
	}
	function pigcms_lists($cate_id) {
		$this->pigcms_init ();
		
		$map ['token'] = get_token ();
		$cateArr = M ( 'weisite_category' )->where ( $map )->getField ( 'id,title' );
		
		$thisClassInfo = array ();
		if ($cate_id) {
			$map ['cate_id'] = $cate_id;
			
			$thisClassInfo = $this->_deal_cate ( $cateArr [$cate_id] );
		}
		
		$data = M ( 'custom_reply_news' )->where ( $map )->order ( 'sort asc, id DESC' )->select ();
		foreach ( $data as $vo ) {
			$info [] = array (
					'id' => $vo ['id'],
					'uid' => 0,
					'uname' => $vo ['author'],
					'keyword' => $vo ['keyword'],
					'type' => 2,
					'text' => $vo ['intro'],
					'classid' => $vo ['cate_id'],
					'classname' => $vo [''],
					'pic' => get_cover_url ( $vo ['cover'] ),
					'showpic' => 1,
					'info' => strip_tags ( htmlspecialchars_decode ( mb_substr ( $vo ['content'], 0, 10, 'utf-8' ) ) ),
					'url' => $this->_getNewsUrl ( $vo ),
					'createtime' => $vo ['cTime'],
					'uptatetime' => $vo ['cTime'],
					'click' => $vo ['view_count'],
					'token' => $vo ['token'],
					'title' => $vo ['title'],
					'usort' => $vo ['sort'],
					'name' => $vo ['title'],
					'img' => get_cover_url ( $vo ['cover'] ) 
			);
		}
		
		$this->assign ( 'info', $info );
		$this->assign ( 'thisClassInfo', $thisClassInfo );
	}
	function pigcms_detail() {
		$this->pigcms_init ();
		
		$cate = $this->_pigcms_cate ( 0 );
		$this->assign ( 'info', $cate );
		
		$map ['id'] = I ( 'get.id', 0, 'intval' );
		$res = M ( 'custom_reply_news' )->where ( $map )->find ();
		if ($res ['is_show'] == 0) {
			unset ( $res ['cover'] );
		}
		$res = $this->_deal_news ( $res, 1 );
		$this->assign ( 'res', $res );
		M ( 'custom_reply_news' )->where ( $map )->setInc ( 'view_count' );
		
		$map2 ['cate_id'] = $res ['cate_id'];
		$map2 ['id'] = array (
				'exp',
				'!=' . $map ['id'] 
		);
		$lists = M ( 'custom_reply_news' )->where ( $map2 )->order ( 'id desc' )->limit ( 5 )->select ();
		foreach ( $lists as &$new ) {
			$new = $this->_deal_news ( $new );
		}
		
		$this->assign ( 'lists', $lists );
	}
	function _pigcms_cate($pid = null) {
		$map ['token'] = get_token ();
		$map ['is_show'] = 1;
		$pid === null || $map ['pid'] = $pid; // 获取一级分类
		
		$category = M ( 'weisite_category' )->where ( $map )->order ( 'sort asc, id desc' )->select ();
		$count = count ( $category );
		foreach ( $category as $k => $vo ) {
			$param ['cate_id'] = $vo ['id'];
			$url = empty ( $vo ['url'] ) ? $vo ['url'] = addons_url ( 'WeiSite://WeiSite/lists', $param ) : $vo ['url'];
			$pid = intval ( $vo ['pid'] );
			$res [$pid] [$vo ['id']] = $this->_deal_cate ( $vo, $count - $k );
		}
		
		foreach ( $res [0] as $vv ) {
			if (! empty ( $res [$vv ['id']] )) {
				$vv ['sub'] = $res [$vv ['id']];
				unset ( $res [$vv ['id']] );
			}
		}
		
		return $res [0];
	}
	function _deal_cate($vo, $key = 1) {
		return array (
				'id' => $vo ['id'],
				'fid' => $vo ['pid'],
				'name' => $vo ['title'],
				'info' => $vo ['title'],
				'sorts' => $vo ['sort'],
				'img' => get_cover_url ( $vo ['icon'] ),
				'url' => $url,
				'status' => 1,
				'path' => empty ( $vo ['pid'] ) ? 0 : '0-' . $vo ['pid'],
				'tpid' => 1,
				'conttpid' => 1,
				'sub' => array (),
				'key' => $key,
				'token' => $vo ['token'] 
		);
	}
	function _deal_news($vo, $type = 0) {
		$map ['id'] = $vo ['cate_id'];
		return array (
				'id' => $vo ['id'],
				'uid' => 0,
				'uname' => $vo ['author'],
				'keyword' => $vo ['keyword'],
				'type' => 2,
				'text' => $vo ['intro'],
				'classid' => $vo ['cate_id'],
				'classname' => empty ( $vo ['cate_id'] ) ? '' : M ( 'weisite_category' )->where ( $map )->getField ( 'title' ),
				'pic' => get_cover_url ( $vo ['cover'] ),
				'showpic' => 1,
				'info' => $type == 0 ? strip_tags ( htmlspecialchars_decode ( mb_substr ( $vo ['content'], 0, 10, 'utf-8' ) ) ) : $vo ['content'],
				'url' => $this->_getNewsUrl ( $vo ),
				'createtime' => $vo ['cTime'],
				'uptatetime' => $vo ['cTime'],
				'click' => $vo ['view_count'],
				'token' => $vo ['token'],
				'title' => $vo ['title'],
				'usort' => $vo ['sort'],
				'name' => $vo ['title'],
				'img' => get_cover_url ( $vo ['cover'] ) 
		);
	}
	function _getNewsUrl($info) {
		$param ['token'] = get_token ();
		$param ['openid'] = get_openid ();
		
		if (! empty ( $info ['jump_url'] )) {
			$url = replace_url ( $info ['jump_url'] );
		} else {
			$param ['id'] = $info ['id'];
			$url = U ( 'detail', $param );
		}
		return $url;
	}
	/* 预览 */
	function preview() {
		$publicid = get_token_appinfo ( '', 'id' );
		$url = addons_url ( 'WeiSite://WeiSite/index', array (
				'publicid' => $publicid 
		) );
		$this->assign ( 'url', $url );
		
		$config = get_addon_config ( 'WeiSite' );
		
		$config ['background_arr'] = explode ( ',', $config ['background'] );
		$config ['background'] = $config ['background_arr'] [0];
		$this->assign ( 'data', $config );
		
		$this->display ();
	}
	function preview_cms() {
		$publicid = get_token_appinfo ( '', 'id' );
		$url = addons_url ( 'WeiSite://WeiSite/lists', array (
				'publicid' => $publicid,
				'from' => 'preview' 
		) );
		$this->assign ( 'url', $url );
		
		$this->display ();
	}
	function preview_old() {
		$publicid = get_token_appinfo ( '', 'id' );
		$url = addons_url ( 'WeiSite://WeiSite/index', array (
				'publicid' => $publicid 
		) );
		$this->assign ( 'url', $url );
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}

	//获取导航与子导航
	function get_sub_column($is_show=1,$pid=0,$limit=3,$order='sort asc, id desc') {
		
		$map ['token'] = get_token ();
		$map ['is_show'] = 1; // 是否显示
		$map ['pid'] = 0; // 是否显示
		$nav1=$this->weisite_category->where($map)->limit(3)->order($order)->select();
		foreach ($nav1 as $k => $v) {
			unset($map['pid']);
			$map['pid']=$v['id'];
			$navs=$this->weisite_category->where($map)->order($order)->select();
			  if($navs){
                $nav1[$k]['nav2'] = $navs;
            }else{
                unset($nav1[$k]);
            }
		}
		return $nav1;

	}

}
