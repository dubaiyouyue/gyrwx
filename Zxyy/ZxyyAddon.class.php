<?php

namespace Addons\Zxyy;
use Common\Controller\Addon;

/**
 * 在线预约插件
 * @author 共振
 */

    class ZxyyAddon extends Addon{

        public $info = array(
            'name'=>'Zxyy',
            'title'=>'在线预约',
            'description'=>'在线预约内容管理插件。',
            'status'=>1,
            'author'=>'共振',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Zxyy/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Zxyy/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }