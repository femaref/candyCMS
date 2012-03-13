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

class Session extends Main {

  /**
   * Route to right action.
   *
   * @access public
   * @return string HTML
   *
   */
  public function show() {
    if (isset($this->_aRequest['action'])) {
      switch ($this->_aRequest['action']) {

        case 'password':

          $this->setTitle(I18n::get('session.password.title'));
          $this->setDescription(I18n::get('session.password.info'));
          return $this->resendPassword();

          break;

        case 'verification':

          $this->setTitle(I18n::get('session.verification.title'));
          $this->setDescription(I18n::get('session.verification.info'));
          return $this->resendVerification();

          break;
      }
    }
    else
      return $this->_show();
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
		return isset($this->_aRequest['create_session']) ? $this->_create() : $this->_show();
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
	private function _create() {
		$this->_setError('email');
		$this->_setError('password');

		if (isset($this->_aError))
			return $this->_show();

		elseif ($this->_oModel->create() === true)
			return Helper::successMessage(I18n::get('success.session.create'), '/');

		else
			return Helper::errorMessage(I18n::get('error.session.create'), '/session/create');
	}

	/**
	 * Build form template to create a session.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
  public function _show() {
    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

		$this->oSmarty->assign('email', isset($this->_aRequest['email']) ? (string) $this->_aRequest['email'] : '');

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'create');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'create');

    $this->setDescription(I18n::get('global.login'));
    $this->setTitle(I18n::get('global.login'));

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
	}

	/**
	 * Resend password.
	 *
	 * @access public
	 * @return string HTML
	 * @todo better error messages
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
			$this->__autoload('Mail');
			$sNewPasswordClean = Helper::createRandomChar(10, true);
			$aData = $this->_oModel->resendPassword(md5(RANDOM_HASH . $sNewPasswordClean));

			if (!empty($aData)) {
				$sContent = str_replace('%u', $aData['name'], I18n::get('session.password.mail.body'));
				$sContent = str_replace('%p', $sNewPasswordClean, $sContent);

				$bStatus = Mail::send(Helper::formatInput($this->_aRequest['email']),
																									I18n::get('session.password.mail.subject'),
																									$sContent,
																									WEBSITE_MAIL_NOREPLY);

				return $bStatus === true ?
								Helper::successMessage(I18n::get('success.mail.create'), '/session/create') :
								Helper::errorMessage(I18n::get('error.mail.create')) . $this->_show();
			}
			else
				# Replace error message with message, that email could not be found
				return Helper::errorMessage('Account not found!', '/');
		}
	}

	/**
	 * Resend verification.
	 *
	 * @access public
	 * @return string HTML
	 * @todo better error messages
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
			$this->__autoload('Mail');
			$aData = & $this->_oModel->resendVerification();

			if (!empty($aData)) {
				$sVerificationUrl = Helper::createLinkTo('user/' . $aData['verification_code'] . '/verification');

				$sContent = str_replace('%u', $aData['name'], I18n::get('session.verification.mail.body'));
				$sContent = str_replace('%v', $sVerificationUrl, $sContent);

				$bStatus = Mail::send(Helper::formatInput($this->_aRequest['email']),
																									I18n::get('session.verification.mail.subject'),
																									$sContent,
																									WEBSITE_MAIL_NOREPLY);

				return $bStatus === true ?
								Helper::successMessage(I18n::get('success.mail.create'), '/session/create') :
								$this->_show();
			}
			else
				# Replace error message with message, that email could not be found
				return Helper::errorMessage(I18n::get('error.sql'), '/');
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
		if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'resend');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'resend');

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