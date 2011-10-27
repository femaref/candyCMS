<?php

/**
 * Translate a string.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Helper;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use sfYaml;

require_once 'lib/symfony_yaml/sfYaml.php';

class I18n {

  /**
   * Read the language yaml and save information into session due to fast access.
   *
   * @access public
   * @param string $sLanguage language to load
   *
   */
  public function __construct($sLanguage) {
    if (empty($_SESSION['lang'])) {
			$sLanguageFile = 'languages/' . $sLanguage . '.language.yml';

			try {
				if (!isset($_SESSION['lang']) || empty($_SESSION['lang']))
					$_SESSION['lang'] = & sfYaml::load(file_get_contents($sLanguageFile));
			}
			catch (AdvancedException $e) {
				die('Unable to load language file.');
			}
		}
  }

  /**
	 * Return the language array.
	 *
	 * @access public
	 * @param string $sPart main part of the array to return string from
	 * @return array $_SESSION['lang'] session array with language data
	 *
	 */
	public function getArray($sPart = '') {
		return empty($sPart) ? $_SESSION['lang'] : $_SESSION['lang'][$sPart];
	}

	/**
	 * Get language as JSON
	 *
	 * @access public
	 * @return string JSON
	 *
	 */
	public function getJson() {
		return json_encode($this->getArray('javascript'));
	}

  /**
   * Get a specific string.
   *
   * @access public
   * @param string $sLanguagePart language part we want to load. Separated by dots.
   * @return string $mTemp
   *
   */
  public function get($sLanguagePart) {
    $aLang = preg_split("/[\s]*[.][\s]*/", $sLanguagePart);

    $mTemp = $_SESSION['lang'];
    foreach ($aLang as $sPart) {
      if (array_key_exists($sPart, $mTemp)) {
        $mTemp = & $mTemp[$sPart];
      }
    }

    try {
      if (is_string($mTemp))
        return $mTemp;
    }
    catch (AdvancedException $e) {
      die('No such translation: ' . $mTemp);
    }
  }
}