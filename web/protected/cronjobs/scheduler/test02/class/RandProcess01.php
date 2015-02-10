// <?php
require_once dirname(__FILE__) . '/../../../../../bootstrap.php';

try {
	$process = null;
	Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
	$task = Task::getAllByCriteria('path = ?', array(__FILE__), true, 1, 1);
	if(count($task) < 1 || !$task[0] instanceof Task)
		throw new Exception('Must create Task before Process');
	else $task = $task[0];
	$lifespan = rand(2,3); // in seconds
	
	echo "i am 01" . "\n";
	$now = new UDate();
	$pid = getmypid();
	
	$process = Process::create($pid, $now, $task, $lifespan);
	
	//test
	sleep(1);
	throw new Exception('Test Exception!');
	
	sleep($lifespan-1);
	
	$now = new UDate();
	$process->setEnd($now)->setActive(false)->save();
	$task->setDone(true)->save();
	
	return;
} catch(Exception $e) {
	if(!$process instanceof Process)
		Process::create($pid, new UDate(), $task, $lifespan);
	$process->setError(2)->setComments($e->getMessage() . "\n" . $e->getTraceAsString())->save();
}
