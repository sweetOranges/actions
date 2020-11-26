<?php

namespace App\Application;

class InI {
	
	public function encode() {
		file_put_contents(__ROOT__. '/config.ini',  \App\Util::encrypt(file_get_contents(__ROOT__. '/config.dev.ini'), getenv('secret_key')));
		\App\Helper::L('ini encrypt done!');
	}

	public function decode() {
		file_put_contents(__ROOT__. '/config.dev.ini',  \App\Util::decrypt(file_get_contents(__ROOT__. '/config.ini'), getenv('secret_key')));
		\App\Helper::L('ini decrypt done!');
	}
}