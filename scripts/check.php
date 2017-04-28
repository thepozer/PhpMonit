<?php
require '_bootstrap.php';

$bDebugEcho = true;

$iCurrTime = time();

$sCheckDirName = 'data/status/' . date('Y', $iCurrTime) . '/' . date('m', $iCurrTime) . '/' . date('d', $iCurrTime) . '/' . date('H', $iCurrTime);
$sCheckFileName = $sCheckDirName . '/status-' . date('Y-m-d-Hi', $iCurrTime) . '.json';
$sNotifDirName = 'data/notifs/' . date('Y', $iCurrTime);
$sNotifFileName = $sNotifDirName . '/notif-' . date('Y-m-d', $iCurrTime) . '.json';
$arResults = ['#General' => ['date' => date('Y-m-d H:i:s', $iCurrTime)]];

$arNotifications = [];

foreach($arServers as $sHostName => $arServer) {
    debug("Looking for {$sHostName}");

    $oHost = Host::getInstance($sHostName, $arServer);
    $arResults[$sHostName] = ['#status' => 'OK'];
    
    
    $oCheck = new CheckPing();
    $arRet = $oCheck->check($oHost, []);
    $arResults[$sHostName] = array_merge($arResults[$sHostName], $arRet);
    $arService = $arRet['ping'];
    
    if ($arService['status'] === 'KO') {
        $arResults[$sHostName]['#status'] = 'KO' ;
        
        $arNotif = $arService;
        $arNotif['name'] = 'Ping';
        $arNotif['date'] = date('Y-m-d H:i:s', $iCurrTime);
        $arNotif['host'] = $arServer['host'];
        $arNotifications[] = $arNotif;
    } else {
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
                        $arNotif['name'] = $sServiceName;
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
}

if (!is_dir($sCheckDirName)) {
    mkdir($sCheckDirName, 0777, true);
}
//debug("check.php : Status  : " . print_r($arResults, true));
$sJsonData = json_encode($arResults);
//debug("check.php : JSON encode (" . json_last_error() . ") : " . json_last_error_msg());
//debug("check.php : JSON data :\n" . $sJsonData);
file_put_contents($sCheckFileName, $sJsonData);
//debug("check.php : Write Status timed file : Ok");
file_put_contents('data/status.json', $sJsonData);
//debug("check.php : Write Status current file : Ok");

if (count($arNotifications) > 0 || !file_exists($sNotifFileName)) {
//debug("sNotifDirName  : '{$sNotifDirName}'");
//debug("sNotifFileName : '{$sNotifFileName}'");

    if (!is_dir($sNotifDirName)) {
        mkdir($sNotifDirName, 0777, true);
    }
    
    $arFullNotifications = (file_exists($sNotifFileName)) ? json_decode(file_get_contents($sNotifFileName), true) : [];
    if (!$arFullNotifications) {
        $arFullNotifications = [];
    }
//debug("check.php : Full Notifications Before : " . print_r($arFullNotifications, true));
    foreach($arNotifications as $arNotif) {
        array_unshift($arFullNotifications, $arNotif);
    }
//debug("check.php : Full Notifications After  : " . print_r($arFullNotifications, true));
    file_put_contents($sNotifFileName, json_encode($arFullNotifications));
    file_put_contents('data/notifs.json', json_encode($arFullNotifications));
}
