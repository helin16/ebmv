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
		return $js;
	}
	
	public function getProductDetails()
	{
	    if(!$this->_product instanceof Product)
	        return 'No Product Found!';
	    
	    $url = "http://au.xhestore.com/book/readbook";
	    $siteId = Config::get('site', 'id');
	    $uid = 0;
	    $pwd = 0;
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
                	    $html .= '<input type="button" value="Read Online" onClick="pageJs.readOnline('. "'" . $url . "', $siteId, $uid, $pwd" . ');"/>';
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
}
?>