<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
require_once dirname(__FILE__) . '/bootstrap.php';
try
{
// 	Core::setUser(BaseServiceAbastract::getInstance('UserAccount')->get(11));
	error_reporting(E_ALL ^ E_NOTICE);
	echo '<pre>';
	ImportProduct::run(array(), array(), 1);
// 	SupplierConnectorAbstract::getInstance(BaseServiceAbastract::getInstance('Supplier')->get(3), 
// 		BaseServiceAbastract::getInstance('Library')->get(1))
// 		->getOnlineReadUrl(BaseServiceAbastract::getInstance('Product')->get(6), Core::getUser())
// 		;
// 	$localFile = '/tmp/test.jpg';
// 	BmvComScriptCURL::downloadFile('http://images.takungpao.com/2013/0825/thumb_347_575_20130825023025149.jpg', $localFile);
// // 	BmvComScriptCURL::downloadFile('http://ebmv.com.au/asset/get?id=4103fbf3c54f013248d825f57375c330', $localFile);
// 	ImageUtilsAbstract::resizeImage($localFile, 150, 197);
// 	ImageUtilsAbstract::saveImage('test.png', 80);

// 	$localFile = '/tmp/test.html';
// 	BmvComScriptCURL::downloadFile('http://news.takungpao.com.hk/paper/20140219.html', $localFile);
// 	$doc = new DOMDocument();
// 	$doc->loadHTMLFile($localFile);
	
// 	$xpath = new DOMXPath($doc);
// 	$books = $xpath->query("//div[@class='books']/div/a/img");
// 	if(count($books) > 0)
// 	{
// 		$src = $books->item(0)->getAttribute('src');
// 		var_dump($src);
// 	}
	
	echo '</pre>';
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