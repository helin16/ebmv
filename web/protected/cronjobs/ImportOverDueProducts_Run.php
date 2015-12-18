<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';

abstract class ImportOverDueIssues
{
  public static function run($libraryId) {
    if (!Core::getUser() instanceof UserAccount)
      Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
     SupplierConnectorAbstract::getInstance(Supplier::get(12), Library::get($libraryId))->setDebugMode(true)->setEchoLogging(true)->getOverDueIssues(ProductType::get(3), new UDate('2015-08-01'));
  }
}
ImportOverDueIssues::run($argv[2]);
