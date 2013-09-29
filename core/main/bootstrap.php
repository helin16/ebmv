<?php
/**
 * Boostrapper for the Core module
 * 
 * @package Core
 * @author  lhe
 */
abstract class SystemCoreAbstract
{
    /**
     * autoloading function
     * 
     * @param string $className The class that we are trying to autoloading
     * 
     * @return boolean Whether we loaded the class
     */
	public static function autoload($className)
	{
		$base = dirname(__FILE__);
		$autoloadPaths = array(
			$base . '/conf/',
			$base . '/db/',
			$base . '/entity/',
			$base . '/entity/store/',
			$base . '/entity/system/',
			$base . '/exception/',
			$base . '/service/',
			$base . '/util/',
		);
		foreach ($autoloadPaths as $path)
		{
			if (file_exists($path . $className . '.php'))
			{
				require_once $path . $className . '.php';
				return true;
			}
		}
		return false;
	}
}
spl_autoload_register(array('SystemCoreAbstract','autoload'));
// Bootstrap the Prado framework
// require_once dirname(__FILE__) . '/framework/prado.php';

?>