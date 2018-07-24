<?php

namespace Addons\Goodsweixin;
use Common\Controller\Addon;

/**
 * 商品插件
 * @author 共振设计
 */

    class GoodsweixinAddon extends Addon{

        public $info = array(
            'name'=>'Goodsweixin',
            'title'=>'商品',
            'description'=>'微信微官网商品。',
            'status'=>1,
            'author'=>'共振设计',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Goodsweixin/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Goodsweixin/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }