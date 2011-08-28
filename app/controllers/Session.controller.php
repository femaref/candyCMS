<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/models/Session.model.php';

class Session extends Main {

  public function __init() {
    $this->_oModel = new Model_Session($this->_aRequest, $this->_aSession);
  }

  # @ Override
  public function create() {
		if( isset($this->_aRequest['create_session']) )
			return $this->_create();
		else
			return $this->showCreateSessionTemplate();
	}

	private function _create() {
		if(	!isset($this->_aRequest['email']) || empty($this->_aRequest['email']) )
			$this->_aError['email'] = LANG_ERROR_FORM_MISSING_EMAIL;

    if (Helper::checkEmailAddress($this->_aRequest['email']) == false)
      $this->_aError['email'] = LANG_ERROR_GLOBAL_WRONG_EMAIL_FORMAT;

		if(	!isset($this->_aRequest['password']) || empty($this->_aRequest['password']) )
			$this->_aError['password'] = LANG_ERROR_FORM_MISSING_PASSWORD;

		if (isset($this->_aError))
      return $this->showCreateSessionTemplate();

		elseif( $this->_oModel->create() === true )
			Helper::successMessage(LANG_SESSION_CREATE_SUCCESSFUL, '/');

		else
      Helper::errorMessage(LANG_ERROR_SESSION_CREATE, '/session/create');
	}

  public function showCreateSessionTemplate() {
    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->_oSmarty->assign('error_' . $sField, $sMessage);
    }

    $this->_oSmarty->assign('email', isset($this->_aRequest['email']) ? (string) $this->_aRequest['email'] : '');

    $this->_oSmarty->assign('lang_lost_password', LANG_SESSION_PASSWORD_TITLE);
    $this->_oSmarty->assign('lang_resend_verification', LANG_SESSION_VERIFICATION_TITLE);

    $this->_oSmarty->template_dir = Helper::getTemplateDir('sessions', 'create');
    return $this->_oSmarty->fetch('create.tpl');
  }

  public function createResendActions() {
    if (isset($this->_aRequest['email'])) {
      if (isset($this->_aRequest['email']) && ( Helper::checkEmailAddress($this->_aRequest['email']) == false ))
        $this->_aError['email'] = LANG_ERROR_GLOBAL_WRONG_EMAIL_FORMAT;

      if (!isset($this->_aRequest['email']) || empty($this->_aRequest['email']))
        $this->_aError['email'] = LANG_ERROR_FORM_MISSING_EMAIL;

      if (isset($this->_aError))
        return $this->_showCreateResendActionsTemplate();

      else {
        if ($this->_aRequest['action'] == 'resendpassword') {
          $sNewPasswordClean	= Helper::createRandomChar(10);
          $sNewPasswordSecure = md5(RANDOM_HASH . $sNewPasswordClean);

          if($this->_oModel->createResendActions($sNewPasswordSecure) === true) {
            $aData = $this->_oModel->getData();

            $sContent = str_replace('%u', $aData['name'], LANG_MAIL_SESSION_PASSWORD_BODY);
            $sContent = str_replace('%p',$sNewPasswordClean, $sContent);

            $bStatus = Mail::send(Helper::formatInput($this->_aRequest['email']),
                            LANG_MAIL_SESSION_PASSWORD_SUBJECT,
                            $sContent,
                            WEBSITE_MAIL_NOREPLY);

            if ($bStatus == true)
              Helper::successMessage(LANG_SESSION_PASSWORD_CREATE_SUCCESSFUL, '/session/create');
            else
              Helper::errorMessage(LANG_ERROR_MAIL_ERROR) . $this->showCreateSessionTemplate();
          }
          else
            Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/');
        }
        elseif ($this->_aRequest['action'] == 'resendverification') {
          if($this->_oModel->createResendActions() === true) {
            $aData = $this->_oModel->getData();

            $sVerificationUrl = Helper::createLinkTo('/user/' . $aData['verification_code'] . '/verification');

            $sContent = str_replace('%u', $aData['name'], LANG_MAIL_SESSION_VERIFICATION_BODY);
            $sContent = str_replace('%v', $sVerificationUrl, $sContent);

            $bStatus = Mail::send(Helper::formatInput($this->_aRequest['email']),
                            LANG_MAIL_SESSION_VERIFICATION_SUBJECT,
                            $sContent,
                            WEBSITE_MAIL_NOREPLY);

            if ($bStatus == true)
              Helper::successMessage(LANG_SUCCESS_MAIL_SENT, '/session/create');
            else
              return $this->showCreateSessionTemplate();
          }
          else
            Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/');
        }
        else
          Helper::errorMessage(LANG_ERROR_REQUEST_MISSING_ACTION, '/');
      }
    }
    else
      return $this->_showCreateResendActionsTemplate();
  }

  private function _showCreateResendActionsTemplate() {
    if($this->_aRequest['action'] == 'resendpassword') {
      $this->_setTitle(LANG_SESSION_PASSWORD_TITLE);
      $this->_setDescription(LANG_SESSION_PASSWORD_INFO);

      $this->_oSmarty->assign('_action_url_', '/session/resendpassword');

      $this->_oSmarty->assign('lang_headline', LANG_SESSION_PASSWORD_TITLE);
      $this->_oSmarty->assign('lang_description', LANG_SESSION_PASSWORD_INFO);
      $this->_oSmarty->assign('lang_submit', LANG_SESSION_PASSWORD_LABEL_SUBMIT);
    }
    else {
      $this->_setTitle(LANG_SESSION_VERIFICATION_TITLE);
      $this->_setDescription(LANG_SESSION_VERIFICATION_INFO);

      $this->_oSmarty->assign('_action_url_', '/session/resendverification');

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

  public function destroy() {
    if (USER_RIGHT == 2) {
      $oFacebook = new FacebookCMS(array(
                  'appId' => FACEBOOK_APP_ID,
                  'secret' => FACEBOOK_SECRET,
                  'cookie' => true,
              ));
      die($oFacebook->getLogoutUrl());
      Header('Location:' . $oFacebook->getLogoutUrl());

      # Message will not be printed
      Helper::successMessage(LANG_SESSION_DESTROY_SUCCESSFUL, '/');
    }
    elseif ($this->_oModel->destroy() === true) {
      Helper::successMessage(LANG_SESSION_DESTROY_SUCCESSFUL, '/');
      unset($_SESSION);
    }
    else
      Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/');
  }
}