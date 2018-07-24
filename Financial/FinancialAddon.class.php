<?php

namespace Addons\Financial;
use Common\Controller\Addon;

/**
 * 支付记录插件
 * @author 共振
 */

    class FinancialAddon extends Addon{

        public $info = array(
            'name'=>'Financial',
            'title'=>'支付记录',
            'description'=>'支付记录，财务系统。',
            'status'=>1,
            'author'=>'共振',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Financial/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Financial/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }