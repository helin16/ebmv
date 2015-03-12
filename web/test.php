<?php
require_once 'bootstrap.php';
require_once dirname(__FILE__) . '/protected/classes/Webuser/WebUserManager.php';

$userAccount = WebUserManager::login(Library::get(3), '11380047', '1234');
var_dump($userAccount);

?>