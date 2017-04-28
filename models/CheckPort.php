<?php

class CheckPort {
    private $arParams = ['list' => [22, 80], 'needed' => [], 'forbidden' => [7, 23]];

    public function __construct($arParams = null) {
        if (is_array($arParams)) {
            $this->arParams = array_merge($this->arParams, $arParams);
        }
    }

    public function check($oHost, $arParams) {
        $arParams = array_merge($this->arParams, $arParams);
        $arRet = [
            'status' => 'OK', 
            'value' => 0,
            'human' => "Etat des ports : \n<ul>\n",
            'description' => 'Ports ouverts de la machine',
            'unit' => '-'
        ];
        
        $arListPort = array_unique(array_merge($arParams['list'], $arParams['needed'], $arParams['forbidden']), SORT_NUMERIC);
        $arOpenPort = [];
        $sHostname = $oHost->getHostname();
        foreach ($arListPort as $iPort) {
            $bPortOpen = $this->testPort($sHostname, $iPort);
//debug(__METHOD__ . " - Port {$iPort} : " . (($bPortOpen) ? 'Ouvert' : 'Fermé'));
            if ($bPortOpen) {
                $sPortStatus = "Port TCP {$iPort} (" . getservbyport($iPort, 'tcp') . ") ouvert.";
                if (in_array($iPort, $arParams['forbidden'])) {
                    $sPortStatus = "DANGER : " . $sPortStatus;
                    $arRet['status'] = 'KO';
                }
                $arRet['value'] ++;
            } else {
                $sPortStatus = "Port TCP {$iPort} (" . getservbyport($iPort, 'tcp') . ") ne répond pas.";
                if (in_array($iPort, $arParams['needed'])) {
                    $sPortStatus = "ERREUR : " . $sPortStatus;
                    $arRet['status'] = 'KO';
                }
            }
            
//debug(__METHOD__ . " - Port {$iPort} - Status : {$sPortStatus}");
            $arRet['human'] .= "<li>{$sPortStatus}</li>\n";
        }
        $arRet['human'] .= "</ul>\n";
        
        return ['port' => $arRet];
    }
    
    protected function testPort($sHostname, $iPort) {
        $rConn = @fsockopen($sHostname, $iPort);
        
        if (is_resource($rConn)) {
            fclose($rConn);
            return true;
        }
        
        return false;
    }
    
}
