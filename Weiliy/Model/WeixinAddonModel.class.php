<?php
        	
namespace Addons\Weiliy\Model;
use Home\Model\WeixinModel;
        	
/**
 * Weiliy的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Weiliy' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	