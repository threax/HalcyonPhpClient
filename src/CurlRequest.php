<?php

namespace threax\halcyonclient;

class CurlRequest {
    private $url;
    private $method;
    private $headers = array();
    private $body = NULL;

    public function __construct(string $url, string $method){
        $this->url = $url;
        $this->method = $method;
    }

    public function setUrl(string $url) {
        $this->url = $url;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function addHeader(string $key, string $value) {
        $this->headers[$key] = $value;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function getBody() {
        return $this->body;
    }
}