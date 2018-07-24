<?php
        	
namespace Addons\Rpass\Model;
use Home\Model\WeixinModel;
        	
/**
 * Rpass的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Rpass' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	