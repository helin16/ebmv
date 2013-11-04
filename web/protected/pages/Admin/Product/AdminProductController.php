<?php
class AdminProductController extends CrudPageAbstract
{
	public function __construct()
    {
    	parent::__construct();
    }
    
    public function onPreInit($param)
    {
    	parent::onPreInit($param);	
    }
    
    public function onInit($param)
    {
    	parent::onInit($param);
    }
    
    public function onLoad($param)
    {
    	parent::onLoad($param);
    }
	
	protected function _getEndJs()
    {
        $pageNumber = 1;
        $pageSize = 10;
        $productId = 0;
    	
    	$js = parent::_getEndJs();
        $js .= 'pageJs.setCallbackId("showItems", "' . $this->showProductsBtn->getUniqueID() . '");';
        $js .= 'pageJs.resultDivId="allProductDiv";';
        $js .= 'pageJs.showItems(' . $pageNumber . ', ' . $pageSize . ', ' . $productId . ');';
        return $js;
    }
    
    public function showProducts($sender, $param)
    {
    	$result = $errors = $productArray = array();
    	try 
    	{
    		$pageNumber = 1;
    		$pageSize = DaoQuery::DEFAUTL_PAGE_SIZE;
    		if(isset($param->CallbackParameter->pagination))
    		{
    			$pagination = $param->CallbackParameter->pagination;
    			$pageNumber = (isset($pagination->pageNo) && trim($pagination->pageNo) !== '' && is_numeric($pagination->pageNo)) ? trim($pagination->pageNo) : $pageNumber;
		    	$pageSize = (isset($pagination->pageSize) && trim($pagination->pageSize) !== '' && is_numeric($pagination->pageSize)) ? trim($pagination->pageSize) : $pageSize;
    		}
	    	$productId = (isset($param->CallbackParameter->itemId) && trim($param->CallbackParameter->itemId) !== '' && is_numeric($param->CallbackParameter->itemId)) ? trim($param->CallbackParameter->itemId) : '0';
	    	
	    	if($productId === '' || $productId === '0')
	    	{
	    		$productArray = BaseServiceAbastract::getInstance('Product')->findAll(false, $pageNumber, $pageSize, array());
	    		$result['pagination'] = BaseServiceAbastract::getInstance('Product')->getPageStats();
	    	}
	    	else
	    	{
	    		$productArray[] = BaseServiceAbastract::getInstance('Product')->get($productId);
	    		$result['pagination'] = BaseServiceAbastract::getInstance('Product')->getPageStats();
	    	}
	    	
	    	foreach($productArray as $product)
	    		$result['items'][] = $product->getJson();
    	}
    	catch(Exception $ex)
    	{
    		$errors[] = $ex->getMessage() . $ex->getTraceAsString();
    	}
    	
    	$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
    }
}
?>