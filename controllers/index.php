<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$oGlobalApp->get('/', function (Request $oRequest, Response $oResponse, $args) {
    return $this->view->render($oResponse, 'index');
});
