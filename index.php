<?php
require __DIR__ . '/vendor/autoload.php';

use threax\halcyonclient\HalEndpointClient;
use threax\halcyonclient\CurlHelper;
use threax\halcyonclient\AccessTokenCurlExtension;

$curlHelper = new CurlHelper();
$bearerExt = new AccessTokenCurlExtension('https://localhost:44390', 'PHPTest', 'notyetdefined', 'Spc.Authority');
$bearerExt->setIgnoreCertErrors(true);
$curlHelper->addRequestExtension($bearerExt);
$curlHelper->setIgnoreCertErrors(true);

$client = HalEndpointClient::Load("https://localhost:44395/api", $curlHelper);

//echo $result->content;
//echo $result->statusCode;