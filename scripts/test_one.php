<?php
require '_bootstrap.php';

$bDebugEcho = true;

$iCurrentTime = $argv[1];
$sHostName    = $argv[2];

$oTestChecks = new TestCheck($arServers, $arNotifs, $iCurrentTime, $sHostName);
$oTestChecks->run();
