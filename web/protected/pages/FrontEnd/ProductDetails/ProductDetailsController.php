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
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param)
	{
	    parent::onLoad($param);
		
		if(!$this->IsPostBack || $param == "reload")
		{}
			
// 					$paArray = $product->getAttributes();
// 					foreach($paArray as $pa)
// 					{
// 						if($pa instanceof ProductAttribute)
// 							$this->_productAttributeArray[$pa->getType()->getId()] = $pa;
// 					}
					
// 					/// Finding the image of the product ///
// 					$imageContent = "NO IMGAGE";
// 					if(isset($this->_productAttributeArray[ProductAttributeType::ID_IMAGE_ATTRIBUTE]))
// 						$imageContent = $this->_productAttributeArray[ProductAttributeType::ID_IMAGE_ATTRIBUTE]->getAttribute();
					
// 					$author = "NO AUTHOR";
// 					if(isset($this->_productAttributeArray[ProductAttributeType::ID_AUTHOR_ATTRIBUTE]))
// 						$author = $this->_productAttributeArray[ProductAttributeType::ID_AUTHOR_ATTRIBUTE]->getAttribute();
						
// 					$productDescription = "NO DESCRIPTION AVAILABLE";	
// 					if(isset($this->_productAttributeArray[ProductAttributeType::ID_DESCRIPTION]))
// 						$productDescription = $this->_productAttributeArray[ProductAttributeType::ID_DESCRIPTION]->getAttribute();
						
// 					$productName = $product->getTitle();
					
// 					$this->productTitle->setText($productName);	
// 					$this->productAuthor->setText($author);
// 					$this->productImageDiv->getControls()->add($imageContent);
// 					$this->productDescription->setText($productDescription);
			
	}
	
	public function getProductDetails()
	{
	    if(!isset($this->Request['id']) || !(($product = BaseService::getInstance('Product')->get($this->Request['id'])) instanceof Product))
	        return 'No Product Found!';
	    $html = "<div class='wrapper'>";
    	    $html .= "<div class='product listitem'>";
        	    $html .= "<span class='inlineblock listcol left'>";
            	    $html .= "<div class='product_image noimage'></div>";
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
                	    $html .= '<input type="button" value="Borrow" />';
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