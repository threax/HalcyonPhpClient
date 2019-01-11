<?php

class AccessTokenCurlExtension {
    private $bearer;

    public function __construct(string $bearer){
        $this->bearer = $bearer;
    }

    public function addConfig($curl) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'bearer: ' . $this->bearer
        ));
    }
}