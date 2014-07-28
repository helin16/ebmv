<?php
function replaceTag(&$dom, $tagName, $attributeName)
{
	foreach($dom->getElementsByTagName($tagName) as $node)
	{
		if(!$node->hasAttribute($attributeName))
			continue;
		$link = trim($node->getAttribute($attributeName));
		if(substr($link, 0, 1) === '/' || substr($link, 0, 4) === 'http')
		{
			if(substr($link, 0, 1) === '/')
				$link  = 'http://www.chinesecio.com' . $link;
			$node->setAttribute($attributeName, '/' . basename(__FILE__) . '?url=' . $link);
		}
	}
}

function getHTML($pageUrl, $isHTML = true)
{
	// to specify http headers like `User-Agent`,
	// you could create a context like so:
	if($isHTML === false)
		return file_get_contents ( $pageUrl);
	
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
	
	$content = str_replace('url("', 'url("/'. basename(__FILE__) . '?url=', $content);
	
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

$linkC = parse_url($url);
$ext = strtolower(trim(pathinfo(basename($linkC['path']), PATHINFO_EXTENSION)));
$isHTML = true;
if(in_array($ext, array('png', 'jpg', 'gif', 'jpge')))
{
	header("content-type: image/" . $ext);
	$isHTML = false;
}
else if($ext === 'js')
{
	header('Content-Type: application/javascript');
	$isHTML = false;
}
else if($ext === 'css')
{
	header("Content-type: text/css", true);
	$isHTML = false;
}

$caching = (! isset ( $_REQUEST ['nocaching'] ) || trim ( $_REQUEST ['nocaching'] ) !== '1') ? true : false;
if($caching === true && extension_loaded('apc') && ini_get('apc.enabled'))
{
	$key = md5($url);
	if(!apc_exists($key))
	{
		$html = getHTML($url, $isHTML);
		apc_add($key, $html);
	}
	else
		$html = apc_fetch($key);
}
else
	$html = getHTML($url, $isHTML);
echo $html;
?>