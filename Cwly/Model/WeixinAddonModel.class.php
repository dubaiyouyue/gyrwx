<?php
        	
namespace Addons\Cwly\Model;
use Home\Model\WeixinModel;
        	
/**
 * Cwly的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Cwly' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	