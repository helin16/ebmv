<?php
/**
 * Config loader
 * 
 * @package    Core
 * @subpackage Config
 * @author     lhe<helin16@gmail.com>
 */
abstract class Config
{
    const DEFAULT_CONF_FILE = 'defaultConfig.php';
    /**
     * The values in the config file
     * 
     * @var array
     */
	private static $_values = null;
	/**
	 * Getting the value from the config file
	 * 
	 * @param string $service The section name that we are trying to load
	 * @param string $name    The item name that we are trying to load
	 * 
	 * @return Mixed
	 * @throws Exception
	 */
	public static function get($service, $name)
	{
	    if(self::$_values === null)
			self::$_values = require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . self::DEFAULT_CONF_FILE);
		if(isset(self::$_values[$service]) && isset(self::$_values[$service][$name]))
			return self::$_values[$service][$name];
		throw new Exception("Service($service)/Name($name) not defined in config.");
	}
}

?>