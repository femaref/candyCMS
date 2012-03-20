<?php

/**
 * Create or destroy a session.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 * @todo recaptcha for registration?
 */

namespace CandyCMS\Controller;

use CandyCMS\Controller\Main as Main;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Plugin\Controller\FacebookCMS as FacebookCMS;

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

        return $this->_showFormTemplate();

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

		elseif ($this->_oModel->create() === true)
			return Helper::successMessage(I18n::get('success.session.create'), '/');

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
    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], '_form');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form');

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

		$this->oSmarty->assign('email', isset($this->_aRequest['email']) ? (string) $this->_aRequest['email'] : '');

    $this->setTitle(I18n::get('global.login'));
    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
	}

	/**
	 * Resend password.
	 *
	 * @access public
	 * @return string HTML
   * @todo refactor to two methods
	 *
	 */
	public function resendPassword() {
		if (isset($this->_aRequest['email']))
			$this->_setError('email');

		else
			return $this->_showCreateResendActionsTemplate();

		if (isset($this->_aError))
			return $this->_showCreateResendActionsTemplate();

		else {
			$this->__autoload('Mails');
			$sNewPasswordClean = Helper::createRandomChar(10, true);
			$aData = $this->_oModel->resendPassword(md5(RANDOM_HASH . $sNewPasswordClean));

			if (!empty($aData)) {
				$sContent = str_replace('%u', $aData['name'], I18n::get('sessions.password.mail.body'));
				$sContent = str_replace('%p', $sNewPasswordClean, $sContent);

				$bStatus = Mails::send(Helper::formatInput($this->_aRequest['email']),
																									I18n::get('sessions.password.mail.subject'),
																									$sContent,
																									WEBSITE_MAIL_NOREPLY);

				return $bStatus === true ?
								Helper::successMessage(I18n::get('success.mail.create'), '/' . $this->_aRequest['controller']) :
								Helper::errorMessage(I18n::get('error.mail.create')) . $this->_show();
			}
			else
				return Helper::errorMessage(I18n::get('error.session.account'), '/' . $this->_aRequest['controller']);
		}
	}

	/**
	 * Resend verification.
	 *
	 * @access public
	 * @return string HTML
   * @todo refactor to two methods
	 *
	 */
	public function resendVerification() {
		if (isset($this->_aRequest['email']))
			$this->_setError('email');

		else
			return $this->_showCreateResendActionsTemplate();

		if (isset($this->_aError))
			return $this->_showCreateResendActionsTemplate();

		else {
			$this->__autoload('Mails');
			$aData = & $this->_oModel->resendVerification();

			if (!empty($aData)) {
				$sVerificationUrl = Helper::createLinkTo('user/' . $aData['verification_code'] . '/verification');

				$sContent = str_replace('%u', $aData['name'], I18n::get('sessions.verification.mail.body'));
				$sContent = str_replace('%v', $sVerificationUrl, $sContent);

				$bStatus = Mails::send(Helper::formatInput($this->_aRequest['email']),
																									I18n::get('sessions.verification.mail.subject'),
																									$sContent,
																									WEBSITE_MAIL_NOREPLY);

				return $bStatus === true ?
								Helper::successMessage(I18n::get('success.mail.create'), '/' . $this->_aRequest['controller']) :
								$this->_show();
			}
			else
				return Helper::errorMessage(I18n::get('error.session.account'), '/' . $this->_aRequest['controller']);
		}
	}

	/**
	 * Build form template to resend verification or resend password.
	 *
	 * @access private
	 * @return string HTML content
	 *
	 */
  private function _showCreateResendActionsTemplate() {
    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], 'resend');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'resend');

		if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
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
			$this->_aSession['facebook']->getLogoutUrl();
			session_destroy();
			unset($this->_aSession, $_SESSION);
			return Helper::successMessage(I18n::get('success.session.destroy'), '/');
		}

		# Standard member
		elseif ($this->_oModel->destroy() === true) {
			session_destroy();
			unset($this->_aSession, $_SESSION);
			return Helper::successMessage(I18n::get('success.session.destroy'), '/');
		}

		else
			return Helper::errorMessage(I18n::get('error.sql'), '/');
	}
}