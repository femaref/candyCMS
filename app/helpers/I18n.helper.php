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

  protected $_aYaml;

  public function __construct($sLanguageFile) {
    try {
      $this->_aYaml = sfYaml::load(file_get_contents($sLanguageFile));
    }
    catch (AdvancedException $e) {
      die('Unable to load language file.');
    }
  }

  public static function get() {
    return parent::$_aYaml;
  }
}