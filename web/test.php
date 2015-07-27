<?php
require_once 'bootstrap.php';
echo '<pre>';
$script = new SC_DLTX(Supplier::get(Supplier::ID_XIN_DONG_FANG), Library::get(1));
$script->getProductListInfo();
var_dump(SC_DLTX::$cache);

?>