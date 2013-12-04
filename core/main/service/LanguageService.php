<?php
/**
 * LanguageService
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class LanguageService extends BaseServiceAbastract
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("Language");
    }
	/**
     * Getting the language by code
     * 
     * @param string $code The code we are searching on
     * 
     * @return NULL|Language
     */
    public function getLangByCode($code)
    {
        $langs = $this->getLangsByCodes(array($code), true, 1, 1);
        return count($langs) > 0 ? $langs[0] : null;
    }
	/**
     * Getting the language by code
     * 
     * @param array $codes The codes we are searching on
     * 
     * @return multiple:Language
     */
    public function getLangsByCodes(array $codes, $searchActiveOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE)
    {
    	if(count($codes) === 0)
    		return array();
    	EntityDao::getInstance('Language')->getQuery()->eagerLoad('Language.codes');
    	return $this->findByCriteria('lcode.code in (' . implode(', ', array_fill(0, count($codes), '?')) . ')', $codes, $searchActiveOnly, $pageNo, $pageSize);
    }
}
?>
