<?php
require '_bootstrap.php';

$bDebugEcho = true;

$oTestChecks = new TestCheck($arServers, $arNotifs);
$oTestChecks->initCurrentTime();

$oTestChecks->doAllChecks();
$oTestChecks->parseCheckStatus();
