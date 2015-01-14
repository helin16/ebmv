<?php

$incpaths = array(
	dirname(__FILE__)
);

set_include_path(implode(PATH_SEPARATOR, $incpaths));
class App
{
	public static function autoload($className)
	{
		$autoloadPaths = array(
			dirname(__FILE__) . '/'
		);
		$found = false;
		foreach ($autoloadPaths as $path)
		{
			if (file_exists($path . $className . '.php'))
			{
				require_once $path . $className . '.php';
				$found = true;
				break;
			}
		}
		return $found;
	}
}

spl_autoload_register(array('App','autoload'));

// Bootstrap the core for its autoloader settings
require_once (dirname(__FILE__) . '/../../../../../bootstrap.php');

?>