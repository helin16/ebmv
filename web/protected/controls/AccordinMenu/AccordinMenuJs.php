<?php
/**
 * The AccordinMenu Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class AccordinMenuJs extends TClientScript
{
    /**
     * (non-PHPdoc)
     * @see TPage::render()
     */
    public function onLoad($param)
    {
        parent::onLoad($param);
        $this->getPage()->getClientScript()->registerScriptFile('accordinJs', $this->publishAsset('ddsmoothmenu.js'));
//         $this->getPage()->getClientScript()->registerStyleSheetFile('accordinCss', $this->publishAsset('ddsmoothmenu.css'));
//         $this->getPage()->getClientScript()->registerStyleSheetFile('accordinCssV', $this->publishAsset('ddsmoothmenu-v.css'));
//         $this->publishAsset('down_over.gif');
//         $this->publishAsset('down.gif');
//         $this->publishAsset('right_over.gif');
//         $this->publishAsset('right.gif');
    }
}