<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
require_once dirname(__FILE__) . '/bootstrap.php';


function Encrypt($data, $secret)
{
	//Generate a key from a hash
	$key = md5(utf8_encode($secret), true);

	//Take first 8 bytes of $key and append them to the end of $key.
	$key .= substr($key, 0, 8);

	//Pad for PKCS7
	$blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$len = strlen($data);
	$pad = $blockSize - ($len % $blockSize);
	$data .= str_repeat(chr($pad), $pad);

	//Encrypt data
	$encData = mcrypt_encrypt('tripledes', $key, $data, MCRYPT_MODE_CBC);

	return base64_encode($encData);
}

var_dump(Encrypt('testing1$apabi$2011041812:20', 'apabikey'));

try
{
// 	$xml = simplexml_load_file(dirname(__FILE__) . '/test.xml');
// 	var_dump($xml);
	Core::setUser(UserAccountService::getInstance('UserAccount')->get(1));
// 	$result = SupplierConnectorAbstract::getInstance(SupplierService::getInstance('Supplier')->get(9), LibraryService::getInstance('Library')->get(1))
// 		->getProduct('n.D310000dycjrb', '');
// 	var_dump($result);

	$url = 'http://www.apabi.com/texas/';
	$now = new UDate('now', 'Asia/Hong_Kong');
	$uid = 'testing1';
	$sign = Encrypt($uid . '$texas$' . $now->format('YmdH:i'), "apabikey");
	$data = array(
		'pid' => 'sso'
		,'uid' => $uid
		,'pwd'=> '11'
		,'sign' => $sign
		,'returnurl' => 'http://www.apabi.com/tiyan/?pid=newspaper.page&issueid=nq.D310000dycjrb_20140709&cult=CN'
		,'autoreg' => '1'
		,'pdm' => '2'
		,'errorurl'=>'http://ebmv.com.au'
	);
	var_dump($sign);
	$result = BmvComScriptCURL::readUrl($url, BmvComScriptCURL::CURL_TIMEOUT, $data);
	echo $result;
	
	
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