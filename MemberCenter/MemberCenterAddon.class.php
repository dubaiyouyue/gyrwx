<?php

namespace Addons\MemberCenter;
use Common\Controller\Addon;

/**
 * 会员中心插件
 * @author 共振设计
 */

    class MemberCenterAddon extends Addon{

        public $info = array(
            'name'=>'MemberCenter',
            'title'=>'会员中心',
            'description'=>'会员中心。',
            'status'=>1,
            'author'=>'共振设计',
            'version'=>'0.1',
            'has_adminlist'=>0
        );

	public function install() {
		$install_sql = './Addons/MemberCenter/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/MemberCenter/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }