<?php
require __DIR__ . '/vendor/autoload.php';

use Jumbojett\OpenIDConnectClient;
use spc\phphalcyon\HalEndpointClient;
use spc\phphalcyon\CurlHelper;
//use spc\phphalcyon\AccessTokenCurlExtension;

$client = new HalEndpointClient();

$idServerHost = 'https://localhost:44390';

$oidc = new OpenIDConnectClient($idServerHost,
                                'PHPTest',
                                'notyetdefined');

//For dev
$oidc->setVerifyHost(false);
$oidc->setVerifyPeer(false);

//For prod
//$oidc->setCertPath(__DIR__ . '/certs/cacert.pem');

$oidc->providerConfigParam(array('token_endpoint'=> $idServerHost . '/connect/token'));
$oidc->addScope('Spc.Authority');

// this assumes success (to validate check if the access_token property is there and a valid JWT) :
$clientCredentialsToken = $oidc->requestClientCredentialsToken()->access_token;
//echo $clientCredentialsToken;

//$bearerExt = new AccessTokenCurlExtension($clientCredentialsToken);

$curlHelper = new CurlHelper();
//$curlHelper->addRequestExtension($bearerExt);
$curlHelper->setIgnoreCertErrors(true);
$result = $curlHelper->load("https://localhost:44395/api");
echo $result->content;
echo $result->statusCode;

//Try to connect to the authority and load some data
// // Get cURL resource
// $curl = curl_init();
// // Set some options - we are passing in a useragent too here
// curl_setopt_array($curl, array(
//     CURLOPT_RETURNTRANSFER => 1,
//     CURLOPT_URL => 'https://localhost:44395/api',
//     CURLOPT_USERAGENT => 'Codular Sample cURL Request',
//     //For dev
//     CURLOPT_SSL_VERIFYHOST => 0,
//     CURLOPT_SSL_VERIFYPEER => 0
// ));

// curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//     'bearer: ' . $clientCredentialsToken
// ));

// // Send the request & save response to $resp
// $resp = curl_exec($curl);
// $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

// echo 'Curl done';
// if(!$resp){
//     die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
// }

// echo $respCode;
// echo $resp;

// // Close request to clear up some resources
// curl_close($curl);