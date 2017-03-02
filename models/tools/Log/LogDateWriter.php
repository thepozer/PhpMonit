<?php
namespace Tools\Log;

/**
 * Classe logger avec datation des lignes
 *
 */
class LogDateWriter {
	/**
	 * Décrit les niveaux de journalisation
	 */
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
    
    private $arLevels = array(
		self::EMERGENCY => 0,
		self::ALERT     => 1,
		self::CRITICAL  => 2,
		self::ERROR     => 3,
		self::WARNING   => 4,
		self::NOTICE    => 5,
		self::INFO      => 6,
		self::DEBUG     => 7,
    );
    
    private $hFileLog = null;
    private $iLogLevel = 3;
    
    public function __construct($hFileLog, $sLogLevel = self::ERROR) {
		$this->hFileLog  = $hFileLog;
		$this->iLogLevel = $this->arLevels[$sLogLevel];
	}
    
    public function setLogLevel($sLogLevel) {
		if (array_key_exists($sLogLevel, $this->arLevels)) {
			$this->iLogLevel = $this->arLevels[$sLogLevel];
		} else {
			throw new Exception("Not defined Log level ....");
		}
	}
    
    /**
     * Le système est inutilisable.
     *
     * @param string $sMessage
     * @param array $arContext
     * @return null
     */
    public function emergency($sMessage, array $arContext = array()) {
		$this->log(self::EMERGENCY, $sMessage, $arContext);
	}

    /**
     * Des mesures doivent être prises immédiatement.
     *
     * Exemple: Tout le site est hors service, la base de données est
     * indisponible, etc. Cela devrait déclencher des alertes par SMS et vous
     * réveiller.
     *
     * @param string $sMessage
     * @param array $arContext
     * @return null
     */
    public function alert($sMessage, array $arContext = array()) {
		$this->log(self::ALERT, $sMessage, $arContext);
	}

    /**
     * Conditions critiques.
     *
     * Exemple: Composant d'application indisponible, exception inattendue.
     *
     * @param string $sMessage
     * @param array $arContext
     * @return null
     */
    public function critical($sMessage, array $arContext = array()) {
		$this->log(self::CRITICAL, $sMessage, $arContext);
	}

    /**
     * Erreurs d'exécution qui ne nécessitent pas une action immédiate 
     * mais qui doivent normalement être journalisées et contrôlées.
     *
     * @param string $sMessage
     * @param array $arContext
     * @return null
     */
    public function error($sMessage, array $arContext = array()) {
		$this->log(self::ERROR, $sMessage, $arContext);
	}

    /**
     * Événements exceptionnels qui ne sont pas des erreurs.
     *
     * Exemple: Utilisation des API obsolètes, mauvaise utilisation d'une API,
     * indésirables élements qui ne sont pas nécessairement mauvais.
     *
     * @param string $sMessage
     * @param array $arContext
     * @return null
     */
    public function warning($sMessage, array $arContext = array()) {
		$this->log(self::WARNING, $sMessage, $arContext);
	}

    /**
     * Événements normaux mais significatifs.
     *
     * @param string $sMessage
     * @param array $arContext
     * @return null
     */
    public function notice($sMessage, array $arContext = array()) {
		$this->log(self::NOTICE, $sMessage, $arContext);
	}

    /**
     * Événements intéressants.
     *
     * Exemple: Connexion utilisateur, journaux SQL.
     *
     * @param string $sMessage
     * @param array $arContext
     * @return null
     */
    public function info($sMessage, array $arContext = array()) {
		$this->log(self::INFO, $sMessage, $arContext);
	}

    /**
     * Informations détaillées de débogage.
     *
     * @param string $sMessage
     * @param array $arContext
     * @return null
     */
    public function debug($sMessage, array $arContext = array()) {
		$this->log(self::DEBUG, $sMessage, $arContext);
	}

    /**
     * Logs avec un niveau arbitraire.
     *
     * @param mixed $level
     * @param string $sMessage
     * @param array $arContext
     * @return null
     */
    public function log($sLevel, $sMessage, array $arContext = array()) {
		if ($this->arLevels[$sLevel] <= $this->iLogLevel) {
			if (!fwrite($this->hFileLog, '[' . date('Y-m-d H:i:s O') . '] ' . $sLevel . ' : ' . $sMessage . PHP_EOL)) {
				throw new Exception("Can't Write in log file ....");
			}
		}
	}
}
