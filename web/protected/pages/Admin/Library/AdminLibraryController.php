<?php
class AdminLibraryController extends CrudPageAbstract
{
	/**
	 * The selected Menu Item name
	 *
	 * @var string
	 */
	public $menuItemCode = 'libraries';
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
        $js .= 'pageJs.resultDivId="alllibraryDiv";';
        $js .= 'pageJs.types = ' . json_encode($this->_getInfoTypes()) . ';';
        $js .= 'pageJs.showItems(' . $pageNumber . ', ' . $pageSize . ', ' . $supplierId . ');';
        return $js;
    }
    /**
     * Getting the SupplierInfoTypes
     * 
     * @return multitype:NULL
     */	
    private function _getInfoTypes()
    {
        $array = array();
        foreach(BaseServiceAbastract::getInstance('LibraryInfoType')->findAll() as $type)
        	$array[] = $type->getJson();
        return $array;
    }
    /**
     * (non-PHPdoc)
     * @see CrudPageAbstract::getItems()
     */	
    public function getItems($sender, $param)
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
    			$supplierArray = BaseServiceAbastract::getInstance('Library')->findAll(false, $pageNumber, $pageSize, array());
    			$result['pagination'] = BaseServiceAbastract::getInstance('Library')->getPageStats();
    		}
    		else
    		{
    			$supplierArray[] = BaseServiceAbastract::getInstance('Library')->get($supplierId);
    			$result['pagination'] = BaseServiceAbastract::getInstance('Library')->getPageStats();
    		}
    		$items = array();
    		foreach($supplierArray as $supplier)
    			$items[] = $supplier->getJson();
    		$result['items'] = $items;
    	}
    	catch(Exception $ex)
    	{
    		$errors[] = $ex->getMessage();
    	}
    	$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
    }
	/**
	 * (non-PHPdoc)
	 * @see CrudPageAbstract::saveItems()
	 */
	public function saveItems($sender, $param)
    {
    	$result = $errors = $supplierArray = array();
    	try
    	{
    		Dao::beginTransaction();
    		if(!isset($param->CallbackParameter->id))
    			throw new Exception("System Error: No item id passed in!");
    		
    		$item = ($item = BaseServiceAbastract::getInstance('Library')->get(trim($param->CallbackParameter->id))) instanceof Library ? $item : new Library();
    		$item->setName(trim($param->CallbackParameter->name));
    		$item->setActive(strtolower(trim($param->CallbackParameter->active)) === 'on');
    		BaseServiceAbastract::getInstance('Library')->save($supplier);
    		foreach($param->CallbackParameter->info as $info)
    		{
    			$info = (($info = BaseServiceAbastract::getInstance('LibraryInfo')->get(trim($info->id))) instanceof LibraryInfo ? $item : new LibraryInfo());
    			$info->setType(BaseServiceAbastract::getInstance('LibraryInfoType')->get(trim($info->typeId)));
    			$info->setValue(trim($info->value));
    			$info->setLibrary($item);
    			BaseServiceAbastract::getInstance('LibraryInfo')->save($info);
    		}
    		
    		Dao::commitTransaction();
    		$result['items'] = array($info->getJson());
    	}
    	catch(Exception $ex)
    	{
    		Dao::rollbackTransaction();
    		$errors[] = $ex->getMessage() ;
    	}
    	$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
    }
	/**
	 * (non-PHPdoc)
	 * @see CrudPageAbstract::saveItems()
	 */
	public function delItems($sender, $param)
    {
    	$result = $errors = $supplierArray = array();
    	try
    	{
    		if(!isset($param->CallbackParameter->itemIds))
    			throw new Exception("System Error: No item ids passed in!");
    		$itemIds = $param->CallbackParameter->itemIds;
    		BaseServiceAbastract::getInstance('Library')->updateByCriteria('active = 0', 'id in (' . implode(', ', array_fill(0, count($itemIds), '?')). ')', $itemIds);
    	}
    	catch(Exception $ex)
    	{
    		$errors[] = $ex->getMessage();
    	}
    	$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
    }
}
?>