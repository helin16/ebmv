<?php
/**
 * OrderItem Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class OrderItem extends BaseEntityAbstract
{
    /**
     * The order this item belongs to
     * 
     * @var Order
     */
    protected $order;
    /**
     * The product
     * 
     * @var Product
     */
    protected $product;
    /**
     * The unit price
     * 
     * @var double
     */
    private $unitPrice;
    /**
     * The qty of the order
     * 
     * @var int
     */
    private $qty;
    /**
     * The total price of the order item
     * 
     * @var double
     */
    private $totalPrice;
    /**
     * Getter for order
     *
     * @return Order
     */
    public function getOrder() 
    {
        return $this->order;
    }
    /**
     * Setter for order
     *
     * @param Order $value The order
     *
     * @return OrderItem
     */
    public function setOrder(Order $value) 
    {
        $this->order = $value;
        return $this;
    }
    /**
     * Getter for product
     *
     * @return Product
     */
    public function getProduct() 
    {
        return $this->product;
    }
    /**
     * Setter for product
     *
     * @param Product $value The product
     *
     * @return OrderItem
     */
    public function setProduct(Product$value) 
    {
        $this->product = $value;
        return $this;
    }
    /**
     * Getter for unitPrice
     *
     * @return double
     */
    public function getUnitPrice() 
    {
        return $this->unitPrice;
    }
    /**
     * Setter for unitPrice
     *
     * @param double $value The unitPrice
     *
     * @return OrderItem
     */
    public function setUnitPrice($value) 
    {
        $this->unitPrice = $value;
        return $this;
    }
    /**
     * Getter for qty
     *
     * @return 
     */
    public function getQty() 
    {
        return $this->qty;
    }
    /**
     * Setter for qty
     *
     * @param int $value The qty
     *
     * @return OrderItem
     */
    public function setQty($value) 
    {
        $this->qty = $value;
        return $this;
    }
    /**
     * Getter for totalPrice
     *
     * @return double
     */
    public function getTotalPrice() 
    {
        return $this->totalPrice;
    }
    /**
     * Setter for totalPrice
     *
     * @param double $value The totalPrice
     *
     * @return OrderItem
     */
    public function setTotalPrice($value) 
    {
        $this->totalPrice = $value;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'ord_item');
        DaoMap::setOneToMany("order", "Order", 'ord_item_order');
        DaoMap::setOneToMany("product", "Product", 'ord_item_product');
        DaoMap::setIntType('unitPrice', 'double', '10,4', false, '0.0000');
        DaoMap::setIntType('qty');
        DaoMap::setIntType('totalPrice', 'double', '10,4', false, '0.0000');
        parent::__loadDaoMap();
    
        DaoMap::createIndex('qty');
        DaoMap::createIndex('unitPrice');
        DaoMap::createIndex('totalPrice');
        DaoMap::commit();
    }
    /**
     * Getting the order by order no
     * 
     * @param string $orderNo The order no
     * 
     * @return Ambigous <NULL, unknown>
     */
    public static function create(Order $order, Product $product, $unitPrice = '0.0000', $qty = 0 , $totalPrice = '0.0000')
    {
    	$item = new OrderItem();
    	$item->setOrder($value)
    		->setProduct($product)
    		->setUnitPrice($unitPrice)
    		->setQty($qty)
    		->setTotalPrice($totalPrice);
    	return $item;
    }
}