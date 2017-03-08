<?php

class CheckLoad {
    private $arParams = ['maximal' => [1, 1, 1]];

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
            'description' => 'Charge courante de la machine',
            'unit' => '-'
        ];

        $sCmd = 'cat /proc/loadavg';

        $sRet = $oHost->executeRemoteCmd($sCmd);
        //debug(__METHOD__ . " - sRet : '{$sRet}'");

        if ($sRet) {
            $arTime = explode(' ', $sRet, 4);
            //debug(__METHOD__ . " - arTime : " . print_r($arTime, true));
            list($fLoad1m, $fLoad5m, $fLoad15m, ) = $arTime;

            $arRet['status'] = (($fLoad1m < $arParams['maximal'][0] && $fLoad5m < $arParams['maximal'][1] && $fLoad15m < $arParams['maximal'][2]) ? 'OK' : 'KO');
            $arRet['value'] = $fLoad1m;
            $arRet['human'] = "load 1 min : {$fLoad1m} - 5 min : {$fLoad5m} - 15 min : {$fLoad15m}";
        }

        return ['load' => $arRet];
    }

}
