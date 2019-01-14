<?php

namespace threax\halcyonclient;

use threax\halcyonclient\CurlHelper;
use threax\halcyonclient\CurlResult;
use threax\halcyonclient\CurlRequest;

class HalEndpointClient {
    private static $HalcyonJsonMimeType = 'application/json+halcyon';

    public static function Load(string $url, CurlHelper $curlHelper): HalEndpointClient {
        $request = new CurlRequest($url, "GET");
        $request->addHeader('Accept', HalEndpointClient::$HalcyonJsonMimeType);
        $result = $curlHelper->load($request);
        $parsed = HalEndpointClient::ParseResult($result);
        if($result->statusCode > 199 && $result->statusCode < 300) {

        }
        else {

        }
    }

    private static function ParseResult(CurlResult $result) {
        $contentHeader = $result->headers['content-type'][0];
        echo $contentHeader;
        echo $result->content;
        if($contentHeader) {

        }
    }

    public function __construct(){

    }
}