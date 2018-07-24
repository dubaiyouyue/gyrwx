<?php
        	
namespace Addons\Cpeizhi\Model;
use Home\Model\WeixinModel;
        	
/**
 * Cpeizhi的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Cpeizhi' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	