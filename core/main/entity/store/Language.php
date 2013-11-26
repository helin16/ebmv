<?php
/**
 * Language Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Language extends BaseEntityAbstract
{
    /**
     * The name of the language
     * 
     * @var string
     */
    private $name;
    /**
     * The products that using this language
     * 
     * @var multiple:Product
     */
    protected $products;
    /**
     * Getters for the name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Setters for the name
     * 
     * @param string $name The name of the language
     * 
     * @return Language
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * Getting the products of the language
     * 
     * @return multiple:Product
     */
    public function getProducts() 
    {
        return $this->products;
    }
    /**
     * setting the products for the language
     * 
     * @param array $value The products
     * 
     * @return Language
     */
    public function setProducts($value) 
    {
        $this->products = $value;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'lan');
        DaoMap::setStringType('name','varchar', 200);
        DaoMap::setManyToMany("products", "Product", DaoMap::RIGHT_SIDE, 'pro');
        parent::__loadDaoMap();
    
        DaoMap::createIndex('name');
        DaoMap::commit();
    }
}