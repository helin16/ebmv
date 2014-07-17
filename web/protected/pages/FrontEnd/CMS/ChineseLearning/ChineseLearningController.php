<?php
/**
 * This is the AboutUs page
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class ChineseLearningController extends FrontEndPageAbstract
{
    /**
     * (non-PHPdoc)
     * @see FrontEndPageAbstract::_getEndJs()
     */
    protected function _getEndJs()
    {
        $js = parent::_getEndJs();
        return $js;
    }
//     protected function onLoad($param)
//     {
//     	parent::onLoad($param);
//     	//$this->title->text = 'test';
//     }
}