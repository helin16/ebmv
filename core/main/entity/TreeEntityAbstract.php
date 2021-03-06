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
     * how many digits of PER LEVEL
     * @var int
     */
    const POS_LENGTH_PER_LEVEL = 4;
    /**
     * The default separator for breadCrubms
     * @var string
     */
    const BREADCRUMBS_SEPARATOR = ' / ';
	/**
     * The parent category of this category
     * 
     * @var Category
     */
    protected $parent = null;
    /**
     * The position of the category with the category tree
     *
     * @var string
     */
    protected $position = 1;
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
    /**
     * Getting the parents including itself
     * 
     * @return multitype:TreeEntityAbstract |Ambigous <Ambigous, multitype:, multitype:BaseEntityAbstract >
     */
    public function getParents()
    {
    	$currentPos = trim($this->getPosition());
    	if($currentPos === '' || $currentPos === '1')
    		return array($this);
    	
    	$posArray = array();
    	$length = strlen($currentPos);
    	for($i = 0; $i < ($length - 1) / self::POS_LENGTH_PER_LEVEL ; $i++)
    	{
    		$posArray[] = trim(substr($currentPos, 0, ($i * self::POS_LENGTH_PER_LEVEL) + 1));
    	}
    	return self::getAllByCriteria('rootId = ? and position in (' . implode(',', array_fill(0, count($posArray), '?')) . ')', array_merge(array($this->getRoot()->getId()), $posArray), false, null, DaoQuery::DEFAUTL_PAGE_SIZE, array('position' => 'asc'));
    }
    /**
     * Getting  the path of the node
     * 
     * @return string
     */
    public function getPath()
    {
    	return implode(' / ', array_map(create_function('$a', 'return trim($a->getName());'), $this->getParents()));
    }
    /**
     * Getting the next position for the new children of the provided parent
     *
     * @throws EntityException
     * @return int
     */
    public function getNextPosition()
    {
        $parentPos = trim($this->getPosition());
        $sql="select position from " . strtolower(get_class($this)) . " where active = 1 and position like '" . $parentPos . str_repeat('_', self::POS_LENGTH_PER_LEVEL). "' order by position asc";
        $result = Dao::getResultsNative($sql);
        if(count($result) === 0)
        return $parentPos . str_repeat('0', self::POS_LENGTH_PER_LEVEL);
         
        $expectedAccountNos = array_map(create_function('$a', 'return "' . $parentPos . '".str_pad($a, ' . self::POS_LENGTH_PER_LEVEL . ', 0, STR_PAD_LEFT);'), range(0, str_repeat('9', self::POS_LENGTH_PER_LEVEL)));
        $usedAccountNos = array_map(create_function('$a', 'return $a["position"];'), $result);
        $unUsed = array_diff($expectedAccountNos, $usedAccountNos);
        sort($unUsed);
        if (count($unUsed) === 0)
        	throw new EntityException("Position over loaded (parentId = " . $this->getId() . ")!");
         
        return $unUsed[0];
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::postSave()
     */
    public function postSave()
    {
    	$class = get_class($this);
        if(!$this->getRoot() instanceof $class)
        {
            $fakeParent = new $class();
            $fakeParent->setProxyMode(true);
            $fakeParent->setId($this->getId());
            $this->setRoot($fakeParent);
            Dao::save($this);
        }
    }
    /**
     * Getting all the children of this node
     * 
     * @param int   $pageNo
     * @param int   $pageSize
     * @param array $orderBy
     * 
     * @return Ambigous <multitype:, multitype:BaseEntityAbstract >
     */
    public function getChildren($activeOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array(), &$stats = array())
    {
    	return self::getAllByCriteria('position like ? and rootId = ?', array($this->getPosition() . '%', $this->getRoot()->getId()), $activeOnly, $pageNo, $pageSize, $orderBy, $stats);
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::getJson()
     */
    public function getJson($extra = array(), $reset = false)
    {
    	$array = array();
    	if(!$this->isJsonLoaded($reset))
    	{
    		$array['path'] = trim($this->getPath());
    	}
    	return parent::getJson($array, $reset);
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