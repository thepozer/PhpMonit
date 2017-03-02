<?php
/**
 * Fichier des fonctions utilitaires
 */

	/** Insère la variable dans un objet, le transforme en JSON et l'affiche sur la sortie standard
	 * 
	 * En cas d'erreur de génération, le code d'erreur et le message associé est affiché
	 * 
	 * @param Mixed   $mContent  Contenu à retourner en JSON
	 * @param Integer $iError    Code d'erreur à retourner (0 par défaut)
	 * @param Integer $sErrorMsg Message d'erreur à retourner (vide par defaut)
	 */
	function _returnEnclosedJson($mContent, $iError = 0, $sErrorMsg = '') {
		if ($iError) {
			$sReturnJson = json_encode(array('content' => null, 'error' => $iError, 'message' => $sErrorMsg));
		} else {
			$sReturnJson = json_encode(array('content' => $mContent, 'error' => 0, 'message' => ''));
		}
		
		if (json_last_error() != 0) {
			$sReturnJson = json_encode(array('content' => null, 'error' => json_last_error(), 'message' => json_last_error_msg()));
		}
		echo $sReturnJson;
	}

	/** Récupère et nettoye la valeur d'une variable de type String
	 * 
	 * @param String $sRawValue Valeur à filtrer
	 * @return String Valeur filtrée
	 */
	function _filterVarString($sRawValue) {
		return filter_var($sRawValue, FILTER_SANITIZE_STRING, 
					array('flags' => array(
						FILTER_FLAG_NO_ENCODE_QUOTES, 
						FILTER_FLAG_STRIP_LOW, 
						FILTER_FLAG_STRIP_HIGH))
				);
	}

	/** Récupère et nettoye la valeur d'une variable de type Integer
	 * 
	 * @param String $sRawValue Valeur à filtrer
	 * @param Integer $iDefault Valeur par défaut
	 * @return Integer Valeur filtrée
	 */
	function _filterVarInteger($sRawValue, $iDefault = -1) {
		return (int)filter_var($sRawValue, FILTER_VALIDATE_INT,
					array('options' => array('default' => $iDefault))
				);
	}

