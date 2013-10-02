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
    * (non-PHPdoc)
    * @see BaseEntity::__loadDaoMap()
    */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'pa');
        DaoMap::setStringType('attribute','varchar', 500);
        DaoMap::setManyToOne('type', 'ProductAttributeType');
        parent::__loadDaoMap();
        DaoMap::createIndex('attribute');
        DaoMap::commit();
    }
}