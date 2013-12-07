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
     * The available copies for view online of the product that the library owns
     * 
     * @var int
     */
    private $availForView;
    /**
     * The total copies of the product for view online that the library owns
     * 
     * @var int
     */
    private $totalForView;
    /**
     * The available copies for download of the product that the library owns
     * 
     * @var int
     */
    private $availForDownload;
    /**
     * The total copies for download of the product that the library owns
     * 
     * @var int
     */
    private $totalForDownload;
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
     * Getter for the availForView
     * 
     * @return number
     */
    public function getAvailForView() 
    {
        return $this->availForView;
    }
    /**
     * Setter for availForView
     * 
     * @param int $value The availForView
     * 
     * @return LibraryOwns
     */
    public function setAvailForView($value) 
    {
        $this->availForView = $value;
        return $this;
    }
    /**
     * Getter for the totalForView
     * 
     * @return number
     */
    public function getTotalForView() 
    {
        return $this->totalForView;
    }
    /**
     * Setter for the totalForView
     * 
     * @param int $value The totalForView
     * 
     * @return LibraryOwns
     */
    public function setTotalForView($value) 
    {
        $this->totalForView = $value;
        return $this;
    }
    /**
     * Getter for the availForDownload
     * 
     * @return number
     */
    public function getAvailForDownload() 
    {
        return $this->availForDownload;
    }
    /**
     * Setter for the availForDownload
     * 
     * @param int $value The availForDownload
     * 
     * @return LibraryOwns
     */
    public function setAvailForDownload($value) 
    {
        $this->availForDownload = $value;
        return $this;
    }
    /**
     * Getter for the totalForDownload
     * 
     * @return number
     */
    public function getTotalForDownload() 
    {
        return $this->totalForDownload;
    }
    /**
     * Setter for the totalForDownload
     * 
     * @param int $value The totalForDownload
     * 
     * @return LibraryOwns
     */
    public function setTotalForDownload($value) 
    {
        $this->totalForDownload = $value;
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
        DaoMap::setIntType('availForView');
        DaoMap::setIntType('totalForView');
        DaoMap::setIntType('availForDownload');
        DaoMap::setIntType('totalForDownload');
        parent::__loadDaoMap();
    
        DaoMap::createIndex('availForView');
        DaoMap::createIndex('totalForView');
        DaoMap::createIndex('availForDownload');
        DaoMap::createIndex('totalForDownload');
        DaoMap::commit();
    }
}