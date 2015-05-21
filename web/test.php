<?php
require_once 'bootstrap.php';
echo '<pre>';
Core::setUser(UserAccount::get(191));
$product = Product::get(22211);
$library = Library::get(10);
$result2 = SupplierConnectorAbstract::getInstance($product->getSupplier(), $library)->getOnlineReadUrl($product, Core::getUser());
var_dump($result2);

?>