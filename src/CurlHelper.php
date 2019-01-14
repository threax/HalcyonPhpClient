<?php

namespace spc\phphalcyon;

use \Exception;

class CurlResult {
    public $statusCode;
    public $content;

    public function __construct($statusCode, $content){
        $this->statusCode = $statusCode;
        $this->content = $content;
    }
}

class CurlHelper {
    private $ignoreCertErrors = false;
    private $userAgent = "spc\\phphalcyon";
    private $certPath = __DIR__ . '/../certs/cacert.pem';
    private $requestExtensions = [];

    public function __construct(){

    }

    public function getIgnoreCertErrors(): bool {
        return $this->ignoreCertErrors;
    }

    public function setIgnoreCertErrors(bool $value): void {
        $this->ignoreCertErrors = $value;
    }

    public function addRequestExtension(ICurlRequestExtension $ext){
        array_push($this->requestExtensions, $ext);
    }

    public function load($url): CurlResult {
        $curl = curl_init();

        try{
            //Set basic options
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_USERAGENT => $this->userAgent
            ));

            //If we are ignoring cert errors, set those options
            if($this->ignoreCertErrors){
                curl_setopt_array($curl, array(
                    //For dev
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0
                ));
            }
            else{
                //If we have a cert path, set it, otherwise no ssl validation will be done.
                if (isset($this->certPath)) {
                    curl_setopt($curl, CURLOPT_CAINFO, $this->certPath);
                }
            }

            //Add user options
            foreach ($this->requestExtensions as $ext) {
                $ext->addConfig($curl);
            }

            // Send the request & save response to $resp
            $resp = curl_exec($curl);
            if(!$resp){
                throw new Exception('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
            }
            $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            return new CurlResult($respCode, $resp);
        }
        finally{
            // Close request to clear up some resources
            curl_close($curl);
        }
    }
}