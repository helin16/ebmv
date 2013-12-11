<?php
$wsdl ='https://ebmv.com.au/?soap=webauth.wsdl';
$client = new SoapClient($wsdl, array('exceptions' => true, 'encoding'=>'utf-8', 'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
$result = $client->authenticate('A3ADC78482897208E84B759E41DD73E9', '37', 'testuser_yl', '2A2877E4DF17AEED392AA42AD36EE5190E1E1DCC');
echo $result;