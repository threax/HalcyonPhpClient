<?php

namespace threax\halcyonclient;

use \Exception;
use threax\halcyonclient\CurlResult;
use threax\halcyonclient\CurlRequest;

class CurlHelper {
    private $ignoreCertErrors = false;
    private $userAgent = "threax\\halcyonclient";
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

    public function getUserAgent(): string {
        return $this->ignoreCertErrors;
    }

    public function setUserAgent(string $value): void {
        $this->userAgent = $value;
    }

    public function load(CurlRequest $request): CurlResult {
        //Add extensions to request
        foreach ($this->requestExtensions as $ext) {
            $ext->addConfig($request);
        }

        $curl = curl_init();

        try{
            //Set basic options
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $request->getUrl(),
                CURLOPT_USERAGENT => $this->userAgent,
                CURLOPT_CUSTOMREQUEST => $request->getMethod()
            ));

            // Thanks to user Geoffery at https://stackoverflow.com/questions/9183178/can-php-curl-retrieve-response-headers-and-body-in-a-single-request
            $headers = [];
            curl_setopt($curl, CURLOPT_HEADERFUNCTION,
                function($curl, $header) use (&$headers)
                {
                    // this function is called by curl for each header received
                    $len = strlen($header);
                    $header = explode(':', $header, 2);
                    if (count($header) < 2) // ignore invalid headers
                        return $len;

                    $name = strtolower(trim($header[0]));
                    if (!array_key_exists($name, $headers))
                        $headers[$name] = [trim($header[1])];
                    else
                        $headers[$name][] = trim($header[1]);

                    return $len;
                }
            );

            //Setup ssl options
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

            //Setup Headers
            $requestHeaders = [];
            $hasHeader = false;
            foreach ($request->getHeaders() as $key => $value) {
                array_push($requestHeaders, $key . ": " . $value);
                $hasHeader = true;
            }
            
            if($hasHeader) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $requestHeaders);
            }

            // Send the request & save response to $resp
            $resp = curl_exec($curl);
            if(!$resp){
                throw new Exception('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
            }
            $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            return new CurlResult($respCode, $resp, $headers);
        }
        finally{
            // Close request to clear up some resources
            curl_close($curl);
        }
    }
}