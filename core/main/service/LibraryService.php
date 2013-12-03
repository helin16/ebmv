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
    	return $this->_getLibsFromInfo(array($code), 'aus_code', $searchActiveOnly, $pageNo, $pageSize, $orderBy);
    }
    /**
     * Getting the librarys from the codes
     * 
     * @param array  $codes
     * @param bool   $searchActiveOnly
     * @param int    $pageNo
     * @param int    $pageSize
     * @param array  $orderBy
     * 
     * @return Ambigous <multiple:Library, Ambigous, multitype:, multitype:BaseEntityAbstract >
     */
    public function getLibsFromCodes(array $codes, $searchActiveOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
    {
    	return $this->_getLibsFromInfo($codes, 'aus_code', $searchActiveOnly, $pageNo, $pageSize, $orderBy);
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
    /**
     * Getting the Libraries for the Li
     * @param array  $infoValues       The value we are searching for
     * @param int    $typeCode         The code of the type for the information
     * @param bool   $searchActiveOnly Whether active only
     * @param int    $pageNo           page number
     * @param int    $pageSize         Page Size
     * @param array  $orderBy          Order by what
     * 
     * @return multiple:Library
     */
    private function _getLibsFromInfo($infoValues, $typeCode, $searchActiveOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
    {
    	$query = EntityDao::getInstance('Library')->getQuery();
    	$query->eagerLoad('Library.infos', DaoQuery::DEFAULT_JOIN_TYPE, 'lib_info')->eagerLoad('LibraryInfo.type', DaoQuery::DEFAULT_JOIN_TYPE, 'lib_info_type');
    	$params = array_merge(array($typeCode), $infoValues);
    	return $this->findByCriteria('lib_info_type.code = ? and lib_info.value in (' . implode(', ', array_fill(0, count($infoValues), '?')) . ')', $params, $searchActiveOnly, $pageNo, $pageSize, $orderBy);
    }
    /**
     * Getting the library by the url
     * 
     * @param string $url The url of the library
     * 
     * @return multiple:Library
     */
    public function getLibByURL($url)
    {
    	$result = $this->_getLibsFromInfo(array($url), 'lib_url', true, 1, 1);
    	return (count($result) === 0 ? null : $result[0]);
    }
}
?>
