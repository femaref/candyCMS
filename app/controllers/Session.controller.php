<?php

/**
 * Create or destroy a session.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

require_once 'app/models/Session.model.php';

class Session extends Main {

	/**
	 * Include the session model.
	 *
	 * @access public
	 * @override app/controllers/Main.controller.php
	 *
	 */
  public function __init() {
    $this->_oModel = new Model_Session($this->_aRequest, $this->_aSession);
  }

	/**
	 * Create a session or show template instead.
	 * We must override the main method due to a diffent required user right.
	 *
	 * @access public
	 * @return string HTML content
	 * @override app/controllers/Main.controller.php
	 *
	 */
  public function create() {
		return isset($this->_aRequest['create_session']) ? $this->_create() : $this->showCreateSessionTemplate();
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
			return $this->showCreateSessionTemplate();

		elseif ($this->_oModel->create() === true)
			return Helper::successMessage(LANG_SESSION_CREATE_SUCCESSFUL, '/');

		else
			return Helper::errorMessage(LANG_ERROR_SESSION_CREATE, '/session/create');
	}

	/**
	 * Build form template to create a session.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
  public function showCreateSessionTemplate() {
		if (!empty($this->_aError)) {
			foreach ($this->_aError as $sField => $sMessage)
				$this->_oSmarty->assign('error_' . $sField, $sMessage);
		}

		$this->_oSmarty->assign('email', isset($this->_aRequest['email']) ? (string) $this->_aRequest['email'] : '');

		# Language
		$this->_oSmarty->assign('lang_lost_password', LANG_SESSION_PASSWORD_TITLE);
		$this->_oSmarty->assign('lang_resend_verification', LANG_SESSION_VERIFICATION_TITLE);

		$this->_oSmarty->template_dir = Helper::getTemplateDir('sessions', 'create');
		return $this->_oSmarty->fetch('create.tpl');
	}

	/**
	 * Resend user verification or resend lost password to user.
	 *
	 * @access public
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 * @todo this must be rewritten
	 * @todo replace error message with wrong password information
	 * @todo error message when email is wrong
	 *
	 */
  public function createResendActions() {
		# If there is no email request then show form template
		if (!isset($this->_aRequest['email']))
			return $this->_showCreateResendActionsTemplate();

		# Check format of email
		elseif(isset($this->_aRequest['email'])) {
			$this->_setError('email');
		}

		if (isset($this->_aError))
			return $this->_showCreateResendActionsTemplate();

		else {
			if ($this->_aRequest['action'] == 'resendpassword') {
				$sNewPasswordClean	= Helper::createRandomChar(10);
				$sNewPasswordSecure = md5(RANDOM_HASH . $sNewPasswordClean);

				if ($this->_oModel->createResendActions($sNewPasswordSecure) === true) {
					$aData = $this->_oModel->getData();

					$sContent = str_replace('%u', $aData['name'], LANG_MAIL_SESSION_PASSWORD_BODY);
					$sContent = str_replace('%p', $sNewPasswordClean, $sContent);

					$bStatus = Mail::send(Helper::formatInput($this->_aRequest['email']),
																LANG_MAIL_SESSION_PASSWORD_SUBJECT,
																$sContent,
																WEBSITE_MAIL_NOREPLY);

					return ($bStatus === true) ?
									Helper::successMessage(LANG_SESSION_PASSWORD_CREATE_SUCCESSFUL, '/session/create') :
									Helper::errorMessage(LANG_ERROR_MAIL_ERROR) . $this->showCreateSessionTemplate();
				}
				else
					return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/'); # TODO: Replace error message
			}
			elseif ($this->_aRequest['action'] == 'resendverification') {
				if ($this->_oModel->createResendActions() === true) {
					$aData = $this->_oModel->getData();

					$sVerificationUrl = Helper::createLinkTo('/user/' . $aData['verification_code'] . '/verification');

					$sContent = str_replace('%u', $aData['name'], LANG_MAIL_SESSION_VERIFICATION_BODY);
					$sContent = str_replace('%v', $sVerificationUrl, $sContent);

					$bStatus = Mail::send(Helper::formatInput($this->_aRequest['email']),
																LANG_MAIL_SESSION_VERIFICATION_SUBJECT,
																$sContent,
																WEBSITE_MAIL_NOREPLY);

					return ($bStatus === true) ?
									Helper::successMessage(LANG_SUCCESS_MAIL_SENT, '/session/create') :
									$this->showCreateSessionTemplate();
				}
				else
					return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/'); # TODO: Replace error message
			}
			else
				return Helper::errorMessage(LANG_ERROR_REQUEST_MISSING_ACTION, '/');
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
    if($this->_aRequest['action'] == 'resendpassword') {
      $this->_setTitle(LANG_SESSION_PASSWORD_TITLE);
      $this->_setDescription(LANG_SESSION_PASSWORD_INFO);

			# Language
      $this->_oSmarty->assign('lang_headline', LANG_SESSION_PASSWORD_TITLE);
      $this->_oSmarty->assign('lang_description', LANG_SESSION_PASSWORD_INFO);
      $this->_oSmarty->assign('lang_submit', LANG_SESSION_PASSWORD_LABEL_SUBMIT);
    }
    else {
      $this->_setTitle(LANG_SESSION_VERIFICATION_TITLE);
      $this->_setDescription(LANG_SESSION_VERIFICATION_INFO);

			# Language
      $this->_oSmarty->assign('lang_headline', LANG_SESSION_VERIFICATION_TITLE);
      $this->_oSmarty->assign('lang_description', LANG_SESSION_VERIFICATION_INFO);
      $this->_oSmarty->assign('lang_submit', LANG_SESSION_VERIFICATION_LABEL_SUBMIT);
    }

    if (!empty($this->_aError)) {
			foreach ($this->_aError as $sField => $sMessage)
				$this->_oSmarty->assign('error_' . $sField, $sMessage);
		}

    $this->_oSmarty->template_dir = Helper::getTemplateDir('sessions', 'resend');
    return $this->_oSmarty->fetch('resend.tpl');
  }

	/**
	 * Destroy user session.
	 *
	 * @access public
	 * @return boolean status of model action
	 * @override app/controllers/Main.controller.php
	 *
	 */
  public function destroy() {
    if (USER_RIGHT == 2) {
      $oFacebook = new FacebookCMS(array(
                  'appId' => FACEBOOK_APP_ID,
                  'secret' => FACEBOOK_SECRET,
                  'cookie' => true,
              ));

			# Redirect user to start page. Success message is not be displayed.
      Header('Location:' . $oFacebook->getLogoutUrl());
      return Helper::successMessage(LANG_SESSION_DESTROY_SUCCESSFUL, '/');
    }
    elseif ($this->_oModel->destroy() === true) {
      unset($_SESSION);
      return Helper::successMessage(LANG_SESSION_DESTROY_SUCCESSFUL, '/');
    }
    else
      return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/');
  }
}