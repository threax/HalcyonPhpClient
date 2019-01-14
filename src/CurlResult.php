<?php

namespace threax\halcyonclient;

class CurlResult {
    public $statusCode;
    public $content;
    public $headers;

    public function __construct($statusCode, $content, $headers){
        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->headers = $headers;
    }
}