<?php

class CheckMem {
    private $arParams = ['mem_minimal' => '10', 'swap_minimal' => '90'];

    public function __construct($arParams = null) {
        if (is_array($arParams)) {
            $this->arParams = array_merge($this->arParams, $arParams);
        }
    }

    public function check($oHost, $arParams) {
        $arParams = array_merge($this->arParams, $arParams);
        $arRet = [
            'mem' => [
                'status' => 'KO', 
                'value' => -1,
                'human' => '-',
                'description' => 'Mémoire physique disponible sur la machine',
                'unit' => 'Octets'
            ],
            'swap' => [
                'status' => 'KO', 
                'value' => -1,
                'human' => '-',
                'description' => 'Mémoire swap disponible sur la machine',
                'unit' => 'Octets'
            ]];

        $sCmd = 'cat /proc/meminfo';

        $sRet = $oHost->executeRemoteCmd($sCmd);
        debug(__METHOD__ . " - sRet : '{$sRet}'");

        if ($sRet) {
            $arElements = ['MemTotal', 'MemFree', 'Buffers', 'Cached', 'SwapTotal', 'SwapFree'];
            $arLines = explode("\n", strtr($sRet, ["\r" => "\n", "\n\n" => "\n"]));
            $arInfo = [];
            foreach($arLines as $sLine) {
                $arItems = explode(':', $sLine, 2);
                if (in_array($arItems[0], $arElements)) {
                    $arInfo[$arItems[0]] = $this->parseUnit(trim($arItems[1]));
                }
            }
            debug(__METHOD__ . " - arInfo : " . print_r($arInfo, true));
            
            // Check Mem
            $iMemLimit = floor($arInfo['MemTotal'] * ($arParams['mem_minimal'] / 100));
            $iMemFree = $arInfo['MemFree'] + $arInfo['Buffers'] + $arInfo['Cached'];
            $arRet['mem']['status'] = (($iMemFree >= $iMemLimit) ? 'OK' : 'KO');
            $arRet['mem']['value'] = $iMemFree;
            $arRet['mem']['human'] = "Memory Free : {$iMemFree} (" . floor($iMemFree / ($arInfo['MemTotal'] / 100)) . '%)';

            // Check Swap
            if ($arInfo['SwapTotal'] > 0) {
                $iSwapLimit = floor($arInfo['SwapTotal'] * ($arParams['swap_minimal'] / 100));
                $iSwapFree = $arInfo['SwapFree'];
                $arRet['swap']['status'] = (($iSwapFree >= $iSwapLimit) ? 'OK' : 'KO');
                $arRet['swap']['value'] = $iSwapFree;
                $arRet['swap']['human'] = "Swap Free : {$iSwapFree} (" . floor($iSwapFree / ($arInfo['SwapTotal'] / 100)) . '%)';
            } else {
                $arRet['swap']['status'] = ((!$arParams['need_swap']) ? 'OK' : 'KO');
                $arRet['swap']['value'] = 0;
                $arRet['swap']['human'] = "No swap activated";
            }
        }

        return $arRet;
    }

    private function parseUnit($sValue) {
        $iRetValue = $sValue;

        $arValue = explode(' ', $sValue, 2);
        $iValue = intval($arValue[0]);
        $sUnit = trim($arValue[1]);

        switch ($sUnit) {
            case 'kB':
                $iRetValue = $iValue * 1024;
                break;
            default : 
                $iRetValue = $iValue;
                break;
        }

        return $iRetValue;
    }

    private function dump ($data) {
        // Init
        $hexi   = '';
        $ascii  = '';
        $dump   = '';
        $offset = 0;
        $len    = strlen($data);

        // Upper or lower case hexadecimal
        $x = ($this->uppercase === false) ? 'x' : 'X';

        // Iterate string
        for ($i = $j = 0; $i < $len; $i++)
        {
            // Convert to hexidecimal
            $hexi .= sprintf("%02$x ", ord($data[$i]));

            // Replace non-viewable bytes with '.'
            if (ord($data[$i]) >= 32) {
                $ascii .= $data[$i];
            } else {
                $ascii .= '.';
            }

            // Add extra column spacing
            if ($j === 7 && $i !== $len - 1) {
                $hexi  .= ' ';
                $ascii .= ' ';
            }

            // Add row
            if (++$j === 16 || $i === $len - 1) {
                // Join the hexi / ascii output
                $dump .= sprintf("%04$x  %-49s  %s", $offset, $hexi, $ascii);

                // Reset vars
                $hexi   = $ascii = '';
                $offset += 16;
                $j      = 0;

                // Add newline
                if ($i !== $len - 1) {
                    $dump .= "\n";
                }
            }
        }

        // Finish dump
        $dump .= "\n";

        return $dump;
    }
}
