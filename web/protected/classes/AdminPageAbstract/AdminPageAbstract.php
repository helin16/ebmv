<?php
require_once dirname(__FILE__) . '/../FrontEndPageAbstract/FrontEndPageAbstract.php';
/**
 * The Admin Page Abstract
 * 
 * @package    Web
 * @subpackage Class
 * @author     lhe<helin16@gmail.com>
 */
abstract class AdminPageAbstract extends TPage 
{
    /**
     * The selected Menu Item name
     * 
     * @var string
     */
	public $menuItemName;
	/**
	 * constructor
	 */
	public function __construct()
	{
	    parent::__construct();
	    if(!Core::getUser() instanceof UserAccount)
	        $this->Response->redirect("/login.html");
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
	    if(!$this->IsPostBack)
	    {}
	}
	/**
	 * (non-PHPdoc)
	 * @see TPage::onPreInit()
	 */
	public function onPreInit($param)
	{
		parent::onPreInit($param);
		$this->getPage()->setMasterClass("Application.layout.default.DefaultLayout");
	}
	/**
	 * (non-PHPdoc)
	 * @see TPage::render()
	 */
	public function onInit($param)
	{
	    parent::onInit($param);
        $cScripts = FrontEndPageAbstract::getLastestJS(get_class($this));
        if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
            $this->getPage()->getClientScript()->registerScriptFile('pageJs', $this->publishAsset($lastestJs));
        if (isset($cScripts['css']) && ($lastestCss = trim($cScripts['css'])) !== '')
            $this->getPage()->getClientScript()->registerStyleSheetFile('pageCss', $this->publishAsset($lastestCss));
	}
}
?>