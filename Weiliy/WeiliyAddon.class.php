<?php

namespace Addons\Weiliy;
use Common\Controller\Addon;

/**
 * 微官网留言插件
 * @author 共振
 */

    class WeiliyAddon extends Addon{

        public $info = array(
            'name'=>'Weiliy',
            'title'=>'微官网留言',
            'description'=>'访客网管网留言内容管理。',
            'status'=>1,
            'author'=>'共振',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Weiliy/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Weiliy/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }