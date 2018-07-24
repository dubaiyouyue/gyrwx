<?php
        	
namespace Addons\Ordernew\Model;
use Home\Model\WeixinModel;
        	
/**
 * Ordernew的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Ordernew' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	