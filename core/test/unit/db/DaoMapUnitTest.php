<?php
/**
 * Test case for Dao - DaoMap
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class DaoMapUnitTest extends CoreDaoUnitTestAbstract
{
    /**
     * (non-PHPdoc)
     * @see CoreDaoUnitTestAbstract::setUp()
     */
    public function setUp()
    {
        parent::setUp();
        DaoMap::$map = array();
    }
    /**
     * testing the DaoMap::loadMap()
     */
    public function testLoadDaoMap()
    {
		$focus = strtolower($this->_focus);
        DaoMap::begin(new $this->_focus, 'p');
        
		DaoMap::setStringType('firstName');
		DaoMap::setOneToMany('userAccounts', 'UserAccount', 'ua');
		DaoMap::setManyToOne('userAccount', 'UserAccount', 'ua');
		DaoMap::setSearchFields('firstName');
		DaoMap::commit();
		
		$expected = array(
		    '_' => array (
				'alias' => 'p',
		    	'sort' => null,
		        'index' => array(array('userAccount')),
		        'search' => array('firstName')
		    ),
			'firstName' => array (
              'type' => 'varchar',
              'size' => 50,
              'nullable' => false,
              'default' => ''
           ),
           'userAccounts' => array (
           		'type' => 'int',
           		'size' => 4,
           		'unsigned' => true,
           		'nullable' => false,
           		'default' => 0,
           		'class' => 'UserAccount',
           		'alias' => 'ua',
           		'rel' => DaoMap::ONE_TO_MANY
           ),
		   'userAccount' => array (
	           'type' => 'int',
    			'size' => 4,
    			'unsigned' => true,
    			'nullable' => false,
    			'default' => 0,
    			'class' => 'UserAccount',
           		'alias' => 'ua',
    			'rel' => DaoMap::MANY_TO_ONE
		   )  
    	);
		$this->assertTrue(isset(DaoMap::$map[$focus]));
        $this->assertEquals($expected, DaoMap::$map[$focus]);
    }
    public function testLoadMap()
    {
        $focus = strtolower($this->_focus);
        DaoMap::loadMap($focus);
        $expected = array(
            '_' => array (
                    'alias' => 'p',
                    'sort' => null,
                    'index' => array(array("createdBy"), array("updatedBy"))
            ),
            'firstName' => array (
                    'type' => 'varchar',
                    'size' => 50,
                    'nullable' => false,
                    'default' => ''
            ),
            'userAccounts' => array (
                    'type' => 'int',
                    'size' => 4,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0,
                    'class' => 'UserAccount',
                    'alias' => 'ua',
                    'rel' => DaoMap::ONE_TO_MANY
            ),
            'active' => array (
                    'type' => 'bool',
                    'default' => 1
            ),
    	   'created' => array (
    	           'type' => 'datetime',
    	           'nullable' => false,
    	           'default' => '0001-01-01 00:00:00'
    	   ),
    	   'createdBy' => array (
    	           'type' => 'int',
    	           'size' => 4,
    	           'unsigned' => true,
    	           'nullable' => false,
    	           'default' => 0,
    	           'class' => 'UserAccount',
    	           'alias' => 'createdBy',
    	           'rel' => DaoMap::MANY_TO_ONE
    	   ),
    	   'updated' => array (
    	           'type' => 'timestamp',
    	           'nullable' => false,
    	           'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    	   ),
    	   'updatedBy' => array (
    	           'type' => 'int',
    	           'size' => 4,
    	           'unsigned' => true,
    	           'nullable' => false,
    	           'default' => 0,
    	           'class' => 'UserAccount',
    	           'alias' => 'updatedBy',
    	           'rel' => DaoMap::MANY_TO_ONE
    	   )
        );
        $this->assertTrue(isset(DaoMap::$map[$focus]));
        $this->assertEquals($expected['_'], DaoMap::$map[$focus]['_']);
    }
    /**
     * testing the DaoMap::hasMap() & DaoMap::loadMap() function
     */
    public function testHasMap()
    {
        $this->assertFalse(DaoMap::hasMap(1));
        $this->assertFalse(DaoMap::hasMap($this->_focus));
        $this->assertFalse(DaoMap::hasMap(new $this->_focus));
        
        DaoMap::loadMap($this->_focus);
        $this->assertTrue(DaoMap::hasMap($this->_focus));
        $this->assertTrue(DaoMap::hasMap(new $this->_focus));
    }
}
?>