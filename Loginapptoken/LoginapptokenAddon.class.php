<?php

namespace Addons\Loginapptoken;
use Common\Controller\Addon;

/**
 * 第三方登录插件
 * @author 共振
 */

    class LoginapptokenAddon extends Addon{

        public $info = array(
            'name'=>'Loginapptoken',
            'title'=>'第三方登录',
            'description'=>'第三方登录',
            'status'=>1,
            'author'=>'共振',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Loginapptoken/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Loginapptoken/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }