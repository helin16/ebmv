<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
require_once dirname(__FILE__) . '/bootstrap.php';


function Encrypt($string, $key)
{
 	//Encryption
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_ECB), MCRYPT_RAND); 
    $encrypted_string = mcrypt_encrypt(MCRYPT_TRIPLEDES, $key, $string, MCRYPT_MODE_ECB, $iv); 
    return base64_encode($encrypted_string);
}


// $expected = 'gxapNMS25J1q3X/TO4tp+5Kq6d/K0LzHreBZZ6PnMj4=';
// $result = trim(Encrypt('testing1$apabi$2011041812:20', 'apabikey'));
// var_dump('Excepted: ' . $expected);
// var_dump('GOT:' . $result);
// var_dump('They are ' . ($expected === $result ? ' EQUAL!!!' : 'NOT equal!'));
// die;

try
{
// 	$xml = simplexml_load_file(dirname(__FILE__) . '/test.xml');
// 	var_dump($xml);
	Core::setUser(UserAccountService::getInstance('UserAccount')->get(1));
// 	$result = SupplierConnectorAbstract::getInstance(SupplierService::getInstance('Supplier')->get(9), LibraryService::getInstance('Library')->get(1))
// 		->getProduct('n.D310000dycjrb', '');
// 	var_dump($result);

	$xxx2 = 'tiyan';
	$url = 'http://www.apabi.com/' . $xxx2 . '/';
	$now = new UDate('now', 'Asia/Hong_Kong');
	$uid = 'auchen';
	$myEncryptData = $uid . '$' . $xxx2 . '$' . $now->format('YmdH:i');
	$myEncryptKey = "apabikey";
	$sign = Encrypt($myEncryptData, $myEncryptKey);
	var_dump('DES("' . $myEncryptData . '", ' . $myEncryptKey . '") = ' . $sign);
	$data = array(
		'pid' => 'sso'
		,'uid' => $uid
		,'pwd'=> md5('111111')
		,'sign' => $sign
		,'returnurl' => 'http://www.apabi.com/' . $xxx2 . '/?pid=newspaper.page&issueid=nq.D310000dycjrb_20140709&cult=CN'
		,'autoreg' => '1'
		,'pdm' => '0'
		,'errorurl'=>'http://ebmv.com.au'
	);
	var_dump($url . '?' . http_build_query($data));
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