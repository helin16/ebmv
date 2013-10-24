<?php
/**
 * ProductAttribute Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class ProductAttribute extends BaseEntityAbstract
{
    /**
     * The type of the attribute
     * 
     * @var ProductAttributeType
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
     * Getter for the productattribute type
     * 
     * @return ProductAttributeType
     */
    public function getType()
    {
        $this->loadManyToOne('type');
        return $this->type;
    }
    /**
     * Setter for the productattribute type
     * 
     * @param ProductAttributeType $type The type of the product attribute
     * 
     * @return ProductAttribute
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
     * @return ProductAttribute
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
        $this->loadManyToOne('product');
        return $this->product;
    }
    /**
     * Setter for the product
     * 
     * @param Product $product The product
     * 
     * @return ProductAttribute
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
        DaoMap::begin($this, 'pa');
        DaoMap::setManyToOne('product', 'Product');
        DaoMap::setStringType('attribute','varchar', 500);
        DaoMap::setManyToOne('type', 'ProductAttributeType');
        parent::__loadDaoMap();
        DaoMap::createIndex('attribute');
        DaoMap::commit();
    }
}