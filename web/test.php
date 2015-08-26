<?php
$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CONNECTTIMEOUT => 0,
		CURLOPT_URL     => 'http://public.dooland.com/v1/Magazine/lists/page/1',
		CURLOPT_CUSTOMREQUEST => 'POST'
// 		,CURLOPT_PROXY   => 'proxy.bytecraft.internal:3128'
);
$data = array('osType' => 'Linux', 'appid' => 'f239776c84236b30c83e86993666cc99');
// $options[CURLOPT_POST] = count($data);
$data = json_encode($data);
$options[CURLOPT_POSTFIELDS] = $data;
var_dump($data);

$ch = curl_init();
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
// 	'Content-Type: application/json',
// 	'Content-Length: ' . strlen($data))
// );
curl_setopt_array($ch, $options);
$result = curl_exec($ch);
curl_close($ch);

var_dump($result);

?>