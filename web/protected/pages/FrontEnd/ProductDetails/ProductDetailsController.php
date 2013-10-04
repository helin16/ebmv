<?php
/**
 * This is the loginpage
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class ProductDetailsController extends FrontEndPageAbstract  
{
	private $_productService;
	
	private $_productAttributeService;
	
	private $_productAttributeTypeService;
	
	private $_imageLocation;
	
	private $_productAttributeArray;
	
	public function __construct()
	{
		parent::__construct();
		$this->_productService = new ProductService();
		$this->_productAttributeService = new ProductAttributeService();
		$this->_productAttributeTypeService = new ProductAttributeTypeService();
	}
	
	/**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param)
	{
	    parent::onLoad($param);
		
		if(!$this->IsPostBack || $param == "reload")
		{
			if(isset($this->Request['id']) && is_numeric($this->Request['id']))
			{
				if(($product = $this->_productService->get($this->Request['id'])) instanceof Product)
				{
					$paArray = $product->getAttributes();
					foreach($paArray as $pa)
					{
						if($pa instanceof ProductAttribute)
							$this->_productAttributeArray[$pa->getType()->getId()] = $pa;
					}
					
					/// Finding the image of the product ///
					$imageContent = "NO IMGAGE";
					if(isset($this->_productAttributeArray[ProductAttributeType::ID_IMAGE_ATTRIBUTE]))
						$imageContent = $this->_productAttributeArray[ProductAttributeType::ID_IMAGE_ATTRIBUTE]->getAttribute();
					
					$author = "NO AUTHOR";
					if(isset($this->_productAttributeArray[ProductAttributeType::ID_AUTHOR_ATTRIBUTE]))
						$author = $this->_productAttributeArray[ProductAttributeType::ID_AUTHOR_ATTRIBUTE]->getAttribute();
						
					$productDescription = "NO DESCRIPTION AVAILABLE";	
					if(isset($this->_productAttributeArray[ProductAttributeType::ID_DESCRIPTION]))
						$productDescription = $this->_productAttributeArray[ProductAttributeType::ID_DESCRIPTION]->getAttribute();
						
					$productName = $product->getTitle();
					
					$this->productTitle->setText($productName);	
					$this->productAuthor->setText($author);
					$this->productImageDiv->getControls()->add($imageContent);
					$this->productDescription->setText($productDescription);
				}	
			}
		}
	}
	
	private function _getNavListJs()
	{
	    return 'ddsmoothmenu.init({
             mainmenuid: "catelist",
             orientation: "v",
             classname: "ddsmoothmenu-v",
             arrowswap: true,
             contentsource: "markup"});
	    var pageJs = new PageJs("productlist"); pageJs.showProducts();';
	}
	
	public function getCategories()
	{
	    $categories = array();
	    $relArray = $this->_getCategoryArray($categories);
	    return $this->_getCateLi($relArray, $relArray, $categories);
	}
	
	private function _getCateLi($array, $relArray, &$categories, $htmlId = '')
	{
	    if(!is_array($array) || count($array) === 0)
	        return;
	    $html = '<ul>';
	    foreach($array as $key => $nextLevelArray)
	    {
	        if(!isset($categories[$key]))
	            continue;
	        $html .= '<li><a href="#">' . $categories[$key]->getName() . '</a>';
	            unset($categories[$key]);
	        if(isset($relArray[$key]))
	            $html .= $this->_getCateLi($relArray[$key], $relArray, $categories);
	        else
	            $html .= $this->_getCateLi($nextLevelArray, $relArray, $categories);
	        $html .= '</li>';
	    }
	    $html .= "</ul>";
	    return $html;
	}
	
	private function _getCategoryArray(&$flatArray = array())
	{
	    $array = array();
	    $flatArray = array();
	    foreach(BaseService::getInstance('Category')->findAll() as $cate)
	    {
	        $flatArray[$cate->getId()] = $cate;
	        if(!$cate->getParent() instanceof Category)
	        {
	            $array[$cate->getId()] = array();
	            continue;
	        }
	        
	        $parentId = $cate->getParent()->getId();
	        if(!isset($array[$parentId]))
	            $array[$parentId] = array();
	        
	        $array[$parentId][$cate->getId()] = $cate->getId();
	    }
	    return $array;
	}
}
?>