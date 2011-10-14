<?php

/**
 * CRUD action of users.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\Upload as Upload;
use CandyCMS\Model\User as Model;

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
		$this->_oModel = new Model($this->_aRequest, $this->_aSession, $this->_aFile);
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
      return Helper::errorMessage($this->oI18n->get('error.session.create_first'), '/');

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
      $this->_aError['password_old'] = $this->oI18n->get('error.user.update.password.old.empty');

    # Check if old password is correct
    if (!empty($this->_aRequest['password_old']) &&
            md5(RANDOM_HASH . $this->_aRequest['password_old']) !==
            $this->_aSession['userdata']['password'])
      $this->_aError['password_old'] = $this->oI18n->get('error.user.update.password.old.wrong');

    # Check if new password fields aren't empty
    if (!empty($this->_aRequest['password_old']) && (
            empty($this->_aRequest['password_new']) ||
            empty($this->_aRequest['password_new2']) ))
      $this->_aError['password_new'] = $this->oI18n->get('error.user.update.password.new.empty');

    # Check if new password fields match
    if (isset($this->_aRequest['password_new']) && isset($this->_aRequest['password_new2']) &&
            $this->_aRequest['password_new'] !== $this->_aRequest['password_new2'])
      $this->_aError['password_new'] = $this->oI18n->get('error.user.update.password.new.match');

		if (isset($this->_aError))
			return $this->_showFormTemplate();

		elseif ($this->_oModel->update((int) $this->_iId) === true) {
			$this->_iId = isset($this->_aRequest['id']) && $this->_aRequest['id'] !== USER_ID ?
							(int) $this->_aRequest['id'] :
							USER_ID;

			Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId);
			return Helper::successMessage($this->oI18n->get('success.update'), '/user/' . $this->_iId);
		}
		else
			return Helper::errorMessage($this->oI18n->get('error.sql'), '/user/' . $this->_iId);
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
		$this->_aData = $this->_oModel->getData($iId, false, true);

		# Override if we want to use request
		if ($bUseRequest == true) {
			foreach ($this->_aData as $sColumn => $sData)
				$this->_aData[$sColumn] = isset($this->_aRequest[$sColumn]) ? $this->_aRequest[$sColumn] : $sData;
		}

		foreach ($this->_aData as $sColumn => $sData)
			$this->oSmarty->assign($sColumn, $sData);

		if (!empty($this->_aError)) {
			foreach ($this->_aError as $sField => $sMessage)
				$this->oSmarty->assign('error_' . $sField, $sMessage);
		}

		$this->oSmarty->assign('uid', $iId);

		# Set Form params
		$this->oSmarty->assign('style', 'display:none');

		$this->oSmarty->template_dir = Helper::getTemplateDir('users', '_form');
		return $this->oSmarty->fetch('_form.tpl');
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

		$this->_setError('terms', $this->oI18n->get('error.file.upload'));

		if(!isset($this->_aFile['image']))
			$this->_aError['image'] = $this->oI18n->get('error.form.missing.file');

		if (isset($this->_aError))
			return $this->_showFormTemplate();

		elseif ($oUpload->uploadAvatarFile(false) === true)
			return Helper::successMessage($this->oI18n->get('success.upload'), '/user/' . $this->_iId);

		else
			return Helper::errorMessage($this->oI18n->get('error.file.upload'), '/user/' . $this->_iId);
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
    $this->oSmarty->assign('user', $this->_aData);

		if (empty($this->_iId)) {
			$this->_setTitle($this->oI18n->get('user.title.overview'));
			$this->_setDescription($this->oI18n->get('user.title.overview'));

			if (USER_RIGHT < 3)
				return Helper::errorMessage($this->oI18n->get('error.missing.permission'), '/');

			else {
				$this->oSmarty->template_dir = Helper::getTemplateDir('users', 'overview');
				return $this->oSmarty->fetch('overview.tpl');
			}
		}
		else {
			# Manage title and description (content)
			$this->_setTitle($this->_aData[1]['full_name']);
			$this->_setDescription($this->_aData[1]['full_name']);

			$this->oSmarty->template_dir = Helper::getTemplateDir('users', 'show');
			return $this->oSmarty->fetch('show.tpl');
		}
	}

	/**
	 * Destroy user avatar images.
	 *
	 * @access private
	 * @param integer $iId user id.
	 *
	 */
	private function _destroyUserAvatars($iId) {
    @unlink(PATH_UPLOAD . '/user/32/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/64/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/100/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/200/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/popup/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/original/' . (int) $iId . '.jpg');
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
				if ($this->_oModel->destroy($this->_iId) === true) {
					$this->_destroyUserAvatars($this->_iId);
					return Helper::successMessage($this->oI18n->get('success.destroy'), '/');
				}
				else
					return Helper::errorMessage($this->oI18n->get('error.sql'), '/user/update');
			} else
				return Helper::errorMessage('Dein eingegebenes Passwort stimmt nicht. Der Account konnte nicht gelöscht werden.', '/user/update');

		# We are admin and can delete users
		} elseif (USER_RIGHT == 4) {
			if ($this->_oModel->destroy($this->_iId) === true) {
				$this->_destroyUserAvatars($this->_iId);
				Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId);
				return Helper::successMessage($this->oI18n->get('success.destroy'), '/user');
			}
			else
				return Helper::errorMessage($this->oI18n->get('error.sql'), '/user');
		}

		# No admin and not the active user
		else
			return Helper::errorMessage($this->oI18n->get('error.missing.permission'), '/');
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
   * @todo Better success message.
	 *
	 */
	protected function _create() {
		$this->_setError('name');
		$this->_setError('email');
		$this->_setError('password');

		if (Model::getExistingUser($this->_aRequest['email']))
			$this->_aError['email'] = $this->oI18n->get('error.user.create.email');

		if ($this->_aRequest['password'] !== $this->_aRequest['password2'])
			$this->_aError['password'] = $this->oI18n->get('error.passwords');

		# Admin does not need to confirm disclaimer
		if (USER_RIGHT < 4) {
			if (!isset($this->_aRequest['disclaimer']))
				$this->_aError['disclaimer'] = $this->oI18n->get('error.form.missing.terms');
		}

		# Generate verification code for users (double-opt-in)
		$iVerificationCode = Helper::createRandomChar(12, true);
		$sVerificationUrl = Helper::createLinkTo('/user/' . $iVerificationCode . '/verification');

		if (isset($this->_aError))
			return $this->_showCreateUserTemplate();

		elseif ($this->_oModel->create($iVerificationCode) === true) {
			$sMailMessage = str_replace('%u', Helper::formatInput($this->_aRequest['name']), $this->oI18n->get('user.mail.body'));
			$sMailMessage = str_replace('%v', $sVerificationUrl, $sMailMessage);

			Log::insert($this->_aRequest['section'], $this->_aRequest['action'], Helper::getLastEntry('users'));
			Mail::send(Helper::formatInput($this->_aRequest['email']), $this->oI18n->get('user.mail.subject'), $sMailMessage, WEBSITE_MAIL_NOREPLY);

			return Helper::successMessage($this->oI18n->get('success.mail.create'), '/');
		}
		else
			return Helper::errorMessage($this->oI18n->get('error.sql'), '/');
	}

	/**
	 * Build form template to create a user.
	 *
	 * @access protected
	 * @return string HTML content
	 *
	 */
	protected function _showCreateUserTemplate() {
		$this->oSmarty->assign('name', isset($this->_aRequest['name']) ?
										Helper::formatInput($this->_aRequest['name']) :
										'');

		$this->oSmarty->assign('surname', isset($this->_aRequest['surname']) ?
										Helper::formatInput($this->_aRequest['surname']) :
										'');

		$this->oSmarty->assign('email', isset($this->_aRequest['email']) ?
										Helper::formatInput($this->_aRequest['email']) :
										'');

    if (!empty($this->_aError)) {
			foreach ($this->_aError as $sField => $sMessage)
				$this->oSmarty->assign('error_' . $sField, $sMessage);
		}

		$this->oSmarty->template_dir = Helper::getTemplateDir('users', 'create');
		return $this->oSmarty->fetch('create.tpl');
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
			return Helper::errorMessage($this->oI18n->get('error.missing.id'), '/');

		elseif ($this->_oModel->verifyEmail($this->_iId) === true)
			return Helper::successMessage($this->oI18n->get('success.user.verification'), '/');

		else
			return Helper::errorMessage($this->oI18n->get('error.user.verification'), '/');
	}
}