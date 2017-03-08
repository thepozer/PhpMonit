<?php

class HostWindows extends Host {

    public function __construct($sHostname) {
        parent::__construct($sHostname);
    }
    
    public function getOS() {
        return self::OS_WINDOWS;
    }
}
