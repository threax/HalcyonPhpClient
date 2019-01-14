<?php

namespace threax\halcyonclient;

use \Exception;
use threax\halcyonclient\CurlHelper;
use threax\halcyonclient\CurlResult;
use threax\halcyonclient\CurlRequest;
use threax\halcyonclient\HalException;
use threax\halcyonclient\HalEmbed;

class HalEndpointClient {
    private static $HalcyonJsonMimeType = 'application/json+halcyon';
    private static $JsonMimeType = 'application/json';

    public static function Load(string $url, CurlHelper $curlHelper): HalEndpointClient {
        return HalEndpointClient::LoadRaw($url, "GET", "query", NULL, $curlHelper);
    }

    private static function LoadRaw(string $url, string $method, string $datamode, $data, CurlHelper $curlHelper): HalEndpointClient {
        //Build request
        $request = new CurlRequest($url, $method);
        $request->addHeader('Accept', HalEndpointClient::$HalcyonJsonMimeType);
        if($data !== NULL) {
            switch($datamode) {
                case "query":
                    $request->setUrl(HalEndpointClient::GetQueryLink($url, $data));
                    break;
                case "body":
                    break;
                case "form":
                    break;
            }
        }

        //Do the request and process results
        $result = $curlHelper->load($request);
        $data = HalEndpointClient::ParseResult($result);
        if($result->statusCode > 199 && $result->statusCode < 300) {
            return new HalEndpointClient($data, $curlHelper);
        }
        else {
            //Is the object a custom server message?
            if(isset($data->message)) {
                throw new HalException($data, $result->statusCode);
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

    private $data;
    private $curlHelper;
    private $links;
    private $embeds;

    public function __construct($data, CurlHelper $curlHelper){
        $this->curlHelper = $curlHelper;
        $this->data = $data;
        if(isset($this->data->_links)) {
            $this->links = $this->data->_links;
            unset($this->data->_links);
        }
        if(isset($this->data->_embedded)) {
            $this->embeds = $this->data->_embedded;
            unset($this->data->_embedded);
        }
    }

    public function getData() {
        return $this->data;
    }

    public function getEmbed(string $name): HalEmbed {
        return new HalEmbed($name, $this->embeds->$name, $this->curlHelper);
    }

    public function hasEmbed(string $name): bool {
        return isset($this->embeds->$name);
    }

    public function loadLink(string $ref): HalEndpointClient {
        return $this->loadLinkWithData($ref, NULL);
    }

    public function loadLinkWithData(string $ref, $data): HalEndpointClient {
        if($this->hasLink($ref)) {
            $link = $this->links->$ref;
            return HalEndpointClient::LoadRaw($link->href, $link->method, isset($link->datamode) ? $link->datamode : "query", $data, $this->curlHelper);
        }
        else {
            throw new Exception("Cannot find link named " . $ref);
        }
    }

    public function hasLink(string $ref): bool {
        return isset($this->links->$ref);
    }

    private static function GetQueryLink(string $href, $data): string {
        if($data !== NULL) {
            return $href . "?" . \http_build_query($data);
        }
        return $href;
    }
}