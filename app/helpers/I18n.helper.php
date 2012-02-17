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

require PATH_STANDARD . '/lib/symfony_yaml/sfYaml.php';

class I18n {

  /**
   * Read the language yaml and save information into session due to fast access.
   *
   * @access public
   * @param string $sLanguage language to load
   *
   */
  public function __construct($sLanguage) {
    if (!isset($_SESSION['lang'])) {
      $sLanguageFile = PATH_STANDARD . '/languages/' . $sLanguage . '.language.yml';

      # Remove mistakenly set cookie to avoid exceptions.
      if (!file_exists($sLanguageFile))
        $_COOKIE['default_language'] = 'en';

      $_SESSION['lang'] = & sfYaml::load(file_get_contents($sLanguageFile));
    }
  }

  /**
	 * Return the language array.
	 *
   * @static
	 * @access public
	 * @param string $sPart main part of the array to return string from
	 * @return array $_SESSION['lang'] session array with language data
	 *
	 */
	public static function getArray($sPart = '') {
		return empty($sPart) ? $_SESSION['lang'] : $_SESSION['lang'][$sPart];
	}

	/**
	 * Get language as JSON
	 *
	 * @static
	 * @access public
	 * @return string JSON
	 *
	 */
	public static function getJson() {
		return json_encode(self::getArray('javascript'));
	}

  /**
   * Get a specific language string.
   *
   * @static
   * @access public
   * @param string $sLanguagePart language part we want to load. Separated by dots.
   * @return string $mTemp
   *
   */
  public static function get($sLanguagePart) {
    $aLang = preg_split("/[\s]*[.][\s]*/", $sLanguagePart);

		if(isset($_SESSION['lang'])) {
			$mTemp = $_SESSION['lang'];
			foreach ($aLang as $sPart) {
				if(!is_string($mTemp)) {
					if (array_key_exists($sPart, $mTemp)) {
						$mTemp = & $mTemp[$sPart];
					}
				}
			}

			try {
				if (is_string($mTemp))
					return $mTemp;
			}
			catch (AdvancedException $e) {
				die('No such translation: ' . $e->getMessage());
			}
		}
  }

	/**
	 * Unset the language saved in the session.
	 *
	 * @access public
	 *
	 */
	public function unsetLanguage() {
		unset($_SESSION['lang']);
	}
}