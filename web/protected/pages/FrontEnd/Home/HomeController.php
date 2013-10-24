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
    public function getMostPopular($sender, $params)
    {
	    $params->ResponseData = $this->_listProducts($params, 'getMostPopularProducts');
    }
    
    private function _listProducts($params, $funcName)
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
            $products = BaseServiceAbastract::getInstance('Product')->$funcName($pageSize);
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