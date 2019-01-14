<?php

namespace threax\halcyonclient;

use \Exception;
use threax\halcyonclient\CurlHelper;
use threax\halcyonclient\CurlResult;
use threax\halcyonclient\CurlRequest;
use threax\halcyonclient\HalException;

class HalEndpointClient {
    private static $HalcyonJsonMimeType = 'application/json+halcyon';
    private static $JsonMimeType = 'application/json';

    public static function Load(string $url, CurlHelper $curlHelper): HalEndpointClient {
        $request = new CurlRequest($url, "GET");
        $request->addHeader('Accept', HalEndpointClient::$HalcyonJsonMimeType);
        $result = $curlHelper->load($request);
        $parsed = HalEndpointClient::ParseResult($result);
        if($result->statusCode > 199 && $result->statusCode < 300) {
            var_dump($parsed->_links);
        }
        else {
            //Is the object a custom server message?
            if(property_exists($parsed, "message")) {
                throw new HalException($parsed, $result->statusCode);
            }
            else {
                throw new Exception("Generic server error with code " . $response->statusCode . " returned.");
            }
        }
    }

    private static function ParseResult(CurlResult $result) {
        $contentHeader = $result->headers['content-type'][0];
        if($contentHeader) {
            if(\substr($contentHeader, 0, strlen(HalEndpointClient::$HalcyonJsonMimeType)) || \substr($contentHeader, 0, strlen(HalEndpointClient::$JsonMimeType))) {
                return \json_decode($result->content);
            }
            else {
                throw new Excetption("Unsupported response type " . $contentHeader . ".");
            }
        }
    }

    public function __construct(){

    }
}