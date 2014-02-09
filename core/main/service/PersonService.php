<?php
/**
 * Person service
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class PersonService extends BaseServiceAbastract
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("Person");
    }
    /**
     * Updating or creating a person
     * 
     * @param string $firstName
     * @param string $lastName
     * @param Person $person    The existing person record
     */
    public function updatePerson($firstName, $lastName, Person &$person = null)
    {
    	$person = ($person instanceof Person ? $person : new Person());
    	$person->setFirstName($firstName);
    	$person->setLastName($lastName);
    	return $this->save($person);
    }
}
?>
