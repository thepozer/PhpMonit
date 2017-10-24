<?php

class TestCheck {
    private $arServers = [];
    private $arDirs = [];
    private $iCurrTime = 0;
    
    public function __construct($arServers) {
        $this->arServers = $arServers;
    }
    
    public function genCurrentTime() {
        $this->iCurrTime = time();
        $this->arDirs['check_name'] = 'data/status/' . date('Y', $this->iCurrTime) . '/' . date('m', $this->iCurrTime) . '/' . date('d', $this->iCurrTime) . '/' . date('H', $this->iCurrTime);
        $this->arDirs['check_current_name'] = $this->arDirs['check_name'] . '/' . date('Y-m-d-Hi', $this->iCurrTime);
        $this->arDirs['notif_name'] = 'data/notifs/' . date('Y', $this->iCurrTime);
        
        if (!is_dir($this->arDirs['check_name'])) {
            mkdir($this->arDirs['check_name'], 0777, true);
        }

        if (!is_dir($this->arDirs['check_current_name'])) {
            mkdir($this->arDirs['check_current_name'], 0777, true);
        }
        
        if (!is_dir($this->arDirs['notif_name'])) {
            mkdir($this->arDirs['notif_name'], 0777, true);
        }
    }
    
    /** 
     *  Générations des résultats par hosts 
     */
    public function doAllChecks() {
        foreach($this->arServers as $sHostName => $arServer) {
            $sCheckCurrentFileName = $this->arDirs['check_current_name'] . '/status-' . $sHostName . '.json';
            $sNotifCurrentFileName = $this->arDirs['check_current_name'] . '/notif-' . $sHostName . '.json';
            debug("Looking for {$sHostName}");

            $oHost = Host::getInstance($sHostName, $arServer);
            $arLocalResult = ['#hostname' => $sHostName, '#status' => 'OK'];
            $arNotifications = [];

            $oCheck = new \Check\Ping();
            $arRet = $oCheck->check($oHost, []);
            $arLocalResult = array_merge($arLocalResult, $arRet);
            $arService = $arRet['ping'];

            if ($arService['status'] === 'KO') {
                $arLocalResult['#status'] = 'KO' ;

                $arNotif = $arService;
                $arNotif['name'] = 'Ping';
                $arNotif['date'] = date('Y-m-d H:i:s', $this->iCurrTime);
                $arNotif['host'] = $arServer['host'];
                $arNotifications[] = $arNotif;
            } else {
                foreach($arServer['services'] as $sServiceName => $arParams) {
                    try {
                        $sCheckName = "\\Check\\" . ucfirst($sServiceName);
                        $oCheck = new $sCheckName();
                        $arRet = $oCheck->check($oHost, $arParams);

                        $arLocalResult = array_merge($arLocalResult, $arRet);

                        foreach($arRet as $arService) {
                            if ($arService['status'] === 'KO') {
                                $arLocalResult['#status'] = 'KO' ;

                                $arNotif = $arService;
                                $arNotif['name'] = $sServiceName;
                                $arNotif['date'] = date('Y-m-d H:i:s', $this->iCurrTime);
                                $arNotif['host'] = $arServer['host'];
                                $arNotifications[] = $arNotif;
                            }

                        }
                    } catch (Exception $e) {
                        error("Exception : $e");
                    }
                }
            }

            $sJsonLocalData = json_encode($arLocalResult);
            file_put_contents($sCheckCurrentFileName, $sJsonLocalData);
            if (count($arNotifications) > 0) {
                $sJsonLocalNotif = json_encode($arNotifications);
                file_put_contents($sNotifCurrentFileName, $sJsonLocalNotif);
            }
        }
    }
    
    /**
     * Regroupement des résultats par hosts 
     */
    public function parseCheckStatus() {
        $sCheckFileName = $this->arDirs['check_name'] . '/status-' . date('Y-m-d-Hi', $this->iCurrTime) . '.json';
        $sNotifFileName = $this->arDirs['notif_name'] . '/notif-' . date('Y-m-d', $this->iCurrTime) . '.json';

        $arResults = ['#General' => ['date' => date('Y-m-d H:i:s', $this->iCurrTime)]];
        $arNotifications = [];

        foreach(glob($this->arDirs['check_current_name'] . '/status-*.json') as $sStatusFileName) {
            $arJsonStatus = json_decode(file_get_contents($sStatusFileName), true);
            $sHostName = $arJsonStatus['#hostname'];
            unset($arJsonStatus['#hostname']);
            $arResults[$sHostName] = $arJsonStatus;
            unlink($sStatusFileName);
        }

        foreach(glob($this->arDirs['check_current_name'] . '/notif-*.json') as $sNotifFileName) {
            $arJsonNotif = json_decode(file_get_contents($sNotifFileName), true);
            $arNotifications = array_merge($arNotifications, $arJsonNotif);
            unlink($sNotifFileName);
        }

        rmdir($this->arDirs['check_current_name']);

        //debug("check.php : Status  : " . print_r($arResults, true));
        $sJsonData = json_encode($arResults);
        //debug("check.php : JSON encode (" . json_last_error() . ") : " . json_last_error_msg());
        //debug("check.php : JSON data :\n" . $sJsonData);
        file_put_contents($sCheckFileName, $sJsonData);
        //debug("check.php : Write Status timed file : Ok");
        file_put_contents('data/status.json', $sJsonData);
        //debug("check.php : Write Status current file : Ok");

        if (count($arNotifications) > 0 || !file_exists($sNotifFileName)) {
        //debug("sNotifDirName  : '{$this->arDirs['notif_name']}'");
        //debug("sNotifFileName : '{$sNotifFileName}'");

            $arFullNotifications = (file_exists($sNotifFileName)) ? json_decode(file_get_contents($sNotifFileName), true) : [];
            if (!$arFullNotifications) {
                $arFullNotifications = [];
            }
        //debug("check.php : Full Notifications Before : " . print_r($arFullNotifications, true));
            foreach($arNotifications as $arNotif) {
                array_unshift($arFullNotifications, $arNotif);
            }
        //debug("check.php : Full Notifications After  : " . print_r($arFullNotifications, true));
            file_put_contents($sNotifFileName, json_encode($arFullNotifications));
            file_put_contents('data/notifs.json', json_encode($arFullNotifications));
        }
    }
}
