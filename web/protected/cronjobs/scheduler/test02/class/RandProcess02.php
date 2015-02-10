// <?php
require_once dirname(__FILE__) . '/../../../../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

echo "i am 02" . "\n";

$now = new UDate();
$pid = getmypid();
$lifespan = rand(7,10); // in seconds

$task = Task::getAllByCriteria('path = ?', array(__FILE__), true, 1, 1);
if(count($task) < 1 || !$task[0] instanceof Task)
	throw new Exception('Must create Task before Process');
else $task = $task[0];

$process = Process::create($pid, $now, $task, $lifespan);

sleep($lifespan);

$now = new UDate();
$process->setEnd($now)->setActive(false)->save();
$task->setDone(true)->save();

return;
