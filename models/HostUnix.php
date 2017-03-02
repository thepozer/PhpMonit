<?php

class HostUnix extends Host {
    private $sHostname = null;

    private $sSshlogin          = null;
    private $sSshPrivateFileKey = null;
    private $sSshPublicFileKey  = null;
    private $rSshLink = null;

    public function __construct($sHostname) {
        debug(__METHOD__ . " - Set host name to '{$sHostname}'");
        $this->sHostname = $sHostname;
    }

    public function setSshAuthInfo($sLogin, $sPrivateFileKey, $sPublicFileKey) {
        $this->sSshLogin          = $sLogin;
        $this->sSshPrivateFileKey = $sPrivateFileKey;
        $this->sSshPublicFileKey  = $sPublicFileKey;

        $this->rSshLink = null;
    }

    public function executeRemoteCmd($sCmd) {
        if (!$this->checkSshLink()) {
            return null;
        }

        $sRetOut = '';
        $bDone = false;

        $sRetOut = $this->rSshLink->exec($sCmd);
        return $sRetOut;
    }

    private function checkSshLink() {
        if (!$this->sSshLogin) {
            return false;
        }

        if (!$this->rSshLink) {
            debug(__METHOD__ . " - Connecting to '{$this->sHostname}'");
            $this->rSshLink = new \phpseclib\Net\SSH2($this->sHostname);

            debug(__METHOD__ . " - Using login '{$this->sSshLogin}' - Public Key '{$this->sSshPublicFileKey}' - Private Key '{$this->sSshPrivateFileKey}' ");
            $oRsaCrypt = new \phpseclib\Crypt\RSA();
            $oRsaCrypt->loadKey(file_get_contents($this->sSshPrivateFileKey));
            if (!$this->rSshLink->login($this->sSshLogin, $oRsaCrypt)) {
                return false;
            }
        }

        return true;
    }
}