<?php
/**
 * Test case for Dao - Dao
 *
 * @package    Test
 * @subpackage Core
 * @since      2012-09-01
 * @author     lhe<helin16@gmail.com>
 *
 */
class DaoUnitTest extends CoreDaoUnitTestAbstract
{
    protected $_focus = 'Transaction';
    /**
     * testing the Dao::connect() function
     */
    public function testConnect()
    {
        Dao::connect();
        Dao::beginTransaction();
        Dao::commitTransaction();
    }
    /**
     * testing the Dao::countByCriteria() function
     */
    public function testCountByCriteria()
    {
        $result = Dao::countByCriteria(new DaoQuery($this->_focus), 'id = ?', array(1));
        $this->assertEquals(1, $result, 'We should get ONE result, but we got:' . $result);
    }
    /**
     * testing the Dao::findAll() function
     */
    public function testFindAll()
    {
        $totalRows = Dao::countByCriteria(new DaoQuery($this->_focus), 'active = ?', array(1));
        $result = Dao::findAll(new DaoQuery($this->_focus), 1, 1, array('id' => 'asc'), Dao::AS_OBJECTS);
        $this->assertEquals(1, count($result), 'We should get ONE result, but we got:' . count($result));
        $this->assertTrue($result[0] instanceof $this->_focus);
        
        $expected = array('totalPages' => $totalRows, 'totalRows' => $totalRows, 'pageNumber' => 1, 'pageSize' => 1);
        $this->assertEquals($expected , Dao::getPageStats());
        
        $result = Dao::findAll(new DaoQuery($this->_focus));
        $expected = array('totalPages' => 1, 'totalRows' => count($result), 'pageNumber' => null, 'pageSize' => count($result));
        $this->assertEquals($expected , Dao::getPageStats());
    }
    /**
     * testing the Dao::findById() function
     */
    public function testFindById()
    {
        $result = Dao::findById(new DaoQuery($this->_focus), 1, Dao::AS_OBJECTS);
        $this->assertEquals('1', $result->getId());
        $this->assertTrue($result instanceof $this->_focus);
        
        $result = Dao::findById(new DaoQuery($this->_focus), 0, Dao::AS_OBJECTS);
        $this->assertTrue(!$result instanceof $this->_focus);
        
        $result = Dao::findById(new DaoQuery($this->_focus), 1, Dao::AS_ARRAY);
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) > 0);
        $this->assertEquals('1', $result[0]);
        
        $result = Dao::findById(new DaoQuery($this->_focus), 1, Dao::AS_ASSOC);
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) > 0);
        $this->assertEquals('1', $result['id']);
        
        $result = Dao::findById(new DaoQuery($this->_focus), 1, Dao::AS_XML);
        $this->assertInstanceOf(SimpleXMLElement, $result);
    }
    /**
     * testing the Dao::findByCriteria() function
     */
    public function testFindByCriteria()
    {
        $result = Dao::findByCriteria(new DaoQuery($this->_focus), 'id = ?', array(1), 1,  1, array('id' => 'asc'), Dao::AS_OBJECTS);
        $this->assertEquals(1, count($result), 'We should get ONE result, but we got:' . count($result));
        $this->assertTrue($result[0] instanceof $this->_focus);
        $this->assertEquals('1', $result[0]->getId());
    }
    /**
     * testing the Dao::findByCriteria() fail by output format function
     * @expectedException DaoException
     */
    public function testFindByCriteriaFail()
    {
        Dao::findByCriteria(new DaoQuery($this->_focus), 'id = ?', array(1), 1,  1, array('id' => 'asc'), 12312);
    }
    /**
     * testing the Dao::beginTransaction(), Dao::rollbackTransaction() and Dao::save() function
     */
    public function testSave()
    {
        Dao::beginTransaction();
        $entity = Dao::findById(new DaoQuery($this->_focus), 1, Dao::AS_OBJECTS);
        Dao::save($entity);
        
        $entity->setId(null);
        Dao::save($entity);
        Dao::rollbackTransaction();
    }
    /**
     * testing the Dao::getSingleResultNative() and Dao::getResultsNative() function
     */
    public function testGetResultNative()
    {
        $results = Dao::getResultsNative("select id from " . strtolower($this->_focus) . ' order by id asc limit 1');
        $this->assertTrue(is_array($results));
        $this->assertTrue(count($results) === 1);
        $this->assertEquals('1', $results[0]['id']);
        
        try
        {
            $sql = 'select id from order by id asc limit 1';
            Dao::getResultsNative($sql);
        }
        catch(Exception $ex)
        {
            $this->assertInstanceOf(DaoException, $ex);
        }
        
        $results = Dao::getSingleResultNative("select id from " . strtolower($this->_focus) . ' order by id asc');
        $this->assertTrue(is_array($results));
        $this->assertTrue(count($results) === 1);
        $this->assertEquals('1', $results['id']);
        
    }
}
?>