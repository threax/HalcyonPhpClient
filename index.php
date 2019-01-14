<?php
require __DIR__ . '/vendor/autoload.php';

use Jumbojett\OpenIDConnectClient;
use threax\halcyonclient\HalEndpointClient;
use threax\halcyonclient\CurlHelper;
use threax\halcyonclient\AccessTokenCurlExtension;

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

$bearerExt = new AccessTokenCurlExtension($clientCredentialsToken);

$curlHelper = new CurlHelper();
$curlHelper->addRequestExtension($bearerExt);
$curlHelper->setIgnoreCertErrors(true);
$result = $curlHelper->load("https://localhost:44395/api");
echo $result->content;
echo $result->statusCode;