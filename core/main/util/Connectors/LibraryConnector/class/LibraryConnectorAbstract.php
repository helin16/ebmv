<?php
/**
 * Library connector interface
 *
 * @package    Core
 * @subpackage Utils
 * @author     lhe<helin16@gmail.com>
 */
class LibraryConnectorAbstract
{
	/**
	 * The library this connect is for
	 * 
	 * @var Library
	 */
	protected $_lib;
	/**
	 * The cache for the scripts
	 * 
	 * @var array
	 */
	private static $_cache;
	/**
	 * Whether the connector is running in debug mode
	 * 
	 * @var bool
	 */
	private $_isDebugMode = false;
	/**
	 * Getting the library connector script
	 * 
	 * @param Library $lib The library we are getting the script for
	 * 
	 * @return LibraryConn
	 */
	public static function getScript(Library $lib)
	{
		if(!isset(self::$_cache[$lib->getId()]))
		{
			$scriptName = trim($lib->getConnector());
			self::$_cache[$lib->getId()] = new $scriptName($lib);
		}
		return self::$_cache[$lib->getId()];
	}
	/**
	 * construct
	 * 
	 * @param Library $lib
	 */
	public function __construct(Library $lib)
	{
		$this->_lib = $lib;
		$this->setDebugMode($this->_lib->isDebugMode());
	}
	/**
	 * Setter for the connector's running mode
	 * 
	 * @param boll $mode
	 * 
	 * @return LibraryConnectorAbstract
	 */
	public function setDebugMode($mode = false)
	{
		$this->_isDebugMode = $mode;
		return $this;
	}
	/**
	 * Getter for for the connector's running mode
	 *  
	 * @return boolean
	 */
	public function getDebugMode()
	{
		return $this->_isDebugMode;
	}
	/**
	 * Getting the formatted url
	 *
	 * @param string $url
	 * @param string $methodName
	 *
	 * @return string
	 */
	private function _formatURL($url, $params = array())
	{
		$url = $this->getLibrary()->getInfo('soap_wsdl');
		foreach($params as $key => $value)
			$url = str_replace('{' . $key . '}', trim($value), $url);
		return trim($url);
	}
	/**
	 * Getting the library from the library connector
	 *
	 * @return Library
	 */
	public function getLibrary()
	{
		return $this->_lib;
	}
}