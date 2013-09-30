<?php
/**
 * Common entity class
 *
 * @package Core
 * @subpackage Entity
 */
abstract class TreeEntityAbstract extends BaseEntityAbstract
{
	/**
     * The parent category of this category
     * 
     * @var Category
     */
    protected $parent;
    /**
     * The position of the category with the category tree
     *
     * @var string
     */
    private $position;
    /**
     * The root category of this category
     * 
     * @var Category
     */
    private $root;
    /**
     * getter position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
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
        $this->loadManyToOne('parent');
        return $this->parent;
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
        $this->parent = $parent;
        return $this;
    }
    /**
     * getter root
     *
     * @return TreeEntityAbstract
     */
    public function getRoot()
    {
        $this->loadManyToOne('root');
        return $this->root;
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
        $this->root = $root;
        return $this;
    }
	/**
	 * load the default elments of the base entity
	 */
	protected function __loadDaoMap()
	{
	    parent::__loadDaoMap();
	    DaoMap::setManyToOne('root', get_class($this));
	    DaoMap::setManyToOne('parent', get_class($this));
		DaoMap::setStringType('position', 'varchar', 255, false, '1');
	}
}

?>