<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
require_once dirname(__FILE__) . '/bootstrap.php';
function resultFunc($funcName, $result)
{
	echo '<h3>Result came back from '. $funcName . ':</h3>';
	echo '<textarea style="width: 100%; height: 200px;">' . $result . '</textarea>';
	echo '</pre>';
}
try
{
	$wsdl ='https://ebmv.com.au/?soap=webauth.wsdl';
	$client = new SoapClient($wsdl, array('exceptions' => true, 'encoding'=>'utf-8', 'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP), 'cache_wsdl'=>WSDL_CACHE_NONE );
	$funcName = "authenticate";
	$result = $client->$funcName('A3ADC78482897208E84B759E41DD73E9', '37', 'testuser_yl', '2A2877E4DF17AEED392AA42AD36EE5190E1E1DCC');
	resultFunc($funcName, $result);
	
	$funcName = "getUserLocalInfo";
	$result = $client->getUserLocalInfo('37', 'test_user', sha1('test_pass'));
	resultFunc($funcName, $result);
}
catch(Exception $ex)
{
	echo '<h3>' . $ex->getMessage() . '</h3>';
	echo $ex->getTraceAsString();
}
?>
</body>
</html>