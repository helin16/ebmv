<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
require_once dirname(__FILE__) . '/bootstrap.php';
try
{
	error_reporting(E_ALL ^ E_NOTICE);
	echo '<pre>';
	$host = '206.187.32.61';
	$port = '8163';
	$patron = 'SIP';
	$patronPwd = 'SIP';
	
	$sip = new SIP2();
	$sip->hostname = $host;
	$sip->port = $port;
	$sip->patron = $username;
			$sip->patronpwd = $password;
			//connect to the ser
			$result = $sip->connect();
			if($result !== true)
				throw new CoreException('SIP2 can NOT connect to HOST:' . $sip->hostname . ':' . $sip->port);
			$connected = true;
			
			//send selfcheck status message
			$in = $sip->msgSCStatus();
			$result = $sip->parseACSStatusResponse($sip->get_message($in));
			
			/*  Use result to populate SIP2 setings
			 *   (In the real world, you should check for an actual value
			 		*   before trying to use it... but this is a simple example)
			*/
// 			$sip->AO = $result['variable']['AO'][0]; /* set AO to value returned */
// 			$sip->AN = $result['variable']['AN'][0]; /* set AN to value returned */
			$sip->AN = 'BOX'; /* set AN to value returned */
			
			// Get Charged Items Raw response
			$in = $sip->msgPatronInformation('none');
			
			// parse the raw response into an array
			$result =  $sip->parsePatronInfoResponse( $sip->get_message($in) );
			
			//disconnect the link
			$sip->disconnect();
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