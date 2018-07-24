<?php

namespace Addons\Cpeizhi;
use Common\Controller\Addon;

/**
 * 网站配置插件
 * @author 共振
 */

    class CpeizhiAddon extends Addon{

        public $info = array(
            'name'=>'Cpeizhi',
            'title'=>'网站配置',
            'description'=>'网站常用配置。',
            'status'=>1,
            'author'=>'共振',
            'version'=>'0.1',
            'has_adminlist'=>0
        );

	public function install() {
		$install_sql = './Addons/Cpeizhi/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Cpeizhi/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }