<?php

/*
 * This software is copyright protected. Use only allowed on licensed
 * websites. Contact author for further information or to receive a license.
 *
 * @link http://marcoraddatz.com
 * @copyright 2007 - 2008 Marco Raddatz
 * @author Marco Raddatz <mr at marcoraddatz dot com>
 * @package CMS
 * @version 1.0
 */

require_once 'app/models/Login.model.php';

class Login extends Main {
	protected $m_aRequest;
	protected $m_oSession;

	public function __init() {
		$this->_oModel = new Model_Login($this->m_aRequest, $this->m_oSession);
	}

	public final function createSession() {
		if( isset($this->m_aRequest['create_session']) &&
				isset($this->m_aRequest['email']) &&
				isset($this->m_aRequest['password']) &&
				!empty($this->m_aRequest['email']) &&
				!empty($this->m_aRequest['password']) )
			return $this->_oModel->createSession();
		else
			return $this->showCreateSessionTemplate();
	}

	public final function showCreateSessionTemplate() {
		$oSmarty = new Smarty();
		$oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
		$oSmarty->assign('lang_login', LANG_GLOBAL_LOGIN);
		$oSmarty->assign('lang_password', LANG_GLOBAL_PASSWORD);

		$oSmarty->template_dir = Helper::templateDir('login/createSession');
		return $oSmarty->fetch('login/createSession.tpl');
	}

	public final function createNewPassword() {
		if( isset($this->m_aRequest['email']) && !empty($this->m_aRequest['email']) ) {

			$sStatus =& $this->_oModel->createNewPassword();
			if( $sStatus == true )
				return Helper::successMessage(LANG_LOGIN_PASSWORD_LOST_MAIL_SUCCESS).
						$this->showCreateSessionTemplate();
			else
				return $sStatus.$this->_showCreateNewPasswordTemplate();
		}
		else
			return $this->_showCreateNewPasswordTemplate();
	}

	private final function _showCreateNewPasswordTemplate() {
		$oSmarty = new Smarty();

		# Language
		$oSmarty->assign('lang_headline', LANG_LOGIN_PASSWORD_LOST);
		$oSmarty->assign('lang_description', LANG_LOGIN_PASSWORD_LOST_DESCRIPTION);
		$oSmarty->assign('lang_submit', LANG_LOGIN_PASSWORD_SEND);

		$oSmarty->template_dir = Helper::templateDir('login/createNewPassword');
		return $oSmarty->fetch('login/createNewPassword.tpl');
	}

	public final function destroySession($sMsg = true) {
		$oStatus =& $this->_oModel->destroySession();

		if( $sMsg == true )
			return Helper::redirectTo('/Start');
	}

#private final function _verifyEmail() {}
# TODO: SHOULD BE MOVED TO V2
	/*public final function createInvite() {
		if( USERID == 0 )
			return Helper::errorMessage(LANG_ERROR_LOGIN_FIRST, LANG_ERROR_GLOBAL_NO_PERMISSION);
		else {
			if( isset($this->m_aRequest['invite_friend']) )
				return $this->_inviteFriendMail();
			else
				return $this->_showInviteFriendTemplate();
		}
	}

	private final function _showInviteFriendTemplate() {
		$sMessage = str_replace('%u', $this->m_oSession['userdata']['name'], LANG_LOGIN_INVITATION_MAIL_BODY);
		$sMessage = str_replace('%notes', LANG_LOGIN_INVITATION_OWN_NOTES, $sMessage);

		$oSmarty = new Smarty();
		$oSmarty->assign('message', $sMessage);

		# Language
		$oSmarty->assign('lang_content', LANG_GLOBAL_CONTENT);
		$oSmarty->assign('lang_headline', LANG_LOGIN_INVITATION_HEADLINE);
		$oSmarty->assign('lang_email_of_friend', LANG_LOGIN_INVITATION_EMAIL_OF_FRIEND);
		$oSmarty->assign('lang_own_message', LANG_LOGIN_INVITATION_OWN_MESSAGE);
		$oSmarty->assign('lang_submit', LANG_LOGIN_INVITATION_SUBMIT);

		if( is_file(PATH_TPL_ADDON.	'/loginInviteFriend.tpl') )
			$oSmarty->template_dir = PATH_TPL_ADDON;

		return $oSmarty->fetch('loginInviteFriend.tpl');
	}

	private final function _inviteFriendMail() {
		$sName = trim($this->m_oSession['userdata']['name'].	' '	.$this->m_oSession['userdata']['surname']);
		$sSubject = str_replace('%u', $sName, LANG_LOGIN_INVITATION_MAIL_SUBJECT);

		$sMessage = str_replace('%u', $this->m_oSession['userdata']['name'], LANG_LOGIN_INVITATION_MAIL_BODY);
		$sMessage = str_replace('%notes', Helper::formatHTMLCode($this->m_aRequest['notes']), $sMessage);

		Mail::send(	Helper::formatHTMLCode($this->m_aRequest['email']),
				$sSubject,
				$sMessage.LANG_MAIL_SIGNATURE,
				false,
				WEBSITE_MAIL_NOREPLY);

		return Helper::successMessage(LANG_LOGIN_INVITATION_SUCCESSFUL);
	} */
}