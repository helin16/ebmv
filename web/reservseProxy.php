<?php
function replaceTag(&$dom, $tagName, $attributeName)
{
	foreach($dom->getElementsByTagName($tagName) as $node)
	{
		if(!$node->hasAttribute($attributeName))
			continue;
		$link = trim($node->getAttribute($attributeName));
		if(substr($link, 0, 1) === '/')
			$link  = 'http://www.chinesecio.com' . $link;
		$node->setAttribute($attributeName, '/' . basename(__FILE__) . '?url=' . $link);
	}
}

function getHTML($pageUrl)
{
	// to specify http headers like `User-Agent`,
	// you could create a context like so:
	$options = array (
			'http' => array (
					'method' => "GET",
					'header' => "User-Agent: PHP\r\nX-PARTNER:ebmv.com.au"
			)
	);
	// create context
	$context = stream_context_create ( $options );
	// open file with the above http headers
	$content = file_get_contents ( $pageUrl, false, $context );
	
	$dom = new DOMDocument();
	$dom->loadHTML($content);
	replaceTag($dom, 'img', 'src');
	replaceTag($dom, 'a', 'href');
	replaceTag($dom, 'script', 'src');
	replaceTag($dom, 'link', 'href');
	return $dom->saveHTML();
}

if (! isset ( $_REQUEST ['url'] ) || ($url = trim ( $_REQUEST ['url'] )) === '')
	die ();

$caching = (! isset ( $_REQUEST ['nocaching'] ) || trim ( $_REQUEST ['nocaching'] ) !== '1') ? true : false;
if($caching === true && extension_loaded('apc') && ini_get('apc.enabled'))
{
	$key = md5($url);
	if(!apc_exists($key))
	{
		$html = getHTML($url);
		apc_add($key, $html);
	}
	else
		$html = apc_fetch($key);
}
else
	$html = getHTML($url);
echo $html;
?>