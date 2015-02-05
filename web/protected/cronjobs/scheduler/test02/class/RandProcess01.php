<?php
class RandProcess01 extends SchedulerAbstract
{
	public function getRandProcess()
	{
		$now = new UDate();
		$pid = getmypid();
		$lifespan = rand(3,10); // in seconds
		$this->_debug('pid: ' . $pid . ', lifespan:' . $lifespan);
		$process = Process::create($pid, $now->setTimeZone('Australia/Melbourne'), $lifespan);
		sleep($lifespan);
		$now = new UDate();
		$process->setEnd($now->setTimeZone('Australia/Melbourne'))->setActive(false)->save();
		return $process;
	}
}
