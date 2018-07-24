<?php
        	
namespace Addons\Shopinfoapi\Model;
use Home\Model\WeixinModel;
        	
/**
 * Shopinfoapi的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Shopinfoapi' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	