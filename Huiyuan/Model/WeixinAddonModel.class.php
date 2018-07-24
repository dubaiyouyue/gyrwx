<?php
        	
namespace Addons\Huiyuan\Model;
use Home\Model\WeixinModel;
        	
/**
 * Huiyuan的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Huiyuan' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	