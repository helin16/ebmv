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
	$patron = '20007005832986';
	$patronPwd = '1234';
	$sip = new SIP2();
	$sip->AN = 'BOX';
	$sip->hostname = $host;
	$sip->port = $port;
	$sip->patron = $patron;
	//send selfcheck status message
	$in = $sip->msgSCStatus();
	$result = $sip->parseACSStatusResponse($sip->get_message($in));
	var_dump($result);
	
// 	$patron = '11380047hj';
// 	$patronPwd = '1234cxzcx';
// 	echo '<pre>';
// // 	$result = BmvComSIP2::getSIP(BaseServiceAbastract::getInstance('Library')->get(2), $host, $port, $patron, $patronPwd)
// // 		->login('11380047', '1234');
// 	$result = BmvComSIP2::getSIP($host, $port, BaseServiceAbastract::getInstance('Library')->get(4)->getInfo('lib_timezone'))->getPatronInfo($patron, $patronPwd);
// 	var_dump($result);
// 	echo '</pre>';
}
catch(Exception $ex)
{
	echo '<h3>' . $ex->getMessage() . '</h3>';
	echo $ex->getTraceAsString();
}
?>
</body>
</html>