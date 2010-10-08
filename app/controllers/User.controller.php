<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/controllers/Session.controller.php';
require_once 'app/controllers/Mail.controller.php';
require_once 'app/models/User.model.php';
require_once 'app/helpers/Image.helper.php';
require_once 'app/helpers/Upload.helper.php';

class User extends Main {

	public final function __init() {
		$this->_oModel = new Model_User($this->_aRequest, $this->_aSession, $this->_aFile);
	}

	# @Override
	public function update() {
    if (empty($this->_iId))
      $this->_iId = USER_ID;

    if (USER_ID == 0)
      return Helper::errorMessage(LANG_GLOBAL_CREATE_SESSION_FIRST);

    else {
      if (isset($this->_aRequest['update_user'])) {
        if ($this->_update($this->_iId) === true)
          return Helper::successMessage(LANG_SUCCESS_UPDATE, '/User/' . $this->_iId);
        else
          return $this->_showFormTemplate($this->_aError);
      }
      elseif (isset($this->_aRequest['create_avatar']))
        return $this->_createAvatar($this->_iId);
      else
        return $this->_showFormTemplate();
    }
  }

	protected function _update() {
    if (empty($this->_aRequest['name']))
      $this->_aError['name'] = LANG_ERROR_FORM_MISSING_NAME;

    if (empty($this->_aRequest['email']))
      $this->_aError['email'] = LANG_ERROR_FORM_MISSING_EMAIL;

    if (Helper::checkEmailAddress($this->_aRequest['email']) == false)
      $this->_aError['email'] = LANG_ERROR_GLOBAL_WRONG_EMAIL_FORMAT;

    if (empty($this->_aRequest['password_old']) &&
            !empty($this->_aRequest['password_new']) &&
            !empty($this->_aRequest['password_new2']))
      $this->_aError['password_old'] = LANG_ERROR_USER_UPDATE_PASSWORD_OLD_EMPTY;

    if (!empty($this->_aRequest['password_old']) &&
            md5(RANDOM_HASH . $this->_aRequest['password_old']) !==
            $this->_aSession['userdata']['password'])
      $this->_aError['password_old'] = LANG_ERROR_USER_UPDATE_PASSWORD_OLD_WRONG;

    if (!empty($this->_aRequest['password_old']) && (
            empty($this->_aRequest['password_new']) ||
            empty($this->_aRequest['password_new2']) ))
      $this->_aError['password_new'] = LANG_ERROR_USER_UPDATE_PASSWORD_NEW_EMPTY;

    if (isset($this->_aRequest['password_new']) && isset($this->_aRequest['password_new2']) &&
            $this->_aRequest['password_new'] !== $this->_aRequest['password_new2'])
      $this->_aError['password_new'] = LANG_ERROR_USER_UPDATE_PASSWORD_NEW_DO_NOT_MATCH;

    if (isset($this->_aError))
      return false;

    else {
      # Fix for missing id
      $this->_iId = isset($this->_aRequest['id']) && $this->_aRequest['id'] !== USER_ID ?
              (int) $this->_aRequest['id'] :
              USER_ID;

      if ($this->_oModel->update($this->_iId) === true)
        return true;
      else
        return false;
    }
  }

	protected function _showFormTemplate($bUseRequest = false) {
    $oSmarty = new Smarty();

    # Set user id of person to update
    if ($this->_iId !== USER_ID && USER_RIGHT == 4)
      $iId = $this->_iId;

    else {
      $iId = USER_ID;

      # Avoid URL manipulation
      if($this->_iId !== USER_ID) {
        Helper::redirectTo('/User/update');
        die();
      }
    }

    # Fetch data from database
    $this->_aSession['user_to_update_data'] = $this->_oModel->getData($this->_iId);

    # Override if we want to use request
    if ($bUseRequest == true) {
      $this->_aSession['user_to_update_data']['name'] = & $this->_aRequest['name'];
      $this->_aSession['user_to_update_data']['surname'] = & $this->_aRequest['surname'];
      $this->_aSession['user_to_update_data']['email'] = & $this->_aRequest['email'];
      $this->_aSession['user_to_update_data']['description'] = & $this->_aRequest['description'];
      $this->_aSession['user_to_update_data']['receive_newsletter'] = & $this->_aRequest['receive_newsletter'];
      $this->_aSession['user_to_update_data']['use_gravatar'] = & $this->_aRequest['use_gravatar'];
      $this->_aSession['user_to_update_data']['user_right'] = & $this->_aRequest['user_right'];
    }

    # Set _own_ USER_RIGHT and USER_ID for updating purposes
    $oSmarty->assign('USER_ID', USER_ID);
    $oSmarty->assign('USER_RIGHT', USER_RIGHT);

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $oSmarty->assign('error_' . $sField, $sMessage);
    }

    $aGravatar = array(
        'use_gravatar' => (int) $this->_aSession['user_to_update_data']['use_gravatar'],
        'email' => $this->_aSession['user_to_update_data']['email']
    );

    $oSmarty->assign('uid', $iId);
    $oSmarty->assign('name', $this->_aSession['user_to_update_data']['name']);
    $oSmarty->assign('surname', $this->_aSession['user_to_update_data']['surname']);
    $oSmarty->assign('email', $this->_aSession['user_to_update_data']['email']);
    $oSmarty->assign('description', $this->_aSession['user_to_update_data']['description']);
    $oSmarty->assign('receive_newsletter', (int) $this->_aSession['user_to_update_data']['receive_newsletter']);
    $oSmarty->assign('use_gravatar', (int) $this->_aSession['user_to_update_data']['use_gravatar']);
    $oSmarty->assign('user_right', (int) $this->_aSession['user_to_update_data']['user_right']);

    $oSmarty->assign('avatar_100', Helper::getAvatar('user', 100, $this->_iId, $aGravatar));
    $oSmarty->assign('avatar_popup', Helper::getAvatar('user', 'popup', $this->_iId, $aGravatar));

    # Set Form params
    $oSmarty->assign('_action_url_', '/User/update');
    $oSmarty->assign('style', 'display:none');

    # Compress slimbox
    $oSmarty->assign('_compress_files_suffix_', WEBSITE_COMPRESS_FILES == true ? '-min' : '');

    # Language
    $oSmarty->assign('lang_about_you', LANG_USER_UPDATE_LABEL_DESCRIPTION);
    $oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
    $oSmarty->assign('lang_headline', LANG_USER_UPDATE_TITLE);
    $oSmarty->assign('lang_image_agreement', LANG_USER_UPDATE_LABEL_TERMS);
    $oSmarty->assign('lang_image_change', LANG_USER_UPDATE_IMAGE_LABEL_CHANGE);
    $oSmarty->assign('lang_image_choose', LANG_USER_UPDATE_IMAGE_LABEL_CHOOSE);
    $oSmarty->assign('lang_image_headline', LANG_USER_UPDATE_IMAGE_LABEL_CHOOSE);
    $oSmarty->assign('lang_image_gravatar_info', LANG_USER_UPDATE_GRAVATAR_INFO);
    $oSmarty->assign('lang_image_upload', LANG_USER_UPDATE_IMAGE_TITLE);
    $oSmarty->assign('lang_image_upload_info', LANG_USER_UPDATE_IMAGE_INFO);
    $oSmarty->assign('lang_name', LANG_GLOBAL_NAME);
    $oSmarty->assign('lang_newsletter', LANG_USER_UPDATE_LABEL_NEWSLETTER);
    $oSmarty->assign('lang_password_change', LANG_USER_UPDATE_LABEL_PASSWORD_CHANGE);
    $oSmarty->assign('lang_password_new', LANG_USER_UPDATE_LABEL_PASSWORD_NEW);
    $oSmarty->assign('lang_password_old', LANG_USER_UPDATE_LABEL_PASSWORD_OLD);
    $oSmarty->assign('lang_password_repeat', LANG_GLOBAL_PASSWORD_REPEAT);
    $oSmarty->assign('lang_required', LANG_GLOBAL_REQUIRED);
    $oSmarty->assign('lang_submit', LANG_USER_UPDATE_LABEL_SUBMIT);
    $oSmarty->assign('lang_surname', LANG_GLOBAL_SURNAME);
    $oSmarty->assign('lang_use_gravatar', LANG_USER_UPDATE_LABEL_GRAVATAR);
    $oSmarty->assign('lang_user_right', LANG_GLOBAL_USERRIGHT);
    $oSmarty->assign('lang_user_right_1', LANG_GLOBAL_USERRIGHT_1);
    $oSmarty->assign('lang_user_right_2', LANG_GLOBAL_USERRIGHT_2);
    $oSmarty->assign('lang_user_right_3', LANG_GLOBAL_USERRIGHT_3);
    $oSmarty->assign('lang_user_right_4', LANG_GLOBAL_USERRIGHT_4);

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
    $oSmarty->template_dir = Helper::getTemplateDir('users/_form');
    return $oSmarty->fetch('users/_form.tpl');
  }

	private function _createAvatar() {
    $iAgreement = isset($this->_aRequest['agreement']) ? 1 : 0;

    if ($iAgreement == false)
      return Helper::errorMessage(LANG_ERROR_USER_UPDATE_AGREE_UPLOAD) .
				$this->_showFormTemplate();

    else {
      $oUpload = new Upload($this->_aRequest, $this->_aFile);
      if ($oUpload->uploadAvatarFile(false) === true)
        return Helper::successMessage(LANG_SUCCESS_UPDATE, '/User/' . $this->_iId);

      else
        return Helper::errorMessage(LANG_ERROR_UPLOAD_CREATE, '/User/' . $this->_iId);
    }
  }

	public function show($iUserId = '') {
		# Fix to avoid empty UID on /User/Settings shortcut
		if (!empty($iUserId))
			$this->_iId = (int) $iUserId;

		$oSmarty = new Smarty();
		$oSmarty->assign('USER_RIGHT', USER_RIGHT);

		# System variables
		$oSmarty->assign('_compress_files_suffix_', WEBSITE_COMPRESS_FILES == true ? '-min' : '');

		if (empty($this->_iId)) {
			$this->_setTitle(LANG_USER_SHOW_OVERVIEW_TITLE);

			if (USER_RIGHT < 3)
				return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);

			else {
				$this->_aData = $this->_oModel->getData();
				$oSmarty->assign('user', $this->_aData);

				# Language
				$oSmarty->assign('lang_create', LANG_USER_CREATE_TITLE);
				$oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY);
				$oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);
				$oSmarty->assign('lang_headline', LANG_GLOBAL_USERMANAGER);

        $oSmarty->cache_dir = CACHE_DIR;
        $oSmarty->compile_dir = COMPILE_DIR;
				$oSmarty->template_dir = Helper::getTemplateDir('users/overview');
				return $oSmarty->fetch('users/overview.tpl');
			}
		}
		else {
			$this->_aData = $this->_oModel->getData($this->_iId);
			$aGravatar = array('use_gravatar' => $this->_aData['use_gravatar'],
					'email' => $this->_aData['email']);

			# Description Fix, format Code to BB
			$this->_aData['description'] = Helper::formatOutput($this->_aData['description']);

			$oSmarty->assign('uid', $this->_iId);
			$oSmarty->assign('last_login', Helper::formatTimestamp($this->_aData['last_login']));
			$oSmarty->assign('date', Helper::formatTimestamp($this->_aData['date']));
			$oSmarty->assign('user', $this->_aData);
			$oSmarty->assign('avatar_100', Helper::getAvatar('user', 100, $this->_iId, $aGravatar));
			$oSmarty->assign('avatar_popup', Helper::getAvatar('user', 'popup', $this->_iId, $aGravatar));

			# Manage PageTitle
			$this->_sName = $this->_aData['name'];
			$this->_setTitle($this->_sName . ' ' . $this->_aData['surname']);

			# Language
			$oSmarty->assign('lang_about_himself', str_replace('%u', $this->_sName, LANG_USER_SHOW_USER_LABEL_DESCRIPTION));
			$oSmarty->assign('lang_contact', LANG_GLOBAL_CONTACT);
			$oSmarty->assign('lang_contact_via_mail', str_replace('%u', $this->_sName, LANG_USER_SHOW_USER_ACTION_CONTACT_VIA_EMAIL));
      $oSmarty->assign('lang_last_login', LANG_USER_SHOW_USER_LABEL_LAST_LOGIN);
      $oSmarty->assign('lang_registered_since', LANG_USER_SHOW_USER_REGISTERED_SINCE);
			$oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);

      $oSmarty->cache_dir = CACHE_DIR;
      $oSmarty->compile_dir = COMPILE_DIR;
			$oSmarty->template_dir = Helper::getTemplateDir('users/show');
			return $oSmarty->fetch('users/show.tpl');
		}
	}

	# @Override
	public function destroy() {
    if (USER_RIGHT == 4) {
      if ($this->_oModel->destroy($this->_iId) === true)
        return Helper::successMessage(LANG_SUCCESS_DESTROY, '/User');
      else
        return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/User');
    }
    else
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
  }

  # @ Override due registration (avoid user right level 3)
	# Merge with function below?
	public function create() {
		if (isset($this->_aRequest['create_user']))
			return $this->_create();
		else
			return $this->_showCreateUserTemplate();
	}

	private function _create() {
		if (!isset($this->_aRequest['name']) || empty($this->_aRequest['name']))
			$this->_aError['name'] = LANG_ERROR_FORM_MISSING_NAME;

		if (Helper::checkEmailAddress($this->_aRequest['email']) == false)
			$this->_aError['email'] = LANG_ERROR_GLOBAL_WRONG_EMAIL_FORMAT;

		if (!isset($this->_aRequest['email']) || empty($this->_aRequest['email']))
			$this->_aError['email'] = LANG_ERROR_FORM_MISSING_EMAIL;

		if (Model_User::getExistingUser($this->_aRequest['email']) == false)
			$this->_aError['email'] = LANG_ERROR_USER_CREATE_EMAIL_ALREADY_EXISTS;

		if (!isset($this->_aRequest['password']) || empty($this->_aRequest['password']))
			$this->_aError['password'] = LANG_ERROR_FORM_MISSING_PASSWORD;

		if ($this->_aRequest['password'] !== $this->_aRequest['password2'])
			$this->_aError['password'] = LANG_ERROR_GLOBAL_PASSWORDS_DO_NOT_MATCH;

		if (USER_RIGHT < 4) {
			if (!isset($this->_aRequest['disclaimer']))
				$this->_aError['disclaimer'] = LANG_ERROR_GLOBAL_READ_DISCLAIMER;
		}

		if (isset($this->_aError))
			return $this->_showCreateUserTemplate();

		else {
			$this->_oModel = new Model_User($this->_aRequest, $this->_aSession);

			$iVerificationCode = Helper::createRandomChar(12, true);
			$sVerificationUrl = Helper::createLinkTo('/User/' . $iVerificationCode . '/verification');

			if ($this->_oModel->create($iVerificationCode) === true) {
				$sMailMessage = str_replace('%u', Helper::formatInput($this->_aRequest['name']),
												LANG_MAIL_USER_CREATE_BODY);
				$sMailMessage = str_replace('%v', $iVerificationCode, $sMailMessage);

				$bStatus = Mail::send(Helper::formatInput($this->_aRequest['email']),
												LANG_MAIL_USER_CREATE_SUBJECT,
												$sMailMessage,
												WEBSITE_MAIL_NOREPLY);

				if($bStatus == true)
					return Helper::successMessage(LANG_USER_CREATE_SUCCESSFUL, '/Session/create');

				else
					return Helper::errorMessage (LANG_ERROR_MAIL_ERROR);
			}
			else
				return Helper::errorMessage(LANG_ERROR_SQL_QUERY);
		}
	}

	private function _showCreateUserTemplate() {
		$oSmarty = new Smarty();

		$oSmarty->assign('USER_RIGHT', USER_RIGHT);

		$sName = isset($this->_aRequest['name']) ? Helper::formatInput($this->_aRequest['name']) : '';
		$oSmarty->assign('name', $sName);
		$sSurname = isset($this->_aRequest['surname']) ? Helper::formatInput($this->_aRequest['surname']) : '';
		$oSmarty->assign('surname', $sSurname);
		$sEmail = isset($this->_aRequest['email']) ? Helper::formatInput($this->_aRequest['email']) : '';
		$oSmarty->assign('email', $sEmail);

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $oSmarty->assign('error_' . $sField, $sMessage);
    }

		# AJAX reload disclaimer
		$oSmarty->assign('_public_folder_', WEBSITE_CDN . '/public/images');

		# Language
		$oSmarty->assign('lang_disclaimer_read', LANG_GLOBAL_TERMS_READ);
		$oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
		$oSmarty->assign('lang_headline', LANG_GLOBAL_REGISTRATION);
		$oSmarty->assign('lang_name', LANG_GLOBAL_NAME);
		$oSmarty->assign('lang_optional', LANG_GLOBAL_OPTIONAL);
		$oSmarty->assign('lang_password', LANG_GLOBAL_PASSWORD);
		$oSmarty->assign('lang_password_repeat', LANG_GLOBAL_PASSWORD_REPEAT);
		$oSmarty->assign('lang_submit', LANG_GLOBAL_REGISTER);
		$oSmarty->assign('lang_surname', LANG_GLOBAL_SURNAME);

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
		$oSmarty->template_dir = Helper::getTemplateDir('users/createUser');
		return $oSmarty->fetch('users/createUser.tpl');
	}

	public function verifyEmail() {
		if (empty($this->_iId))
			return Helper::errorMessage(LANG_ERROR_GLOBAL_WRONG_ID, '/Start');

		elseif ($this->_oModel->verifyEmail($this->_iId) === true)
			return Helper::successMessage(LANG_USER_VERIFICATION_SUCCESSFUL, '/Start');

		else
			return Helper::errorMessage(LANG_ERROR_USER_VERIFICATION, '/Start');
	}
}