<?php
/**
 * This is the loginpage
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class HomeController extends FrontEndPageAbstract  
{
    /**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param)
	{
	    if(!$this->IsPostBack || !$this->IsCallback)
	    {
	    }
	}
}
?>