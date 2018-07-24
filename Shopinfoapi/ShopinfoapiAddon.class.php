<?php

namespace Addons\Shopinfoapi;
use Common\Controller\Addon;

/**
 * 获取商品信息。插件
 * @author 共振
 */

    class ShopinfoapiAddon extends Addon{

        public $info = array(
            'name'=>'Shopinfoapi',
            'title'=>'获取商品信息。',
            'description'=>'获取商品信息API。',
            'status'=>1,
            'author'=>'共振',
            'version'=>'0.1',
            'has_adminlist'=>0
        );

	public function install() {
		$install_sql = './Addons/Shopinfoapi/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Shopinfoapi/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }