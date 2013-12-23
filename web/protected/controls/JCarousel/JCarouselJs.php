<?php
/**
 * The JCarouselJs Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class JCarouselJs extends TClientScript
{
    /**
     * (non-PHPdoc)
     * @see TPage::render()
     */
    public function onLoad($param)
    {
        parent::onLoad($param);
        $skin = 'skins/tango/';
        $this->getPage()->getClientScript()->registerScriptFile('JCarouselJs', $this->publishAsset('lib/jquery.jcarousel.min.js'));
        $this->getPage()->getClientScript()->registerStyleSheetFile('JCarouselCss', $this->publishAsset($skin . 'skin.css'));
        $this->publishAsset($skin . 'next-horizontal.png');
        $this->publishAsset($skin . 'next-vertical.png');
        $this->publishAsset($skin . 'prev-horizontal.png');
        $this->publishAsset($skin . 'prev-vertical.png');
    }
}