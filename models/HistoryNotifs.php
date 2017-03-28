<?php

class HistoryNotifs {
    private $sBaseDir = null;
    private $arHistoryNames = [];
    
    public function __construct($sBaseDir = './data/notifs') {
        $this->sBaseDir = $sBaseDir;
        $this->fillHistoryList();
    }
    
    private function fillHistoryList() {
        $this->arHistoryNames = [];
        $oDirYear = dir($this->sBaseDir);
        
        while (false !== ($sDirItem = $oDirYear->read())) {
            $sDirItem = trim($sDirItem);
            if ($sDirItem != "." && $sDirItem != "..") {
                $this->arHistoryNames = array_merge($this->arHistoryNames, glob($this->sBaseDir . '/' . $sDirItem . '/*.json'));
            }
        }
        
        $oDirYear->close();
        
        return true;
    }
    
    public function getHistory() {
        return $this->arHistoryNames;
    }
    
    public function getNowIdx() {
        return array_search($this->getNow(), $this->arHistoryNames) ;
    }
    
    public function getNow() {
        return $this->sBaseDir . '/' . date('Y') . '/notif-' . date('Y-m-d') . '.json' ;
    }
    
    public function getPreviousIdx($sFullFile) {
        $iKey = array_search($sFullFile, $this->arHistoryNames);

        return ($iKey !== false && $iKey > 0) ? $iKey - 1 : false ;
    }
    
    public function getPrevious($sFullFile) {
        $iKey = $this->getPreviousIdx($sFullFile);

        return ($iKey !== false) ? $this->arHistoryNames[$iKey] : null ;
    }
    
    public function getNextIdx($sFullFile) {
        $iKey = array_search($sFullFile, $this->arHistoryNames);

        return ($iKey !== false && $iKey < count($this->arHistoryNames) - 1) ? $iKey + 1 : false ;
    }
    
    public function getNext($sFullFile) {
        $iKey = $this->getNextIdx($sFullFile);
        
        return ($iKey !== false) ? $this->arHistoryNames[$iKey] : null ;
    }
    
}
