<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
require_once dirname(__FILE__) . '/bootstrap.php';
try
{
	$host = '206.187.32.61';
	$port = '8163';
	$patron = 'BMVCO';
	$patronPwd = 'YPRLBMV';
	$result = BmvComSIP2::getSIP($host, $port, $patron, $patronPwd)->connect();
	var_dump($result);
}
catch(Exception $ex)
{
	echo '<h3>' . $ex->getMessage() . '</h3>';
	echo $ex->getTraceAsString();
}
?>
</body>
</html>