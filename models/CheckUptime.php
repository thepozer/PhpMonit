<?php

class CheckUptime {
    private $arParams = ['minimal' => 3600];

    public function __construct($arParams = null) {
        if (is_array($arParams)) {
            $this->arParams = array_merge($this->arParams, $arParams);
        }
    }

    public function check($oHost, $arParams) {
        $arParams = array_merge($this->arParams, $arParams);
        $arRet = [
            'status' => 'KO', 
            'value' => -1,
            'human' => '-',
            'description' => 'Nombre de secondes depuis le dernier démarrage de la machine',
            'unit' => 'Secondes'
        ];

        $sCmd = 'cat /proc/uptime';

        $sRet = $oHost->executeRemoteCmd($sCmd);
        //debug(__METHOD__ . " - sRet : '{$sRet}'");

        if ($sRet) {
            $arTime = explode(' ', $sRet, 2);
            $iDelta = intval($arTime[0]);

            //debug(__METHOD__ . " - PT{$iDelta}S");
            $d1 = new DateTime();
            $d2 = new DateTime();
            $d2->add(new DateInterval('PT'.$iDelta.'S'));
            $oDate = $d2->diff($d1);
            //debug(__METHOD__ . " - oDate : " . print_r($oDate, true));

            $arRet['status'] = (($iDelta >= $arParams['minimal']) ? 'Ok' : 'KO');
            $arRet['value'] = $iDelta;
            $arRet['human'] = $oDate->format('%a j %H h %M m %S s');
        }

        return ['uptime' => $arRet];
    }

}