<?php
require '_bootstrap.php';

require 'data/servers.php';

$iCurrTime = time();

$sCheckDirName = 'data/checks/' . date('Y', $iCurrTime) . '/' . date('m', $iCurrTime) . '/' . date('d', $iCurrTime) . '/' . date('H', $iCurrTime);
$sCheckFileName = $sCheckDirName . '/status-' . date('Y-m-d-Hi', $iCurrTime) . '.json';
$sNotifDirName = 'data/notifs/' . date('Y', $iCurrTime);
$sNotifFileName = $sNotifDirName . '/notif-' . date('Y-m-d', $iCurrTime) . '.json';
$arResults = ['#General' => ['date' => date('Y-m-d H:i:s', $iCurrTime)]];

$arNotifications = [];

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
                if ($arService['status'] === 'KO') {
                    $arResults[$sHostName]['#status'] = 'KO' ;
                    
                    $arNotif = $arService;
                    $arNotif['date'] = date('Y-m-d H:i:s', $iCurrTime);
                    $arNotif['host'] = $arServer['host'];
                    $arNotifications[] = $arNotif;
                }
                 
            }
        } catch (Exception $e) {
            error("Exception : $e");
        }
    }
}

debug("sCheckDirName  : '{$sCheckDirName}'");
debug("sCheckFileName : '{$sCheckFileName}'");

if (!is_dir($sCheckDirName)) {
    mkdir($sCheckDirName, 0777, true);
}
file_put_contents($sCheckFileName, json_encode($arResults));
file_put_contents('data/status.json', json_encode($arResults));


debug("sNotifDirName  : '{$sNotifDirName}'");
debug("sNotifFileName : '{$sNotifFileName}'");

if (!is_dir($sNotifDirName)) {
    mkdir($sNotifDirName, 0777, true);
}
$arFullNotifications = (file_exists($sNotifFileName)) ? json_decode(file_get_contents($sNotifFileName), true) : [];
$arFullNotifications += $arNotifications;
file_put_contents($sNotifFileName, json_encode($arFullNotifications));
file_put_contents('data/notif.json', json_encode($arFullNotifications));
