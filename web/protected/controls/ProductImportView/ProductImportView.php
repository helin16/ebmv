<?php
/**
 * The ProductImportView Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class ProductImportView extends TTemplateControl
{
	/**
	 * (non-PHPdoc)
	 * @see TPage::render()
	 */
	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->getPage()->getClientScript()->registerScriptFile('ProductImportViewJs', $this->publishAsset( get_class($this) . '.js'));
	}
}