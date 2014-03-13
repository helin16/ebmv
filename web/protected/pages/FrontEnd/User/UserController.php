<?php
/**
 * This is the user details page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class UserController extends FrontEndPageAbstract
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if(!Core::getUser() instanceof UserAccount)
			$this->Response->redirect('/login.html');
	}
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= 'pageJs.resultDivId = "resultdiv";';
		$js .= 'pageJs.borrowStatusId = "' . ProductShelfItem::ITEM_STATUS_BORROWED .'";';
		$js .= 'pageJs.setCallbackId("getProducts", "' . $this->getProductsBtn->getUniqueID() . '")';
			$js .= '.setCallbackId("borrowItem", "' . $this->borrowItemBtn->getUniqueID() . '")';
			$js .= '.setCallbackId("returnItem", "' . $this->returnItemBtn->getUniqueID() . '")';
			$js .= '.setCallbackId("removeProduct", "' . $this->removeFromShelfBtn->getUniqueID() . '");';
		$js .= '$$(".leftmenu.singlelevel .menulist .menuitem .menulink").first().click();';
		return $js;
	}
	/**
	 * Logging out from system
	 * 
	 * @param unknown $sender
	 * @param unknown $params
	 */
	public function logout($sender, $params)
	{
		$auth = $this->getApplication()->Modules['auth'];
		$auth->logout();
		$this->Response->Redirect("/");
	}
	/**
	 * Getting the bookshelf items
	 * 
	 * @param unknown $sender
	 * @param unknown $params
	 */
	public function getProducts($sender, $params)
	{
		$errors = $result = array();
		try
		{
			$pageNo = 1;
			$pageSize = DaoQuery::DEFAUTL_PAGE_SIZE;
			 
			if(isset($params->CallbackParameter->pagination))
			{
				$pageNo = trim(isset($params->CallbackParameter->pagination->pageNo) ? $params->CallbackParameter->pagination->pageNo : $pageNo);
				$pageSize = trim(isset($params->CallbackParameter->pagination->pageSize) ? $params->CallbackParameter->pagination->pageSize : $pageSize);
			}
			$items = BaseServiceAbastract::getInstance('ProductShelfItem')
				->cleanUpShelfItems(Core::getUser())
				->getShelfItems(Core::getUser(), null, $pageNo, $pageSize, array('psitem.updated' => 'desc'));
			$result['pagination'] = BaseServiceAbastract::getInstance('ProductShelfItem')->getPageStats();
			$result['items'] = array();
			foreach($items as $item)
			{
				$result['items'][] = $item->getJson();
			}
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage() . $ex->getTraceAsString();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	/**
	 * removing items from bookshelf
	 * 
	 * @param unknown $sender
	 * @param unknown $params
	 * @throws Exception
	 */
	public function removeFromShelf($sender, $params)
	{
		$errors = $result = array();
		try
		{
			if(!isset($params->CallbackParameter->itemId) || !($item = BaseServiceAbastract::getInstance('ProductShelfItem')->get(trim($params->CallbackParameter->itemId))) instanceof ProductShelfItem)
				throw new Exception("System Error: invalid shelfitem!");
			BaseServiceAbastract::getInstance('ProductShelfItem')->removeItem(Core::getUser(), $item->getProduct(), Core::getLibrary());
			$result['item'] = $item->getJson();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage() . $ex->getTraceAsString();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	/**
	 * return Item
	 * 
	 * @param unknown $sender
	 * @param unknown $params
	 * 
	 * @throws Exception
	 */
	public function returnItem($sender, $params)
	{
		$errors = $result = array();
		try
		{
			if(!isset($params->CallbackParameter->itemId) || !($item = BaseServiceAbastract::getInstance('ProductShelfItem')->get(trim($params->CallbackParameter->itemId))) instanceof ProductShelfItem)
				throw new Exception("System Error: invalid shelfitem!");
			$item = BaseServiceAbastract::getInstance('ProductShelfItem')
				->returnItem(Core::getUser(), $item->getProduct(), Core::getLibrary())
				->get($item->getId());
			$result['item'] = $item->getJson();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage() . $ex->getTraceAsString();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	/**
	 * return Item
	 * 
	 * @param unknown $sender
	 * @param unknown $params
	 * 
	 * @throws Exception
	 */
	public function borrowItem($sender, $params)
	{
		$errors = $result = array();
		try
		{
			if(!isset($params->CallbackParameter->itemId) || !($item = BaseServiceAbastract::getInstance('ProductShelfItem')->get(trim($params->CallbackParameter->itemId))) instanceof ProductShelfItem)
				throw new Exception("System Error: invalid shelfitem!");
			$item = BaseServiceAbastract::getInstance('ProductShelfItem')
				->borrowItem(Core::getUser(), $item->getProduct(), Core::getLibrary())
				->resetQuery()
				->get($item->getId());
			$result['item'] = $item->getJson();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage() . $ex->getTraceAsString();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
}