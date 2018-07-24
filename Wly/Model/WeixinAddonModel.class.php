<?php
        	
namespace Addons\Wly\Model;
use Home\Model\WeixinModel;
        	
/**
 * Wly的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Wly' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	