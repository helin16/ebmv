<?php
/**
 * Common entity class
 *
 * @package Core
 * @subpackage Entity
 */
abstract class BaseEntityAbstract
{
	/**
	 * Internal id used by all application entities
	 * 
	 * @var int
	 */
	protected $_id = null;
	/**
	 * @var bool
	 */
	protected $_active;
	/**
	 * @var UDate
	 */
	protected $_created;
	/**
	 * @var User
	 */
	protected $_createdBy;
	/**
	 * @var UDate
	 */
	protected $_updated;
	/**
	 * @var User
	 */
	protected $_updatedBy;
	/**
	 * Is this a proxy object?
	 * 
	 * @var bool
	 */
	protected $_proxyMode = false;
	/**
	 * Set the primary key for this entity
	 *
	 * @param int $id
	 * 
	 * @return BaseEntityAbstract
	 */
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}
	/**
	 * Get the primary key for this entity
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->_id;
	}
    /**
     * Setter for whether the entity is active
     * 
     * @param bool $active whether the entity is active
     * 
     * @return BaseEntityAbstract
     */
    public function setActive($active)
    {
    	$this->active = intval($active);
    	return $this;
    }
    /**
     * Getter for whether the entity is active
     * 
     * @return bool
     */
    public function getActive()
    {
    	return trim($this->_active) === '1';
    }
    /**
     * Set when this entity was created
     *
     * @param string $created The UDate time string
     *
     * @return BaseEntityAbstract
     */
    public function setCreated($created)
    {
        $this->_created = $created;
        return $this;
    }
    /**
     * When was this entity created
     *
     * @return UDate
     */
    public function getCreated()
    {
        if (is_string($this->_created))
        $this->_created = new UDate($this->_created);
        return $this->_created;
    }
    /**
     * Set who created this entity
     *
     * @param User $user The new CreatedBy useraccount
     *
     * @return BaseEntityAbstract
     */
    public function setCreatedBy(User $user)
    {
        $this->_createdBy = $user;
        return $this;
    }
    /**
     * Who created this entity
     *
     * @return UserAccount
     */
    public function getCreatedBy()
    {
        $this->loadManyToOne('createdBy');
        return $this->_createdBy;
    }
    /**
     * Set when this entity was last updated
     *
     * @param string $updated The UDate time string
     *
     * @return BaseEntityAbstract
     */
    public function setUpdated($updated)
    {
        $this->_updated = $updated;
        return $this;
    }
    /**
     * When was this entity last updated
     *
     * @return UDate
     */
    public function getUpdated()
    {
        if (is_string($this->_updated))
        $this->_updated = new UDate($this->_updated);
        return $this->_updated;
    }
    /**
     * Set who last updated this entity
     *
     * @param UserAccount $user The UpdatedBy useraccount
     *
     * @return BaseEntityAbstract
     */
    public function setUpdatedBy(User $user)
    {
        $this->_updatedBy = $user;
        return $this;
    }
    /**
     * Who last updated this entity
     *
     * @return User
     */
    public function getUpdatedBy()
    {
        $this->loadManyToOne('updatedBy');
        return $this->_updatedBy;
    }
	/**
	 * Dictates if the entity is a proxy object or not for lazy loading purposes
	 * 
	 * @param bool $bool Whether we are in proxy mode
	 * 
	 * @return BaseEntityAbstract
	 */
	public function setProxyMode($bool)
	{
		$this->_proxyMode = (bool)$bool;
		return $this;
	}
	/**
	 * Check if an entity is a proxy object
	 *
	 * @return bool
	 */
	public function getProxyMode()
	{
		return $this->_proxyMode;
	}
	/**
	 * Lazy load a one-to-many relationship 
	 *
	 * @param string $property The property that we are trying to load
	 * 
	 * @return Mixed
	 */
	protected function loadOneToMany($property)
	{
		// Figure out what the object type is on the many side
		$this->__loadDaoMap();
		$thisClass = get_class($this);
		$cls = DaoMap::$map[strtolower($thisClass)][$property]['class'];

		DaoMap::loadMap($cls);
		$alias = DaoMap::$map[strtolower($cls)]['_']['alias'];
		$field = strtolower(substr($thisClass, 0, 1)) . substr($thisClass, 1);
		$this->$property = Dao::findByCriteria(new DaoQuery($cls), sprintf('%s.`%sId`=?', $alias, $field), array($this->getId()));
		
		return $this->$property;
	}
	/**
	 * Lazy load a one-to-one relationship 
	 *
	 * @param string $property
	 */
	protected function loadOneToOne($property)
	{
		return $this->loadManyToOne($property);
	}
	/**
	 * Lazy load a many-to-one relationship 
	 *
	 * @param string $property The property that we are trying to load
	 * 
	 * @return BaseEntityAbstract
	 */
	protected function loadManyToOne($property)
	{
		$this->__loadDaoMap();
		if (is_null($this->$property))
		{
		    //if the proerty is allow to have null value, then let it be
			if (DaoMap::$map[strtolower(get_class($this))][$property]['nullable'])
			{
				$this->$property = null;
				return $this;
			}
			//if the property is one of these, as when we are trying to save them, we don't have the iniated value
			if (in_array($property, array('createdBy', 'updatedBy')))
			    $this->$property = Core::getUser();
			else
			    throw new Exception('Property (' . get_class($this) . '::' . $property . ') must be initialised to integer or proxy prior to lazy loading.', 1);
		}
		
		// Load the DAO map for this entity
		$cls = DaoMap::$map[strtolower(get_class($this))][$property]['class'];
		if (!$this->$property instanceof BaseEntityAbstract)
		    throw new DaoException('The property(' . $property . ') for "' . get_class($this) . '" is NOT a BaseEntity!');
		$this->$property = Dao::findById(new DaoQuery($cls), $this->$property->getId());
		return $this->$property;
	}
	/**
	 * Lazy load a many-to-many relationship 
	 *
	 * @param string $property The property that we are trying to load
	 * 
	 * @return mixed
	 */
	protected function loadManyToMany($property)
	{
		// Grab the DaoMap data for both ends of the join
		$this->__loadDaoMap();
		$cls = DaoMap::$map[strtolower(get_class($this))][$property]['class'];
		$obj = new $cls;
		$obj->__loadDaoMap();

		$thisClass = get_class($this);
		$qry = new DaoQuery($cls);
		$qry->eagerLoad($cls . '.' . strtolower(substr($thisClass, 0, 1)) . substr($thisClass, 1) . 's');
		
		// Load this end with an array of entities typed to the other end
		DaoMap::loadMap($cls);
		$alias = DaoMap::$map[strtolower($cls)]['_']['alias'];
		$field = strtolower(substr($thisClass, 0, 1)) . substr($thisClass, 1);
		$this->$property = Dao::findByCriteria($qry, sprintf('`%sId`=?', $field), array($this->getId()));
		return $this->$property;
	}
	/**
	 * getting the account entry for json
	 * 
	 * @throws EntityException
	 */
	public function getJsonArray()
	{
		$array = array('id' => trim($this->getId()));
	    DaoMap::loadMap(get_class($this));
	    foreach(DaoMap::$map[strtolower(get_class($this))] as $field => $fieldMap)
	    {
	        if($field === '_' || isset($fieldMap['rel']))
	            continue;
	        $getterMethod = 'get' . ucfirst($field);
	        $array[$field] = trim($this->$getterMethod());
	        if(trim($fieldMap['type']) === 'bool')
	            $array[$field] = (trim($array[$field]) === '1' ? true : false);
	    }
	    return $array;
	}
	/**
	 * Default toString implementation
	 *
	 * @return string
	 */
	public function __toString()
	{
		return get_class($this) . ' (#' . $this->getId() . ')';
	}
	/**
	 * load the default elments of the base entity
	 */
	protected function __loadDaoMap()
	{
// 	    DaoMap::setIntType('_id', 'int', 1);
	    DaoMap::setBoolType('active', 'bool', 1);
	    DaoMap::setDateType('created');
	    DaoMap::setManyToOne('createdBy', 'User');
	    DaoMap::setDateType('updated', 'timestamp', false, 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
	    DaoMap::setManyToOne('updatedBy', 'User');
	}
	/**
	 * validates all rules before save in EntityDao!!!
	 * 
	 * @todo need to be implemented!!!!!
	 * 
	 * @return boolean
	 */
	public function validateAll()
	{
	    $errorMsgs = array();
	    return $errorMsgs;
	}
	public function preSave() {}
	public function postSave() {}
}

?>