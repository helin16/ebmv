<?php
require_once dirname(__FILE__) . '/class/SchedulerAbstract.php';

$scheduler = SchedulerAbstract::run(true);

$scheduler->addTask('01');