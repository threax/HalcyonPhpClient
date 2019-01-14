<?php

namespace threax\halcyonclient;

class CurlResult {
    public $statusCode;
    public $content;

    public function __construct($statusCode, $content){
        $this->statusCode = $statusCode;
        $this->content = $content;
    }
}