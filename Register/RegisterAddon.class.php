<?php

namespace Addons\Register;
use Common\Controller\Addon;

/**
 * 注册会员插件
 * @author 共振设计
 */

    class RegisterAddon extends Addon{

        public $info = array(
            'name'=>'Register',
            'title'=>'注册会员',
            'description'=>'微信官网注册会员。',
            'status'=>1,
            'author'=>'共振设计',
            'version'=>'0.1',
            'has_adminlist'=>0
        );

	public function install() {
		$install_sql = './Addons/Register/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Register/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }