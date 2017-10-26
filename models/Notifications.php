<?php

class Notifications {
    private $arNotifTargets = ['emails' => [], 'urls' => []];
    
    public function __construct($arNotifTargets) {
        $this->arNotifTargets = $arNotifTargets;
    }
    
    public function sendAllNotifs($arListNotifs) {
        $this->sendEmails($arListNotifs);
        $this->sendUrls($arListNotifs);
    }
    
    public function sendEmails($arListNotifs) {
        $sSubject = '[PHP-Monit] Notifications';
        
        $sTo = '';
        foreach($this->arNotifTargets['emails'] as $sEmail => $sName) {
            $sTo .= ($sTo != '') ? ", {$sName} <{$sEmail}>" : "{$sName} <{$sEmail}>";
        }
//debug(__METHOD__ . " - sTo : '{$sTo}'");

        $sMessage = "Notification list : \n\n";
        foreach($arListNotifs as $arNotif) {
            $sMessage .= "Host : {$arNotif['host']} - Date : {$arNotif['date']}\n";
            $sMessage .= "[{$arNotif['name']}] {$arNotif['description']} : {$arNotif['value']} ({$arNotif['status']})\n";
            $sMessage .= strip_tags($arNotif['human']) . "\n\n";
        }
//debug(__METHOD__ . " - sMessage : \n{$sMessage}");
        
        $sMessage = strtr($sMessage, ["\n" => "\r\n"]);
        return mail($sTo, $sSubject, $sMessage);
    }
    
    public function sendUrls($arListNotifs) {
        return true;
    }
    
}