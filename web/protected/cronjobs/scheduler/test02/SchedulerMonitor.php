<?php
require_once dirname(__FILE__) . '/class/SchedulerAbstract.php';
const delay = 1; // sec
const terminateTime = 5;
const phpPath = 'C:\wamp\bin\php\php5.5.12\php.exe ';
const retryLimit = 2;
$alldone = false;

// add all tasks to 'task' table in db
execAndWait(phpPath . __DIR__ . '\SchedulerRunner.php');

foreach (Task::getAll() as $task)
{
	execInBackground(phpPath . $task->getPath());
}

while($alldone === false)
{
	monitor();
	if(count(Process::getAllByCriteria('active = ? AND error = ?', array(true, 0), false, 1, 1, array('id'=> 'desc'))) == 0 
		&& count(Task::getAllByCriteria('done = ?', array(true), true, 1, 1, array('id'=> 'desc'))) > 0 )
	{
		redoTasks();
	}
	sleep(delay);
	debug(str_repeat('-', 100));
	
	//if no running process and all tasks EXCEPT retry > const retryLimit done, all done, this monitor terminate
	if(count(Process::getAllByCriteria('active = ? AND error = ?', array(true, 0), false, 1, 1, array('id'=> 'desc'))) == 0
			&& count(Task::getAllByCriteria('done = ? AND retry < ?', array(false, retryLimit))) == 0)
	{
		$alldone = true;
		debug('** Notice: All task within retry limit in done. The Scheduler Monitor QUIT now.');
	}
}
/**
 * Redo all forced quited tasks
 */
function redoTasks()
{
	if(count($tasks = Task::getAllByCriteria('done = ? AND retry < ?', array(false, retryLimit))) == 0)
		return;
	debug('** Notice: Redo unfinished Tasks ....');
	foreach ($tasks as $task)
	{
		$task->retry();
		execInBackground(phpPath . $task->getPath());
	}
}
function monitor()
{
	try
	{
		$now = getNow();
		debug('-----ACTIVE-----');
		foreach (Process::getAllByCriteria('active = ? AND error = ?', array(true, 0), false, 1, 99, array('id'=> 'desc')) as $process)
		{
			$diff = $now->getUnixTimeStamp() - $process->getStart()->getUnixTimeStamp();
			if($diff > terminateTime) // check if timeout
				killProcess($process->getProcessId(), $process);
			debug(getMonitorMsg($process, $diff));
		}
		debug('-----TERMINATED-----');
		foreach (Process::getAllByCriteria('active = ? AND error = ?', array(false, 0), false, 1, 3, array('id'=> 'desc')) as $process)
		{
			debug(getMonitorMsg($process));
		}
		debug('-----FORED QUIT-----');
		foreach (Process::getAllByCriteria('error != ?', array(0), false, 1, 3, array('id'=> 'desc')) as $process)
		{
			debug(getMonitorMsg($process) . ', ERROR: ' . getReason($process->getError()) . '(' . $process->getError() . ')');
		}
	} catch(Exception $ex)
	{
		debug(__FUNCTION__, '** Error: ' . $ex->getMessage());
	}
}
function getMonitorMsg(Process $process, $diff = '')
{
	$msg = 'PID: ' . $process->getProcessId() . ', START: ' . $process->getStart() . ', LIFETIME: ';
	if($process->getActive() == true && $process->getError() == 0)
		$msg .=  $diff . '/';
	$msg .= $process->getLifespan() . ', END: ' . $process->getEnd() . ', TASK: ' . basename($process->getTask()->getPath());
	if(($retry = intval($process->getTask()->getRetry())) != 0)
		$msg .= ', RETRY: ' . $retry;
	return $msg;
}
/**
 * kill process
 * 
 * @param int $pid
 * @param Process $process
 * @param string $errorCode
 * @throws Exception
 * @return Process
 */
function killProcess($pid, Process $process, $errorCode = '1') // 1: timeout
{
	win_kill($pid);
	$process->setError($errorCode)->setEnd(getNow())->setActive(false)->save();
	switch (intval($errorCode))
	{
		case 1:
			debug('** Warning: Process(pid=' . $pid . ') terminated, Task ' . basename($process->getTask()->getPath()) . '(id=' . $process->getTask()->getId() . ') ' . ((intval($process->getTask()->getRetry()) >= retryLimit) ? 'TERMINATED' : 'RETRY') . ' due to ' . strtoupper(getReason($errorCode)) . ', retry limit(' . $process->getTask()->getRetry() . '/' . retryLimit . ')');
			break;
		default:
			debug('** Warning: Task ' . basename($process->getTask()->getPath()) . '(id=' . $process->getTask()->getId() . ' terminated due to ' . strtoupper(getReason($errorCode)));
	}
	return $process;
}
/**
 * get current time
 * 
 * @return UDate
 */
function getNow()
{
	$now = new UDate();
	return $now;
}
function getReason($errorCode)
{
	switch (intval($errorCode))
	{
		case 1:
			return 'TIMEOUT';
			break;
		default:
			throw new Exception(__FUNCTION__, '** Error: ' . 'Invalid Error code passed in');
	}
}
function win_kill($pid){
	$wmi=new COM("winmgmts:{impersonationLevel=impersonate}!\\\\.\\root\\cimv2");
	$procs=$wmi->ExecQuery("SELECT * FROM Win32_Process WHERE ProcessId='".$pid."'");
	foreach($procs as $proc)
		$proc->Terminate();
}
function execAndWait($cmd) {
	if (substr(php_uname(), 0, 7) == "Windows"){
		$WshShell = new COM("WScript.Shell");
		$oExec = $WshShell->Run($cmd, 3, true);
	}
	else {
		exec($cmd);
	}
}
function execInBackground($cmd) {
if (substr(php_uname(), 0, 7) == "Windows"){
		$WshShell = new COM("WScript.Shell");
		$oExec = $WshShell->Run($cmd, 3, false);
	}
	else {
		exec($cmd);
	}
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