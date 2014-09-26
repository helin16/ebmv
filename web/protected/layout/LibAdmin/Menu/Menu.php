<?php
/**
 * Menu template
 *
 * @package    Web
 * @subpackage Layout
 * @author     lhe
 */
class Menu extends TTemplateControl
{
	public function getMenu()
	{
		$html = "";
		foreach($this->_getMenuItems() as $item)
		{
			$html .= "<li " . (trim($item['code']) === trim($this->getPage()->menuItemCode) ? 'class="active"' : '')  . ">";
				$html .= "<a href='" . $item['href'] . "'>" . $item['name'] . "</a>";
			$html .= "</li>";
		}
		return $html;
	}
	private function _getMenuItems()
	{
		$array = array(
				array('name' => 'Home', 'code' => 'home', 'href' => '/libadmin/')
				,array('name' => 'Products', 'code' => 'products', 'href' => '/libadmin/product')
			);
		return $array;
	}
}