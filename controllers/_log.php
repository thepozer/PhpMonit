<?php

// ***** Fonctions de logs
$sFilePart = date('Y-m-d');
use \Tools\Log\LogDateWriter as LogDateWriter;
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
    global $oLog, $bDevMode;
    
    $oLog->debug($sMessage);
    if ($bDevMode) {
        echo "{$sMessage}\n";
    }
}

/**
    * Fonction de logging gloable - niveau error
    * 
    * @global Object $oGlobalApp Objet Application Slim
    * @param String $sMessage Message à enregistrer
    */
function error ($sMessage) {
    global $oLog;
    
    $oLog->error($sMessage);
}
