<?php

namespace App;

class Util {

	public static function get_config() {
		static $config = [];
		if (empty($config)) {
			$str = self::decrypt(file_get_contents(__ROOT__ . '/config.ini'), getenv('secret_key'));
			$config = parse_ini_string($str, true);
		}
		return $config;
	}

	public static function http_get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, 'https://') !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus['http_code']) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

	public static function http_post($url, $param, $post_file = false)
	{
		$oCurl = curl_init();
		if (stripos($url, 'https://') !== false) {
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (is_string($param) || $post_file) {
			$strPOST = $param;
		} else {
			$aPOST = [];
			foreach ($param as $key => $val) {
				$aPOST[] = $key.'='.urlencode($val);
			}
			$strPOST = join('&', $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($oCurl, CURLOPT_POST, true);

		curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);

		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if (intval($aStatus['http_code']) == 200) {
			return $sContent;
		} else {
			return false;
		}
	}

	public static function wechat_push($msg) {
		$access_token = '';
		$config = self::get_config();
		do {
			$filename = __ROOT__ . '/run/access_token.json';
			if(file_exists($filename)) {
				$json = json_decode(file_get_contents($filename), true);
				if (!empty($json) && $json['expire'] > time()) {
					$access_token = $json['access_token'];
					break;
				}
			}
			$appid = $config['wechat']['appid'];
			$secret = $config['wechat']['secret'];
			$url = sprintf('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s', $appid, $secret);
			$json = json_decode(file_get_contents($url), true);
			$data = ['access_token' => $json['access_token'], 'expire' => time() + $json['expires_in']];
			file_put_contents($filename, json_encode($data));
			$access_token = $json['access_token'];
		} while (false);

		$url = sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s', $access_token);

		$openids = explode(",", $config['wechat']['openids']);
		foreach($openids as $id) {
			$data = [
				'touser' => $id,
				'template_id' => $config['wechat']['template_id'],
				'data'=> ['msg'=> ['value' => $msg]]
			];
			$res = self::http_post($url, json_encode($data));
			self::log($res);
		}
	}


	public static function decrypt($str, $secret) {
		$decrypted = openssl_decrypt(base64_decode($str), 'AES-128-ECB', $secret, OPENSSL_RAW_DATA);
		return $decrypted;
	}

	public static function encrypt($str, $secret) {
	    $data = openssl_encrypt($str, 'AES-128-ECB', $secret, OPENSSL_RAW_DATA);
	    $data = base64_encode($data);
	    return $data;
	}

	public static function log($msg) {
		echo sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $msg);
	}
}