<?php

/**
 * Fichier d'initialisation de l'objet Global Slim
 */

session_start();

require_once 'controllers/_config.php';

require_once 'controllers/_log.php';

// Create container
$oContainer = new \Slim\Container();
// Register component on container
$oContainer['view'] = function ($c) {
    return new \Tools\View\SimpleView('views/');
};
$oContainer['log'] = $oLog;

/**
 * Charge le fichier de configuration de dev si il existe, celui de prod sinon
 */
if ($bDevMode) {
	$oContainer['settings']['displayErrorDetails'] = true;
}

$oGlobalApp = new \Slim\App($oContainer);

/**
 * Inclusion du fichier utilitaire
 */
require_once 'controllers/_utils.php';

/**
 * Inclusion des fichiers de controller (fichier ne commencant pas par un '_'
 */
foreach (glob('controllers/[^_]*.php') as $sCntrlFile) {
    include_once $sCntrlFile;
}

$oGlobalApp->run();
