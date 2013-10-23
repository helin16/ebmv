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
     * The type of the value
     * 
     * @var ProductStaticsType
     */
    protected $type;
    /**
     * The value of the product
     * 
     * @var string
     */
    private $value;
    /**
     * The product this value is belonging to
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
     * @param ProductStaticsType $type The type of the product value
     * 
     * @return ProductStatics
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    /**
     * Getter for the value
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * Setter for the value
     * 
     * @param string $value The value
     * 
     * @return ProductStatics
     */
    public function setValue($value)
    {
        $this->value = $value;
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
        DaoMap::setIntType('value','int', 100);
        DaoMap::setManyToOne('type', 'ProductStaticsType');
        parent::__loadDaoMap();
        DaoMap::createIndex('value');
        DaoMap::commit();
    }
}