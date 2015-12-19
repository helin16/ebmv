<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
ini_set('max_execution_time', 0);
ini_set('memory_limit','1024M');
abstract class ImportOverDueIssues
{
  public static function run($libraryId) {
    if (!Core::getUser() instanceof UserAccount)
      Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
     SupplierConnectorAbstract::getInstance(Supplier::get(12), Library::get($libraryId))->setDebugMode(true)->setEchoLogging(true)->getOverDueIssues(ProductType::get(3), new UDate('2015-08-01'));
  }
}
ImportOverDueIssues::run($argv[1]);
