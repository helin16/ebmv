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
}