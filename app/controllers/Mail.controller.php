<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/models/Blog.model.php';
require_once 'app/controllers/User.controller.php';
require_once 'lib/recaptcha/recaptchalib.php';

class Mail extends Main {
  protected $_sRecaptchaPublicKey = RECAPTCHA_PUBLIC;
  protected $_sRecaptchaPrivateKey = RECAPTCHA_PRIVATE;
  protected $_oRecaptchaResponse = '';
  protected $_sRecaptchaError = '';

  public function create() {
    if (isset($this->_aRequest['send_mail'])) {
      # Disable at AJAX due to a bug in reloading JS code
      if (USER_RIGHT === 0 && RECAPTCHA_ENABLED === true && AJAX_REQUEST === false)
        return $this->_checkCaptcha();
      else
        return $this->_standardMail(false);
    }
    else {
      $bShowCaptcha = ( USER_RIGHT == 0 ) ? true : false;
      return $this->_showCreateMailTemplate($bShowCaptcha);
    }
  }

  protected function _showCreateMailTemplate($bShowCaptcha) {
    # Look for existing E-Mail address
    if( isset($this->_aRequest['email']))
      $sEmail = (string)$this->_aRequest['email'];

    elseif( isset($this->_aSession['userdata']['email']) )
      $sEmail = $this->_aSession['userdata']['email'];

    else
      $sEmail = USER_EMAIL;

    $sSubject = isset($this->_aRequest['subject']) ?
            (string)$this->_aRequest['subject']:
            '';

    $sContent = isset($this->_aRequest['content']) ?
            (string)$this->_aRequest['content']:
            '';

    $this->_oSmarty->assign('contact', Model_User::getUserNamesAndEmail($this->_iId));
    $this->_oSmarty->assign('content', $sContent);
    $this->_oSmarty->assign('email', $sEmail);
    $this->_oSmarty->assign('subject', $sSubject);

    if ($bShowCaptcha === true && RECAPTCHA_ENABLED === true)
      $this->_oSmarty->assign('_captcha_', recaptcha_get_html($this->_sRecaptchaPublicKey,
                      $this->_sRecaptchaError));

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->_oSmarty->assign('error_' . $sField, $sMessage);
    }

    # Create page title and description
    $this->_setDescription(LANG_GLOBAL_CONTACT);
    $this->_setTitle(LANG_GLOBAL_CONTACT);

    # Language
    $this->_oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
    $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_CONTACT);
		$this->_oSmarty->assign('lang_submit', LANG_GLOBAL_MAIL_SEND);

    $this->_oSmarty->template_dir = Helper::getTemplateDir('mails', 'create');
    return $this->_oSmarty->fetch('create.tpl');
  }

  protected function _checkCaptcha() {
    if( isset($this->_aRequest['recaptcha_response_field']) ) {
      $this->_oRecaptchaResponse = recaptcha_check_answer (
              $this->_sRecaptchaPrivateKey,
              $_SERVER['REMOTE_ADDR'],
              $this->_aRequest['recaptcha_challenge_field'],
              $this->_aRequest['recaptcha_response_field']);

      if ($this->_oRecaptchaResponse->is_valid)
        return $this->_standardMail(true);

      else {
        $this->_aError['captcha'] = LANG_ERROR_MAIL_CAPTCHA_NOT_CORRECT;
        return $this->_showCreateMailTemplate(true);
      }
    }
    else
      return Helper::errorMessage(LANG_ERROR_MAIL_CAPTCHA_NOT_LOADED, '/');
  }

  protected function _standardMail($bShowCaptcha = true) {
    if (!isset($this->_aRequest['email']) || empty($this->_aRequest['email']))
       $this->_aError['email'] = LANG_ERROR_FORM_MISSING_EMAIL;

		if (Helper::checkEmailAddress($this->_aRequest['email']) !== true)
			$this->_aError['email'] = LANG_ERROR_GLOBAL_WRONG_EMAIL_FORMAT;

    if (!isset($this->_aRequest['content']) || empty($this->_aRequest['content']))
       $this->_aError['content'] = LANG_ERROR_FORM_MISSING_CONTENT;

    if (isset($this->_aError))
      return $this->_showCreateMailTemplate($bShowCaptcha);

    else {
      # Select user name and surname
      require_once 'app/models/User.model.php';
      $aRow = Model_User::getUserNamesAndEmail($this->_iId);

			# When mail is set, send to mail. Otherwise send to system mail
      $sMailTo	= isset($aRow['email']) ? $aRow['email'] : WEBSITE_MAIL;

			# Reply to mail
			$sReplyTo = Helper::formatInput($this->_aRequest['email']);

      $sSendersName = isset($this->_aSession['userdata']['name']) ?
              $this->_aSession['userdata']['name'] :
              LANG_GLOBAL_SYSTEMBOT;

      $sSubject = isset($this->_aRequest['subject']) && !empty($this->_aRequest['subject']) ?
              Helper::formatInput($this->_aRequest['subject']) :
              str_replace('%u', $sSendersName, LANG_MAIL_GLOBAL_SUBJECT_BY);

      $sMessage = Helper::formatInput($this->_aRequest['content']);

      # Mail to, Subject, Message, Reply to
      $bStatus = Mail::send(	$sMailTo,
              $sSubject,
              $sMessage,
              $sReplyTo);

      if ($bStatus == true) {
        Log::insert($this->_aRequest['section'], 'create', (int) $this->_iId);
				return $this->_showSuccessMessage();
      }
			else
				return Helper::errorMessage(LANG_ERROR_MAIL_ERROR, '/');
    }
  }

  private function _showSuccessMessage() {
    $this->_setTitle(LANG_MAIL_GLOBAL_SENT_TITLE);

    $this->_oSmarty->assign('lang_info', LANG_MAIL_GLOBAL_SENT_INFO);
    $this->_oSmarty->assign('lang_title', LANG_MAIL_GLOBAL_SENT_TITLE);

    $this->_oSmarty->template_dir = Helper::getTemplateDir('mails', 'success');
    return $this->_oSmarty->fetch('success.tpl');
  }

  public static function send($sTo, $sSubject, $sMessage, $sReplyTo = WEBSITE_MAIL, $sAttachment = '') {
    require_once 'lib/phpmailer/class.phpmailer.php';

		# Parse message and replace with (footer) variables
		$sMessage = str_replace('%NOREPLY', LANG_MAIL_GLOBAL_NO_REPLY, $sMessage);
		$sMessage = str_replace('%SIGNATURE', LANG_MAIL_GLOBAL_SIGNATURE, $sMessage);
		$sMessage = str_replace('%WEBSITE_NAME', WEBSITE_NAME, $sMessage);
		$sMessage = str_replace('%WEBSITE_URL', WEBSITE_URL, $sMessage);

		$sSubject = str_replace('%WEBSITE_NAME', WEBSITE_NAME, $sSubject);
		$sSubject = str_replace('%WEBSITE_URL', WEBSITE_URL, $sSubject);

    $oMail = new PHPMailer(true);

    if (SMTP_ON == true)
      $oMail->IsSMTP();
    else
      $oMail->IsMail();

    try {
      if (SMTP_ON == true) {
        if (WEBSITE_DEV == true) {
          $oMail->SMTPDebug = 1;
          $oMail->SMTPAuth = false;
        }
        else {
          # enables SMTP debug information (for testing)
          $oMail->SMTPDebug = 0;
          $oMail->SMTPAuth = true;
        }

        $oMail->Host = SMTP_HOST;
        $oMail->Port = SMTP_PORT;
        $oMail->Username = SMTP_USER;
        $oMail->Password = SMTP_PASSWORD;
      }

      $oMail->CharSet = 'utf-8';
      $oMail->AddReplyTo($sReplyTo);
      $oMail->SetFrom(WEBSITE_MAIL, WEBSITE_NAME);
      $oMail->AddAddress($sTo);
      $oMail->Subject = $sSubject;
      $oMail->MsgHTML(nl2br($sMessage));

      if(!empty($sAttachment))
        $oMail->AddAttachment($sAttachment);

      return $oMail->Send();
    }
    catch (phpmailerException $e) {
      return $e->errorMessage();
    }
  }
}