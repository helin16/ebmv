<?php
/**
 * Product Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Product extends BaseEntityAbstract
{
	/**
	 * attributes - cached
	 * 
	 * @var array
	 */
	private $_attris;
    /**
     * The title of the book
     * 
     * @var string
     */
    private $title;
    /**
     * Supplier Unique Key string
     * 
     * @var string
     */
    private $suk = '';
	/**
	 * The categories that the products are belongin to 
	 * 
	 * @var multiple:Category
	 */
	protected $categorys;
	/**
	 * The attributes of the products
	 * 
	 * @var multiple:ProductAttribute
	 */
	protected $attributes;
	/**
	 * The languages of the book
	 * 
	 * @var multiple:Language
	 */
	protected $languages;
	/**
	 * The ProductType of the book
	 * 
	 * @var ProductType
	 */
	protected $productType;
	/**
	 * The ProductStatics of the book
	 * 
	 * @var ProductStatics
	 */
	protected $productStatics;
	/**
	 * The shelf items
	 * 
	 * @var multiple:ProductShelfItem
	 */
	protected $shelfItems;
	/**
	 * The supplier of this product
	 * 
	 * @var Supplier
	 */
	protected $supplier;
	/**
	 * The library of the products
	 * 
	 * @var LibraryOwns
	 */
	protected $libOwns;
	/**
	 * Getter for the title
	 * 
	 * @return string
	 */
	public function getTitle()
	{
	    return $this->title;
	}
	/**
	 * Setter for 
	 * 
	 * @param string $title The title of product
	 * 
	 * @return Product
	 */
	public function setTitle($title)
	{
	    $this->title = $title;
	    return $this;
	}
	/**
	 * Getter for the suk
	 * 
	 * @return string
	 */
	public function getSuk()
	{
	    return $this->suk;
	}
	/**
	 * Setter for suk
	 * 
	 * @param string $suk The suk of product
	 * 
	 * @return Product
	 */
	public function setSuk($suk)
	{
	    $this->suk = $suk;
	    return $this;
	}
	/**
	 * getter Categorys
	 *
	 * @return multiple:Category
	 */
	public function getCategorys()
	{
	    $this->loadManyToMany("categorys");
	    return $this->categorys;
	}
	/**
	 * Setter Categorys
	 *
	 * @param array $categorys The categories that the products are belongin to 
	 *
	 * @return Product
	 */
	public function setCategorys($categorys)
	{
	    $this->categorys = $categorys;
	    return $this;
	}
	/**
	 * Adding a product to a category
	 *
	 * @param Category $category The category
	 *
	 * @return ProductService
	 */
	public function addCategory(Category $category)
	{
		EntityDao::getInstance('Product')->saveManyToManyJoin($category, $this);
		return $this;
	}
	/**
	 * Removing a product from a category
	 *
	 * @param Category $category The category
	 *
	 * @return ProductService
	 */
	public function removeCategory(Category $category)
	{
		EntityDao::getInstance('Product')->deleteManyToManyJoin($category, $this);
		return $this;
	}
	/**
	 * Removing all the categories
	 */
	public function removeAllCategories()
	{
		foreach($this->getCategorys() as $category)
			$this->removeCategory($category);
		return $this;
	}
	/**
	 * getter attributes
	 *
	 * @return multiple:ProductAttribute
	 */
	public function getAttributes()
	{
	    $this->loadOneToMany('attributes');
	    return $this->attributes;
	}
	/**
	 * Setter attributes
	 *
	 * @param array $attributes The attributes that this product has
	 *
	 * @return Product
	 */
	public function setAttributes($attributes)
	{
	    $this->attributes = $attributes;
	    return $this;
	}
	/**
	 * Getting the attribute
	 * 
	 * @param string $typeCode  The code of the ProductAttributeType
	 * @param string $separator The separator of the returned attributes, in case there are multiple
	 * @param bool   $reset     Forcing to get the information from DB
	 * 
	 * @return Ambigous <>
	 */
	public function getAttribute($typeCode, $separator = ',', $reset = false)
	{
		if(!isset($this->_attris[$typeCode]) || $reset === true)
		{
			$sql = 'select group_concat(pa.attribute separator ?) `attr` from productattribute pa inner join productattributetype pat on (pat.id = pa.typeId and pat.active = 1 and pat.code = ?) where pa.active = 1 and pa.productId = ?';
		    $result = Dao::getSingleResultNative($sql, array($separator, $typeCode, $this->getId()), PDO::FETCH_ASSOC);
		    $this->_attris[$typeCode] = $result['attr'];
		}
	    return $this->_attris[$typeCode];
	}
	/**
	 * Getter for the language
	 * 
	 * @return Multiple:Language
	 */
	public function getLanguages()
	{
	    $this->loadManyToMany('languages');
	    return $this->languages;
	}
	/**
	 * Setter for the language
	 * 
	 * @param Language $language The language of the product
	 * 
	 * @return Product
	 */
	public function setLanguages(array $languages)
	{
	    $this->languages = $languages;
	    return $this;
	}
	/**
	 * updating the languages
	 * 
	 * @param array $languages The wanted languages
	 * 
	 * @return Product
	 */
	public function updateLanguages(array $languages)
	{
		if(count($languages) === 0)
			return;
		
		foreach($languages as $lang)
		{
			EntityDao::getInstance('Product')->replaceInto('language_product', array('languageId', 'productId', 'createdById'), array('?', $this->getId(), Core::getUser()->getId()), array($lang->getId()));
		}
		return $this;
	}
	/**
	 * Adding a library for owning this product
	 * 
	 * @param Library         $lib         The owner
	 * @param LibraryOwnsType $type        Which type does the library owns this product
	 * @param number          $availCopies How many copies
	 * @param number          $totalCopies How many copies in total
	 * 
	 * @return Product
	 */
	public function updateLibrary(Library $lib, LibraryOwnsType $type, $avail = 0, $total = 0)
	{
		$owns = $this->getLibraryOwn($lib, $type);
		if(count($owns) === 0)
			$owns = new LibraryOwns();
		else
		{
			$owns = $owns[0];
			$this->removeLibrary($lib, $type);
		}
		$owns->setLibrary($lib)
			->setProduct($this)
			->setType($type)
			->setAvail($avail)
			->setTotal($total)
			->setActive(true)
			->save();
		return $this;
	}
	/**
	 * Removing a product form a library
	 * 
	 * @param Library         $lib  The library
	 * @param LibraryOwnsType $type The ownership type
	 * 
	 * @return Product
	 */
	public function removeLibrary(Library $lib, LibraryOwnsType $type)
	{
		LibraryOwns::updateByCriteria('active = 0' , 'libraryId = ? and typeId = ? and productId = ?', array($lib->getId(), $type->getId(), $this->getId()));
		return $this;
	}
	/**
	 * Getting the library own for this product
	 * 
	 * @param Library         $lib  The owner
	 * @param LibraryOwnsType $type The ownership type
	 * 
	 * @return NULL|LibraryOwns
	 */
	public function getLibraryOwn(Library $lib, LibraryOwnsType $type = null, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array(), $activeOnly = true, &$stats = array())
	{
		$where = 'libraryId = ? and productId = ?';
		$params =  array($lib->getId(), $this->getId());
		if($type instanceof LibraryOwnsType)
		{
			$where .= ' and typeId = ?';
			$params[] = $type->getId();
		}
		$owns = LibraryOwns::getAllByCriteria($where, $params, $activeOnly, $pageNo, $pageSize, $orderBy, $stats);
		return $owns;
	}
	/**
	 * Getter for the ProductType
	 * 
	 * @return ProductType
	 */
	public function getProductType()
	{
	    $this->loadManyToOne('productType');
	    return $this->productType;
	}
	/**
	 * Setter for the productType
	 * 
	 * @param ProductType $productType The productType
	 * 
	 * @return Product
	 */
	public function setProductType(ProductType $productType)
	{
	    $this->productType = $productType;
	    return $this;
	}
	/**
	 * Getter for the ProductStatics
	 * 
	 * @return ProductStatics
	 */
	public function getProductStatics()
	{
	    $this->loadOneToMany('productStatics');
	    return $this->productStatics;
	}
	/**
	 * Setter for the ProductStatics
	 * 
	 * @param array $productStatics The array of ProductStatics
	 * 
	 * @return Product
	 */
	public function setProductStatics($productStatics)
	{
	    $this->productStatics = $productStatics;
	    return $this;
	}
	/**
	 * add statics to a product
	 * 
	 * @param Library            $lib
	 * @param ProductStaticsType $type
	 * @param number             $increaseBy
	 * @return Product
	 */
	public function addStatic(Library $lib, ProductStaticsType $type, $increaseBy = 1)
	{
		if(trim($this->getId()) === '')
			$this->save();
		ProductStatics::create($this, $type, $lib)->add($increaseBy);
		return $this;
	}
	/**
	 * Getting the suppliers for this product
	 * 
	 * @return multitype:|Ambigous <multitype:, multitype:BaseEntityAbstract >
	 */
	public function getSuppliers()
	{
		return array($this->getSupplier());
	}
	/**
	 * Getting the supplier
	 * 
	 * @return Supplier
	 */
	public function getSupplier() 
	{
		$this->loadManyToOne('supplier');
	    return $this->supplier;
	}
	/**
	 * Setter for the supplier
	 * 
	 * @param Supplier $value The Supplier
	 * 
	 * @return Product
	 */
	public function setSupplier($value) 
	{
	    $this->supplier = $value;
	    return $this;
	}
	/**
	 * Getter for the shelfItems
	 * 
	 * @return multiple:ProductShelfItem
	 */
	public function getShelfItems() 
	{
		$this->loadOneToMany('shelfItems');
	    return $this->shelfItems;
	}
	/**
	 * Getters for the libOwns
	 * 
	 * @return Multiple:LibraryOwns
	 */
	public function getLibOwns() 
	{
		$this->loadOneToMany('libOwns');
	    return $this->libOwns;
	}
	/**
	 * Setter for the libOwns
	 * 
	 * @param multiple:libOwns $value The libOwns
	 * 
	 * @return Product
	 */
	public function setLibOwns($value) 
	{
	    $this->libOwns = $value;
	    return $this;
	}
	/**
	 * Setter for the shelfItems
	 * 
	 * @param array $value The shelf items
	 * 
	 * @return Product
	 */
	public function setShelfItems($value) 
	{
	    $this->shelfItems = $value;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJson()
	 */
	public function getJson($extra = array(), $reset = false)
	{
	    $array = array();
	    if(!$this->isJsonLoaded($reset))
	    {
	    	$array['attributes'] = array();
		    foreach($this->getAttributes() as $attr)
		    {
		        $typeId = $attr->getType()->getCode();
		        if(!isset($array['attributes'][$typeId]))
		            $array['attributes'][$typeId] = array();
	            $array['attributes'][$typeId][] = $attr->getJson();
		    }
		    $array['languages'] = array();
		    foreach($this->getLanguages() as $lang)
		    	$array['languages'][] = $lang->getJson();
	    }
	    return parent::getJson($array, $reset);
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::preSave()
	 */
	public function preSave()
	{
	    if(trim($this->getSuk()) === '')
	        $this->setSuk(md5($this->getTitle()));
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'pro');
		DaoMap::setStringType('title','varchar', 200);
		DaoMap::setStringType('suk','varchar', 50);
		DaoMap::setManyToMany("categorys", "Category", DaoMap::LEFT_SIDE, "pcat");
		DaoMap::setOneToMany("attributes", "ProductAttribute");
		DaoMap::setManyToMany("languages", "Language", DaoMap::LEFT_SIDE, 'lang');
		DaoMap::setManyToOne("productType", "ProductType");
		DaoMap::setOneToMany("productStatics", "ProductStatics");
		DaoMap::setOneToMany("shelfItems", "ProductShelfItem");
		DaoMap::setManyToOne('supplier', 'Supplier');
		DaoMap::setOneToMany("libOwns", "LibraryOwns");
		parent::__loadDaoMap();
		
		DaoMap::createIndex('title');
		DaoMap::createIndex('suk');
		DaoMap::commit();
	}
}

?>