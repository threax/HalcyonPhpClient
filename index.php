<?php
require __DIR__ . '/vendor/autoload.php';

use threax\halcyonclient\HalEndpointClient;
use threax\halcyonclient\CurlHelper;
use threax\halcyonclient\AccessTokenCurlExtension;

$client = new HalEndpointClient();

$bearerExt = new AccessTokenCurlExtension('https://localhost:44390', 'PHPTest', 'notyetdefined', 'Spc.Authority');
$bearerExt->setIgnoreCertErrors(true);

$curlHelper = new CurlHelper();
$curlHelper->addRequestExtension($bearerExt);
$curlHelper->setIgnoreCertErrors(true);
$result = $curlHelper->load("https://localhost:44395/api");
echo $result->content;
echo $result->statusCode;