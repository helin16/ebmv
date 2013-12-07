<?php
/**
 * The SupplierConnector Exception
 * 
 * @package    Core
 * @subpackage Exception
 * @author     lhe<helin16@gmail.com>
 */
class SupplierConnectorException extends Exception
{
	public function __construct($message)
	{
		parent::__construct($message, 10);
	}
}

?>