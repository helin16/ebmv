<?php
require 'bootstrap.php';


if (! isset ( $_REQUEST ['url'] ) || ($url = trim ( $_REQUEST ['url'] )) === '')
	die ();
$proxy = new ProxyHandler(array(
		'proxyUri' => '',
		'baseUri' => '/' . basename(__FILE__) ,
		'requestUri' => $url,
));

// Prevents cURL from hanging on errors
$proxy->setCurlOption(CURLOPT_CONNECTTIMEOUT, 1);
$proxy->setCurlOption(CURLOPT_TIMEOUT, 5);

// Check for a success
if ($proxy->execute()) {
	//print_r($proxy->getCurlInfo()); // Uncomment to see request info
} else {
	echo $proxy->getCurlError();
}

$proxy->close();