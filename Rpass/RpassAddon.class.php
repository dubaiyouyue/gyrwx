<?php

namespace Addons\Rpass;
use Common\Controller\Addon;

/**
 * 找回密码插件
 * @author 共振
 */

    class RpassAddon extends Addon{

        public $info = array(
            'name'=>'Rpass',
            'title'=>'找回密码',
            'description'=>'找回密码。',
            'status'=>1,
            'author'=>'共振',
            'version'=>'0.1',
            'has_adminlist'=>0
        );

	public function install() {
		$install_sql = './Addons/Rpass/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Rpass/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }