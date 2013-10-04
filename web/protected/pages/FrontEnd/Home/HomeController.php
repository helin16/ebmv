<?php
/**
 * This is the loginpage
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class HomeController extends FrontEndPageAbstract  
{
    /**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param)
	{
	    if(!$this->IsPostBack || !$this->IsCallback)
	    {
	        $this->getClientScript()->registerEndScript('navListJs', $this->_getNavListJs());
	    }
	}
	
	private function _getNavListJs()
	{
	    return 'ddsmoothmenu.init({
            mainmenuid: "catelist",
            orientation: "v",
            classname: "ddsmoothmenu-v",
            arrowswap: true,
            contentsource: "markup"
		});
		var pageJs = new PageJs("productlist", "' . $this->getProductsBtn->getUniqueID() . '"); 
		pageJs.showProducts();
	   ';
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
	
	public function getProducts($sender, $params)
	{
	    $errors = $result = array();
	    try
	    {
	        $products = BaseService::getInstance('Product')->findByCriteria('active = 1', array(), false, 1, 30, array());
	        foreach($products as $product)
	        {
	            $result[] = $product->getJson();
	        } 
	    }
	    catch(Exception $ex)
	    {
	        $errors[] = $ex->getMessage();
	    }
	    $params->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
}
?>