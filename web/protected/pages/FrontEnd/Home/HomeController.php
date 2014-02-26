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
    public function getNewRelease($sender, $params)
    {
	    $params->ResponseData = $this->_listProducts($params, 'getNewReleasedProducts');
    }
    public function getNewNewsPaper($sender, $params)
    {
	    $params->ResponseData = $this->_listProducts($params, 'getNewReleasedProducts', null, BaseServiceAbastract::getInstance('ProductType')->get(ProductType::ID_NEWSPAPER));
    }
    public function getNewMagazine($sender, $params)
    {
	    $params->ResponseData = $this->_listProducts($params, 'getNewReleasedProducts', null, BaseServiceAbastract::getInstance('ProductType')->get(ProductType::ID_MAGAZINE));
    }
    public function getNewBooks($sender, $params)
    {
	    $params->ResponseData = $this->_listProducts($params, 'getNewReleasedProducts', null, BaseServiceAbastract::getInstance('ProductType')->get(ProductType::ID_BOOK));
    }
    public function getMostPopular($sender, $params)
    {
	    $params->ResponseData = $this->_listProducts($params, 'getMostPopularProducts');
    }
    
    private function _listProducts($params, $funcName, Language $lang = null, ProductType $type = null)
    {
        $errors = $result = array();
        try
        {
            $pageNo = 1;
	        $pageSize = 10;
	        if(isset($params->CallbackParameter->pagination))
	        {
	            $pageNo = trim(isset($params->CallbackParameter->pagination->pageNo) ? $params->CallbackParameter->pagination->pageNo : $pageNo);
	            $pageSize = trim(isset($params->CallbackParameter->pagination->pageSize) ? $params->CallbackParameter->pagination->pageSize : $pageSize);
	        }
	        
            $result['products'] = array();
            $products = BaseServiceAbastract::getInstance('Product')->$funcName(Core::getLibrary(), $pageSize, $lang, $type);
            foreach($products as $product)
            {
                $result['products'][] = $product->getJson();
            }
        }
        catch(Exception $ex)
        {
            $errors = array($ex->getMessage() . $ex->getTraceAsString());
        }
        return StringUtilsAbstract::getJson($result, $errors);
    }
}
?>