<?php
abstract class StringUtilsAbstract
{
	/**
	 * getting the JSON string
	 *
	 * @param array $data   The result data
	 * @param array $errors The errors
	 *
	 * @return string The json string
	 */
	public static function getJson($data = array(), $errors = array())
	{
		return json_encode(array('resultData' => $data, 'errors' => $errors, 'succ' => (count($errors) === 0 ? true : false)));
	}
	/**
	 * convert the first char into lower case
	 *
	 * @param Role $role The role
	 */
	public static function lcFirst($string)
	{
	    return strtolower(substr($string, 0, 1)) . substr($string, 1);
	}
	
	select distinct p.`id`, p.`firstName`, p.`lastName`, p.`active`, p.`created`, p.`createdById`, p.`updated`, p.`updatedById` from person p inner join useraccount `ua` on (p.id = ua.PersonId) where (p.active = 1)'(210), 
	select distinct p.`id`, p.`firstName`, p.`lastName`, p.`active`, p.`created`, p.`createdById`, p.`updated`, p.`updatedById` from person p inner join useraccount `ua` on (p.id = ua.personId) where (p.active = 1)'(210)
}