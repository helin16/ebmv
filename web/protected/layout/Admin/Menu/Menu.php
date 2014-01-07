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
		$html = "<ul>";
		foreach($this->_getMenuItems() as $item)
		{
			$html .= "<li>";
				$html .= "<a href='" . $item['href'] . "' class='menuitem " . (trim($item['code']) === trim($this->getPage()->menuItemCode) ? 'active' : '')  . "'>" . $item['name'] . "</a>";
			$html .= "</li>";
		}
		$html .= "</ul>";
		return $html;
	}
	private function _getMenuItems()
	{
		$array = array(
				array('name' => 'Home', 'code' => 'home', 'href' => '/admin/')
				,array('name' => 'Products', 'code' => 'products', 'href' => '/admin/product')
				,array('name' => ' Suppliers ', 'code' => 'suppliers ', 'href' => '/admin/supplier.html')
				,array('name' => ' Libraries ', 'code' => 'libraries ', 'href' => '/admin/library.html')
				,array('name' => ' Test SIP ', 'code' => 'testsip ', 'href' => '/admin/testsip.html')
				,array('name' => ' Logs ', 'code' => 'logs', 'href' => '/admin/logs.html')
				,array('name' => ' Logout ', 'code' => 'logout ', 'href' => '/logout.html?url=/admin/')
			);
		return $array;
	}
}