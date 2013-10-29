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
        $pageSize = 12;
        $productId = 0;
    	
    	$js = parent::_getEndJs();
        $js .= 'pageJs.setCallbackId("showProduct", "' . $this->showProductsBtn->getUniqueID() . '");';
        $js .= 'pageJs.setCallbackId("pageNumber", '.$pageNumber.');';
        $js .= 'pageJs.setCallbackId("pageSize", '.$pageSize.');';
        $js .= 'pageJs.setCallbackId("productId", '.$productId.');';
        $js .= 'pageJs.showProducts();';
        return $js;
    }
    
    public function showProducts($sender, $param)
    {
    	$result = $errors = $productArray = array();
    	try 
    	{
	    	$pageNumber = (isset($param->CallbackParameter->pageNumber) && trim($param->CallbackParameter->pageNumber) !== '' && is_numeric($param->CallbackParameter->pageNumber)) ? trim($param->CallbackParameter->pageNumber) : null;
	    	$pageSize = (isset($param->CallbackParameter->pageSize) && trim($param->CallbackParameter->pageSize) !== '' && is_numeric($param->CallbackParameter->pageSize)) ? trim($param->CallbackParameter->pageSize) : 30;
	    	$productId = (isset($param->CallbackParameter->productId) && trim($param->CallbackParameter->productId) !== '' && is_numeric($param->CallbackParameter->productId)) ? trim($param->CallbackParameter->productId) : '0';
	    	
	    	if($productId === '' || $productId === '0')
	    		$productArray = BaseServiceAbastract::getInstance('Product')->findAll(false, $pageNumber, $pageSize, array());
	    	else
	    	{
	    		
	    	}
	    	
	    	foreach($productArray as $product)
	    		$result['products'][] = $product->getJson();
    	}
    	catch(Exception $ex)
    	{
    		$errors[] = $ex->getMessage() . $ex->getTraceAsString();
    	}
    	
    	$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
    }
}
?>