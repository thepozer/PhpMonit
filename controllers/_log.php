<?php

// ***** Fonctions de logs
$sFilePart = date('Y-m-d');
use \Thepozer\Log\SimpleLog as LogDateWriter;
$oLog = new LogDateWriter(fopen('logs/application-' . $sFilePart . '.log', 'a+'), LogDateWriter::ERROR);

if ($bDevMode) {
	$oLog->setLogLevel(LogDateWriter::DEBUG);
}

/**
    * Fonction de logging gloable - niveau debug
    * 
    * @global Object $oGlobalApp Objet Application Slim
    * @param String $sMessage Message à enregistrer
    */
function debug ($sMessage) {
    global $oLog, $bDebugEcho;
    
    $oLog->debug($sMessage);
    if ($bDebugEcho) {
        echo "Debug : {$sMessage}\n";
    }
}

/**
    * Fonction de logging gloable - niveau error
    * 
    * @global Object $oGlobalApp Objet Application Slim
    * @param String $sMessage Message à enregistrer
    */
function error ($sMessage) {
    global $oLog, $bDebugEcho;
    
    $oLog->error($sMessage);
    if ($bDebugEcho) {
        echo "Error : {$sMessage}\n";
    }
}
