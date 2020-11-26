<?php

date_default_timezone_set('Asia/Shanghai');

define('__ROOT__', dirname(__FILE__));

require __ROOT__ . '/vendor/autoload.php';

$app = isset($argv[1]) ? ucfirst($argv[1]) : 'Index';
$action = isset($argv[2]) ? ucfirst($argv[2]) : 'run';

$className = "\\App\\Application\\$app";
if (!class_exists($className)) {
	\App\Util::log(sprintf("%s not found", $app));
    exit;
}


$class = new \ReflectionClass($className);

if (!$class->hasMethod($action)) {
    \App\Util::log(sprintf("%s not found in %s", $action));
    exit;
}
$method = $class->getMethod($action);
if ($method->isPrivate() || $method->isProtected()) {
  	\App\Util::log(sprintf("%s can not be invoke", $action));
    exit;
}
$instance = $class->newInstance();
$method->invoke($instance);
exit;