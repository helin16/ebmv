<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
require_once dirname(__FILE__) . '/bootstrap.php';
try
{
// 	$xml = simplexml_load_file(dirname(__FILE__) . '/test.xml');
// 	var_dump($xml);
	Core::setUser(UserAccountService::getInstance('UserAccount')->get(1));
	$result = SupplierConnectorAbstract::getInstance(SupplierService::getInstance('Supplier')->get(9), LibraryService::getInstance('Library')->get(1))
		->getProductList();
	var_dump($result);
	
// 	$wsdl = "http://localhost:8080/?soap=webauth.wsdl";
// 	$params = array(
// 			'libCode' => '37',
// 			'Uid' => 'test_user',
// 			'Pwd' => sha1('test_pass'),
// 	);
// 	$client = new SoapClient($wsdl);
// 	$result = $client->getUserLocalInfo('37', 'test_user',  sha1('test_pass'));
// 	$params = array('37', 'test_user',  sha1('test_pass'));
// 	$result = BmvComScriptSoap::getScript($wsdl)->getUserLocalInfo('37', 'test_user',  sha1('test_pass'));
// 	$library = BaseServiceAbastract::getInstance('Library')->get(1);
// 	$connector = new LC_LocalSOAP(BaseServiceAbastract::getInstance('Library')->get(1));
// 	$result = $connector->getUserInfo($library, 'test_user', 'test_pass');
// 	$auth = new WebAuth();
// 	$result = $auth->getUserLocalInfo('37', 'test_user',  sha1('test_pass'));

	//test apabi
	var_dump('done');
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