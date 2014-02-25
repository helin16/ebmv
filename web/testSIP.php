<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<style>
.testDiv {
	display: block;
	margin: 20px;
}
.testDiv .request {
	font-size: 20px;
	font-weight: bold;
}
.testDiv .response {
	padding: 0 0 0 20px;
}
.testDiv .smltxt {
	font-size: 10px;
	font-style: italic;
}
.testDiv .blockView {
	overflow: auto;
	height: 100px;
}
</style>
<?php
require_once dirname(__FILE__) . '/bootstrap.php';
try
{
	if(!isset($_REQUEST['hostName']) || ($hostname = trim($_REQUEST['hostName'])) === '')
		throw new Exception('hostName needed!');
	if(!isset($_REQUEST['port']) || ($port = trim($_REQUEST['port'])) === '')
		throw new Exception('port needed!');
	if(!isset($_REQUEST['patron']) || ($patron = trim($_REQUEST['patron'])) === '')
		throw new Exception('patron needed!');
	if(!isset($_REQUEST['patronpwd']) || ($patronpwd = trim($_REQUEST['patronpwd'])) === '')
		throw new Exception('patronpwd needed!');
	
	$mysip = new SIP2();
	// Set host name
	$mysip->hostname = $hostname;
	$mysip->port = $port;
	
	// Identify a patron
	$mysip->patron = $patron;
	$mysip->patronpwd = $patronpwd;
	
	// asgining all params
	$refClass = new ReflectionClass($mysip);
	$props  = $refClass->getProperties(ReflectionProperty::IS_PUBLIC);
	foreach($props as $pro)
	{
		if(isset($_REQUEST[$pro->name]))
			$mysip->$pro = $_REQUEST[$pro->name];
	}
	
	// connect to SIP server
	echo '<div class="testDiv">';
		echo '<h3 class="request">Connect to ' . $mysip->hostname . ':' . $mysip->port . '</h3>';
		$result = $mysip->connect();
		echo '<div class="response">Result: ' . print_r($result, true) . '</div>';
	echo '</div>';
	
	// selfcheck status mesage goes here...
	$in = $mysip->msgSCStatus();
	echo '<div class="testDiv">';
		echo '<h3 class="request">Self check <span class="smltxt">' . $in . '</span></h3>';
		$rawResp = $mysip->get_message($in);
		$result = $mysip->parseACSStatusResponse($rawResp);
		echo '<div class="response">Result <span class="smltxt">Raw response: ' . $rawResp . '</span>:<div class="blockView">' . print_r($result, true). '</div></div>';
	echo '</div>';
	
	
	// Get Charged Items Raw response
	$in = $mysip->msgPatronInformation('charged');
	echo '<div class="testDiv">';
		echo '<h3 class="request">Get Raw Response for charged:<span class="smltxt">' . print_r($in, true) . '</span></h3>';
		$rawResp = $mysip->get_message($in);
		// parse the raw response into an array
		$result = $mysip->parsePatronInfoResponse($rawResp);
		echo '<div class="response">Result <span class="smltxt">Raw response: ' . $rawResp . '</span>:<div class="blockView">' . print_r($result, true). '</div></div>';
	echo '</div>';
	
	
	echo '<h3>Result came back from '. $wsdl . ':</h3>';
	echo '<textarea style="width: 100%; height: 200px;">' . $result . '</textarea>';
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