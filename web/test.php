<?php
require_once 'bootstrap.php';

$userAccount = WebUserManager::login(Library::get(3), '11380047', '1234');
var_dump($userAccount);

?>