<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$oGlobalApp->get('/', function (Request $oRequest, Response $oResponse, $args) {
    $sStatusFile = 'data/status.json';
    $sNotifsFile = 'data/notifs.json';
    
    $this->view->arStatus = json_decode(file_get_contents($sStatusFile), true);
    //debug("get / - Content sNotifFile({$sNotifFile}) : " . file_get_contents($sNotifFile));
    $this->view->arNotifs = json_decode(file_get_contents($sNotifsFile), true);
    
    return $this->view->render($oResponse->withHeader('Refresh', '300'), 'index');
});

$oGlobalApp->get('/config', function (Request $oRequest, Response $oResponse, $args) use ($arServers) {
    $sStatusFile = 'data/status.json';
    
    $this->view->arServers = $arServers;
    
    return $this->view->render($oResponse, 'config');
});
