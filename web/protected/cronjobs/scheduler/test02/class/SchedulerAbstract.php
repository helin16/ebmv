<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
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
	 * @return Process
	 */
	public static function addTask()
	{
		require_once('RandProcess.php');
		$class = new RandProcess();
		$process = $class->getRandProcess();
		self::_debug('Process ID: ' . $process->getProcessId());
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