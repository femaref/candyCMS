<?php

/**
 * Recaptcha Plugin.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Hauke Schade <http://hauke-schade.de>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Plugin\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\SmartySingleton as SmartySingleton;
use CandyCMS\Helper\I18n as I18n;

if (!defined('SHOW_CAPTCHA'))
  define('SHOW_CAPTCHA', MOBILE === false && WEBSITE_MODE !== 'test');

final class Recaptcha {

	/**
	 * ReCaptcha public key.
	 *
	 * @var string
	 * @access protected
	 * @see config/Plugins.inc.php
   *
	 */
	protected $_sPublicKey = PLUGIN_RECAPTCHA_PUBLIC_KEY;

	/**
	 * ReCaptcha private key.
	 *
	 * @var string
	 * @access protected
	 * @see config/Plugins.inc.php
   *
	 */
	protected $_sPrivateKey = PLUGIN_RECAPTCHA_PRIVATE_KEY;

	/**
	 * ReCaptcha object.
	 *
	 * @var object
	 * @access protected
   *
	 */
	protected $_oResponse = '';

	/**
	 * Provided ReCaptcha error message.
	 *
	 * @var string
	 * @access protected
   *
	 */
	protected $_sError = '';

  /**
   * Identifier for template replacements
   *
   * @var constant
   *
   */
  const IDENTIFIER = 'recaptcha';

  /**
   *
   * @var static
	 * @access private
   *
   */
  private static $_oInstance = null;

  /**
   * Error Message of last captcha check
   *
   * @var string
   * @access private
   */
  private $_sErrorMessage = '';

  /**
   * Get the Smarty instance
   *
	 * @static
	 * @access public
   * @return object self::$_oInstance Recaptcha instance that was found or generated
   *
   */
  public static function getInstance() {
    if (self::$_oInstance === null)
      self::$_oInstance = new self();

    return self::$_oInstance;
  }

  /**
   * @todo documentation
   */
  public function __construct() {
    require PATH_STANDARD . '/vendor/recaptcha/recaptchalib.php';
  }

	/**
	 * Get The HTML-Code for the Recaptcha Form.
	 *
	 * @access public
	 * @return string HTML
	 *
	 */
  public function show(&$aRequest, &$aSession) {
    $sTemplateDir   = Helper::getPluginTemplateDir('recaptcha', 'show');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'show');

    $oSmarty = SmartySingleton::getInstance();
    $oSmarty->setTemplateDir($sTemplateDir);
    # no caching for this very dynamic form
    $oSmarty->setCaching(SmartySingleton::CACHING_OFF);

    $oSmarty->assign('WEBSITE_MODE', WEBSITE_MODE);
    $oSmarty->assign('MOBILE', MOBILE);
    $oSmarty->assign('_captcha_', recaptcha_get_html($this->_sPublicKey, $this->_sError));

    if ($this->_sErrorMessage)
      $oSmarty->assign('_error_', $this->_sErrorMessage);

    return $oSmarty->fetch($sTemplateFile);
  }

  /**
   * Check if the entered captcha is correct.
   *
   * @access public
   * @return boolean status of recpatcha check
   *
   */
  public function checkCaptcha($aRequest) {
    if (isset($aRequest['recaptcha_response_field'])) {
      $this->_oRecaptchaResponse = recaptcha_check_answer (
              $this->_sPrivateKey,
              $_SERVER['REMOTE_ADDR'],
              $aRequest['recaptcha_challenge_field'],
              $aRequest['recaptcha_response_field']);

      if ($this->_oRecaptchaResponse->is_valid) {
        $this->_sErrorMessage = '';
        return true;
      }

      else {
        $this->_sErrorMessage = I18n::get('error.captcha.incorrect');
        return Helper::errorMessage(I18n::get('error.captcha.incorrect'));
      }
    }
    else {
      $this->_sErrorMessage = I18n::get('error.captcha.loading');
      return Helper::errorMessage(I18n::get('error.captcha.loading'));
    }
  }

}