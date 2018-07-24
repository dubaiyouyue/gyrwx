<?php
        	
namespace Addons\Goodsweixin\Model;
use Home\Model\WeixinModel;
        	
/**
 * Goodsweixin的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Goodsweixin' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	