<?php

namespace threax\halcyonclient;

use Jumbojett\OpenIDConnectClient;
use threax\halcyonclient\ICurlRequestExtension;

class AccessTokenCurlExtension implements ICurlRequestExtension {
    private $idServerHost;
    private $clientId;
    private $clientSecret;
    private $ignoreCertErrors = false;
    private $scopes;
    private $token = null;

    public function __construct(string $idServerHost, string $clientId, string $clientSecret, string ...$scopes){
        $this->idServerHost = $idServerHost;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scopes = $scopes;
    }

    public function getIgnoreCertErrors(): bool {
        return $this->ignoreCertErrors;
    }

    public function setIgnoreCertErrors(bool $value): void {
        $this->ignoreCertErrors = $value;
    }

    public function addConfig($curl) {
        if($this->token === null) {
            $oidc = new OpenIDConnectClient($this->idServerHost, $this->clientId, $this->clientSecret);

            if($this->ignoreCertErrors) {
                $oidc->setVerifyHost(false);
                $oidc->setVerifyPeer(false);
            }
            else {
                $oidc->setCertPath(__DIR__ . '/../certs/cacert.pem');
            }

            $oidc->providerConfigParam(array('token_endpoint'=> $this->idServerHost . '/connect/token'));
            foreach ($this->scopes as $scope) {
                $oidc->addScope($scope);
            }

            // this assumes success (to validate check if the access_token property is there and a valid JWT) :
            $this->token = $oidc->requestClientCredentialsToken()->access_token;
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'bearer: ' . $this->token
        ));
    }
}