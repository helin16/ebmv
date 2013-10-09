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
	        $this->Response->redirect("/admin/login.html");
	}
}
?>