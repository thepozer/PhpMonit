<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class IndexController {
    protected $oContainer;

    /**
     * Contructeur
     * 
     * @param ContainerInterface $oContainer Contenaire de l'application Slim'
     */
    public function __construct(ContainerInterface $oContainer) {
        $this->oContainer = $oContainer;
    }

    public function index($oRequest, $oResponse, $args) {
        $sStatusFile = 'data/status.json';
        $sNotifsFile = 'data/notifs.json';

        $this->oContainer->view->arStatus = json_decode(file_get_contents($sStatusFile), true);
        //debug("get / - Content sNotifFile({$sNotifFile}) : " . file_get_contents($sNotifFile));
        $this->oContainer->view->arNotifs = json_decode(file_get_contents($sNotifsFile), true);

        return $this->oContainer->view->render($oResponse->withHeader('Refresh', '300'), 'index');
    }

    public function config($oRequest, $oResponse, $args) {
        $this->oContainer->view->arServers = $this->oContainer['arServers'];

        return $this->oContainer->view->render($oResponse, 'config');
    }

    public function history($oRequest, $oResponse, $args) {
        $oHistoryNotifs = new HistoryNotifs();


        $arHistoryFiles = $oHistoryNotifs->getHistory();
        $this->oContainer->view->arHistoryFiles = $arHistoryFiles;

        $sFileNow = $oHistoryNotifs->getNow();
        $sFileCurrent = $oRequest->getParam('f', $sFileNow);

        $this->oContainer->view->sFileNow  = $sFileCurrent;
        $this->oContainer->view->sFilePrev = $oHistoryNotifs->getPrevious($sFileCurrent);
        $this->oContainer->view->sFileNext = $oHistoryNotifs->getNext($sFileCurrent);

        $this->oContainer->view->arNotifs = json_decode(file_get_contents($sFileCurrent), true);

        return $this->oContainer->view->render($oResponse, 'history');
    }
}

if($oGlobalApp) {
    $oGlobalApp->get('/',        \IndexController::class . ':index');
    $oGlobalApp->get('/config',  \IndexController::class . ':config');
    $oGlobalApp->get('/history', \IndexController::class . ':history');
}
