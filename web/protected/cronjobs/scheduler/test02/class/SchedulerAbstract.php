<?php
require_once dirname(__FILE__) . '/../../../../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

class SchedulerAbstract
{
	protected static $_debug = false;
	/**
	 * runner
	 * 
	 * @param string $debug
	 * @return SchedulerAbstract
	 */
	public static function run($debug = false)
	{
		self::$_debug = $debug;
		return new self;
	}
	/**
	 * add new task
	 * 
	 * @param number the index of randprocess
	 * 
	 * @return Process
	 */
	public static function addTask($number)
	{
		$className = 'RandProcess' . $number;
		$classPhpName = 'RandProcess' . $number . '.php';
		require_once($classPhpName);
		$class = new $className();
		$process = $class->getRandProcess();
		return $process;
	}
	/**
	 * Debug output function
	 *
	 * @param string $message
	 * @param string $newLine
	 *
	 */
	protected static function _debug($message, $newLine = "\n")
	{
		if(self::$_debug === true)
			echo $message . $newLine;
	}
}