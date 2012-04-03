<?php

/**
 * Create or destroy a session.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Controller\Main as Main;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Plugin\Controller\FacebookCMS as FacebookCMS;
use CandyCMS\Plugin\Controller\Recaptcha as Recaptcha;

class Sessions extends Main {

  /**
   * Route to right action.
   *
   * @access public
   * @return string HTML
   *
   */
  public function show() {
    if (!isset($this->_aRequest['action']))
      $this->_aRequest['action'] = 'show';

    switch ($this->_aRequest['action']) {

      case 'password':

        $this->setTitle(I18n::get('sessions.password.title'));
        $this->setDescription(I18n::get('sessions.password.description'));
        return $this->resendPassword();

        break;

      case 'verification':

        $this->setTitle(I18n::get('sessions.verification.title'));
        $this->setDescription(I18n::get('sessions.verification.description'));
        return $this->resendVerification();

        break;

      default:
      case 'show':

        return Helper::redirectTo('/' . $this->_aRequest['controller'] . '/create');

        break;
    }
  }

  /**
   * Create a session or show template instead.
   * We must override the main method due to a diffent required user right.
   *
   * @access public
   * @return string HTML content
   *
   */
  public function create() {
    if ($this->_aSession['user']['role'] > 0)
      return Helper::errorMessage('', '/');

    else
      return isset($this->_aRequest['create_sessions']) ? $this->_create() : $this->_showFormTemplate();
  }

  /**
   * Create a session.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, create session.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create() {
    $this->_setError('email');
    $this->_setError('password');

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->create() === true) {
      # clear the cache for users, since a new session updates some users last login date
      $this->oSmarty->clearCacheForController('users');
      return Helper::successMessage(I18n::get('success.session.create'), '/');
    }

    else
      return Helper::errorMessage(I18n::get('error.session.create'), '/' . $this->_aRequest['controller'] . '/create');
  }

  /**
   * Build form template to create a session.
   *
   * @access public
   * @return string HTML content
   *
   */
  public function _showFormTemplate() {
    $sTemplateDir = Helper::getTemplateDir($this->_aRequest['controller'], '_form');
    $sTemplateFile = Helper::getTemplateType($sTemplateDir, '_form');

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->oSmarty->assign('email', isset($this->_aRequest['email']) ? (string) $this->_aRequest['email'] : '');

    $this->setTitle(I18n::get('global.login'));
    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Resend password or show form.
   *
   * @access public
   * @return string HTML
   *
   */
  public function resendPassword() {
    $bShowCaptcha = class_exists('\CandyCMS\Plugin\Controller\Recaptcha') ? SHOW_CAPTCHA : false;

    return isset($this->_aRequest['email']) ?
            $this->_resendPassword($bShowCaptcha) :
            $this->_showCreateResendActionsTemplate($bShowCaptcha);
  }

  /**
   * Resend password.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, try to send mail.
   *
   * @access public
   * @return string HTML
   *
   */
  protected function _resendPassword($bShowCaptcha) {
    $this->_setError('email');

    if ($bShowCaptcha === true && Recaptcha::getInstance()->checkCaptcha($this->_aRequest) === false)
        $this->_aError['captcha'] = I18n::get('error.captcha.incorrect');

    if (isset($this->_aError))
      return $this->_showCreateResendActionsTemplate($bShowCaptcha);

    $this->__autoload('Mails');
    $sNewPasswordClean = Helper::createRandomChar(10, true);
    $aData = $this->_oModel->resendPassword(md5(RANDOM_HASH . $sNewPasswordClean));

    if (!empty($aData)) {
      $sContent = I18n::get('sessions.password.mail.body', $aData['name'], $sNewPasswordClean);

      $bStatus = Mails::send(
              Helper::formatInput($this->_aRequest['email']),
              I18n::get('sessions.password.mail.subject'),
              $sContent,
              WEBSITE_MAIL_NOREPLY);

      return $bStatus === true ?
              Helper::successMessage(I18n::get('success.mail.create'), '/' . $this->_aRequest['controller'] . '/create') :
              Helper::errorMessage(I18n::get('error.mail.create'), '/' . $this->_aRequest['controller'] . '/create');
    }
    else
      return Helper::errorMessage(I18n::get('error.session.account'), '/' . $this->_aRequest['controller'] . '/create');
  }

  /**
   * Resend verification or show form.
   *
   * @access public
   * @return string HTML
   *
   */
  public function resendVerification() {
    $bShowCaptcha = class_exists('\CandyCMS\Plugin\Controller\Recaptcha') ? SHOW_CAPTCHA : false;

    return isset($this->_aRequest['email']) ?
            $this->_resendVerification($bShowCaptcha) :
            $this->_showCreateResendActionsTemplate($bShowCaptcha);
  }

  /**
   * Resend verification.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, try to send mail.
   *
   * @access protected
   * @return string HTML
   *
   */
  protected function _resendVerification($bShowCaptcha) {
    $this->_setError('email');

    if ($bShowCaptcha === true && Recaptcha::getInstance()->checkCaptcha($this->_aRequest) === false)
        $this->_aError['captcha'] = I18n::get('error.captcha.incorrect');

    if (isset($this->_aError))
      return $this->_showCreateResendActionsTemplate($bShowCaptcha);

    $this->__autoload('Mails');
    $aData = & $this->_oModel->resendVerification();

    if (!empty($aData)) {
      $sVerificationUrl = Helper::createLinkTo('users/' . $aData['verification_code'] . '/verification');

      $sContent = I18n::get('sessions.verification.mail.body', $aData['name'], $sVerificationUrl);

      $bStatus = Mails::send(
              Helper::formatInput($this->_aRequest['email']),
              I18n::get('sessions.verification.mail.subject'),
              $sContent,
              WEBSITE_MAIL_NOREPLY);

      return $bStatus === true ?
              Helper::successMessage(I18n::get('success.mail.create'), '/' . $this->_aRequest['controller'] . '/create') :
              Helper::errorMessage(I18n::get('error.mail.create'), '/' . $this->_aRequest['controller'] . '/create');
    }
    else
      return Helper::errorMessage(I18n::get('error.session.account'), '/' . $this->_aRequest['controller'] . '/create');
  }

  /**
   * Build form template to resend verification or resend password.
   *
   * @access private
   * @return string HTML content
   *
   */
  private function _showCreateResendActionsTemplate($bShowCaptcha) {
    $sTemplateDir = Helper::getTemplateDir($this->_aRequest['controller'], 'resend');
    $sTemplateFile = Helper::getTemplateType($sTemplateDir, 'resend');

    if ($bShowCaptcha) $this->oSmarty->assign('_captcha_', Recaptcha::getInstance()->show());

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * There is no update action for the sessions controller.
   *
   * @access public
   *
   */
  public function update() {
    Helper::redirectTo('/errors/404');
  }

  /**
   * Destroy user session.
   *
   * @access public
   * @return boolean status of model action
   *
   */
  public function destroy() {
    # Facebook logout
    if ($this->_aSession['user']['role'] == 2) {
      unset($this->_aSession['user']);
      return Helper::successMessage(I18n::get('success.session.destroy'),
              $this->_aSession['facebook']->getLogoutUrl(array('next' => WEBSITE_URL)));
    }

    # Standard member
    elseif ($this->_aSession['user']['role'] > 0 && $this->_oModel->destroy(session_id()) === true) {
      unset($this->_aSession['user']);
      return Helper::successMessage(I18n::get('success.session.destroy'), '/');
    }

    else
      return Helper::errorMessage('', '/');
  }
}