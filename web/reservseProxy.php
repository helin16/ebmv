<?php
class ReservseProxy
{
	private function _replaceTag(&$dom, $tagName, $attributeName)
	{
		foreach($dom->find('[' . $attributeName . ']') as $node)
		{
			$link = trim($node->$attributeName);
			if(substr($link, 0, 1) === '/' || substr($link, 0, 4) === 'http')
			{
				if(substr($link, 0, 1) === '/')
					$link  = 'http://www.chinesecio.com' . $link;
				$node->$attributeName =  '/' . basename(__FILE__) . '?url=' . $link;
			}
		}
		return $this;
	}
	
	private function _getContent($pageUrl, $isHTML = true)
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
		
		$dom = Simple_HTML_DOM_Abstract::str_get_html($content);
		$this->_replaceTag($dom, 'src')
			->_replaceTag($dom, 'href');
		return trim($dom);
	}
	
	private function _addHeader($url)
	{
		$linkC = parse_url($url);
		$fileName = trim(basename($linkC['path']));
		$ext = trim(pathinfo($fileName, PATHINFO_EXTENSION));
		$isHTML = false;
		switch(strtolower($ext))
		{
			case 'png':
			case 'jpg':
			case 'gif':
			case 'jpge':
			{
				header("content-type: image/" . $ext);
				break;
			}
			case 'js':
			{
				header('Content-Type: application/javascript');
				break;
			}
			case 'css':
			{
				header("Content-type: text/css", true);
				break;
			}
			case 'pdf':
			{
				header("Content-type:application/pdf");
				header("Content-Disposition:attachment;filename='" . $fileName . "'");
				break;
			}
			case 'mp3':
			{
				header("Content-Disposition: inline;filename='" . $fileName . "'");
				break;
			}
			default:
			{
				$isHTML = true;
				break;
			}
		}
		return $isHTML;
	}
	
	public function render($url, $caching = true)
	{
		$isHTML = $this->_addHeader($url);
		if($caching === true && extension_loaded('apc') && ini_get('apc.enabled'))
		{
			$key = md5($url);
			if(!apc_exists($key))
			{
				$html = $this->_getContent($url, $isHTML);
				apc_add($key, $html);
			}
			else
				$html = apc_fetch($key);
		}
		else
			$html = $this->_getContent($url, $isHTML);
		return $html;
	}
}


if (! isset ( $_REQUEST ['url'] ) || ($url = trim ( $_REQUEST ['url'] )) === '')
	die ();
$caching = (! isset ( $_REQUEST ['nocaching'] ) || trim ( $_REQUEST ['nocaching'] ) !== '1') ? true : false;
$proxy = new ReservseProxy();
echo $proxy->render($url, $caching);
?>