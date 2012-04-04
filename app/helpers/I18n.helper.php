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

class I18n {

  /**
   *
   * holds all translations
   *
   * @var static
	 * @access private
   *
   */
  private static $_aLang = null;

  /**
   *
   * holds the object
   *
   * @var static
	 * @access private
   *
   */
  private static $_oObject = null;

  /**
   * Read the language yaml and save information into session due to fast access.
   *
   * @access public
   * @param string $sLanguage language to load
   * @param array $aSession the session object, if given save the translations in S_SESSION['lang']
   *
   */
  public function __construct($sLanguage, &$aSession = null) {
    if ($aSession)
      $this->_aSession = $aSession;

    I18n::$_oObject = $this;

    if (!isset(I18n::$_aLang) || WEBSITE_MODE == 'development' || WEBSITE_MODE == 'test') {
			require_once PATH_STANDARD . '/vendor/symfony_yaml/sfYaml.php';
      $sLanguageFile = PATH_STANDARD . '/languages/' . $sLanguage . '.language.yml';

      # Remove mistakenly set cookie to avoid exceptions.
      if (!file_exists($sLanguageFile))
        $_COOKIE['default_language'] = 'en';

      if (WEBSITE_MODE == 'development' || WEBSITE_MODE == 'test' || !isset($aSession['lang'])) {
        I18n::$_aLang = & sfYaml::load(file_get_contents($sLanguageFile));
        if ($aSession != null)
          $aSession['lang'] = & I18n::$_aLang;
      }
      else
        I18n::$_aLang = & $aSession['lang'];
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
		return !$sPart ? I18n::$_aLang : I18n::$_aLang[$sPart];
	}

	/**
	 * Get language as JSON for JavaScript.
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
    if (isset( I18n::$_aLang)) {
      $mTemp =  I18n::$_aLang;
      foreach (explode('.', $sLanguagePart) as $sPart) {
        if (!is_string($mTemp)) {
          if (array_key_exists($sPart, $mTemp)) {
            $mTemp = & $mTemp[$sPart];
          }
        }
      }

      # do we have other parameters?
      $iNumArgs = func_num_args();
      if ($iNumArgs > 1) {
        # use sprintf
        $aArgs = func_get_args();
        array_shift($aArgs);
        $mTemp = vsprintf($mTemp, $aArgs);
      }

      try {
        return is_string($mTemp) ? (string) $mTemp : '';
      }
      catch (AdvancedException $e) {
        die('No such translation: ' . $e->getMessage());
      }
    }
  }

  /**
   * Unset the language saved in the session.
   *
   * @static
   * @access public
   *
   */
  public static function unsetLanguage() {
    I18n::$_aLang = null;
    if (I18n::$_oObject != null)
      unset(I18n::$_oObject->_aSession['lang']);
  }
}