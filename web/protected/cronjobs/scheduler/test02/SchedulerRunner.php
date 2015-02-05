<?php
require_once dirname(__FILE__) . '/class/SchedulerAbstract.php';

$scheduler = SchedulerAbstract::run(true);

$processess = array();
for($i = 0; $i < 10; $i++)
{
	$processess[] = $scheduler->addTask();
}
