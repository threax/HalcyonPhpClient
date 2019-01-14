<?php

namespace threax\halcyonclient;

class CurlRequest {
    private $url;
    private $method;
    private $headers = array();

    public function __construct(string $url, string $method){
        $this->url = $url;
        $this->method = $method;
    }

    public function setUrl(string $url): void {
        $this->url = $url;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function addHeader(string $key, string $value): void {
        $this->headers[$key] = $value;
    }

    public function getHeaders() {
        return $this->headers;
    }
}