<?php

class CheckPing {
    private $arParams = ['max' => 1];

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
            'human' => 'Avg : -1',
            'description' => 'Accès réseau à la machine',
            'unit' => 'ms'
        ];

        $sCmd = "ping -c 2 -i 0.2 " . $oHost->getHostname();

        $arRet = $oHost->executeCmd($sCmd);
//debug(__METHOD__ . " - arRet : " . print_r($arRet, true));

        if ($arRet['code'] == 0) {
            $arData = explode(' ', $arRet['last_line'], 5);
//debug(__METHOD__ . " - arData : " . print_r($arData, true));
            $arTime = explode('/', $arData[3]);
//debug(__METHOD__ . " - arTime : " . print_r($arTime, true));
            list($fPingMin, $fPingAvg, $fPingMax, $fPingDelta) = $arTime;

            $arRet['status'] = 'OK';
            $arRet['value'] = (float)$fPingAvg;
            $arRet['human'] = "Min : {$fPingMin} - Avg : {$fPingAvg} - Max : {$fPingMax} - Delta : {$fPingDelta}";
        }

        return ['ping' => $arRet];
    }

}
