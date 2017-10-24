<?php
require '_bootstrap.php';

$bDebugEcho = true;

$oTestChecks = new TestCheck($arServers);
$oTestChecks->genCurrentTime();

$oTestChecks->doAllChecks();
$oTestChecks->parseCheckStatus();
