<?php

class HostUnix extends Host {

    public function __construct($sHostname) {
        parent::__construct($sHostname);
    }

    public function getOS() {
        return self::OS_UNIX;
    }
}
