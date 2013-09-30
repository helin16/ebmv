<?php
/**
 * Session Entity - storing the session data in the database
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Session extends BaseEntityAbstract
{
    /**
     * The session ID
     * 
     * @var string
     */
    private $_key;
    /**
     * The session data
     * 
     * @var string
     */
    private $_data;
    /**
     * Getting the sesison ID
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }
    /**
     * Setter for the session ID
     * 
     * @param string $key The 
     * 
     * @return string
     */
    public function setKey($key)
    {
        $this->_key = $key;
        return $this;
    }
    /**
     * Getter for the session data
     * 
     * @return string
     */
    public function getData()
    {
        return $this->_data;
    }
    /**
     * Setter for the session data
     * 
     * @param string $data The session data
     * 
     * @return Session
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__toString()
	 */
	public function __toString()
	{
        return $tis->_data;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'sess');
		DaoMap::setStringType('_key', 'varchar', 32);
		DaoMap::setStringType('_data', 'longtext');
		parent::__loadDaoMap();
		DaoMap::commit();
	}
}

?>