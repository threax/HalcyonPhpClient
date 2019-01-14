<?php

namespace threax\halcyonclient;

use \Exception;

class HalException extends Exception {
    private $data;
    private $status;

    public function __construct($data, $status, Exception $previous = null) {
        $this->data = $data;
        $this->status = $status;

        parent::__construct($data->message, $status, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}