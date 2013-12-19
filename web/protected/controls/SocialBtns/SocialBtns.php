<?php
class SocialBtns extends TClientScript
{
	private $_btnsHolderId;
	
	/**
	 * Getter for the _btnsHolderId
	 */
	public function getBtnHolderId() 
	{
	    return $this->_btnsHolderId;
	}
	/**
	 * Setter for the _btnsHolderId
	 * 
	 * @param string $value The _btnsHolderId
	 * 
	 * @return SocialBtns
	 */
	public function setBtnHolderId($value) 
	{
	    $this->_btnsHolderId = $value;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		$page = $this->getPage();
		if(!$page->IsPostBack || !$page->IsCallback)
		{
			$page->getClientScript()->registerHeadScriptFile('socialbtns_addthis', "https://s7.addthis.com/js/300/addthis_widget.js#pubid=helin16");
			$page->getClientScript()->registerScriptFile('socialbtns_js', $this->publishAsset(get_class($this) . '.js'));
			$page->getClientScript()->registerBeginScript('socialbtns_js_ini', 'var socialBtnJs = new SocialBtnsJs();');
			if(trim($this->_btnsHolderId) !== '')
				$page->getClientScript()->registerEndScript('socialbtns_js_load', 'socialBtnJs.load(" . trim($this->_btnsHolderId) . ");');
		}
	}
}