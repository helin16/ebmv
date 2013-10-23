<?php
/**
 * ProductStatics Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class ProductStatics extends BaseEntityAbstract
{
    /**
     * The type of the attribute
     * 
     * @var ProductStaticsType
     */
    protected $type;
    /**
     * The attribute of the product
     * 
     * @var string
     */
    private $attribute;
    /**
     * The product this attribute is belonging to
     * 
     * @var Product
     */
    protected $product;
    /**
     * Getter for the ProductStatics type
     * 
     * @return ProductStaticsType
     */
    public function getType()
    {
        $this->loadManyToOne('type');
        return $this->type;
    }
    /**
     * Setter for the ProductStatics type
     * 
     * @param ProductStaticsType $type The type of the product attribute
     * 
     * @return ProductStatics
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    /**
     * Getter for the attribute
     * 
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }
    /**
     * Setter for the attribute
     * 
     * @param string $attribute The attribute
     * 
     * @return ProductStatics
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }
    /**
     * Getter for the product
     * 
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }
    /**
     * Setter for the product
     * 
     * @param Product $product The product
     * 
     * @return ProductStatics
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::getJson()
     */
    public function getJson()
    {
        $array = parent::getJson();
        $array['type'] = $this->getType()->getJson();
        return $array;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'pstats');
        DaoMap::setManyToOne('product', 'Product');
        DaoMap::setStringType('attribute','varchar', 500);
        DaoMap::setManyToOne('type', 'ProductStaticsType');
        parent::__loadDaoMap();
        DaoMap::createIndex('attribute');
        DaoMap::commit();
    }
}