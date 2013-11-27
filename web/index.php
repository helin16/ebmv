<?php
define('PRADO_CHMOD',0755);
$basePath=dirname(__FILE__);
$assetsPath=$basePath.'/assets';
$runtimePath=$basePath.'/protected/runtime';

if(!is_writable($assetsPath))
	die("Please make sure that the directory $assetsPath is writable by Web server process.");
if(!is_writable($runtimePath))
	die("Please make sure that the directory $runtimePath is writable by Web server process.");

require 'bootstrap.php';
//enforce https
if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on")
{
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

try
{
	Config::setConfFile($_SERVER['SERVER_NAME']);
}
catch(Exception $e)
{
	header("HTTP/1.0 404 Not Found");
	echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    exit();
}
$application=new TApplication;
$application->run();
?>