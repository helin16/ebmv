<?php
require_once 'bootstrap.php';
class ReservseProxy
{
	private function _replaceTag(&$dom, $attributeName)
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
		$moreCss = '';
		$extraCss = array('blocks.css', 'comments.css', 'navigation.css', 'normalize.css', 'pages.css', 'print.css', 'style.css');
		foreach($extraCss as $css)
			$moreCss .= '<link rel="stylesheet" type="text/css" href="/' . basename(__FILE__) . '?url=http://www.chinesecio.com/cms/sites/all/themes/cio/css/' . $css . '">';
		$moreJs = $this->_getMoreJs();
		$head = $dom->find("head", 0);
		$head->outertext = $head->makeup() . $head->innertext . $moreCss . $moreJs . '</head>';
		return trim($dom);
	}
	private function _getMoreJs()
	{
		return '<script language="javascript">
				<!--
				' . file_get_contents(dirname(__FILE__) . '/chineseJs.js') . 
				'//-->
				</script>';
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
				header("Content-Disposition: attachment;filename='" . $fileName . "'");
				break;
			}
			case 'mp3':
			{
				header("Content-Disposition: attachment;filename='" . $fileName . "'");
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
	
	public function render($url, $caching = true, $directRead = false)
	{
		$isHTML = $this->_addHeader($url);
		if($directRead === true)
			$isHTML = false;
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
$directRead = (! isset ( $_REQUEST ['directRead'] ) || trim ( $_REQUEST ['directRead'] ) !== '1') ? true : false;
$proxy = new ReservseProxy();
echo $proxy->render($url, $caching, $directRead);
?>