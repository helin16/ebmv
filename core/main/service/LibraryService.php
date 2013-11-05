<?php
/**
 * LibraryService
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class LibraryService extends BaseServiceAbastract
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("Library");
    }
    /**
     * Getting the librarys from the code
     * 
     * @param string $code
     * @param bool   $searchActiveOnly
     * @param int    $pageNo
     * @param int    $pageSize
     * @param array  $orderBy
     * 
     * @return Ambigous <Ambigous, multitype:, multitype:BaseEntityAbstract >
     */
    public function getLibsFromCode($code, $searchActiveOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
    {
    	$query = EntityDao::getInstance('Library')->getQuery();
    	$query->eagerLoad('Library.infos', DaoQuery::DEFAULT_JOIN_TYPE, 'lib_info')->eagerLoad('LibraryInfo.type', DaoQuery::DEFAULT_JOIN_TYPE, 'lib_info_type');
    	return $this->findByCriteria('lib_info_type.code = ? and lib_info.value = ?', array('aus_code', $code), $searchActiveOnly, $pageNo, $pageSize, $orderBy);
    }
   /**
    * Getting the libraray from the code
    * 
    * @param string $code
    * 
    * @return Library|null
    */
    public function getLibFromCode($code)
    {
    	$result = $this->getLibsFromCode($code, true, 1, 1);
    	return (count($result) === 0 ? null : $result[0]);
    }
}
?>
