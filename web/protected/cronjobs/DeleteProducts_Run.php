<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';

abstract class DeleteProducts
{
  const TAB = '  ';
  public static function run($argc, $argv, $preFix = '')
  {
    //checking usage
    if ($argc < 3 || $argc > 4)
      die("Usage: DeleteProduct_Run.php siteCode(37,werew,121fd|all) supplierids(1,2,3|all) typeIds(1,2,3)[optional]\r\n");
    
    $start = self::log("== Start =======", __CLASS__ . '::' . __FUNCTION__, $preFix);
    if (!Core::getUser() instanceof UserAccount)
      Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
    self::log("Set core user: " . Core::getUser()->getPerson()->getFullName(), '', $preFix . self::TAB);
    
    self::log("== Params ===================================================", '', $preFix . self::TAB);
    $libIds = (($libIds = trim($argv[1])) === 'all' ? array() : explode(',', str_replace(' ', '', $libIds)));
    self::log("== library Ids: " . implode(", ", $libIds), '', $preFix . self::TAB);
    
    $supplierIds = (($supplierIds = trim($argv[2])) === 'all' ? array() : explode(',', str_replace(' ', '', $supplierIds)));
    self::log("== Supplier IDS: " . implode(", ", $supplierIds), '', $preFix . self::TAB);
    $typeIds = array();
    if(isset($argv[4]) && is_array($someTypeIds = explode(',', trim($argv[4]))) && count($someTypeIds) >0)
      $typeIds = $someTypeIds;
    self::log("== TYPE IDS: " . implode(', ', $typeIds), '', $preFix . self::TAB);
    self::log('');
    self::log('');
    
    //getting all the products
    $products = self::_getProducts($libIds, $supplierIds, $typeIds);
    self::log('== Start deleting ' . count($products) . ' Product(s)', '', $preFix . self::TAB);
    foreach($products as $product) {
      self::_deleteRelationship($product, $libIds, $preFix . self::TAB . self::TAB);
      self::log('');
    }
    self::log('== FINISHED deleting ' . count($products) . ' Product(s)', '', $preFix . self::TAB);
    
    self::log("== Cleanup Assets ===================================================", '', $preFix . self::TAB);
    CleanupAssets::run();
    self::log("== Done with Assets ===================================================", '', $preFix . self::TAB);
    self::log('');
    
    self::log("== FINISHED =======", __CLASS__ . '::' . __FUNCTION__, $preFix, $start);
  }
  /**
   * deleting the relationship for the products
   * 
   * @param Product $product
   * @param unknown $preFix
   */
  private static function _deleteRelationship(Product $product, array $libIds = array(), $preFix) 
  {
    self::log("DELETING PRODUCT ID = " . $product->getId(), '', $preFix);
    
    if(count($libIds) > 0) {
      self::log("DELETING ProductShelfItem with provided libIds(" . implode(', ', $libIds) . ") ... ", '', $preFix . self::TAB);
      $users = UserAccount::getAllByCriteria('libraryId in (' . implode(', ', $libIds) . ')');
      self::log("found " . count($users) . ' owner(s).', '', $preFix . self::TAB . self::TAB);
      $ownerIdsSql = '';
      if (count($users) > 0) {
        $ownerIdsSql .= ' and ownerId in ( ' . implode(', ', array_map(create_fuction('$a', 'reutrn $a->getId()'), $users)) . ')';
      }
      ProductShelfItem::deleteByCriteria('productId = ?' . $ownerIdsSql, array($product->getId()));
      self::log("DONE", '', $preFix . self::TAB . self::TAB);
  
      self::log("DELETING LibraryOwns with provided libIds(" . implode(', ', $libIds) . ") ... ", '', $preFix . self::TAB);
      LibraryOwns::deleteByCriteria('productId = ? and libraryId in (' . implode(', ', $libIds) . ')', array($product->getId()));
      self::log("DONE", '', $preFix . self::TAB . self::TAB);
    } else {
      self::log("DELETING ProductShelfItem ... ", '', $preFix . self::TAB);
      ProductShelfItem::deleteByCriteria('productId = ?', array($product->getId()));
      self::log("DONE", '', $preFix . self::TAB . self::TAB);
      
      self::log("DELETING LibraryOwns ... ", '', $preFix . self::TAB);
      LibraryOwns::deleteByCriteria('productId = ? ', array($product->getId()));
      self::log("DONE", '', $preFix . self::TAB . self::TAB);
    }
    
    $extracShelfItemcount = ProductShelfItem::countByCriteria('productId = ? and active = 1', array($product->getId()));
    $extracLibOwnsCount = LibraryOwns::countByCriteria('productId = ? and active = 1', array($product->getId()));
    if ($extracShelfItemcount > 0 || $extracLibOwnsCount > 0) {
      self::log("Break the library relationship only, as there are more libraries owns that product", '', $preFix . self::TAB);
      return;
    }
    
    self::log("DELETING ProductAttribute ... ", '', $preFix . self::TAB);
    ProductAttribute::deleteByCriteria('productId = ?', array($product->getId()));
    self::log("DONE", '', $preFix . self::TAB . self::TAB);
    
    self::log("DELETING ProductStatics ... ", '', $preFix . self::TAB);
    ProductStatics::deleteByCriteria('productId = ?', array($product->getId()));
    self::log("DONE", '', $preFix . self::TAB . self::TAB);
    
    self::log("DELETING ProductStaticsLog ... ", '', $preFix . self::TAB);
    ProductStaticsLog::deleteByCriteria('productId = ?', array($product->getId()));
    self::log("DONE", '', $preFix . self::TAB . self::TAB);
    
    self::log("DELETING Product ... ", '', $preFix . self::TAB);
    Product::deleteByCriteria('id = ?', array($product->getId()));
    self::log("DONE", '', $preFix . self::TAB . self::TAB);
  }
  /**
   * Getting all the product for deleting
   * 
   * @param array  $libIds
   * @param array  $supplierIds
   * @param array  $typeIds
   * @param string $preFix
   * 
   * @throws Exception
   * @return Ambigous <Ambigous, multitype:, multitype:BaseEntityAbstract >
   */
  private static function _getProducts($libIds = array(), $supplierIds = array(), $typeIds = array(), $preFix = self::TAB)
  {
    self::log("-- Trying to get all the products ----- ", __CLASS__ . '::' . __FUNCTION__, $preFix);
    $where = $joins = $params = array();
    if(count($libIds) > 0) {
      $wheres = array();
      foreach($libIds as $index => $libId) {
        $key = 'lib_' . $index;
        $params[$key] = $libId;
        $wheres[] = ':' . $key;
      }
      $joins['libOwns'] = 'libown.productId = pro.id and libown.active = 1 and libown.libraryId in (' . implode(', ', $wheres) . ')';
    }
    if(count($supplierIds) > 0) {
      $wheres = array();
      foreach($supplierIds as $index => $supId) {
        $key = 'sup_' . $index;
        $params[$key] = $supId;
        $wheres[] = ':' . $key;
      }
      $where[] = 'pro.supplierId in (' . implode(',', $wheres) . ')';
    }
    if(count($typeIds) > 0) {
      $wheres = array();
      foreach($typeIds as $index => $typeId) {
        $key = 'type_' . $index;
        $params[$key] = $supId;
        $wheres[] = ':' . $key;
      }
      $where[] = 'pro.productTypeId in (' . implode(',', $wheres) . ')';
    }
    if (isset($joins['libOwns'])) {
      Product::getQuery()->eagerLoad('Product.libOwns', 'inner join', 'libown', $joins['libOwns']);
      $where[] = 'pro.active = 1';
    }
    if (count($where) > 0) {
      $products = Product::getAllByCriteria(implode(' AND ', $where), $params, true);
    } else {
      throw new Exception("DANGEROURS ACTION: no condition provided for getting the products!");
    }
    self::log("-- Got (" . count($products) . ") product(s)", __CLASS__ . '::' . __FUNCTION__, $preFix);
    return $products;
  }
  /**
   * generating the log
   * 
   * @param string $msg
   * @param string $funcName
   * @param string $preFix
   * @param UDate  $start
   * @param string $postFix
   * 
   * @return UDate
   */
  private static function log($msg, $funcName = '', $preFix = '', UDate $start = null, $postFix = "\n\r")
  {
    $now = new UDate();
    $funcName = ($funcName === '' ? '': (' [' . $funcName . ']'));
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