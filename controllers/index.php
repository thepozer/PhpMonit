<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'data/servers.php';

$oGlobalApp->get('/', function (Request $oRequest, Response $oResponse, $args) {
    $sStatusFile = 'data/status.json';
    
    $this->view->arStatus = json_decode(file_get_contents($sStatusFile), true);
    
    return $this->view->render($oResponse, 'index');
});

$oGlobalApp->get('/config', function (Request $oRequest, Response $oResponse, $args) use ($arServers) {
    $sStatusFile = 'data/status.json';
    
    $this->view->arServers = $arServers;
    
    return $this->view->render($oResponse, 'config');
});
