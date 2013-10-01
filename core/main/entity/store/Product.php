<?php
/**
 * Product Entity - storing the session data in the database
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Product extends BaseEntityAbstract
{
    /**
     * The title of the book
     * 
     * @var string
     */
    private $title;
    /**
     * The auther 
     * @var string
     */
    private $author;
    /**
     * The ISBN
     * 
     * @var string
     */
    private $isbn;
    /**
     * The description
     * 
     * @var string
     */
    private $description;
    /**
     * The noOfWords
     * 
     * @var int
     */
    private $noOfWords;
    /**
     * The publisher
     * 
     * @var string
     */
    private $publisher;
    /**
     * The published date
     * 
     * @var UDate::
     */
    private $publishDate;
	/**
	 * The categories that the products are belongin to 
	 * 
	 * @var multiple:Category
	 */
	protected $categorys;
	public function getTitle()
	{
	    return $this->title;
	}
	public function setTitle($title)
	{
	    $this->title = $title;
	    return $this;
	}
	public function getIsbn()
	{
	    return $this->isbn;
	}
	public function setIsbn($isbn)
	{
	    $this->isbn = $isbn;
	    return $this;
	}
	public function getNoOfWords()
	{
	    return $this->noOfWords;
	}
	public function setNoOfWords($noOfWords)
	{
	    $this->noOfWords = $noOfWords;
	    return $this;
	}
	public function getDescription()
	{
	    return $this->description;
	}
	public function setDescription($description)
	{
	    $this->description = $description;
	    return $this;
	}
	public function getAuthor()
	{
	    return $this->author;
	}
	public function setAuthor($author)
	{
	    $this->author = $author;
	    return $this;
	}
	public function getPublisher()
	{
	    return $this->publisher;
	}
	public function setPublisher($publisher)
	{
	    $this->publisher = $publisher;
	    return $this;
	}
	public function getPublishDate()
	{
	    return $this->publishDate;
	}
	public function setPublishDate($publishDate)
	{
	    $this->publishDate = $publishDate;
	    return $this;
	}
	/**
	* getter Categorys
	*
	* @return multiple:Category
	*/
	public function getCategorys()
	{
	    $this->loadManyToMany("categorys");
	    return $this->categorys;
	}
	/**
	 * Setter Person
	 *
	 * @param array $categorys The categories that the products are belongin to 
	 *
	 * @return Product
	 */
	public function setCategorys($categorys)
	{
	    $this->categorys = $categorys;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'p');
		DaoMap::setStringType('title','varchar', 200);
		DaoMap::setStringType('isbn');
		DaoMap::setStringType('author','varchar', 200);
		DaoMap::setStringType('publisher','varchar', 255);
		DaoMap::setDateType('publishDate');
		DaoMap::setIntType('noOfWords');
		DaoMap::setStringType('description','varchar', 255);
		DaoMap::setManyToMany("categorys", "Category", DaoMap::LEFT_SIDE, "pcat");
		parent::__loadDaoMap();
		
		DaoMap::createIndex('title');
		DaoMap::createIndex('author');
		DaoMap::createIndex('publisher');
		DaoMap::createIndex('publishDate');
		DaoMap::createIndex('isbn');
		DaoMap::createIndex('description');
		DaoMap::commit();
	}
}

?>