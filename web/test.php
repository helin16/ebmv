<?php
$client = new SoapClient("http://au.xhestore.com/AULibService.asmx?wsdl");
$result = $client->GetBookList(array("SiteID" => 37, "Index" => 6, "Size" => 100));
$xml = new SimpleXMLElement($result->GetBookListResult->any);
var_dump($xml->Book->asXml());