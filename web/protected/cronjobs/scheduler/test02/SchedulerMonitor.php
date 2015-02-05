<?php
require_once dirname(__FILE__) . '/class/SchedulerAbstract.php';
$_delay = 1; // sec
while(1)
{
	try
	{
		passthru('clear');
		$now = new UDate();
		debug('-----ACTIVE-----');
		foreach (Process::getAllByCriteria('active = ?', array(true), false, 1, 99, array('id'=> 'desc')) as $task)
		{
			var_dump($task->getLifespan()->diff($now));
			debug('PID: ' . $task->getProcessId() . ', START: ' 
					. $task->getStart() . ', LIFETIME: ' 
					. $task->getLifespan() . '/' . $task->getLifespan() . ', END: ' . $task->getEnd());
		}
		debug('-----TERMINATED-----');
		foreach (Process::getAllByCriteria('active = ?', array(false), false, 1, 99, array('id'=> 'desc')) as $task)
		{
			debug('PID: ' . $task->getProcessId() . ', START: ' 
					. $task->getStart() . ', LIFETIME: ' 
					. $task->getLifespan(). '/' . $task->getLifespan() . ', END: ' . $task->getEnd());
		}
	} catch(Exception $ex)
	{
		debug(__FUNCTION__, '** Error: ' . $ex->getMessage());
	}
	sleep($_delay);
}
/**
 * Debug output function
 *
 * @param string $message
 * @param string $newLine
 *
 */
function debug($message, $newLine = "\n")
{
	echo $message . $newLine;
}