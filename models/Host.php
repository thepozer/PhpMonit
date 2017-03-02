<?php

class Host {
    public static function getInstance($sName, $arServer) {
        $oHost = null;

        if ($arServer['type'] === 'HostUnix') {
            $oHost = new HostUnix($arServer['host']);
            $oHost->setSshAuthInfo($arServer['ssh']['login'],$arServer['ssh']['private_key'],$arServer['ssh']['public_key']);
        }
        
        return $oHost;
    }

    public function executeCmd($sCmd) {
        
        exec($sCmd, $arRetOut);

        return implode('\n', $arRetOut);
    }
    
}