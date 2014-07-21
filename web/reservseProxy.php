<?php
if (! isset ( $_REQUEST ['url'] ) || ($url = trim ( $_REQUEST ['url'] )) === '')
	die ();
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
$content = file_get_contents ( $url, false, $context );
$content = preg_replace ( "#(<\s*img\s+[^>]*src\s*=\s*[\"']\s*)(?!http)([^\"'>]+)(\s*[\"'>]+)#", '$1http://www.chinesecio.com/$2$3', $content );
$content = preg_replace ( "#(<\s*a\s+[^>]*href\s*=\s*[\"']\s*)(?!http)([^\"'>]+)(\s*[\"'>]+)#", '$1http://www.chinesecio.com/$2$3', $content );
echo $content;
?>