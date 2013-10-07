<?php
/**
 * This is the Products
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class ProductsController extends FrontEndPageAbstract  
{
    public $pageSize = 12;
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
	    $js = 'ddsmoothmenu.init({';
           $js .= 'mainmenuid: "catelist",';
           $js .= 'orientation: "v",';
           $js .= 'classname: "ddsmoothmenu-v",';
           $js .= 'arrowswap: true,';
           $js .= 'contentsource: "markup"';
		$js .= '});';
		$js .= 'var pageJs = new PageJs("productlist", "' . $this->getProductsBtn->getUniqueID() . '"); ';
		$js .= 'pageJs.pagination.pageSize = ' . $this->pageSize . ';';
		$js .= (($catId = trim($this->Request['cateid'])) === '' ? '' : 'pageJs.searchCriteria.categoryIds.push(' . $catId . ');');
		$js .= (($searchtext = trim($this->Request['searchtext'])) === '' ? '' : 'pageJs.searchCriteria.searchString = "' . $searchtext . '";');
		$js .= 'pageJs.showProducts();';
	   return $js;
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
	        $html .= '<li><a href="/products/category/' . $categories[$key]->getId() . '">' . $categories[$key]->getName() . '</a>';
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
	        $pageNo = 1;
	        $pageSize = $this->pageSize;
	        
	        if(isset($params->CallbackParameter->pagination))
	        {
	            $pageNo = trim(isset($params->CallbackParameter->pagination->pageNo) ? $params->CallbackParameter->pagination->pageNo : $pageNo);
	            $pageSize = trim(isset($params->CallbackParameter->pagination->pageSize) ? $params->CallbackParameter->pagination->pageSize : $pageSize);
	        }
	        
	        $categoryIds = array();
	        $searchText = '';
	        if(isset($params->CallbackParameter->searchCriteria))
	        {
	            $searchCriteria = json_decode(json_encode($params->CallbackParameter->searchCriteria), true);
	            $searchText = $searchCriteria['searchString'];
	            $categoryIds = is_array($searchCriteria['categoryIds']) ? $searchCriteria['categoryIds'] : array();
	        }
	        
	        $products = BaseService::getInstance('Product')->findProductsInCategory($searchText, $categoryIds, false, $pageNo, $pageSize, array());
	        $result['pagination'] = BaseService::getInstance('Product')->getPageStats();
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
?>