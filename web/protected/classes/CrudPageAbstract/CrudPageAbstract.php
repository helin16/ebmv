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
	 * @var TCallback
	 */
	protected $_showItemsBtn;
	/**
	 * constructor
	 */
	public function __construct()
	{
	    parent::__construct();
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onInit()
	 */
	public function onInit($param)
	{
		parent::onInit($param);
	
		$this->_showItemsBtn = new TCallback();
		$this->_showItemsBtn->ID = 'showItems';
		$this->_showItemsBtn->OnCallback = 'Page.getItems';
		$this->getControls()->add($this->_showItemsBtn);
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
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= 'if(typeof(PageJs) !== "undefined"){pageJs.setCallbackId("showItems", "' . $this->_showItemsBtn->getUniqueID() . '");}';
		return $js;
	}
	
}
?>