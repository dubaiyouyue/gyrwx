<?php
        	
namespace Addons\Loginapptoken\Model;
use Home\Model\WeixinModel;
        	
/**
 * Loginapptoken的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Loginapptoken' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	