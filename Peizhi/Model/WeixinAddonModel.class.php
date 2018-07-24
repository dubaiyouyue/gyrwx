<?php
        	
namespace Addons\Peizhi\Model;
use Home\Model\WeixinModel;
        	
/**
 * Peizhi的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Peizhi' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	