<?php

class CheckProcess {
    private $arParams = ['params' => ['ErrorOnZombie' => true], 'process' => []];

    public function __construct($arParams = null) {
        if (is_array($arParams)) {
            $this->arParams = array_merge($this->arParams, $arParams);
        }
    }

    public function check($oHost, $arParams) {
        $arParams = array_merge($this->arParams, $arParams);
        $arRet = [];
        $arDefaultRet = [
            'status' => 'OK', 
            'value' => -1,
            'human' => '',
            'description' => 'Information sur le processus : ',
            'unit' => '-'
        ];

        $sCmd = 'ps -eo pid,s,user,comm,etimes,args --sort pid';

        $sRet = $oHost->executeRemoteCmd($sCmd);
//debug(__METHOD__ . " - sRet : '{$sRet}'");

        if ($sRet) {
            $arProcessList = [];
            $iFoundZombie = 0;
            $arLines = explode ("\n", $sRet);
            unset($arLines[0]);
            foreach ($arLines as $sLine) {
                if (trim($sLine) !== '') {
                    list($iPid, $sStatus, $sUser, $sCmd, $iTimeStarted, $sCmdFull) = explode(' ', preg_replace('#(\s+)#', ' ', trim($sLine)), 6);
if ($iPid == '') {
    debug(__METHOD__ . " - sLine : '" . trim($sLine) . "'");
    debug(__METHOD__ . " - iPid : '{$iPid}' - sStatus : '{$sStatus}' - sUser : '{$sUser}' - sCmd : '{$sCmd}' - iTimeStarted : '{$iTimeStarted}' - sCmdFull : '{$sCmdFull}'");
}

                    if ($sStatus === 'Z') {
                        $iFoundZombie ++;
                    }
                    if (!isset($arProcessList[$sCmd])) {
                        $arProcessList[$sCmd] = [
                            'count' => 0,
                            'pids' => [],
                            'users' => [],
                        ];
                    }
                    
                    $arProcessList[$sCmd]['count'] ++;
                    $arProcessList[$sCmd]['pids'][] = $iPid;
                    $arProcessList[$sCmd]['users'][$sUser] = $sUser;
                }
            }
            
/*
'process' => [
    'postgres' => ['min' => 1, 'max' => 15, 'user' => 'postgres'],
    'mysqld_safe' => ['min' => 1, 'user' => 'mysql'], 
    'php-fpm' => ['min' => 1, 'max' => 5, 'user' => 'www-data'],
    'nginx' => ['min' => 1, 'max' => 5, 'user' => 'www-data'],
    'fail2ban-server' => ['min' => 1, 'user' => 'root'],
]
*/
            foreach($arParams['process'] as $sProcessName => $arProcessParams) {
                $sRetName = 'process: ' . $sProcessName;
//debug(__METHOD__ . " - sRetName : '{$sRetName}'");
                
                $arRet[$sRetName] = $arDefaultRet;
                $arRet[$sRetName]['description'] .= $sProcessName;
                if (isset($arProcessList[$sProcessName])) {
                    $arRet[$sRetName]['value'] = $arProcessList[$sProcessName]['count'];
                    
                    if (array_key_exists('min', $arProcessParams) && $arRet[$sRetName]['value'] < $arProcessParams['min']) {
                        $arRet[$sRetName]['status'] = 'KO';
                        $arRet[$sRetName]['human'] .= "<li>Il n'y a pas assez de processus {$sProcessName}.</li>";
                    }
                    if (array_key_exists('max', $arProcessParams) && $arRet[$sRetName]['value'] > $arProcessParams['max']) {
                        $arRet[$sRetName]['status'] = 'KO';
                        $arRet[$sRetName]['human'] .= "<li>Il y a trop de processus {$sProcessName}.</li>";
                    }
                    if (array_key_exists('user', $arProcessParams) && !in_array($arProcessParams['user'], $arProcessList[$sProcessName]['users'])) {
                        $arRet[$sRetName]['status'] = 'KO';
                        $arRet[$sRetName]['human'] .= "<li>Le process {$sProcessName} ne tourne pas sous le bon utilisateur : '{$arProcessParams['user']}' not in ('" . implode("', '", $arProcessList[$sProcessName]['users']) . "') .</li>";
                    }
                    
                    if ($arRet[$sRetName]['status'] = 'OK') {
                        $arRet[$sRetName]['human'] = "<li>Le processus {$sProcessName} est présent {$arRet[$sRetName]['value']} fois sous le(s) utilisateur(s) suivant(s) : '" . implode("', '", $arProcessList[$sProcessName]['users']) . "'.</li>";
                    }
                } else {
                    $arRet[$sRetName]['status'] = 'KO';
                    $arRet[$sRetName]['value'] = 0;
                    $arRet[$sRetName]['human'] = "<li>Le processus {$sProcessName} est introuvable !!!</li>";
                }
                
                $arRet[$sRetName]['human'] = "<ul>{$arRet[$sRetName]['human']}</ul>";
            }
            
            $arRet['zombie'] = $arDefaultRet;
            $arRet['zombie']['status'] = ($iFoundZombie > 1 && $arParams['params']['ErrorOnZombie']) ? 'KO' : 'OK' ;
            $arRet['zombie']['value'] = $iFoundZombie;
            $arRet['zombie']['human'] = "Il y a {$iFoundZombie} processus zombie dans le système</li>";
            $arRet['zombie']['description'] = "Nombre de processus zombie dans le système</li>";
        }

        return $arRet;
    }

}
