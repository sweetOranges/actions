<?php

namespace App;


class Helper {

	public static function L($msg) {
		\App\Util::log($msg);
	}


	public static function P($msg) {
		\App\Util::wechat_push($msg);
	}

	public static function C() {
		return \App\Util::get_config();
	}

}