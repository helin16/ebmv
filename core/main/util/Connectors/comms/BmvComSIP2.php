<?php
class BmvComSIP2
{
	/**
	 * The sip2 object
	 * 
	 * @var SIP2
	 */
	private $_sip2;
	/**
	 * The cache of the bmvcomsip2 objects
	 * 
	 * @var array
	 */
	private static $_cache;
	/**
	 * Getting the BmvComSIP2 script
	 * 
	 * @param unknown $host
	 * @param unknown $port
	 * @param unknown $patron
	 * @param unknown $patronPwd
	 * 
	 * @return BmvComSIP2
	 */
	public static function getSIP($host, $port, $patron, $patronPwd)
	{
		$key = md5($host . $port . $patron . $patronPwd);
		if(!isset(self::$_cache[$key]))
		{
			$className = trim(get_called_class());
			self::$_cache[$key] = new $className($host, $port, $patron, $patronPwd);
		}
		return self::$_cache[$key];
	}
	/**
	 * Constructor
	 * 
	 * @param string $host
	 * @param string $port
	 * @param string $patron
	 * @param string $patronPwd
	 * 
	 */
	public function __construct($host, $port, $patron, $patronPwd)
	{
		$this->_sip2 = new SIP2();
		$this->_sip2->hostname = $host;
		$this->_sip2->port = $port;
		$this->_sip2->patron = $patron;
		$this->_sip2->patronpwd = $patronPwd;
	}
	/**
	 * connects to a SIP2 server
	 * 
	 * @return boolean
	 */
	public function connect()
	{
		return $this->_sip2->connect();
	}
}