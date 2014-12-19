<?php
/**
 * Category Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Category extends TreeEntityAbstract
{
    /**
     * The name of the category
     * 
     * @var string
     */
    private $name;
    /**
     * The products that the products are belongin to
     *
     * @var multiple:Product
     */
    protected $products;
	/**
     * getter Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * setter Name
     *
     * @param string $Name The name of the role
     *
     * @return Category
     */
    public function setName($Name)
    {
        $this->name = $Name;
        return $this;
    }
    /**
     * Getting the products
     * 
     * @return multiple:Product
     */
    public function getProducts()
    {
        $this->loadManyToMany('products');
        return $this->products;
    }
    /**
     * setter for products
     *
     * @param string $Name The name of the role
     *
     * @return Category
     */
    public function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }
    /**
     * Getting the no of product that belongs to this category
     * 
     * @return number
     */
    public function getNoOfProducts(ProductType $type = null)
    {
    	$result = Dao::getSingleResultNative('select count(distinct x.productid) `count` from category_product x inner join product pro on (pro.id = x.productId and pro.active = 1 ' . ($type instanceof ProductType ? 'and pro.productTypeId = ' . $type->getId() : '') . ') where x.categoryId = ? ', array($this->getId()));
    	return intval($result['count']);
    }
    /**
     * Getting the language ids for a category
     * 
     * @return array
     */
    public function getLangIds(ProductType $type = null)
    {
    	$result = Dao::getResultsNative('select x.languageId from language_product x inner join product pro on (pro.active = 1 and pro.id = x.productId ' . ($type instanceof ProductType ? ' AND pro.productTypeId = ' . $type->getId() : '') . ') inner join category_product cp on (cp.productId = pro.id and cp.categoryId = ?) ', array($this->getId()));
    	return array_map(create_function('$a', 'return $a["languageId"];'), $result);
    }
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'pcat');
		DaoMap::setStringType('name', 'varchar', 255);
		DaoMap::setManyToMany("products", "Product", DaoMap::RIGHT_SIDE, "pro", false);
		parent::__loadDaoMap();
		
		DaoMap::createIndex('name');
		DaoMap::commit();
	}
	/**
	 * Getting the name for
	 * 
	 * @param string $name
	 * 
	 * @return Ambigous <NULL, BaseEntityAbstract>
	 */
	public static function getByName($name)
	{
		return (count($cates = self::getAllByCriteria('name = ?', array(trim($name)), true, 1, 1)) === 0 ) ? null : $cates[0];
	}
	/**
	 * Getting the categories for the language and type
	 *
	 * @param Language    $lang
	 * @param ProductType $type
	 * @param string      $searchActiveOnly
	 * @param int         $pageNo
	 * @param int         $pageSize
	 * @param array       $orderBy
	 *
	 * @return Ambigous <Ambigous, multitype:, multitype:BaseEntityAbstract >
	 */
	public static function getCategories(ProductType $type, Library $lib = null, Language $lang = null, $searchActiveOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array(), &$stats = array())
	{
		$query = self::getQuery();
		$query->eagerLoad('Category.products')->eagerLoad('Product.languages');
		$params = array();
		if($lib instanceof Library)
		{
			$query->eagerLoad('Product.libOwns', 'inner join', 'x_libowns', '`x_libowns`.`productId` = `pro`.id and x_libowns.active = 1 and x_libowns.libraryId = :libId');
			$params['libId'] =  $lib->getId();
		}
		$where = '`pro`.productTypeId = :productTypeId';
		$params['productTypeId'] =  $type->getId();
		if($lang instanceof Language)
		{
			$where .= ' AND lang.id = :languageId';
			$params['languageId'] =  $lang->getId();
		}
		return self::getAllByCriteria($where, $params, $searchActiveOnly, $pageNo, $pageSize, $orderBy, $stats);
	}
	/**
	 * Find or create a category with the same name
	 *
	 * @param string   $categoryName The name of the category
	 * @param Category $parent       The parent category
	 * @param bool     $isNew        Whether we create a new category for this
	 *
	 * @return Category
	 */
	public static function updateCategory($categoryName, Category $parent = null, &$isNew = false)
	{
		if(($category = self::getByName($categoryName)) instanceof Category)
		{
			$isNew = false;
			return $category;
		}
	
		$isNew = true;
		$category = new Category();
		$category->setName($categoryName)
			->save();
		return self::moveCategory($category, $parent);
	}
	/**
	 * move the category to another
	 *
	 * @param Category $category The moving category
	 * @param Caregory $parent   The target category
	 *
	 * @return Category
	 */
	public static function moveCategory(Category &$category, Category $parent = null)
	{
		if(($pos = trim($category->getPosition())) === '' || $pos === '1')
			$category->save();

		if($parent instanceof Category)
		{
			$newPos = $parent->getNextPosition();
			self::updateByCriteria('position = CONCAT(:newPos, substring(position, :posLen)), rootId = :newRootId', 'rootId = :rootId and position like :oldPos',
					array(
							'newPos' => $newPos,
							'oldPos' => $pos,
							'posLen' => strlen($pos) + 1,
							'newRootId' => $parent->getRoot()->getId(),
							'rootId' => $category->getRoot()->getId()
					)
			);
		}
		else
		{
			$newPos = '1';
			self::updateByCriteria('position = CONCAT(:newPos, substring(position, :posLen)), rootId = :newRootId', 'rootId = :rootId and position like :oldPos',
					array(
							'newPos' => $newPos,
							'oldPos' => $pos,
							'posLen' => strlen($pos) + 1,
							'newRootId' => $category->getId(),
							'rootId' => $category->getRoot()->getId()
					)
			);
		}

		$category = self::get($category->getId());
		$category->setPosition($newPos)
			->setParent($parent)
			->setRoot($parent instanceof Category ? $parent->getRoot() : $category)
			->save();
		return $category;
	
	}
	/**
	 * Searching the categories based on the product searching
	 * 
	 * @param unknown $productSearchString
	 * @param Library $lib
	 * @param string $searchActiveOnly
	 * @param string $pageNo
	 * @param unknown $pageSize
	 * @param unknown $orderBy
	 * 
	 * @return Ambigous <Ambigous, multitype:, multitype:BaseEntityAbstract >
	 */
	public static function searchCategoryByProduct($productSearchString, Library $lib = null, $searchActiveOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array(), &$stats = array())
	{
		$query = self::getQuery();
		$query->eagerLoad('Category.products');
		$params = array();
		if($lib instanceof Library)
		{
			$query->eagerLoad('Product.libOwns', 'inner join', 'x_libowns', '`x_libowns`.`productId` = `pro`.id and x_libowns.active = 1 and x_libowns.libraryId = :libId');
			$params['libId'] =  $lib->getId();
		}
		$where = 'pro.title like :searchTxt';
		$params['searchTxt'] = '%' . $productSearchString . '%';
		return self::getAllByCriteria($where, $params, $searchActiveOnly, $pageNo, $pageSize, $orderBy, $stats);
	}
}

?>