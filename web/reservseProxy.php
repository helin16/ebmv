<?php
	$url = $_REQUEST ['url'];
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
	echo $content;
?>