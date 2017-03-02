<?php
require '_bootstrap.php';

require 'data/servers.php';

$arResults = ['#General' => ['date' => date('Y-m-d H:i:s')]];

foreach($arServers as $sHostName => $arServer) {
    echo "Looking for {$sHostName}\n";

    $oHost = Host::getInstance($sHostName, $arServer);
    $arResults[$sHostName] = [];

    foreach($arServer['services'] as $sServiceName => $arParams) {
        try {
            $sCheckName = 'Check' . ucfirst($sServiceName);
            $oCheck = new $sCheckName();
            $arRet = $oCheck->check($oHost, $arParams);

            $arResults[$sHostName] = array_merge($arResults[$sHostName], $arRet);
        } catch (Exception $e) {
            echo "Exception : $e\n";
        }
    }
}

print_r($arResults);
