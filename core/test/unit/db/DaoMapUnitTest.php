<?php
/**
 * Test case for Dao - DaoMap
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 */
class DaoMapUnitTest extends CoreUnitTestAbstract
{
    /**
     * pre-test for each test function
     */
    public function setUp()
    {
    }
    /**
     * post test for each test function
     */
    public function tearDown()
    {
        DaoMap::$map = array();
    }
    /**
     * testing DaoMap::loadMap()
     */
	public function testLoadMap()
	{
	    $class = 'User';
		DaoMap::loadMap($class);
		$this->assertTrue(isset(DaoMap::$map[strtolower($class)]['_']['base']) && is_string(DaoMap::$map[strtolower($class)]['_']['base']), 'Site requires a base query');
	}
    /**
     * testing DaoMap::loadMap()
     * 
     * 
     * @expectedException        HydraDaoException
     * @expectedExceptionMessage You can NOT create an object with empty classname!
     */
	public function testHasMapWithException()
	{
		DaoMap::loadMap(null);
	}
    /**
     * testing DaoMap::hasMap()
     */
	public function testHasMap()
	{
		$this->assertFalse(DaoMap::hasMap('User'));
		$this->assertFalse(DaoMap::hasMap(new stdClass()));
		
		DaoMap::loadMap('User');
		$this->assertTrue(DaoMap::hasMap('User'));
		$this->assertTrue(DaoMap::hasMap(new User()));
		$this->assertFalse(DaoMap::hasMap('false'));
	}
    /**
     * testing DaoMap::begin()
     */
	public function testBegin()
	{
	    $class = 'User';
	    $object = new $class();
		DaoMap::begin($object);
		DaoMap::commit();
		
		//check on map key
		$this->assertTrue(isset(DaoMap::$map[strtolower($class)]));
		//check on alias key '_'
		$this->assertEquals(strtolower($class), DaoMap::$map[strtolower($class)]['_']['alias']);
		$this->assertEquals($object instanceof HydraVersionedEntity, DaoMap::$map[strtolower($class)]['_']['versioned']);
		$this->assertEquals(null, DaoMap::$map[strtolower($class)]['_']['sort']);
	}
	
	public function testManyToOne()
	{
	    $alias = null;
	    $nullable = true;
	    $class = 'User';
	    $field = 'manytomany';
	    $defaultId = 0;
	    $object = new $class();
	    DaoMap::begin($object);
	    DaoMap::setManyToOne($field, $class, $alias, $nullable, $defaultId);
	    DaoMap::commit();
	    
	    //check on fields
	    $this->assertTrue(isset(DaoMap::$map[strtolower($class)][$field]));
	    $excepted = array('type' => 'int', 
    	    	'size' => 10,
    	    	'unsigned' => true,    	    	
    	    	'nullable' => $nullable,
    	    	'default' => $defaultId, 
    	    	'class' => $class, 
    	    	'alias' => $field,
    	    	'rel' => DaoMap::MANY_TO_ONE);
	    $this->assertEquals($excepted, DaoMap::$map[strtolower($class)][$field], "Should get: " . print_r($excepted, true) . " but got: " . print_r(DaoMap::$map[strtolower($class)][$field], true));
	}
	
	public function testManyToMany()
	{
	    $alias = null;
	    $nullable = true;
	    $class = 'User';
	    $field = 'manytomany';
	    $object = new $class();
	    DaoMap::begin($object);
	    DaoMap::setManyToMany($field, $class, DaoMap::LEFT_SIDE);
	    DaoMap::commit();
	    
	    //check on fields
	    $this->assertTrue(isset(DaoMap::$map[strtolower($class)][$field]));
	    $excepted = array('type' => 'int',
	        	    	'size' => 10,
	        	    	'unsigned' => true,    	    	
	        	    	'nullable' => $nullable,
	        	    	'default' => $defaultId, 
	        	    	'class' => $class, 
	        	    	'alias' => $field,
	        	    	'rel' => DaoMap::MANY_TO_ONE);
	    $this->assertEquals(array('type' => 'int', 'size' => 10,'unsigned' => true,'nullable' => $nullable,'default' => $defaultId, 'class' => $class, 'alias' => $alias,'rel' => DaoMap::MANY_TO_MANY), DaoMap::$map[strtolower($class)][$field]);
	}
	
	
}

?>