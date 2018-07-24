<?php

namespace Addons\Financial\Controller;
use Home\Controller\AddonsController;

class FinancialController extends AddonsController{
	function wxf(){
		//echo 'fdsafsd';
		//微信支付
		$s['oid']=$_GET['transaction_id']; //支付订单
		$s['sum']=$_GET['cash_fee']+0; //支付金额
		$s['ctime']=time();
		if($s['oid'] && $s['sum']) $sr=M('financial')->add($s);
	}
}
