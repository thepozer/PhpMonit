<?php
require '_bootstrap.php';

require 'data/servers.php';

$iCurrTime = time();

$sDirName = 'data/checks/' . date('Y', $iCurrTime) . '/' . date('m', $iCurrTime) . '/' . date('d', $iCurrTime) . '/' . date('H', $iCurrTime);
$sFileName = $sDirName . '/status-' . date('Y-m-d-Hi', $iCurrTime) . '.json';
$arResults = ['#General' => ['date' => date('Y-m-d H:i:s', $iCurrTime)]];

foreach($arServers as $sHostName => $arServer) {
    debug("Looking for {$sHostName}");

    $oHost = Host::getInstance($sHostName, $arServer);
    $arResults[$sHostName] = ['#status' => 'OK'];

    foreach($arServer['services'] as $sServiceName => $arParams) {
        try {
            $sCheckName = 'Check' . ucfirst($sServiceName);
            $oCheck = new $sCheckName();
            $arRet = $oCheck->check($oHost, $arParams);

            $arResults[$sHostName] = array_merge($arResults[$sHostName], $arRet);
            
            foreach($arRet as $arService) {
                 $arResults[$sHostName]['#status'] = ($arService['status'] === 'KO') ? 'KO' : $arResults[$sHostName]['#status'] ;
            }
        } catch (Exception $e) {
            error("Exception : $e");
        }
    }
}

debug("sDirName  : '{$sDirName}'");
debug("sFileName : '{$sFileName}'");

if (!is_dir($sDirName)) {
    mkdir($sDirName, 0777, true);
}
file_put_contents($sFileName, json_encode($arResults));
file_put_contents('data/status.json', json_encode($arResults));
