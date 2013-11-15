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
        $langs = $this->findByCriteria('code = ?', array($code), true, 1, 1);
        return count($langs) > 0 ? $langs[0] : null;
    }
}
?>
