<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/models/Session.model.php';

class Session extends Main {
  protected $_aRequest;
  protected $_aSession;

  public function __init() {
    $this->_oModel = new Model_Session($this->_aRequest, $this->_aSession);
  }

  /*
   * @ Override
   */
  public final function create() {
		if (isset($this->_aRequest['create_session']) &&
						isset($this->_aRequest['email']) &&
						isset($this->_aRequest['password']) &&
						!empty($this->_aRequest['email']) &&
						!empty($this->_aRequest['password'])) {

			if ($this->_oModel->create() == true)
				return Helper::successMessage(LANG_LOGIN_LOGIN_SUCCESSFUL) .
					Helper::redirectTo('/Start');
			else
				return Helper::errorMessage(LANG_ERROR_LOGIN_WRONG_USERDATA, LANG_ERROR_LOGIN_HEADLINE).
					$this->showCreateSessionTemplate();
		}
		else
			return $this->showCreateSessionTemplate();

	}

  public final function showCreateSessionTemplate() {
    $oSmarty = new Smarty();
    $oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
    $oSmarty->assign('lang_login', LANG_GLOBAL_LOGIN);
    $oSmarty->assign('lang_lost_password', LANG_LOGIN_PASSWORD_LOST);
    $oSmarty->assign('lang_password', LANG_GLOBAL_PASSWORD);
    $oSmarty->assign('lang_resend_verification', LANG_LOGIN_RESEND_VERIFICATION);

    $oSmarty->template_dir = Helper::getTemplateDir('session/createSession');
    return $oSmarty->fetch('session/createSession.tpl');
  }

  public final function createResendActions() {
    if (isset($this->_aRequest['email']) && !empty($this->_aRequest['email'])) {

      if ($this->_aRequest['action'] == 'resendpassword') {
        $sNewPasswordClean	= Helper::createRandomChar(10);
        $sNewPasswordSecure = md5(RANDOM_HASH . $sNewPasswordClean);

        if($this->_oModel->createResendActions($sNewPasswordSecure) == true) {
          $aData = $this->_oModel->getData();

          $sContent = str_replace('%u', $aData['name'], LANG_LOGIN_PASSWORD_LOST_MAIL_BODY);
          $sContent = str_replace('%p',$sNewPasswordClean, $sContent);

          $bStatus = Mail::send(Helper::formatInput($this->_aRequest['email']),
                          LANG_LOGIN_PASSWORD_LOST_MAIL_SUBJECT,
                          $sContent,
                          WEBSITE_MAIL_NOREPLY);

          if ($bStatus == true)
            return Helper::successMessage(LANG_SUCCESS_MAIL_SENT) . $this->showCreateSessionTemplate();
          else
            Helper::errorMessage(LANG_ERROR_MAIL_FAILED_SUBJECT) . $this->showCreateSessionTemplate();
        }
        else
          Helper::errorMessage(LANG_ERROR_DB_QUERY);
      }
      elseif ($this->_aRequest['action'] == 'resendverification') {
        if($this->_oModel->createResendActions() == true) {
          $aData = $this->_oModel->getData();

          $sVerificationUrl = Helper::createLinkTo('/User/' . $aData['verification_code'] . '/verification');

          $sContent = str_replace('%u', $aData['name'], LANG_LOGIN_RESEND_VERIFICATION_MAIL_BODY);
          $sContent = str_replace('%v', $sVerificationUrl, $sContent);

          $bStatus = Mail::send(Helper::formatInput($this->_aRequest['email']),
                          LANG_LOGIN_RESEND_VERIFICATION_MAIL_SUBJECT,
                          $sContent,
                          WEBSITE_MAIL_NOREPLY);

          if ($bStatus == true)
            return Helper::successMessage(LANG_SUCCESS_MAIL_SENT) . $this->showCreateSessionTemplate();
          else
            Helper::errorMessage(LANG_ERROR_MAIL_FAILED_SUBJECT) . $this->showCreateSessionTemplate();
        }
        else
          Helper::errorMessage(LANG_ERROR_DB_QUERY);
      }
      else
        return Helper::errorMessage(LANG_ERROR_ACTION_NOT_SPECIFIED);
    }
    else
      return $this->_showCreateResendActionsTemplate();
  }

  private final function _showCreateResendActionsTemplate() {
    $oSmarty = new Smarty();

    if($this->_aRequest['action'] == 'resendpassword') {
      $this->_setTitle(LANG_LOGIN_PASSWORD_LOST);

      $oSmarty->assign('action', '/Session/resendpassword');

      # Language
      $oSmarty->assign('lang_headline', LANG_LOGIN_PASSWORD_LOST);
      $oSmarty->assign('lang_description', LANG_LOGIN_PASSWORD_LOST_DESCRIPTION);
      $oSmarty->assign('lang_submit', LANG_LOGIN_PASSWORD_SEND);
    }
    else {
      $this->_setTitle(LANG_LOGIN_RESEND_VERIFICATION);

      $oSmarty->assign('action', '/Session/resendverification');

      # Language
      $oSmarty->assign('lang_headline', LANG_LOGIN_RESEND_VERIFICATION);
      $oSmarty->assign('lang_description', LANG_LOGIN_RESEND_VERIFICATION_DESCRIPTION);
      $oSmarty->assign('lang_submit', LANG_LOGIN_RESEND_VERIFICATION_SEND);
    }

    $oSmarty->template_dir = Helper::getTemplateDir('session/createResendActions');
    return $oSmarty->fetch('session/createResendActions.tpl');
  }

  public final function destroy() {
    if ($oStatus = & $this->_oModel->destroy() == true) {
      unset($_SESSION);
      return Helper::redirectTo('/Start') . Helper::successMessage(LANG_LOGIN_LOGOUT_SUCCESSFUL);
    }
    else
      return Helper::errorMessage(LANG_ERROR_DB_QUERY);
  }
}