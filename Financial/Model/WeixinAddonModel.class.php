<?php
        	
namespace Addons\Financial\Model;
use Home\Model\WeixinModel;
        	
/**
 * Financial的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Financial' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	