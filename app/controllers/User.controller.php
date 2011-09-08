<?php

/**
 * CRUD action of users.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

require_once 'app/controllers/Session.controller.php';
require_once 'app/controllers/Mail.controller.php';
require_once 'app/models/User.model.php';
require_once 'app/helpers/Image.helper.php';
require_once 'app/helpers/Upload.helper.php';

class User extends Main {

	/**
	 * Include the user model.
	 *
	 * @access public
	 * @override app/controllers/Main.controller.php
	 *
	 */
	public function __init() {
		$this->_oModel = new Model_User($this->_aRequest, $this->_aSession, $this->_aFile);
	}

	/**
	 * Update a user.
	 *
	 * Update entry or show form template if we have enough rights.
	 *
	 * @access public
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 *
	 */
	public function update() {
    if (empty($this->_iId))
      $this->_iId = USER_ID;

    if (USER_ID == 0)
      return Helper::errorMessage(LANG_ERROR_GLOBAL_CREATE_SESSION_FIRST, '/');

    else {
      if (isset($this->_aRequest['create_avatar']))
				return $this->_createAvatar($this->_iId);

			elseif (isset($this->_aRequest['update_user']))
				return $this->_update();

			else
				return $this->_showFormTemplate();
    }
  }

	/**
	 * Update a user.
	 *
	 * Activate model, insert data into the database and redirect afterwards.
	 *
	 * @access protected
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 *
	 */
	protected function _update() {
		$this->_setError('name');
		$this->_setError('email');

		# Check if old password is set
		if (empty($this->_aRequest['password_old']) &&
						!empty($this->_aRequest['password_new']) &&
						!empty($this->_aRequest['password_new2']))
			$this->_aError['password_old'] = LANG_ERROR_USER_UPDATE_PASSWORD_OLD_EMPTY;

		# Check if old password is correct
		if (!empty($this->_aRequest['password_old']) &&
						md5(RANDOM_HASH . $this->_aRequest['password_old']) !==
						$this->_aSession['userdata']['password'])
			$this->_aError['password_old'] = LANG_ERROR_USER_UPDATE_PASSWORD_OLD_WRONG;

		# Check if new password fields aren't empty
		if (!empty($this->_aRequest['password_old']) && (
						empty($this->_aRequest['password_new']) ||
						empty($this->_aRequest['password_new2']) ))
			$this->_aError['password_new'] = LANG_ERROR_USER_UPDATE_PASSWORD_NEW_EMPTY;

		# Check if new password fields match
		if (isset($this->_aRequest['password_new']) && isset($this->_aRequest['password_new2']) &&
						$this->_aRequest['password_new'] !== $this->_aRequest['password_new2'])
			$this->_aError['password_new'] = LANG_ERROR_USER_UPDATE_PASSWORD_NEW_DO_NOT_MATCH;

		if (isset($this->_aError))
			return $this->_showFormTemplate();

		elseif ($this->_oModel->update((int) $this->_iId) === true) {
			$this->_iId = isset($this->_aRequest['id']) && $this->_aRequest['id'] !== USER_ID ?
							(int) $this->_aRequest['id'] :
							USER_ID;

			Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId);
			return Helper::successMessage(LANG_SUCCESS_UPDATE, '/user/' . $this->_iId);
		}
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/user/' . $this->_iId);
	}

	/**
	 * Build form template to create or update a user.
	 *
	 * @access protected
	 * @return string HTML content
	 *
	 */
	protected function _showFormTemplate($bUseRequest = false) {
		# Avoid URL manipulation
		if ($this->_iId !== USER_ID && USER_RIGHT < 4) {
			Helper::redirectTo('/user/update');
			exit();
		}

		# Set user id of person to update
		$iId = ($this->_iId !== USER_ID && USER_RIGHT == 4) ? $this->_iId : USER_ID;

		# Fetch data from database
		$this->_aData = $this->_oModel->getData($iId);

		# Override if we want to use request
		if ($bUseRequest == true) {
			foreach ($this->_aData as $sColumn => $sData)
				$this->_aData[$sColumn] = isset($this->_aRequest[$sColumn]) ? $this->_aRequest[$sColumn] : $sData;
		}

		foreach ($this->_aData as $sColumn => $sData)
			$this->_oSmarty->assign($sColumn, $sData);

		if (!empty($this->_aError)) {
			foreach ($this->_aError as $sField => $sMessage)
				$this->_oSmarty->assign('error_' . $sField, $sMessage);
		}

		$this->_oSmarty->assign('uid', $iId);

		# Set Form params
		$this->_oSmarty->assign('style', 'display:none');

		# Language
		$this->_oSmarty->assign('lang_account_title', LANG_USER_UPDATE_ACCOUNT_TITLE);
		$this->_oSmarty->assign('lang_account_info', LANG_USER_UPDATE_ACCOUNT_INFO);
		$this->_oSmarty->assign('lang_image_change', LANG_USER_UPDATE_IMAGE_LABEL_CHANGE);
		$this->_oSmarty->assign('lang_image_choose', LANG_USER_UPDATE_IMAGE_LABEL_CHOOSE);
		$this->_oSmarty->assign('lang_image_headline', LANG_USER_UPDATE_IMAGE_LABEL_CHOOSE);
		$this->_oSmarty->assign('lang_image_terms', LANG_USER_UPDATE_IMAGE_LABEL_TERMS);
		$this->_oSmarty->assign('lang_image_upload', LANG_USER_UPDATE_IMAGE_TITLE);
		$this->_oSmarty->assign('lang_image_upload_info', LANG_USER_UPDATE_IMAGE_INFO);
		$this->_oSmarty->assign('lang_password_change', LANG_USER_UPDATE_PASSWORD_TITLE);
		$this->_oSmarty->assign('lang_password_new', LANG_USER_UPDATE_PASSWORD_LABEL_NEW);
		$this->_oSmarty->assign('lang_password_old', LANG_USER_UPDATE_PASSWORD_LABEL_OLD);
		$this->_oSmarty->assign('lang_password_repeat', LANG_GLOBAL_PASSWORD_REPEAT);
		$this->_oSmarty->assign('lang_user_content', LANG_USER_UPDATE_USER_LABEL_DESCRIPTION); # TODO: Rename to content
		$this->_oSmarty->assign('lang_user_gravatar', LANG_USER_UPDATE_USER_LABEL_GRAVATAR);
		$this->_oSmarty->assign('lang_user_gravatar_info', LANG_USER_UPDATE_USER_GRAVATAR_INFO);
		$this->_oSmarty->assign('lang_user_newsletter', LANG_USER_UPDATE_USER_LABEL_NEWSLETTER);
		$this->_oSmarty->assign('lang_user_submit', LANG_USER_UPDATE_USER_LABEL_SUBMIT);
		$this->_oSmarty->assign('lang_user_title', LANG_USER_UPDATE_USER_TITLE);

		$this->_oSmarty->template_dir = Helper::getTemplateDir('users', '_form');
		return $this->_oSmarty->fetch('_form.tpl');
	}

	/**
	 * Upload user profile image.
	 *
	 * @access private
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 *
	 */
	private function _createAvatar() {
		$oUpload = new Upload($this->_aRequest, $this->_aFile);

		$this->_setError('terms', LANG_ERROR_USER_UPDATE_AGREE_UPLOAD);

		if(!isset($this->_aFile['image']))
			$this->_aError['image'] = LANG_ERROR_FORM_MISSING_FILE;

		if (isset($this->_aError))
			return $this->_showFormTemplate();

		elseif ($oUpload->uploadAvatarFile(false) === true)
			return Helper::successMessage(LANG_SUCCESS_UPDATE, '/user/' . $this->_iId);

		else
			return Helper::errorMessage(LANG_ERROR_UPLOAD_CREATE, '/user/' . $this->_iId);
	}

	/**
	 * Show user or user overview (depends on a given ID or not and user right).
	 *
	 * @access public
	 * @param integer $iUserId user to show
	 * @return string HTML content
	 *
	 */
	public function show($iUserId = '') {
		# Fix to avoid empty UID on /user/Settings shortcut
		if (!empty($iUserId))
			$this->_iId = (int) $iUserId;

		$this->_aData = $this->_oModel->getData($iUserId);

		if (empty($this->_iId)) {
			$this->_setTitle(LANG_USER_SHOW_OVERVIEW_TITLE);
			$this->_setDescription(LANG_USER_SHOW_OVERVIEW_TITLE);

			if (USER_RIGHT < 3)
				return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');

			else {
				$this->_oSmarty->assign('user', $this->_aData);

				# Language
				$this->_oSmarty->assign('lang_create', LANG_USER_CREATE_TITLE);
				$this->_oSmarty->assign('lang_headline', LANG_GLOBAL_USERMANAGER);

				$this->_oSmarty->template_dir = Helper::getTemplateDir('users', 'overview');
				return $this->_oSmarty->fetch('overview.tpl');
			}
		}
		else {
			$this->_oSmarty->assign('u', $this->_aData);

			# Manage title and description (content)
			$this->_setTitle($this->_aData['full_name']);
			$this->_setDescription($this->_aData['full_name']);

			# Language
			$this->_oSmarty->assign('lang_about_himself', str_replace('%u', $this->_aData['full_name'], LANG_USER_SHOW_USER_LABEL_DESCRIPTION));
			$this->_oSmarty->assign('lang_contact_via_mail', str_replace('%u', $this->_aData['full_name'], LANG_USER_SHOW_USER_ACTION_CONTACT_VIA_EMAIL));
      $this->_oSmarty->assign('lang_last_login', LANG_USER_SHOW_USER_LABEL_LAST_LOGIN);
      $this->_oSmarty->assign('lang_registered_since', LANG_USER_SHOW_USER_REGISTERED_SINCE);

			$this->_oSmarty->template_dir = Helper::getTemplateDir('users', 'show');
			return $this->_oSmarty->fetch('show.tpl');
		}
	}

	/**
	 * Delete a user account.
	 *
	 * @access public
	 * @return boolean status message
	 * @override app/controllers/Main.controller.php
	 * @todo translate error message
	 *
	 */
	public function destroy() {
		# We are a user and want to delete our account
		if (isset($this->_aRequest['destroy_user']) && USER_ID === $this->_iId) {
			if (md5(RANDOM_HASH . $this->_aRequest['password']) === USER_PASSWORD) {
				if ($this->_oModel->destroy($this->_iId) === true)
					return Helper::successMessage(LANG_SUCCESS_DESTROY, '/');
				else
					return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/user/update');
			} else
				return Helper::errorMessage('Dein eingegebenes Passwort stimmt nicht. Der Account konnte nicht gelöscht werden.', '/user/update');

		# We are admin and can delete users
		} elseif (USER_RIGHT == 4) {
			if ($this->_oModel->destroy($this->_iId) === true) {
				Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId);
				return Helper::successMessage(LANG_SUCCESS_DESTROY, '/user');
			}
			else
				return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/user');
		}

		# No admin and not the active user
		else
			return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');
	}

	/**
	 * Create user or show form template.
	 *
	 * @access public
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 * @override app/controllers/Main.controller.php
	 *
	 */
	public function create() {
		return isset($this->_aRequest['create_user']) ? $this->_create() : $this->_showCreateUserTemplate();
	}

	/**
	 * Create a user.
	 *
	 * Check if required data is given or throw an error instead.
	 * If data is given, activate the model, insert them into the database, send mail and redirect afterwards.
	 *
	 * @access protected
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 *
	 */
	protected function _create() {
		$this->_setError('name');
		$this->_setError('email');
		$this->_setError('password');

		if (Model_User::getExistingUser($this->_aRequest['email']))
			$this->_aError['email'] = LANG_ERROR_USER_CREATE_EMAIL_ALREADY_EXISTS;

		if ($this->_aRequest['password'] !== $this->_aRequest['password2'])
			$this->_aError['password'] = LANG_ERROR_GLOBAL_PASSWORDS_DO_NOT_MATCH;

		# Admin does not need to confirm disclaimer
		if (USER_RIGHT < 4) {
			if (!isset($this->_aRequest['disclaimer']))
				$this->_aError['disclaimer'] = LANG_ERROR_GLOBAL_READ_DISCLAIMER;
		}

		# Generate verification code for users (double-opt-in)
		$iVerificationCode = Helper::createRandomChar(12, true);
		$sVerificationUrl = Helper::createLinkTo('/user/' . $iVerificationCode . '/verification');

		if (isset($this->_aError))
			return $this->_showCreateUserTemplate();

		elseif ($this->_oModel->create($iVerificationCode) === true) {
			$sMailMessage = str_replace('%u', Helper::formatInput($this->_aRequest['name']), LANG_MAIL_USER_CREATE_BODY);
			$sMailMessage = str_replace('%v', $sVerificationUrl, $sMailMessage);

			Log::insert($this->_aRequest['section'], $this->_aRequest['action'], Helper::getLastEntry('users'));
			return Mail::send(Helper::formatInput($this->_aRequest['email']),
																								LANG_MAIL_USER_CREATE_SUBJECT,
																								$sMailMessage,
																								WEBSITE_MAIL_NOREPLY);
		}
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/');
	}

	/**
	 * Build form template to create a user.
	 *
	 * @access protected
	 * @return string HTML content
	 *
	 */
	protected function _showCreateUserTemplate() {
		$this->_oSmarty->assign('name', isset($this->_aRequest['name']) ?
										Helper::formatInput($this->_aRequest['name']) :
										'');

		$this->_oSmarty->assign('surname', isset($this->_aRequest['surname']) ?
										Helper::formatInput($this->_aRequest['surname']) :
										'');

		$this->_oSmarty->assign('email', isset($this->_aRequest['email']) ?
										Helper::formatInput($this->_aRequest['email']) :
										'');

    if (!empty($this->_aError)) {
			foreach ($this->_aError as $sField => $sMessage)
				$this->_oSmarty->assign('error_' . $sField, $sMessage);
		}

		$this->_oSmarty->template_dir = Helper::getTemplateDir('users', 'create');
		return $this->_oSmarty->fetch('create.tpl');
	}

	/**
	 * Verify email address.
	 *
	 * @access public
	 * @return boolean status of message
	 *
	 */
	public function verifyEmail() {
		if (empty($this->_iId))
			return Helper::errorMessage(LANG_ERROR_GLOBAL_WRONG_ID, '/');

		elseif ($this->_oModel->verifyEmail($this->_iId) === true)
			return Helper::successMessage(LANG_USER_VERIFICATION_SUCCESSFUL, '/');

		else
			return Helper::errorMessage(LANG_ERROR_USER_VERIFICATION, '/');
	}
}