<?php

/**
 * Handle all mail stuff.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\User as Model;

require_once 'app/models/Blog.model.php';
require_once 'app/controllers/User.controller.php';
require_once 'lib/recaptcha/recaptchalib.php';

class Mail extends Main {

	/**
	 * ReCaptcha public key.
	 *
	 * @var string
	 * @access protected
	 * @see config/Candy.inc.php
	 */
	protected $_sRecaptchaPublicKey = RECAPTCHA_PUBLIC;

	/**
	 * ReCaptcha private key.
	 *
	 * @var string
	 * @access protected
	 * @see config/Candy.inc.php
	 */
	protected $_sRecaptchaPrivateKey = RECAPTCHA_PRIVATE;

	/**
	 * ReCaptcha object.
	 *
	 * @var object
	 * @access protected
	 */
	protected $_oRecaptchaResponse = '';

	/**
	 * Provided ReCaptcha error message.
	 *
	 * @var string
	 * @access protected
	 */
	protected $_sRecaptchaError = '';


	/**
	 * Create a mail.
	 *
	 * Create entry or show form template if we have enough rights. Due to spam bots we provide
	 * a captcha and need to override the original method.
	 * We must override the main method due to a diffent required user right and a captcha.
	 *
	 * @access public
	 * @return string HTML content
	 * @override app/controllers/Main.controller.php
	 *
	 */
  public function create() {
		if (isset($this->_aRequest['create_mail'])) {
			# Disable at AJAX due to a bug in reloading JS code
			if (USER_RIGHT === 0 && RECAPTCHA_ENABLED === true && AJAX_REQUEST === false)
				return $this->_checkCaptcha();
			else
				return $this->_standardMail(false);
		}
		else
			return $this->_showCreateMailTemplate(( USER_RIGHT == 0 ) ? true : false);
	}

	/**
	 * Create a mail template.
	 *
	 * Show the create mail form and check data for correct information.
	 *
	 * @access protected
	 * @param boolean $bShowCaptcha show captcha or not.
	 * @return string HTML content
	 *
	 */
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

    $this->oSmarty->assign('contact', Model::getUserNamesAndEmail($this->_iId));
		$this->oSmarty->assign('content', $sContent);
		$this->oSmarty->assign('email', $sEmail);
		$this->oSmarty->assign('subject', $sSubject);

		if ($bShowCaptcha === true && RECAPTCHA_ENABLED === true)
			$this->oSmarty->assign('_captcha_', recaptcha_get_html($this->_sRecaptchaPublicKey, $this->_sRecaptchaError));

		if (!empty($this->_aError)) {
			foreach ($this->_aError as $sField => $sMessage)
				$this->oSmarty->assign('error_' . $sField, $sMessage);
		}

    # Create page title and description
    $this->_setDescription($this->oI18n->get('global.contact'));
    $this->_setTitle($this->oI18n->get('global.contact'));

    $this->oSmarty->template_dir = Helper::getTemplateDir('mails', 'create');
    return $this->oSmarty->fetch('create.tpl');
  }

	/**
	 * Check if the entered captcha is correct.
	 *
	 * @access protected
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 *
	 */
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
        $this->_aError['captcha'] = $this->oI18n->get('error.captcha.incorrect');
        return $this->_showCreateMailTemplate(true);
      }
    }
    else
      return Helper::errorMessage($this->oI18n->get('error.captcha.loading'), '/');
  }

	/**
	 * Check if required data is given or throw an error instead.
	 * If data is correct, send mail.
	 *
	 * @access protected
	 * @param boolean $bShowCaptcha Show the captcha?
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 * @todo rename method to create?
	 *
	 */
  protected function _standardMail($bShowCaptcha = true) {
		$this->_setError('email');
		$this->_setError('content');

		if (isset($this->_aError))
			return $this->_showCreateMailTemplate($bShowCaptcha);

    else {
      # Select user name and surname
      require_once 'app/models/User.model.php';
      $aRow = Model::getUserNamesAndEmail($this->_iId);

			# When mail is set, send to mail. Otherwise send to system mail
      $sMailTo	= isset($aRow['email']) ? $aRow['email'] : WEBSITE_MAIL;

			# Reply to mail
			$sReplyTo = Helper::formatInput($this->_aRequest['email']);

      $sSendersName = isset($this->_aSession['userdata']['name']) ?
              $this->_aSession['userdata']['name'] :
              $this->oI18n->get('global.system');

      $sSubject = isset($this->_aRequest['subject']) && !empty($this->_aRequest['subject']) ?
              Helper::formatInput($this->_aRequest['subject']) :
              str_replace('%u', $sSendersName, $this->oI18n->get('mail.subject.by'));

      $sMessage = Helper::formatInput($this->_aRequest['content']);

      # Mail to, Subject, Message, Reply to
      $bStatus = Mail::send($sMailTo, $sSubject, $sMessage, $sReplyTo);

      if ($bStatus == true) {
        Log::insert($this->_aRequest['section'], 'create', (int) $this->_iId);
				return $this->_showSuccessMessage();
      }
			else
				Helper::errorMessage($this->oI18n->get('error.mail.create'), '/');
    }
  }

  private function _showSuccessMessage() {
    $this->_setTitle($this->oI18n->get('mail.info.redirect'));
    $this->oSmarty->template_dir = Helper::getTemplateDir('mails', 'success');
    return $this->oSmarty->fetch('success.tpl');
  }

  public static function send($sTo, $sSubject, $sMessage, $sReplyTo = WEBSITE_MAIL, $sAttachment = '') {
    require_once 'lib/phpmailer/class.phpmailer.php';

		# Parse message and replace with (footer) variables
		$sMessage = str_replace('%NOREPLY', I18n::get('mail.body.no_reply'), $sMessage);
		$sMessage = str_replace('%SIGNATURE', I18n::get('mail.body.signature'), $sMessage);
		$sMessage = str_replace('%WEBSITE_NAME', WEBSITE_NAME, $sMessage);
		$sMessage = str_replace('%WEBSITE_URL', WEBSITE_URL, $sMessage);

		$sSubject = str_replace('%WEBSITE_NAME', WEBSITE_NAME, $sSubject);
		$sSubject = str_replace('%WEBSITE_URL', WEBSITE_URL, $sSubject);

    $oMail = new \PHPMailer(true);

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