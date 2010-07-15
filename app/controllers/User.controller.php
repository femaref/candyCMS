<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/controllers/Login.controller.php';
require_once 'app/controllers/Mail.controller.php';
require_once 'app/models/User.model.php';
require_once 'app/helpers/Image.helper.php';
require_once 'app/helpers/Upload.helper.php';

class User extends Main {
	public final function __init() {
		$this->_oModel = new Model_User($this->m_aRequest, $this->m_oSession, $this->m_aFile);
	}

	# @Override
	public function update() {
		if( empty($this->_iID) )
			$this->_iID = USERID;

		if( USERID == 0 )
			return Helper::errorMessage(LANG_ERROR_LOGIN_FIRST, LANG_ERROR_GLOBAL_NO_PERMISSION);
		else {
			if( isset($this->m_aRequest['update_user']) )
				return $this->_update($this->_iID);
			elseif( isset($this->m_aRequest['create_avatar']) )
				return $this->_createAvatar($this->_iID);
			else
				return $this->_showFormTemplate();
		}
	}

	private function _createAvatar() {
		$iAgreement = isset($this->m_aRequest['agreement']) ? 1 : 0;

		if( $iAgreement == 0 )
			return Helper::errorMessage(LANG_ERROR_USER_SETTINGS_UPLOAD_AGREEMENT).
					$this->_showFormTemplate();
		else {
			$oUpload = new Upload($this->m_aRequest, $this->m_aFile);
			return $oUpload->uploadAvatarFile(false).
					$this->_showFormTemplate();
		}
	}

	protected function _showFormTemplate($bUseRequest = false) {
		$oSmarty = new Smarty();
		if( $this->_iID !== USERID && USERRIGHT == 4) {
			$this->_aData = $this->_oModel->getData($this->_iID);

			$oSmarty->assign('uid',
					$this->_iID	);
			$oSmarty->assign('name',
					$this->_aData['name'] );
			$oSmarty->assign('surname',
					$this->_aData['surname'] );
			$oSmarty->assign('email',
					$this->_aData['email'] );
			$oSmarty->assign('description',
					$this->_aData['description'] );
			$oSmarty->assign('newsletter_default',
					(int)$this->_aData['newsletter_default'] );
			$oSmarty->assign('userright',
					(int)$this->_aData['userright'] );
		}
		else {
		# Avoid redisplay-Bug
			if( $bUseRequest == true ) {
				$this->m_oSession['userdata']['name'] =& $this->m_aRequest['name'];
				$this->m_oSession['userdata']['surname'] =& $this->m_aRequest['surname'];
				$this->m_oSession['userdata']['email'] =& $this->m_aRequest['email'];
				$this->m_oSession['userdata']['description'] =& $this->m_aRequest['description'];
				$this->m_oSession['userdata']['newsletter_default'] =& $this->m_aRequest['newsletter_default'];
			}

			$oSmarty->assign('uid',
					USERID );
			$oSmarty->assign('name',
					$this->m_oSession['userdata']['name'] );
			$oSmarty->assign('surname',
					$this->m_oSession['userdata']['surname'] );
			$oSmarty->assign('email',
					$this->m_oSession['userdata']['email'] );
			$oSmarty->assign('description',
					$this->m_oSession['userdata']['description'] );
			$oSmarty->assign('newsletter_default',
					(int)$this->m_oSession['userdata']['newsletter_default'] );

			# Avoid Smarty Bug if you're an administrator
			$oSmarty->assign('userright', USERRIGHT );
		}

		$oSmarty->assign('avatar100', Helper::getAvatar('user/100/', $this->_iID) );

		# Set Form params
		$oSmarty->assign('action', '/User/Settings' );
		$oSmarty->assign('style', 'display:none' );

		# Set _own_ USERRIGHT and USERID for updating purposes
		$oSmarty->assign('USERID', USERID );
		$oSmarty->assign('UR', USERRIGHT );

		# Language
		$oSmarty->assign('lang_about_you', LANG_USER_SETTINGS_ABOUT_YOU);
		$oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
		$oSmarty->assign('lang_headline', LANG_USER_SETTINGS_HEADLINE);
		$oSmarty->assign('lang_image_agreement', LANG_USER_SETTINGS_IMAGE_AGREEMENT);
		$oSmarty->assign('lang_image_change', LANG_USER_SETTINGS_IMAGE_CHANGE);
		$oSmarty->assign('lang_image_choose', LANG_USER_SETTINGS_IMAGE_CHOOSE);
		$oSmarty->assign('lang_image_upload', LANG_USER_SETTINGS_IMAGE_UPLOAD);
		$oSmarty->assign('lang_image_upload_info', LANG_USER_SETTINGS_IMAGE_UPLOAD_INFO);
		$oSmarty->assign('lang_name', LANG_GLOBAL_NAME);
		$oSmarty->assign('lang_newsletter', LANG_USER_SETTINGS_NEWSLETTER);
		$oSmarty->assign('lang_password_change', LANG_USER_SETTINGS_PASSWORD_CHANGE);
		$oSmarty->assign('lang_password_new', LANG_USER_SETTINGS_PASSWORD_NEW);
		$oSmarty->assign('lang_password_old', LANG_USER_SETTINGS_PASSWORD_OLD);
		$oSmarty->assign('lang_password_repeat', LANG_GLOBAL_PASSWORD_REPEAT);
		$oSmarty->assign('lang_required', LANG_GLOBAL_REQUIRED);
		$oSmarty->assign('lang_submit', LANG_USER_SETTINGS_SUBMIT);
		$oSmarty->assign('lang_surname', LANG_GLOBAL_SURNAME);
		$oSmarty->assign('lang_userright', LANG_GLOBAL_USERRIGHT);
		$oSmarty->assign('lang_userright_1', LANG_GLOBAL_USERRIGHT_1);
		$oSmarty->assign('lang_userright_2', LANG_GLOBAL_USERRIGHT_2);
		$oSmarty->assign('lang_userright_3', LANG_GLOBAL_USERRIGHT_3);
		$oSmarty->assign('lang_userright_4', LANG_GLOBAL_USERRIGHT_4);

		$oSmarty->template_dir = Helper::templateDir('user/_form');
		return $oSmarty->fetch('user/_form.tpl').
				$oSmarty->fetch('user/createAvatar.tpl');
	}

	protected function _update() {
		$sError = '';

		if(	!isset($this->m_aRequest['name']) ||
				empty($this->m_aRequest['name']))
			$sError .= LANG_GLOBAL_NAME.	'<br />';

		if(	!isset($this->m_aRequest['email']) ||
				empty($this->m_aRequest['email']))
			$sError .= LANG_GLOBAL_EMAIL.	'<br />';

		if( empty($this->m_aRequest['oldpw']) &&
				!empty($this->m_aRequest['newpw']) &&
				!empty($this->m_aRequest['newpw2']) )
			$sError .= LANG_ERROR_USER_SETTINGS_PW_OLD.	'<br />';

		if( !empty($this->m_aRequest['oldpw']) &&
				md5(RANDOM_HASH.$this->m_aRequest['oldpw']) !==
				$this->m_oSession['userdata']['password'] )
			$sError .= LANG_ERROR_USER_SETTINGS_PW_OLD_WRONG.	'<br />';

		if( !empty($this->m_aRequest['oldpw']) && (
				empty($this->m_aRequest['newpw']) ||
				empty($this->m_aRequest['newpw2']) ))
			$sError .= LANG_ERROR_USER_SETTINGS_PW_NEW.	'<br />';

		if( $this->m_aRequest['newpw'] !== $this->m_aRequest['newpw2'] )
			$sError .= LANG_ERROR_USER_SETTINGS_PW_NEW_WRONG.	'<br />';

		if( !empty($sError) ) {
			$sReturn  = Helper::errorMessage($sError);
			$sReturn .= $this->_showFormTemplate();
			return $sReturn;
		}
		else {
			if($this->_oModel->update($this->_iID) == true)
				return Helper::successMessage(LANG_SUCCESS_UPDATE).
						$this->_showFormTemplate(true);
			else
				return Helper::errorMessage(LANG_ERROR_DB_QUERY);
		}
	}

	public function show($iUID = '') {
	# Fix to avoid empty UID on /User/Settings shortcut
		if( !empty($iUID))
			$this->_iID = (int)$iUID;

		$this->_aData = $this->_oModel->getData($this->_iID);

		# Get ImageSize to adjust the spacer.gif
		$aPopupInfo	= @getImageSize(PATH_UPLOAD.	'/'.
				Helper::getAvatar('user/' .POPUP_DEFAULT_X.  '/', $this->_iID));
		$aThumbInfo		= @getImageSize(PATH_UPLOAD.	'/'	.
				Helper::getAvatar('user/100/', $this->_iID));

		# Description Fix, format Code to BB
		$this->_aData['description'] = Helper::formatBBCode($this->_aData['description']);

		$oSmarty = new Smarty();
		$oSmarty->assign('uid', $this->_iID);
		$oSmarty->assign('UR', USERRIGHT);
		$oSmarty->assign('last_login',
				Helper::formatTimestamp($this->_aData['last_login']) );
		$oSmarty->assign('regdate',
				Helper::formatTimestamp($this->_aData['regdate']) );
		$oSmarty->assign('user', $this->_aData );
		$oSmarty->assign('avatar100', Helper::getAvatar('user/100/', $this->_iID) );
		$oSmarty->assign('avatarPopup',
				Helper::getAvatar('user/' .POPUP_DEFAULT_X.  '/', $this->_iID) );
		$oSmarty->assign('avatarPopupInfo', $aPopupInfo);
		$oSmarty->assign('avatarThumbInfo', $aThumbInfo);

		# Manage PageTitle
		$this->_sName = $this->_aData['name'];
		$this->_setTitle($this->_sName.	' '	.$this->_aData['surname']);

		# Language
		$oSmarty->assign('lang_about_himself',
				str_replace('%u', $this->_sName, LANG_USER_ABOUT_HIMSELF) );
		$oSmarty->assign('lang_contact', LANG_GLOBAL_CONTACT );
		$oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE );
		$oSmarty->assign('lang_last_login', LANG_USER_LAST_LOGIN );
		$oSmarty->assign('lang_registered_since', LANG_USER_REGISTERED_SINCE );

		$oSmarty->template_dir = Helper::templateDir('user/show');
		return $oSmarty->fetch('user/show.tpl');
	}

	public function overview() {
		$this->_aData = $this->_oModel->getData();

		$oSmarty = new Smarty();
		$oSmarty->assign('user', $this->_aData);
		$oSmarty->assign('UR', USERRIGHT);

		# Language
		$oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY);
		$oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);
		$oSmarty->assign('lang_headline', LANG_GLOBAL_USERMANAGER);
		$oSmarty->assign('lang_last_login', LANG_USER_LAST_LOGIN );
		$oSmarty->assign('lang_registered_since', LANG_USER_REGISTERED_SINCE );

		$oSmarty->template_dir = Helper::templateDir('user/overview');
		return $oSmarty->fetch('user/overview.tpl');
	}

	# @Override
	public function destroy() {
		if( USERRIGHT == 4) {
			if($this->_oModel->delete($this->_iID) == true)
				return Helper::successMessage(LANG_SUCCESS_DESTROY).
						$this->showOverview();
			else
				return Helper::errorMessage(LANG_ERROR_DB_QUERY);
		}
		else
			return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
	}

	public function create() {
		if( isset($this->m_aRequest['create_user']) )
			return $this->_create();
		else
			return $this->_showCreateUserTemplate();
	}

	private function _showCreateUserTemplate() {
		$oSmarty = new Smarty();

		$sName = isset($this->m_aRequest['name']) ? Helper::formatHTMLCode($this->m_aRequest['name']) : '';
		$oSmarty->assign('name', $sName);
		$sSurname = isset($this->m_aRequest['surname']) ? Helper::formatHTMLCode($this->m_aRequest['surname']) : '';
		$oSmarty->assign('surname', $sSurname);
		$sEmail = isset($this->m_aRequest['email']) ? Helper::formatHTMLCode($this->m_aRequest['email']) : '';
		$oSmarty->assign('email', $sEmail);

		# Language
		$oSmarty->assign('lang_disclaimer_read', LANG_LOGIN_REGISTRATION_DISCLAIMER_READ);
		$oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
		$oSmarty->assign('lang_headline', LANG_GLOBAL_REGISTRATION);
		$oSmarty->assign('lang_name', LANG_GLOBAL_NAME);
		$oSmarty->assign('lang_optional', LANG_GLOBAL_OPTIONAL);
		$oSmarty->assign('lang_password', LANG_GLOBAL_PASSWORD);
		$oSmarty->assign('lang_password_repeat', LANG_GLOBAL_PASSWORD_REPEAT);
		$oSmarty->assign('lang_submit', LANG_GLOBAL_REGISTER);
		$oSmarty->assign('lang_surname', LANG_GLOBAL_SURNAME);

		$oSmarty->template_dir = Helper::templateDir('user/createUser');
		return $oSmarty->fetch('user/createUser.tpl');
	}

	private function _create() {
		$sError = '';
		if( empty($this->m_aRequest['name']) )
			$sError .= LANG_ERROR_LOGIN_ENTER_NAME.	'<br />';

		if( empty($this->m_aRequest['email']) )
			$sError .= LANG_ERROR_LOGIN_ENTER_EMAIL.	'<br />';

		if( empty($this->m_aRequest['password']) )
			$sError .= LANG_ERROR_LOGIN_ENTER_PASSWORD.	'<br />';

		if( $this->m_aRequest['password'] !== $this->m_aRequest['password2'] )
			$sError .= LANG_ERROR_LOGIN_CHECK_PASSWORDS.	'<br />';

		if( !isset($this->m_aRequest['disclaimer']) )
			$sError .= LANG_ERROR_LOGIN_CHECK_DISCLAIMER.	'<br />';

		if( !empty($sError) ) {
			$sReturn  = Helper::errorMessage($sError);
			$sReturn .= $this->_showCreateUserTemplate();
			return $sReturn;
		}
		else {
		# Switch to user model
		# @Override Model
		# NOTE: Dirty method, no OO used
			$this->_oController = new Login($this->m_aRequest, $this->m_oSession);
			$this->_oModel = new Model_User($this->m_aRequest, $this->m_oSession);

			if( $this->_oModel->create() == true) {
				$sMail = str_replace('%n', Helper::formatHTMLCode($this->m_aRequest['name']),
						LANG_LOGIN_REGISTRATION_MAIL_BODY);

				$bStatus = Mail::send(	Helper::formatHTMLCode($this->m_aRequest['email']),
						LANG_LOGIN_REGISTRATION_MAIL_SUBJECT,
						$sMail,
						false,
						WEBSITE_MAIL_NOREPLY);

        if($bStatus == true)
          return Helper::successMessage(LANG_LOGIN_REGISTRATION_SUCCESSFUL).
              $this->_oController->showCreateSessionTemplate();
        else
          return Helper::errorMessage(LANG_ERROR_MAIL_FAILED_SUBJECT);
			}
			else
				return Helper::errorMessage(LANG_ERROR_DB_QUERY);
		}
	}
}