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
	 * The library
	 * 
	 * @var Library
	 */
	private $_lib;
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
	public static function getSIP(Library $lib, $host, $port, $patron, $patronPwd)
	{
		$key = md5($lib->getId(), $host . $port . $patron . $patronPwd);
		if(!isset(self::$_cache[$key]))
		{
			$className = trim(get_called_class());
			self::$_cache[$key] = new $className($lib, $host, $port, $patron, $patronPwd);
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
	public function __construct(Library $lib, $host, $port, $patron, $patronPwd)
	{
		$this->_lib = $lib;
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
		date_default_timezone_set($this->_lib->getInfo('lib_timezone'));
		$result = $this->_sip2->connect();
		var_dump('connect: ');
		var_dump($result);
		//send selfcheck status message
		$in = $this->_sip2->msgSCStatus();
		var_dump('msgSCStatus: ');
		var_dump($in);
		$result = $this->_sip2->parseACSStatusResponse($this->_sip2->get_message($in));
		var_dump('parseACSStatusResponse: ');
		var_dump($result);
		/*  Use result to populate SIP2 setings
		 *   (In the real world, you should check for an actual value
		 		*   before trying to use it... but this is a simple example)
		*/
		$this->_sip2->AO = $result['variable']['AO'][0]; /* set AO to value returned */
		$this->_sip2->AN = $result['variable']['AN'][0]; /* set AN to value returned */
		
		// Get Charged Items Raw response
		$in = $this->_sip2->msgPatronInformation('charged');
		var_dump('msgPatronInformation: ');
		var_dump($in);
		
		// parse the raw response into an array
		$result =  $this->_sip2->parsePatronInfoResponse( $this->_sip2->get_message($in) );
		var_dump('parsePatronInfoResponse ');
		var_dump($in);
		
		msgLogin
	}
	
}