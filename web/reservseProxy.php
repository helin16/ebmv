<?php
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
	$content = preg_replace ( "#(<\s*src\s*=\s*[\"']\s*)(?!http)([^\"'>]+)(\s*[\"'>]+)#", '$1/' . basename(__FILE__) . '?url=http://www.chinesecio.com/$2$3', $content );
	$content = preg_replace ( "#(<\s*href\s*=\s*[\"']\s*)(?!http)([^\"'>]+)(\s*[\"'>]+)#", '$1/' . basename(__FILE__) . '?url=http://www.chinesecio.com/$2$3', $content );
	return $content;
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