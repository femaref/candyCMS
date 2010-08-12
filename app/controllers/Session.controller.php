<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/models/Session.model.php';

class Session extends Main {
  protected $m_aRequest;
  protected $m_oSession;

  public function __init() {
    $this->_oModel = new Model_Session($this->m_aRequest, $this->m_oSession);
  }

  /*
   * @ Override
   */
  public final function create() {
    if( isset($this->m_aRequest['create_session']) &&
            isset($this->m_aRequest['email']) &&
            isset($this->m_aRequest['password']) &&
            !empty($this->m_aRequest['email']) &&
            !empty($this->m_aRequest['password']) )
      return $this->_oModel->create();
    else
      return $this->showCreateSessionTemplate();
  }

  public final function showCreateSessionTemplate() {
    $oSmarty = new Smarty();
    $oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
    $oSmarty->assign('lang_login', LANG_GLOBAL_LOGIN);
    $oSmarty->assign('lang_password', LANG_GLOBAL_PASSWORD);

    $oSmarty->template_dir = Helper::templateDir('session/createSession');
    return $oSmarty->fetch('session/createSession.tpl');
  }

  public final function createNewPassword() {
    if( isset($this->m_aRequest['email']) && !empty($this->m_aRequest['email']) ) {

      $sStatus =& $this->_oModel->createNewPassword();
      if( $sStatus == true )
        return Helper::successMessage(LANG_LOGIN_PASSWORD_LOST_MAIL_SUCCESS).
                $this->showCreateSessionTemplate();
      else
        return Helper::errorMessage(LANG_ERROR_MAIL_FAILED_SUBJECT).
                $this->_showCreateNewPasswordTemplate();
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

    $oSmarty->template_dir = Helper::templateDir('session/createNewPassword');
    return $oSmarty->fetch('session/createNewPassword.tpl');
  }

  public final function destroy($sMsg = true) {
    $oStatus =& $this->_oModel->destroy();

    if( $sMsg == true )
      return Helper::successMessage(LANG_LOGIN_LOGOUT_SUCCESSFUL).
              Helper::redirectTo('/Start');
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
				WEBSITE_MAIL_NOREPLY);

		return Helper::successMessage(LANG_LOGIN_INVITATION_SUCCESSFUL);
	} */
}