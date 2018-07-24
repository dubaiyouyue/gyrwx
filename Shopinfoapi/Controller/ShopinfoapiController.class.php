<?php

namespace Addons\Shopinfoapi\Controller;
use Home\Controller\AddonsController;

class ShopinfoapiController extends AddonsController{
	function index(){
		
		/*
		$ps=$_POST['np']+0;//当前页数
		$pm=$_POST['pm']+0;//每页显示
		$or=$_POST['or'];//排序
		//最新降序 za
		//最新升序 zd
		//销量降序 xa
		//销量升序 xd
		//价格降序 ja
		//价格升序 jd
		*/
		
		$url=GZ_USERAPITOKENURL.'index.php?g=Home&m=Shopinfoapi&a=index';
		$data=array(
			'np'=>0,
			'pm'=>10,
			'or'=>'za'
		);
		$sinfo=$this->gzpostData($url,$data);
		$sinfo=json_decode($sinfo,true);
		dump($sinfo);
	}
}
