<?php
require_once dirname(__FILE__) . '/../AdminPageAbstract/AdminPageAbstract.php';
/**
 * The CRUD Page Abstract
 * 
 * @package    Web
 * @subpackage Class
 * @author     mrahman<murahman2008@gmail.com>
 */
abstract class CrudPageAbstract extends AdminPageAbstract 
{
	/**
	 * constructor
	 */
	public function __construct()
	{
	    parent::__construct();
	}
	/**
	 * (non-PHPdoc)
	 * @see FrontEndPageAbstract::_loadPageJsClass()
	 */
	protected function _loadPageJsClass()
	{
	    parent::_loadPageJsClass();
	    $this->getPage()->getClientScript()->registerScriptFile('crudPageJs', Prado::getApplication()->getAssetManager()->publishFilePath(dirname(__FILE__) . '/' . __CLASS__ . '.js', true));
	    return $this;
	}
	
}
?>