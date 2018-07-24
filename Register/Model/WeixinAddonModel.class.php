<?php
        	
namespace Addons\Register\Model;
use Home\Model\WeixinModel;
        	
/**
 * Register的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Register' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	