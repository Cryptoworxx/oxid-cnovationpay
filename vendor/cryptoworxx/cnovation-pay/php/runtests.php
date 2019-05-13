<?php

require("CNovationPayClient.php");

array_shift($argv);
$token = array_shift($argv);
$url = array_shift($argv);

$client = new CNovationPayClient($token);
if( $url )
	$client->url = $url;

try
{
	$res = $client->info();
	//$res = $client->currencies();
	//$res = $client->payments();
}
catch(CNovationException $ex)
{
	$res = ['exception'=>$ex->getMessage()];
}

echo json_encode($res,JSON_PRETTY_PRINT);
