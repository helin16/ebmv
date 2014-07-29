<?php
require_once 'bootstrap.php';

class ReservseProxy
{
	/**
	 * @var string
	 */
	const RN = "\r\n";
	
	/**
	 * @var boolean
	 */
	private $_cacheControl = false;
	/**
	 * @var boolean
	 */
	private $_chunked = false;
	/**
	 * @var array
	 */
	private $_clientHeaders = array ();
	/**
	 * @var resource
	 */
	private $_curlHandle;
	/**
	 * @var boolean
	 */
	private $_pragma = false;
	/**
	 * whether this is cached in apc
	 * 
	 * @var bool
	 */
	private $_apc_cached = false;
	/**
	 * The key of the apc cache
	 * 
	 * @var bool
	 */
	private $_apc_key = '';
	/**
	 * The content the get from curl
	 * 
	 * @var string
	 */
	private $_content = '';
	/**
	 * constructor
	 * 
	 * @param string $targetURL
	 */
	public function __construct($targetURL, $caching = true)
	{
		$this->_apc_key = md5($targetURL);
		if($caching === true && extension_loaded('apc') && ini_get('apc.enabled') && apc_exists($this->_apc_key))
		{
			$this->_content = apc_fetch($this->_apc_key);
			$this->_apc_cached = true;
		}
		else
		{
			$this->_curlHandle = curl_init($targetURL);
			// Set various cURL options
			$this->setCurlOption(CURLOPT_FOLLOWLOCATION, true);
			$this->setCurlOption(CURLOPT_RETURNTRANSFER, true);
			$this->setCurlOption(CURLOPT_BINARYTRANSFER, true); // For images, etc.
			$this->setCurlOption(CURLOPT_WRITEFUNCTION, array($this, '_readResponse'));
			$this->setCurlOption(CURLOPT_HEADERFUNCTION, array($this, '_readHeaders'));
			$this->setCurlOption(CURLOPT_ENCODING, 'identity');
// 			$this->setCurlOption(CURLOPT_PROXY, 'proxy.bytecraft.internal:3128');
			// This ignores HTTPS certificate verification, libcurl decided not to bundle ca certs anymore.
			// Alternatively, specify CURLOPT_CAINFO, or CURLOPT_CAPATH if you have access to your cert(s)
			$this->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
			// Handle the client headers.
			$this->_handleClientHeaders();
		}
	}
	/**
	 * Sets a cURL option.
	 *
	 * @param string $option        	
	 * @param string $value        	
	 * @return void
	 *
	 */
	public function setCurlOption($option, $value)
	{
		curl_setopt ( $this->_curlHandle, $option, $value );
	}
	/**
	 * replacing the tags from DOMDocument
	 * 
	 * @param DOMDocument $dom
	 * @param string      $attributeName
	 * 
	 * @return ReservseProxy
	 */
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
	/**
	 * Used as value for cURL option CURLOPT_HEADERFUNCTION
	 *
	 * @param resource $cu
	 * @param string $string
	 * @return int
	 */
	private function _readHeaders(&$cu, $header)
	{
		$length = strlen($header);
	
		if (preg_match(',^Cache-Control:,', $header)) {
			$this->_cacheControl = true;
		}
		elseif (preg_match(',^Pragma:,', $header)) {
			$this->_pragma = true;
		}
		elseif (preg_match(',^Transfer-Encoding:,', $header)) {
			$this->_chunked = strpos($header, 'chunked') !== false;
		}
	
		if ($header !== self::RN) {
			header(rtrim($header));
		}
	
		return $length;
	}
	/**
	 * @param string $headerName
	 * @return void
	 */
	private function _removeHeader($headerName)
	{
		if (function_exists('header_remove')) {
			header_remove($headerName);
		} else {
			header($headerName . ': ');
		}
	}
	/**
	 * Getting content
	 * 
	 * @param resource $cu
	 * @param string   $body
	 * 
	 * @return int The lenght of the body
	 */
	private function _readResponse(&$cu, $body)
	{
		$headersParsed = false;
		// Clear the Cache-Control and Pragma headers
		// if they aren't passed from the proxy application.
		if ($headersParsed === false) {
			if (!$this->_cacheControl) {
				$this->_removeHeader('Cache-Control');
			}
			if (!$this->_pragma) {
				$this->_removeHeader('Pragma');
			}
			$headersParsed = true;
		}
		$length = strlen($body);
		if ($this->_chunked) {
			$this->_content .= dechex($length) . self::RN . $body . self::RN;
		} else {
			$this->_content .= $body;
		}
		return $length;
	}
	/**
	 * Close the cURL handle and a possible chunked response
	 *
	 * @return void
	 */
	public function close()
	{
		if ($this->_chunked) {
			$this->_content .= '0' . self::RN . self::RN;
		}
		
		$dom = Simple_HTML_DOM_Abstract::str_get_html($this->_content);
		if(trim($dom) !== '')
		{
			$this->_replaceTag($dom, 'src');
			$this->_replaceTag($dom, 'href');
			$this->_content = trim($dom);
		}
		
		if(extension_loaded('apc') && ini_get('apc.enabled') && $this->_apc_cached === false)
			apc_add($this->_apc_key, $this->_content);
		if($this->_curlHandle)
			curl_close($this->_curlHandle);
		return $this;
	}
	/**
	 * Executes the cURL handler, making the proxy request.
	 * Returns true if request is successful, false if there was an error.
	 * By checking this return, you may output the return from getCurlError
	 * Or output your own bad gateway page.
	 *
	 * @return boolean
	 */
	public function execute()
	{
		if($this->_apc_cached === true)
			return true;
		
		$this->setCurlOption(CURLOPT_HTTPHEADER, $this->_clientHeaders);
		return curl_exec($this->_curlHandle) !== false;
	}
	/**
	 * Get possible cURL error.
	 * Should NOT be called before exec.
	 *
	 * @return string
	 */
	public function getCurlError()
	{
		return curl_error($this->_curlHandle);
	}
	
	/**
	 * Get information about the request.
	 * Should NOT be called before exec.
	 *
	 * @return array
	 */
	public function getCurlInfo()
	{
		return curl_getinfo($this->_curlHandle);
	}
	/**
	 * Sets a new header that will be sent with the proxy request
	 *
	 * @param string $headerName
	 * @param string $value
	 * @return void
	 */
	public function setClientHeader($headerName, $value)
	{
		$this->_clientHeaders[] = $headerName . ': ' . $value;
	}
	/**
	 * @return array
	 */
	private function _getRequestHeaders()
	{
		if (function_exists('apache_request_headers')) {
			if ($headers = apache_request_headers()) {
				return $headers;
			}
		}
	
		$headers = array();
		foreach ($_SERVER as $key => $value) {
			if (substr($key, 0, 5) == 'HTTP_' && !empty($value)) {
				$headerName = strtolower(substr($key, 5, strlen($key)));
				$headerName = str_replace(' ', '-', ucwords(str_replace('_', ' ', $headerName)));
				$headers[$headerName] = $value;
			}
		}
		return $headers;
	}
	/**
	 * Called at the end of the constructor
	 *
	 * @return void
	 */
	private function _handleClientHeaders()
	{
		$headers = $this->_getRequestHeaders();
		$xForwardedFor = array();
	
		foreach ($headers as $headerName => $value) {
			switch($headerName) {
				case 'Host':
				case 'X-Real-IP':
					break;
				case 'X-Forwarded-For':
					$xForwardedFor[] = $value;
					break;
				default:
					$this->setClientHeader($headerName, $value);
					break;
			}
		}
	
		$xForwardedFor[] = $_SERVER['REMOTE_ADDR'];
		$this->setClientHeader('X-Forwarded-For', implode(',', $xForwardedFor));
		$this->setClientHeader('X-Real-IP', $xForwardedFor[0]);
// 		$this->setClientHeader('User-Agent', 'X-PARTNER:ebmv.com.au');
	}
	
	public function getContent()
	{
		return $this->_content;
	}
}


if (! isset ( $_REQUEST ['url'] ) || ($url = trim ( $_REQUEST ['url'] )) === '')
	die ();
$caching = (! isset ( $_REQUEST ['nocaching'] ) || trim ( $_REQUEST ['nocaching'] ) !== '1') ? true : false;

$proxy = new ReservseProxy($url, $caching);
if($proxy->execute())
{
	echo$proxy->close()
		 ->getContent();
}
else
{
	var_dump($proxy->getCurlError());
	$proxy->close();
}

?>