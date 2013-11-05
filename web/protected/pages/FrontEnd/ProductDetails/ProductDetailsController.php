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
	 * The cheapest supplier
	 * @var Supplier
	 */
	private $_supplier;
	
	public function __construct()
	{
		parent::__construct();
		if(isset($this->Request['id']))
		{
			$this->_product = BaseServiceAbastract::getInstance('Product')->get($this->Request['id']);
			$this->_supplier = BaseServiceAbastract::getInstance('Supplier')->getCheapestSupplier($this->_product);
		}
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
		$js .= 'pageJs.setCallbackId("download", "' . $this->getDownloadUrlBtn->getUniqueID(). '");';
		return $js;
	}
	
	public function getProductDetails()
	{
	    if(!$this->_product instanceof Product)
	        return 'No Product Found!';
	    list($uid, $pwd) = $this_getUserInfo();
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
            	    $html .= "<div class='row btns'>";
	            	    $viewUrl = "";
	            	    if($this->_supplier instanceof Supplier)
	            	    	$viewUrl = trim($this->_supplier->getInfo('view_url'));
	            	    $siteId = Config::get('site', 'code');
                	    $html .= '<input type="button" value="Read Online" onClick="pageJs.readOnline('. "'" . $viewUrl . "', $siteId, $uid, $pwd" . ');"/>';
                	    $html .= '<input type="button" value="Download This Book" onClick="pageJs.download(this);"/>';
            	    $html .= "</div>";
            	    $html .= "<div class='row product_description'>";
                    	    $html .= $product->getAttribute('description');
            	    $html .= "</div>";
        	    $html .= "</span>";
    	    $html .= "</div>";
	    $html .= "</div>";
	    return $html;
	}
	
	private function _getAtts(Product $product, $attrcode, $title, $className = '')
	{
	    $html = "<span class='inlineblock $className'>";
    	    $html .="<label>Author: </label>";
    	    $html .="<span>" . $product->getAttribute('author') . "</span>";
	    $html .= "</span>";
	    return $html;
	}
	
	public function getDownloadUrl($sender, $params)
	{
		list($uid, $pwd) = $this_getUserInfo();
		$errors = $results = array();
        try 
        {
        	if(!$this->_supplier instanceof Supplier)
        		throw new Exception('System Error: no supplier found for this book!');
        	$downloadUrl = trim($this->_supplier->getInfo('download_url'));
        	$urlParams = array('SiteID' => Config::get('site', 'code'), 
        			'Isbn' => $this->_product->getAttribute('isbn'),
        			'NO' => $this->_product->getAttribute('cno'),
        			'Format' => 'xml',
        			'Uid' => $uid,
        			'Pwd' => $pwd
        	);
        	$url = $downloadUrl . '?' . http_build_query($urlParams);
        	$result = SupplierConnector::readUrl($url);
        	$xml = new SimpleXMLElement($result);
        	if(trim($xml->Code) !== '0')
        		throw new Exception('Error:' . trim($xml->Value));
        	$results['url'] = trim($xml->Value);
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