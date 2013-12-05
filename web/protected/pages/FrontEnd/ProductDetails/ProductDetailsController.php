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
		$js .= 'pageJs.setCallbackId("geturl", "' . $this->getUrlBtn->getUniqueID(). '");';
		return $js;
	}
	
	public function getProductDetails()
	{
	    if(!$this->_product instanceof Product)
	        return 'No Product Found!';
	    list($uid, $pwd) = $this->_getUserInfo();
	    $product = $this->_product;
	    $html = "<div class='wrapper'>";
    	    $html .= "<div class='product listitem'>";
        	    $html .= "<span class='inlineblock listcol left'>";
        	        if(($thumb = trim($product->getAttribute('image_thumb'))) === '')
            	        $html .= "<div class='product_image noimage'></div>";
        	        else
            	        $html .= "<div class='product_image'><img  src='/asset/get?id=" . $thumb . "' /></div>";
        	    $html .= "</span>";
        	    $html .= "<span class='inlineblock listcol right'>";
            	    $html .= "<div class='product_title'>" . $product->getTitle() . "</div>";
            	    $html .= "<div class='row'>";
            	        $html .= $this->_getAtts($product, 'author', 'Author', 'author');
                	    $html .= $this->_getAtts($product, 'isbn', 'ISBN', 'product_isbn');
            	    $html .= "</div>";
            	    $html .= "<div class='row'>";
            	        $html .= $this->_getAtts($product, 'publisher', 'Publisher', 'product_publisher');
                	    $html .= $this->_getAtts($product, 'publish_date', 'Publisher Date', 'product_publish_date');
            	    $html .= "</div>";
            	    $html .= "<div class='row'>";
            	        $html .= $this->_getAtts($product, 'no_of_words', 'Length', 'product_no_of_words');
            	    	$langs = array_map(create_function('$a', 'return $a->getName();'), $this->_product->getLanguages());
            	        $html .= $this->_getAtts($product, 'languages', 'Languages', 'product_languages', implode(', ', $langs));
            	    $html .= "</div>";
            	    $html .= "<div class='row'>";
            	    	$availCopies = $totalCopies = 0;
            	    	if (($libOwn = $this->_product->getLibraryOwn(Core::getLibrary())) instanceof LibraryOwns)
            	    	{
            	    		$availCopies = $libOwn->getAvailCopies();
            	    		$totalCopies = $libOwn->getTotalCopies();
            	    	}
            	        $html .= $this->_getAtts($product, 'avail_copies', 'Available Copies', 'product_avail_copies', $availCopies);
                	    $html .= $this->_getAtts($product, 'total_copies', 'Total Copies', 'product_total_copies', $totalCopies);
            	    $html .= "</div>";
            	    $html .= "<div class='row btns'>";
            	    	if($availCopies <= 0)
            	    	{
                	    	$html .= '<input class="button rdcrnr" type="button" disabled value="No copy available");"/>';
            	    	}
            	    	else
            	    	{
		            	    $viewUrl = $downloadUrl = "";
		            	    if($this->_product->getSupplier() instanceof Supplier)
		            	    {
		            	    	$viewUrl = trim($this->_product->getSupplier()->getInfo('view_url'));
		            	    	$downloadUrl = trim($this->_product->getSupplier()->getInfo('download_url'));
		            	    }
		            	    $siteId = Core::getLibrary()->getInfo('aus_code');
		            	    if(trim($viewUrl) !== '')
	                	    	$html .= '<input class="button rdcrnr" type="button" value="在线阅读/在線閱讀&#x00A;Read Online" onClick="pageJs.readOnline(this);"/>';
		            	    if(trim($downloadUrl) !== '')
		                	    $html .= ' <input class="button rdcrnr" type="button" value="下载阅读/下載閱讀&#x00A;Download This Book" onClick="pageJs.download(this);"/>';
            	    	}
            	    $html .= "</div>";
            	    $html .= "<div class='row product_description'>";
                    	    $html .= $product->getAttribute('description');
            	    $html .= "</div>";
        	    $html .= "</span>";
    	    $html .= "</div>";
	    $html .= "</div>";
	    return $html;
	}
	
	private function _getAtts(Product $product, $attrcode, $title, $className = '', $overRideContent = '')
	{
	    $html = "<span class='inlineblock $className'>";
    	    $html .="<label>$title: </label>";
    	    $html .="<span>" . (trim($overRideContent) === '' ? $product->getAttribute($attrcode) : $overRideContent) . "</span>";
	    $html .= "</span>";
	    return $html;
	}
	
	public function getUrl($sender, $params)
	{
		list($uid, $pwd) = $this->_getUserInfo();
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
	
	/**
	 * Getting the userinformation of the current user
	 * 
	 * @return multitype:number Ambigous <number, string>
	 */
	private function _getUserInfo()
	{
		$uid = 0;
		$pwd = 0;
		if (($user = Core::getUser()) instanceof UserAccount)
		{
			$uid = $user->getUserName();
			$pwd = $user->getPassword();
		}
		return array($uid, $pwd);
	}
}
?>