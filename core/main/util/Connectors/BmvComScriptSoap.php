<?php
class BmvComScriptSoap
{
	/**
	 * @SoapClient
	 */
	private $_client;
	private static $_cache;
	/**
	 * Getting the BmvComScriptSoap
	 *  
	 * @param string $wsdl
	 * @param string $params
	 * 
	 * @return BmvComScriptSoap
	 */
	public static function getScript($wsdl, $params = null)
	{
		$key = md5($wsdl . json_encode($params));
		if(!isset(self::$_cache[$key]))
		{
			$className = trim(get_called_class());
			self::$_cache[$key] = new $className($wsdl, $params);
		}
		return self::$_cache[$key];
	}
	/**
	 * constructor
	 * 
	 * @param unknown $wsdl
	 * @param string $params
	 */
	public function __construct($wsdl, $params = null)
	{
		if($params === null)
			$params = array('exceptions' => true, 'encoding'=>'utf-8', 'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP);
		$this->_client = new SoapClient($wsdl, $params);
	}
	/**
	 * Calling the function of a soup
	 * 
	 * @param string $funcName
	 * @param string $params
	 * @param string $succ
	 * 
	 * @return SimpleXMLElement|null
	 */
	public function call($funcName, $params, &$succ = false)
	{
		$result = null;
		try 
		{
			$result = $this->_client->$funcName($params);
			$result = new SimpleXMLElement($result);
			$succ = true;
		}
		catch (Exception $ex)
		{
			$succ = false;
		}
		return $result;
	}
}