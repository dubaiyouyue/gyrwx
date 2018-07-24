<?php
        	
namespace Addons\MemberCenter\Model;
use Home\Model\WeixinModel;
        	
/**
 * MemberCenter的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'MemberCenter' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	