<?php
/**
 * LibraryOwns Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class LibraryOwns extends BaseEntityAbstract
{
    /**
     * The product that a library owns
     * 
     * @var Product
     */
    protected $product;
    /**
     * The library
     * 
     * @var Library
     */
    protected $library;
    /**
     * The available copies of the product that the library owns
     * 
     * @var int
     */
    private $availCopies;
    /**
     * The total copies of the product that the library owns
     * 
     * @var int
     */
    private $totalCopies;
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
     * @param Product $value The Product
     * 
     * @return LibraryOwns
     */
    public function setProduct($value) 
    {
        $this->product = $value;
        return $this;
    }
    /**
     * Getter for the library
     * 
     * @return Library
     */
    public function getLibrary() 
    {
    	$this->loadManyToOne('library');
        return $this->library;
    }
    /**
     * Setter for the library
     * 
     * @param Library $value The Library
     * 
     * @return LibraryOwns
     */
    public function setLibrary($value) 
    {
        $this->library = $value;
        return $this;
    }
    /**
     * Getter for the availCopies
     * 
     * @return number
     */
    public function getAvailCopies() 
    {
        return $this->availCopies;
    }
    /**
     * Setter for availCopies
     * 
     * @param int $value The availCopies
     * 
     * @return LibraryOwns
     */
    public function setAvailCopies($value) 
    {
        $this->availCopies = $value;
        return $this;
    }
    /**
     * Getter for the totalCopies
     * 
     * @return number
     */
    public function getTotalCopies() 
    {
        return $this->totalCopies;
    }
    /**
     * Setter for the totalCopies
     * 
     * @param int $value The totalCopies
     * 
     * @return LibraryOwns
     */
    public function setTotalCopies($value) 
    {
        $this->totalCopies = $value;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'lib_own');
        DaoMap::setManyToOne('library', 'Library');
        DaoMap::setManyToOne('product', 'Product');
        DaoMap::setIntType('totalCopies');
        DaoMap::setIntType('availCopies');
        parent::__loadDaoMap();
    
        DaoMap::createIndex('availCopies');
        DaoMap::createIndex('totalCopies');
        DaoMap::commit();
    }
}