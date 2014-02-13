<?php
class LC_Bankstown extends LibraryConnectorAbstract
{
	/**
	 * Getting the user information for a user
	 *
	 * @param unknown $username
	 * @param unknown $password
	 *
	 * @return LibraryConnectorUser
	 */
	public function getUserInfo($username, $password)
	{
		try
		{
			$wsdl = trim($this->getLibrary()->getInfo('soap_wsdl'));
			$params = array(
					'soap_method' => 'GetMemberInformation',
					'dbName' => 'DBK',
					'MemberCode' => $username,
					'password' => $password,
			);
			$result = BmvComScriptCURL::readUrl($wsdl, null, $params);
			
			$result = new SimpleXMLElement($result);
			$xml = $result->children('SOAP-ENV', TRUE)->Body->children('', TRUE)->GetMemberInformationResponse->GetMemberInformationResult;
			$xml = new SimpleXMLElement($xml->asXml());
			$infos = array();
			foreach($xml->xpath("//Fields") as $field)
			{
				$infos[trim($field['field'])] = trim($field->value);
			}
			return LibraryConnectorUser::getUser($this->getLibrary(), $username, sha1($password), $infos['GivenName'], $infos['Surname'], $infos);
		}
		catch (Exception $ex)
		{
			var_dump($ex);
			return null;
		}
	}
	/**
	 * Checking whether the user exists
	 * 
	 * @param unknown $username
	 * @param unknown $password
	 */
	public function chkUser($username, $password)
	{
		try 
		{
			$wsdl = trim($this->getLibrary()->getInfo('soap_wsdl'));
			$params = array(
				'soap_method' => 'VerifyMember',
				'dbName' => 'DBK',
				'MemberCode' => $username,
				'password' => $password,
			);
			$result = BmvComScriptCURL::readUrl($wsdl, null, $params);
			
			$result = new SimpleXMLElement($result);
			$xml = $result->children('SOAP-ENV', TRUE)->Body->children('', TRUE)->VerifyMemberResponse->VerifyMemberResult;
			return strtolower(trim($xml)) === 'true';
		} 
		catch (Exception $ex)
		{
			return false;
		}
	}
}