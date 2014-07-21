<?php
/**
 * Header template
 *
 * @package    Web
 * @subpackage Layout
 * @author     lhe
 */
class Header extends TTemplateControl
{
	/**
	 * (non-PHPdoc)
	 * @see TPage::render()
	 */
	public function onLoad($param)
	{
		parent::onLoad($param);
		$cScripts = FrontEndPageAbstract::getLastestJS(get_class($this));
		if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
			$this->getPage()->getClientScript()->registerScriptFile('headerJs', $this->publishAsset($lastestJs));
		if (isset($cScripts['css']) && ($lastestCss = trim($cScripts['css'])) !== '')
			$this->getPage()->getClientScript()->registerStyleSheetFile('headerCss', $this->publishAsset($lastestCss));
		$this->getPage()->getClientScript()->registerEndScript('headerEndJs', $this->_getJs());
	}
	
	private function _getJs()
	{
		$js = 'jQuery(".mainmenu .fancyboxmenuitem").fancybox({
				type	    : "iframe",
				fitToView	: true,
				width		: "70%",
				height		: "70%",
				autoSize	: false,
				closeClick	: false,
				openEffect	: "none",
				closeEffect	: "none"
			  });';
		return $js;
	}
}
?>