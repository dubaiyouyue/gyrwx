<?php

namespace Addons\Wly;
use Common\Controller\Addon;

/**
 * 微留言插件
 * @author 共振
 */

    class WlyAddon extends Addon{

        public $info = array(
            'name'=>'Wly',
            'title'=>'微留言',
            'description'=>'这是一个临时描述',
            'status'=>1,
            'author'=>'共振',
            'version'=>'0.1',
            'has_adminlist'=>0
        );

	public function install() {
		$install_sql = './Addons/Wly/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Wly/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }