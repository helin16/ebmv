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
	const ID_SIMPLIFIED_CHINESE = 1;
	const ID_TRADITIONAL_CHINESE = 2;
	private static $_cache;
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
     * @var multiple:Product
     */
    protected $products;
    /**
     * The language codes for this language
     * 
     * @var multiple:LanguageCode
     */
    protected $codes;
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
     * Getter for the language code
     * 
     * @return multiple:LanguageCode
     */
    public function getCodes() 
    {
    	$this->loadOneToMany('codes');
        return $this->codes;
    }
    /**
     * Setter for the LanguageCode
     * 
     * @param array $value The array of the LanguageCodes
     * 
     * @return Language
     */
    public function setCodes($value) 
    {
        $this->codes = $value;
        return $this;
    }
    /**
     * Getting the lanuage
     * 
     * @param int $id The id of the language
     * 
     * @return Ambigous <multitype:, multitype:BaseEntityAbstract >
     */
    public static function getLanguage($id)
    {
    	if(!isset(self::$_cache[$lang->getId()]))
    		self::$_cache[$lang->getId()] = EntityDao::getInstance(get_called_class())->findById($lang->getId());
    	return self::$_cache[$lang->getId()];
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
        DaoMap::setOneToMany("codes", "LanguageCode");
        parent::__loadDaoMap();
    
        DaoMap::createIndex('name');
        DaoMap::commit();
    }
}