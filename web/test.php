<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
require_once dirname(__FILE__) . '/bootstrap.php';

function encrypt($encrypt, $key="") 
{
	$iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND );
	$passcrypt = mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $key, $encrypt, MCRYPT_MODE_ECB, $iv );
	$encode = base64_encode ( $passcrypt );
	return $encode;
}
$result = encrypt('testing1$apabi$2011041812:20','apabikey');
var_dump($result);die;
try
{
// 	$xml = simplexml_load_file(dirname(__FILE__) . '/test.xml');
// 	var_dump($xml);
	Core::setUser(UserAccountService::getInstance('UserAccount')->get(1));
	ImportProduct::run(array('37'), array(8), 10);
// 	$result = SupplierConnectorAbstract::getInstance(SupplierService::getInstance('Supplier')->get(9), LibraryService::getInstance('Library')->get(1))
// 		->getProduct('n.D310000dycjrb', '');
// 	var_dump($result);

// 	$url = 'http://www.apabi.com/texas/';
// 	$now = new UDate('now', 'Asia/Hong_Kong');
// 	$uid = 'testing1';
// 	$sign = Encrypt($uid . '$texas$' . $now->format('YmdH:i'), "apabikey");
// 	$data = array(
// 		'pid' => 'sso'
// 		,'uid' => $uid
// 		,'pwd'=> '11'
// 		,'sign' => $sign
// 		,'returnurl' => 'http://www.apabi.com/tiyan/?pid=newspaper.page&issueid=nq.D310000dycjrb_20140709&cult=CN'
// 		,'autoreg' => '1'
// 		,'pdm' => '2'
// 		,'errorurl'=>'http://ebmv.com.au'
// 	);
// 	var_dump($sign);
// 	$result = BmvComScriptCURL::readUrl($url, BmvComScriptCURL::CURL_TIMEOUT, $data);
// 	echo $result;
	
	
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
?>
</body>
</html>