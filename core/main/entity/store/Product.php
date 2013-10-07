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
    private $suk;
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
	 * 
	 * @return Ambigous <>
	 */
	public function getAttribute($typeCode, $separator = ',')
	{
	    $sql = 'select group_concat(pa.attribute separator ?) `attr` from productattribute pa inner join productattributetype pat on (pat.id = pa.typeId and pat.active = 1 and pat.code = ?) where pa.active = 1 and pa.productId = ?';
	    $result = Dao::getSingleResultNative($sql, array($separator, $typeCode, $this->getId()), PDO::FETCH_ASSOC);
	    return $result['attr'];
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJson()
	 */
	public function getJson()
	{
	    $array = parent::getJson();
	    $array['attributes'] = array();
	    foreach($this->getAttributes() as $attr)
	    {
	        $typeId = $attr->getType()->getCode();
	        if(!isset($array['attributes'][$typeId]))
	            $array['attributes'][$typeId] = array();
            $array['attributes'][$typeId][] = $attr->getJson();
	    }
	    return $array;
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
		parent::__loadDaoMap();
		
		DaoMap::createIndex('title');
		DaoMap::createIndex('suk');
		DaoMap::commit();
	}
}

?>