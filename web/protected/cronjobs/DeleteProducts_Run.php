<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';

abstract class DeleteProducts
{
  const TAB = '  ';
  public static function run($argc, $argv, $preFix = '') {
    //checking usage
    if ($argc < 3 || $argc > 4)
      die("Usage: DeleteProduct_Run.php siteCode(37,werew,121fd|all) supplierids(1,2,3|all) typeIds(1,2,3)[optional]\r\n");
    
    $start = self::log("== Start =======", __CLASS__ . '::' . __FUNCTION__, $preFix);
    if (!Core::getUser() instanceof UserAccount)
      Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
    self::log("Set core user: " . Core::getUser()->getPerson()->getFullName(), '', $preFix . self::TAB);
    
    self::log("== Params ===================================================", '', $preFix . self::TAB);
    $libIds = (($libIds = trim($argv[1])) === 'all' ? array() : explode(',', str_replace(' ', '', $libIds)));
    self::log("== library Ids: '" . implode("', '", $libIds), '', $preFix . self::TAB);
    
    $supplierIds = (($supplierIds = trim($argv[2])) === 'all' ? array() : explode(',', str_replace(' ', '', $supplierIds)));
    self::log("== Supplier IDS: '" . implode("', '", $supplierIds), '', $preFix . self::TAB);
    $typeIds = array();
    if(isset($argv[4]) && is_array($someTypeIds = explode(',', trim($argv[4]))) && count($someTypeIds) >0)
      $typeIds = $someTypeIds;
    self::log("== TYPE IDS: " . implode(', ', $typeIds), '', $preFix . self::TAB);
    self::log('');
    self::log('');
    
    
    self::log("== Cleanup Assets ===================================================", '', $preFix . self::TAB);
    CleanupAssets::run();
    self::log("== Done with Assets ===================================================", '', $preFix . self::TAB);
    self::log('');
    
    self::log("== FINISHED =======", __CLASS__ . '::' . __FUNCTION__, $preFix, $start);
  }
  
  private static function getProducts($libIds = array(), $supplierIds = array(), $typeIds = array(), $preFix = self::TAB) {
    $where = $params = array();
    if(count($libIds) > 0) {
      foreach($libIds as $index => $libId)
      $where[] = 'supplierId in (' . implode(',', array_fill(0, count($libIds), '?')) . ')';
      $params = array_merge($params, $libIds);
    }
    if(count($supplierIds) > 0) {
      $where[] = 'supplierId in (' . implode(',', array_fill(0, count($supplierIds), '?')) . ')';
      $params = array_merge($params, $supplierIds);
    }
    
    Product::getAllByCriteria('supplierId in (')
  }
  
  private static function log($msg, $funcName = '', $preFix = '', UDate $start = null, $postFix = "\n\r") {
    $now = new UDate();
    $funcName = ($funcName === '' ? (' [' . $funcName . ']') : '');
    $timeDiff = '';
    if($start instanceof UDate) {
      $timeDiff = ' TOOK '  . ($now->UnixTimestamp() - $start->UnixTimestamp()) . '(s)';
    }
    $newMsage = trim($now) . $preFix . $msg .  $funcName . $timeDiff . $postFix;
    echo $newMsage;
    return $now;
  }
}

DeleteProducts::run($argc, $argv);