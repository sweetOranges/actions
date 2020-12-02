<?php

namespace App\Application;

class Notifyer {

	public function run() {
		$calender = $this->get_calender();
		$nextTimeStamp =  strtotime('+1 day');
		$nextDay = date('Ymd', $nextTimeStamp);
		if(!isset($calender[$nextDay])) {
			\App\Helper::L("$nextDay is empty");
			return;
		}
		$nl = $calender[$nextDay];
		\App\Helper::L("nextDay=$nextDay nl=$nl");
		$config = \App\Helper::C();
		if (isset($config['notifyer-1']) && isset($config['notifyer-1'][$nl])) {
			$eventname = $config['notifyer-1'][$nl];
			\App\Helper::P(sprintf("[github] notifyer 明天是%s", $eventname));
		}
		$yl = date('m月d日', $nextTimeStamp);
		if (isset($config['notifyer-2']) && isset($config['notifyer-2'][$yl])) {
			$eventname = $config['notifyer-2'][$yl];
			\App\Helper::P(sprintf("[github] notifyer 明天是%s", $eventname));
		}
	}

	private function get_calender() {
		$lines = explode("\n", file_get_contents(__ROOT__ . '/data/calender.txt'));
		$calender = [];
		array_shift($lines); // skip title
		foreach ($lines as $line) {
			if (empty($line)) continue;
			$a = explode("\t", $line);
			if (count($a) != 7) continue;
			$calender[$a[0]] = "{$a[3]}{$a[4]}";
		}
		return $calender;
	}
}