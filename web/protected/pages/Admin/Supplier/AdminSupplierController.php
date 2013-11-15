<?php
class AdminSupplierController extends CrudPageAbstract
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
        $supplierId = 0;
    	
    	$js = parent::_getEndJs();
        $js .= 'pageJs.resultDivId="allSupplierDiv";';
        $js .= 'pageJs.showItems(' . $pageNumber . ', ' . $pageSize . ', ' . $supplierId . ');';
        return $js;
    }
    
    public function showSuppliers($sender, $param)
    {
    		$result = $errors = $supplierArray = array();
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
    			$supplierId = (isset($param->CallbackParameter->itemId) && trim($param->CallbackParameter->itemId) !== '' && is_numeric($param->CallbackParameter->itemId)) ? trim($param->CallbackParameter->itemId) : '0';
    	
    			if($supplierId === '' || $supplierId === '0')
    			{
    				$supplierArray = BaseServiceAbastract::getInstance('Supplier')->findAll(false, $pageNumber, $pageSize, array());
    				$result['pagination'] = BaseServiceAbastract::getInstance('Supplier')->getPageStats();
    			}
    			else
    			{
    				$supplierArray[] = BaseServiceAbastract::getInstance('Supplier')->get($supplierId);
    				$result['pagination'] = BaseServiceAbastract::getInstance('Supplier')->getPageStats();
    			}
    			foreach($supplierArray as $supplier)
    				$result['items'][] = $supplier->getJson();
    				
    		}
    		catch(Exception $ex)
    		{
    			$errors[] = $ex->getMessage() . $ex->getTraceAsString();
    		}
    		 
    		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
    	}
    	}
    	?>