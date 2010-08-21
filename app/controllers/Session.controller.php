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
		if (isset($this->m_aRequest['create_session']) &&
						isset($this->m_aRequest['email']) &&
						isset($this->m_aRequest['password']) &&
						!empty($this->m_aRequest['email']) &&
						!empty($this->m_aRequest['password'])) {

			if ($this->_oModel->create() == true) {
				if (empty($this->_aData['last_login']))
					return Helper::successMessage(LANG_LOGIN_LOGIN_SUCCESSFUL) .
							Helper::redirectTo('/Start');
			}
			else
				return Helper::errorMessage(LANG_ERROR_DB_QUERY);
		}
		else
			return $this->showCreateSessionTemplate();
	}

  public final function showCreateSessionTemplate() {
    $oSmarty = new Smarty();
    $oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
    $oSmarty->assign('lang_login', LANG_GLOBAL_LOGIN);
    $oSmarty->assign('lang_password', LANG_GLOBAL_PASSWORD);

    $oSmarty->template_dir = Helper::getTemplateDir('session/createSession');
    return $oSmarty->fetch('session/createSession.tpl');
  }

  public final function createNewPassword() {
    if( isset($this->m_aRequest['email']) && !empty($this->m_aRequest['email']) ) {

      if( $this->_oModel->createNewPassword() == true ) {
        return Helper::successMessage(LANG_LOGIN_PASSWORD_LOST_MAIL_SUCCESS).
                $this->showCreateSessionTemplate();
			} else
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

    $oSmarty->template_dir = Helper::getTemplateDir('session/createNewPassword');
    return $oSmarty->fetch('session/createNewPassword.tpl');
  }

  public final function destroy() {
    if($oStatus =& $this->_oModel->destroy() == true) {
			unset($_SESSION);
      return Helper::successMessage(LANG_LOGIN_LOGOUT_SUCCESSFUL).
              Helper::redirectTo('/Start');
		}
		else
		 return Helper::errorMessage(LANG_ERROR_DB_QUERY);
  }
}