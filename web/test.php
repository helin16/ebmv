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
	$host = '206.187.32.61';
	$port = '8163';
	$patron = '20007005832986';
	$patronPwd = '1234';
	$location = 'BOX';
	
	$sip = new SIP2();
	$sip->AN = $location;
	$sip->hostname = $host;
	$sip->port = $port;
	$sip->patron = $patron;
	$sip->connect();
	
	//send selfcheck status message
	$in = $sip->msgSCStatus();
	$result = $sip->parseACSStatusResponse($sip->get_message($in));
	var_dump('SElf check:');
	var_dump($result);
	
	$in = $sip2->msgPatronInformation('none');
	var_dump('msgPatronInformation:');
	var_dump($in);
	
	$msgIn = $sip2->get_message($in);
	var_dump('get_message:');
	var_dump($msgIn);
	// parse the raw response into an array
	$result =  $sip2->parsePatronInfoResponse( $msgIn );
	var_dump('parsePatronInfoResponse:');
	var_dump($result);
		
	//disconnect the link
	$sip2->disconnect();
	
// 	$patron = '11380047hj';
// 	$patronPwd = '1234cxzcx';
// // 	$result = BmvComSIP2::getSIP(BaseServiceAbastract::getInstance('Library')->get(2), $host, $port, $patron, $patronPwd)
// // 		->login('11380047', '1234');
// 	$result = BmvComSIP2::getSIP($host, $port, BaseServiceAbastract::getInstance('Library')->get(4)->getInfo('lib_timezone'))->getPatronInfo($patron, $patronPwd);
// 	var_dump($result);
	echo '</pre>';
}
catch(Exception $ex)
{
	echo '<h3>' . $ex->getMessage() . '</h3>';
	echo $ex->getTraceAsString();
}
?>
</body>
</html>