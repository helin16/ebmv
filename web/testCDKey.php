<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
require_once dirname(__FILE__) . '/bootstrap.php';
try
{
	echo '<pre>';
	$key = isset($_REQUEST['key']) ? trim($_REQUEST['key']) : '';
	$uid = isset($_REQUEST['uid']) ? trim($_REQUEST['uid']) : '';
	$siteid = isset($_REQUEST['siteid']) ? trim($_REQUEST['siteid']) : '';
	echo 'key: ' . $key . '<br />';
	echo 'uid: ' . $uid . '<br />';
	echo 'siteid: ' . $siteid . '<br />';
	$cdkey = StringUtilsAbstract::getCDKey($key, $uid, $siteid);
	echo 'CDKey: ' . $cdkey . '<br />';
	echo '</pre>';
}
catch(Exception $ex)
{
	echo '<h3>' . $ex->getMessage() . '</h3>';
	echo $ex->getTraceAsString();
}
	foreach(Log::getLatestLogs() as $log)
	{
		echo $log . '<br />';
	}
?>
</body>
</html>