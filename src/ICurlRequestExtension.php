<?php

namespace threax\halcyonclient;

interface ICurlRequestExtension {
    public function addConfig($curl);
}