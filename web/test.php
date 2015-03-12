<?php
require_once 'bootstrap.php';
$result1 = BmvComSIP2::getSIP('206.187.32.35', '8164')->getPatronInfo('11380047', '1234');
var_dump($result1);

var_dump('==============================');

$result2 = BmvComSIP2::getSIP('206.187.32.35', '8164')->getPatronInfo('11380047', 'abc');
var_dump($result2);

?>