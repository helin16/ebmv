<?php
require 'bootstrap.php';
$url = trim ( $_REQUEST ['url'] );
if($url === '')
	die;

$reuqestUrlParams = parse_url ( $url );
$proxy = new ProxyHandler ( array (
		'baseUri' => '/' . basename ( __FILE__ ),
		'proxyUri' => trim ( $_REQUEST ['url'] ) 
) );
$proxy->setCurlOption ( CURLOPT_HTTPHEADER, array('X-PARTNER: ebmv.com.au') );
// $proxy->setCurlOption ( CURLOPT_PROXY, 'proxy.bytecraft.internal:3128' );

ob_start ();
// Check for a success
if ($proxy->execute ()) {
	// print_r($proxy->getCurlInfo()); // Uncomment to see request info
} else {
	echo $proxy->getCurlError ();
}
$proxy->close ();
$html = trim(ob_get_contents ());
ob_end_clean ();

if($html === '') {
	FrontEndPageAbstract::show404Page('Can NOT requested page', 'Can NOT requested page: ' . $url);
}

$doc = new DOMDocument ();
@$doc->loadHTML ( $html ); // supress parsing errors with @

$imgs = $doc->getElementsByTagName ( 'img' );
foreach ( $imgs as $img ) {
	$img_src = trim($img->getAttribute ( 'src' ));
	if (substr($img_src, 0, 1) === '/') {
		$img->setAttribute ( 'src', $reuqestUrlParams['scheme'] . '://' . $reuqestUrlParams['host'] . $img_src );
	}
}
$html = $doc->saveHTML();
echo $html;
?>