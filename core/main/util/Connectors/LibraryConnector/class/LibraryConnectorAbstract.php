<?php
/**
 * Library connector interface
 *
 * @package    Core
 * @subpackage Utils
 * @author     lhe<helin16@gmail.com>
 */
class LibraryConnectorAbstract
{
	/**
	 * The library this connect is for
	 * 
	 * @var Library
	 */
	protected $_lib;
	/**
	 * construct
	 * 
	 * @param Library $lib
	 */
	public function __construct(Library $lib)
	{
		$this->_lib = $lib;
	}
	/**
	 * Getting the library from the library connector
	 *
	 * @return Library
	 */
	public function getLibrary()
	{
		return $this->_lib;
	}
}