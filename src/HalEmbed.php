<?php

namespace threax\halcyonclient;

use \threax\halcyonclient\CurlHelper;
use \threax\halcyonclient\HalEndpointClient;

class HalEmbed {
    private $name;
    private $embeds;
    private $curlHelper;

    public function __construct(string $name, $embeds, CurlHelper $curlHelper){
        $this->name = $name;
        $this->embeds = $embeds;
        $this->curlHelper = $curlHelper;
    }

    public function getAllClients(): array {
        $clients = [];

        foreach ($this->embeds as $embed) {
            array_push($clients, new HalEndpointClient($embed, $this->curlHelper));
        }

        return $clients;
    }
}