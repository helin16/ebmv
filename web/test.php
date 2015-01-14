<?php 
require_once 'bootstrap.php';


$url = 'http://localhost:8888/app/v2/getToken';
$params = array(
		'username' => 'admin',
		'password' => sha1('admin123admin')
);
$result = BmvComScriptCURL::readUrl($url, null, $params);
echo '<pre>';
$result = json_decode($result, true);
var_dump($result);
echo '</pre>';


?>