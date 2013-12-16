<?php
/**
 * This is the product details page
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class ProductDetailsController extends FrontEndPageAbstract  
{
	/**
	 * @var Product
	 */
	private $_product;
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		if(isset($this->Request['id']))
			$this->_product = BaseServiceAbastract::getInstance('Product')->get($this->Request['id']);
	}
	
	/**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param)
	{
		if(!$this->IsPostBack)
		{
		    if(!$this->_product->getLibraryOwn(Core::getLibrary()) instanceof LibraryOwns)
		    {
		    	FrontEndPageAbstract::show404Page('Product NOT Exsits!', 'Requested book/magazine/newspaper is not viewable for this library!');
		    	die;
		    }
		}
	    parent::onLoad($param);
	}
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= 'pageJs.product = ' . json_encode($this->_product->getJson()) . ';';
		$js .= 'pageJs.resultDivId = "product_details";';
		$js .= 'pageJs.setCallbackId("geturl", "' . $this->getUrlBtn->getUniqueID(). '");';
		$js .= 'pageJs.setCallbackId("getCopies", "' . $this->getCopiesBtn->getUniqueID(). '");';
		$js .= 'pageJs.displayProduct();';
		return $js;
	}
	
	public function getUrl($sender, $params)
	{
		$errors = $results = array();
        try 
        {
        	if(!$this->_product->getSupplier() instanceof Supplier)
        		throw new Exception('System Error: no supplier found for this book!');
        	$type = trim($params->CallbackParameter->type);
        	switch($type)
        	{
        		case 'read':
        			{
        				$method = "getOnlineReadUrl";
        				break;
        			}
        		case 'download':
        			{
        				$method = "getDownloadUrl";
			        	$results['redirecturl'] = '/user.html';
        				break;
        			}
        		default:
        			{
        				throw new Exception("invalid type:" . $type);
        			}
        	}
        	$results['url'] = SupplierConnectorAbstract::getInstance($this->_product->getSupplier(), Core::getLibrary())->$method($this->_product, Core::getUser());
        }
        catch(Exception $ex)
        {
        	$errors[] = $ex->getMessage();
        }
        $params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
	
	public function updateProduct($sender, $params)
	{
		$errors = $results = array();
        try 
        {
        	if(!($supplier = $this->_product->getSupplier()) instanceof Supplier)
        		throw new Exception('System Error: no supplier found for this book!');
        	
        	if(!($user = Core::getUser()) instanceof UserAccount)
        		Core::setUser(BaseServiceAbastract::getInstance('UserAccount')->get(UserAccount::ID_GUEST_ACCOUNT));
        	SupplierConnectorAbstract::getInstance($this->_product->getSupplier(), Core::getLibrary())->updateProduct($this->_product);
        	$results['urls'] = array('viewUrl' => (trim($supplier->getInfo('view_url')) !== ''), 'downloadUrl' => (trim($supplier->getInfo('download_url')) !== ''));
        	$results['copies'] = ($libOwn = $this->_product->getLibraryOwn(Core::getLibrary())) instanceof LibraryOwns ? $libOwn->getJson() : array();
        }
        catch(Exception $ex)
        {
        	$errors[] = $ex->getMessage();
        }
        $params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>