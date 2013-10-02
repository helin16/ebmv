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
    protected $position;
    /**
     * The root category of this category
     * 
     * @var Category
     */
    protected $root;
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
    public function postSave()
    {
        $class = get_class($this);
        if(!$this->root instanceof $class)
        {
            $fakeParent = new $class();
            $fakeParent->setProxyMode(true);
            $fakeParent->setId($this->getId());
            EntityDao::getInstance($class)->save($this);
        }
    }
	/**
	 * load the default elments of the base entity
	 */
	protected function __loadDaoMap()
	{
	    DaoMap::setManyToOne('root', get_class($this), null, true);
	    DaoMap::setManyToOne('parent', get_class($this), null, true);
		DaoMap::setStringType('position', 'varchar', 255, false, '1');
	    parent::__loadDaoMap();
	}
}

?>