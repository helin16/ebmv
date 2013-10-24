<?php
/**
 * SupplierPrice Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class SupplierPrice extends BaseEntityAbstract
{
    /**
     * The supplier of the product
     * 
     * @var Supplier
     */
    protected $supplier;
    /**
     * The price of the product
     * 
     * @var string
     */
    private $price;
    /**
     * The product this attribute is belonging to
     * 
     * @var Product
     */
    protected $product;
    /**
     * Getter for the supplier
     * 
     * @return Supplier
     */
    public function getSupplier()
    {
        $this->loadManyToOne('supplier');
        return $this->supplier;
    }
    /**
     * Setter for the productattribute type
     * 
     * @param ProductAttributeType $type The type of the product attribute
     * 
     * @return SupplierPrice
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
        return $this;
    }
    /**
     * Getter for the price
     * 
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }
    /**
     * Setter for the Price
     * 
     * @param string $price The price
     * 
     * @return SupplierPrice
     */
    public function setPice($price)
    {
        $this->price = $price;
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
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'sup_price');
        DaoMap::setManyToOne('product', 'Product');
        DaoMap::setManyToOne('supplier', 'Supplier');
        DaoMap::setIntType('price','double', '10,4');
        parent::__loadDaoMap();
        DaoMap::createIndex('price');
        DaoMap::commit();
    }
}