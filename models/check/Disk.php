<?php
namespace Check;

class Disk {
    private $arParams = ['minimal' => 10];

    public function __construct($arParams = null) {
        if (is_array($arParams)) {
            $this->arParams = array_merge($this->arParams, $arParams);
        }
    }

    public function check($oHost, $arParams) {
        $arMountPoint = $arParams['mounts'];
        $arParams = array_merge($this->arParams, $arParams['params']);
        $arRet = [];
        $arDefaultRet = [
            'status' => 'KO', 
            'value' => -1,
            'human' => '-',
            'description' => 'Espace libre sur chaque montage',
            'unit' => 'octets'
        ];

        $sCmd = 'df -B1 --sync ' . implode (' ', $arMountPoint);

        $sRet = $oHost->executeRemoteCmd($sCmd);
//debug(__METHOD__ . " - sRet : '{$sRet}'");

        if ($sRet) {
            $arLines = explode ("\n", $sRet);
            unset($arLines[0]);
            foreach ($arLines as $sLine) {
                if (trim($sLine) !== '') {
                    list(, $iSizeTotal, , $iSizeAvail, , $sMount) = explode(' ', preg_replace('#(\s+)#', ' ', $sLine), 6);
                    
                    $fPercentSize  = $iSizeAvail / $iSizeTotal * 100;
                    $sDiskName = 'disk: ' . $sMount;
//debug(__METHOD__ . " - sDiskName : '{$sDiskName}'");
                    
                    $arRet[$sDiskName]  = $arDefaultRet;
                    $arRet[$sDiskName]['status'] = ($fPercentSize > $arParams['minimal']) ? 'OK' : 'KO' ;
                    $arRet[$sDiskName]['value'] = $iSizeAvail;
                    $arRet[$sDiskName]['human'] = "Disponible : " . number_format($iSizeAvail / 1024, 0, '.', ' ') . "K (" . number_format($fPercentSize, 2, '.', ' ') . "%) - Total : " . number_format($iSizeTotal / 1024, 0, '.', ' ') . "K";
                }
            }
            
        }

        return $arRet;
    }

}
