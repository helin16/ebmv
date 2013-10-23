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
	    $params->ResponseData = $this->_listProducts($params);
    }
    public function getMostPopular($sender, $params)
    {
	    $params->ResponseData = $this->_listProducts($params);
    }
    
    private function _listProducts($params)
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
	        
            $result['pagination'] = BaseServiceAbastract::getInstance('Product')->findByCriteria('');
            $result['products'] = array();
            foreach($products as $product)
            {
                $result['products'][] = $product->getJson();
            }
        }
        catch(Exception $ex)
        {
            $errors[] = $ex->getMessage() . $ex->getTraceAsString();
        }
        return StringUtilsAbstract::getJson($errors, $result);
    }
}
?>