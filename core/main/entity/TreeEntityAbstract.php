<?php
/**
 * Common entity class
 *
 * @package Core
 * @subpackage Entity
 */
abstract class TreeEntityAbstract extends BaseEntityAbastract
{
	/**
     * The parent category of this category
     * 
     * @var Category
     */
    protected $_parent;
    /**
     * The position of the category with the category tree
     *
     * @var string
     */
    private $_position;
    /**
     * The root category of this category
     * 
     * @var Category
     */
    private $_root;
    /**
     * getter position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->_position;
    }
    /**
     * setter position
     * 
     * @param string $position The new position
     * 
     * @return TreeEntityAbstract
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }
    /**
     * getter parent
     *
     * @return TreeEntityAbstract
     */
    public function getParent()
    {
        $this->loadManyToOne('_parent');
        return $this->_parent;
    }
    /**
     * setter parent
     * 
     * @param TreeEntityAbstract $parent The parent node
     * 
     * @return TreeEntityAbstract
     */
    public function setParent($parent)
    {
        $this->_parent = $parent;
        return $this;
    }
    /**
     * getter root
     *
     * @return TreeEntityAbstract
     */
    public function getRoot()
    {
        $this->loadManyToOne('_root');
        return $this->_root;
    }
    /**
     * setter parent
     * 
     * @param TreeEntityAbstract $root The root node
     * 
     * @return TreeEntityAbstract
     */
    public function setRoot($root)
    {
        $this->_root = $root;
        return $this;
    }
	/**
	 * load the default elments of the base entity
	 */
	protected function __loadDaoMap()
	{
	    parent::__loadDaoMap();
	    DaoMap::setManyToOne('_root', get_class($this));
	    DaoMap::setManyToOne('_parent', get_class($this));
		DaoMap::setStringType('_position', 'varchar', 255, false, '1');
	}
}

?>