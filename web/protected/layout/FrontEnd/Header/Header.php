<?php
/**
 * Header template
 *
 * @package    Web
 * @subpackage Layout
 * @author     lhe
 */
class Header extends TTemplateControl
{
	const MAX_ITEM_NO = 10;
	/**
	 * (non-PHPdoc)
	 * @see TPage::render()
	 */
	public function onLoad($param)
	{
		parent::onLoad($param);
		$cScripts = FrontEndPageAbstract::getLastestJS(get_class($this));
		if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
			$this->getPage()->getClientScript()->registerScriptFile('headerJs', $this->publishAsset($lastestJs));
		if (isset($cScripts['css']) && ($lastestCss = trim($cScripts['css'])) !== '')
			$this->getPage()->getClientScript()->registerStyleSheetFile('headerCss', $this->publishAsset($lastestCss));
		if(!$this->getPage()->IsPostBack && !$this->getPage()->IsCallBack)
			$this->getPage()->getClientScript()->registerEndScript('headerEndJs', $this->_getJs());
	}

	private function _getJs()
	{
		$products = ($supplier = Supplier::get(Supplier::ID_CIO)) instanceof Supplier ? $supplier->getProducts(array(), array(ProductType::ID_COURSE)) : array();
		$array = array();
		foreach($products as $product)
			$array[] = array('id' => $product->getId(), 'title' => $product->getTitle());
		$js = 'var headerJs = new HeaderJs();';
		$js .= 'headerJs.load(' . json_encode($this->getCourseFlag(Core::getLibrary())) . ');';
		return $js;
	}
	private function getMenuListRow($language, ProductType $productType, $titles, Product $product = null)
	{
		$html = '';
		if($language instanceof Language)
		{
			$html .= '<li>';
			$html .= 	' <a href="/products/' . $language->getId() . '/' . $productType->getId() . '" class="iconbtn">';
			$html .= 		'<div class="row">';
			$html .= 			'<div class="col-xs-4">' . $titles['CN'] . '</div>';
			$html .= 			'<div class="col-xs-8 en">' . $titles['EN'] . '</div>';
			$html .= 		'</div>';
			$html .= 	' </a>';
			$html .= '</li>';
		} elseif(isset($product)) {
			$html .= '<li role="presentation">';
			$html .= 	' <a href="/product/' . $product->getId() . '" class="iconbtn" role="menuitem">';
			$html .= 		'<div class="row">';
			$html .= 			'<div class="col-xs-12">' .$product->getTitle() . '</div>';
			$html .= 		'</div>';
			$html .= 	' </a>';
			$html .= '</li>';
		}
		return $html;
	}
	public function getCourseFlag(Library $library)
	{
		$productTypes = $library->getProductTypes();
		$courseFlag = false;
		foreach ($productTypes as $productType)
		{
			if(!$courseFlag && intval($productType->getId()) === ProductType::ID_COURSE)
				$courseFlag = true;
		}
		return $courseFlag;
	}
	public function getMenuList($language, Library $library, $btnName = array(), $products = array())
	{
		$html = '';
		$html .= '<li class="dropdown visible-lg visible-md visible-sm visible-xs">';
		$html .= 	'<a href="#" role="button" class="dropdown-toggle iconbtn" data-toggle="dropdown" id="schinese-dropdown-btn" data-target="#">';
		if($language instanceof Language)
		{
			if(intval($language->getId()) === Language::ID_SIMPLIFIED_CHINESE) {
				$html .= '<div class="btnname">' . $language->getName() . '<small>Simplifed Chinese</small></div>';
			} elseif(intval($language->getId()) === Language::ID_TRADITIONAL_CHINESE) {
				$html .= '<div class="btnname">' . $language->getName() . '<small>Traditional Chinese</small></div>';
			}
		} elseif(isset($btnName)) {
			$html .= 	'<div class="btnname learn-chinese-menu">' . $btnName['CN'] . '<small>' . $btnName['EN'] . '</small></div>';
		}
		$html .= 		'<b class="caret"></b>';
		$html .= 	'</a>';
		$html .= 	'<ul class="dropdown-menu' . (isset($product) ? ' extra-long' : '') . '" role="menu" aria-labelledby="menu-dropdown-btn">';
		$productTypes = $library->getProductTypes();
		foreach ($productTypes as $productType)
		{
			if(intval($productType->getId()) === ProductType::ID_BOOK)
				$html .= $this->getMenuListRow($language, $productType, array('CN'=> '书', 'EN'=> 'Books'));
			if(intval($productType->getId()) === ProductType::ID_MAGAZINE)
				$html .= $this->getMenuListRow($language, $productType, array('CN'=> '杂志', 'EN'=> 'Magazines'));
			if(intval($productType->getId()) === ProductType::ID_NEWSPAPER)
				$html .= $this->getMenuListRow($language, $productType, array('CN'=> '报纸', 'EN'=> 'NewsPapers'));
			if(intval($productType->getId()) === ProductType::ID_COURSE && isset($products))
			{
				if(count($products) > self::MAX_ITEM_NO) {
					foreach (array_splice($products, 0, self::MAX_ITEM_NO) as $product) {
						$html .= $this->getMenuListRow($language, $productType, array('CN'=> '报纸', 'EN'=> 'NewsPapers'), $product);
					}
					$html .= '<li role="separator" class="divider"></li>';
					$categories = $products[self::MAX_ITEM_NO + 1]->getCategorys();
					if(count($categories) > 0)
						$html .= '<li><a href="/products/category/' . $categories[0]->getId() . '">显示所有/ 顯示全部 / Show All</a></li>';
				} else {
					foreach ($products as $product) {
						$html .= $this->getMenuListRow($language, $productType, array('CN'=> '书', 'EN'=> 'Books'), $product);
					}
				}
			}
		}
		$html .= 	'</ul>';
		$html .= '</li>';
		return $html;
	}
}
?>