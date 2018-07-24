<?php
        	
namespace Addons\Login\Model;
use Home\Model\WeixinModel;
        	
/**
 * Login的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Login' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	