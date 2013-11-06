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
		$js .= 'pageJs.setCallbackId("getProducts", "' . $this->getProductsBtn->getUniqueID() . '");';
		$js .= '$$(".leftmenu.singlelevel .menulist .menuitem .menulink").first().click();';
		return $js;
	}
	public function logout($sender, $params)
	{
		$auth = $this->getApplication()->Modules['auth'];
		$auth->logout();
		$this->Response->Redirect("/");
	}
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
			$products = BaseServiceAbastract::getInstance('Product')->getShelfItems(Core::getUser(), null, $pageNo, $pageSize, array('shelf_item.updated' => 'desc'));
			$result['pagination'] = BaseServiceAbastract::getInstance('Product')->getPageStats();
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
		$params->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	
	public function removeFromShelf($sender, $params)
	{
		$errors = $result = array();
		try
		{
			if(!isset($params->CallbackParameter->productid) || !($product = BaseServiceAbastract::getInstance('Product')->get(trim($params->CallbackParameter->productid))) instanceof Product)
				throw new Exception("System Error: invalid product!");
			$result['pagination'] = BaseServiceAbastract::getInstance('Product')->getPageStats();
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
		$params->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
}