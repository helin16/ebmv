<?php
define('PRADO_CHMOD',0755);
set_include_path('F:/mySandBox/ezcashflow/Web' . PATH_SEPARATOR . 'F:/mySandBox/ezcashflow/Web');
$basePath=dirname(__FILE__);
$assetsPath=$basePath.'/assets';
$runtimePath=$basePath.'/protected/runtime';

if(!is_writable($assetsPath))
	die("Please make sure that the directory $assetsPath is writable by Web server process.");
if(!is_writable($runtimePath))
	die("Please make sure that the directory $runtimePath is writable by Web server process.");

require 'bootstrap.php';
$application=new TApplication;
$application->run();


?>