<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';

abstract class ImportOverDueIssues
{
  public static function run() {
    if (!Core::getUser() instanceof UserAccount)
      Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
    foreach (Library::getAll() as $library) {
      SupplierConnectorAbstract::getInstance(Supplier::get(12), $library)->setEchoLogging(true)->getOverDueIssues(ProductType::get(3));
    }
  }
}
ImportOverDueIssues::run();
