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
        $libraryId = 0;
    	
    	$js = parent::_getEndJs();
        $js .= 'pageJs.resultDivId="alllibraryDiv";';
        $js .= 'pageJs.types = ' . json_encode($this->_getInfoTypes()) . ';';
        $js .= 'pageJs.showItems(' . $pageNumber . ', ' . $pageSize . ', ' . $libraryId . ');';
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
    	$result = $errors = $libraries = array();
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
    		$libraryId = (isset($param->CallbackParameter->itemId) && trim($param->CallbackParameter->itemId) !== '' && is_numeric($param->CallbackParameter->itemId)) ? trim($param->CallbackParameter->itemId) : '0';
    		if($libraryId === '' || $libraryId === '0')
    		{
    			$libraries = BaseServiceAbastract::getInstance('Library')->findAll(false, $pageNumber, $pageSize, array());
    			$result['pagination'] = BaseServiceAbastract::getInstance('Library')->getPageStats();
    		}
    		else
    		{
    			$libraries[] = BaseServiceAbastract::getInstance('Library')->get($libraryId);
    			$result['pagination'] = BaseServiceAbastract::getInstance('Library')->getPageStats();
    		}
    		$items = array();
    		foreach($libraries as $library)
    			$items[] = $library->getJson();
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
    	$result = $errors = array();
    	try
    	{
    		Dao::beginTransaction();
    		if(!isset($param->CallbackParameter->id))
    			throw new Exception("System Error: No item id passed in!");
    		
    		$item = ($item = BaseServiceAbastract::getInstance('Library')->get(trim($param->CallbackParameter->id))) instanceof Library ? $item : new Library();
    		$item->setName(trim($param->CallbackParameter->name));
    		try
    		{
    			if(!class_exists($connector = trim($param->CallbackParameter->connector)))
    				throw new Exception($connector . " does NOT exsit!");
    		}
    		catch (Exception $e)
    		{
    			throw new Exception("Connector Script: " . $connector . " does NOT exsit!" . $e->getMessage());
    		}
    		$item->setConnector($connector);
    		$item->setActive(strtolower(trim($param->CallbackParameter->active)) === 'on');
    		BaseServiceAbastract::getInstance('Library')->save($item);
    		foreach($param->CallbackParameter->info as $info)
    		{
    			$infoItem = (($infoItem = BaseServiceAbastract::getInstance('LibraryInfo')->get(trim($info->id))) instanceof LibraryInfo ? $infoItem : new LibraryInfo());
    			$infoItem->setType(BaseServiceAbastract::getInstance('LibraryInfoType')->get(trim($info->typeId)));
    			$infoItem->setValue(trim($info->value));
    			$infoItem->setLibrary($item);
    			$infoItem->setActive(trim($info->active) === '1');
    			BaseServiceAbastract::getInstance('LibraryInfo')->save($infoItem);
    		}
    		
    		Dao::commitTransaction();
    		$result['items'] = array($item->getJson());
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
    	$result = $errors = array();
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