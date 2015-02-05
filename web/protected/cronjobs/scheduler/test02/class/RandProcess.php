<?php
class RandProcess extends SchedulerAbstract
{
	public function getRandProcess()
	{
		$now = new UDate();
		$pid = getmypid();
		$lifespan = rand(10,60); // in seconds
		$process = Process::create($pid, $now->setTimeZone('Australia/Melbourne'), $lifespan);
		
		return $process;
	}
}