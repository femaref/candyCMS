<?php

/**
 * Handle all mail stuff.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Model\User as Model;
use Smarty;

require PATH_STANDARD . '/lib/recaptcha/recaptchalib.php';

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
   * Redirect to admin if no ID is given.
   *
   * @access public
   *
   */
  public function __init() {
    if (empty($this->_iId))
      Helper::redirectTo('/mail/1');
  }

  /**
   * Redirect to create method due to logic at the dispatcher.
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    return $this->create();
  }

	/**
	 * Show a mail form or direct it to the user.
	 *
	 * Create entry or show form template if we have enough rights. Due to spam bots we provide
	 * a captcha and need to override the original method.
	 * We must override the main method due to a diffent required user role and a captcha.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
  public function create() {
		if (isset($this->_aRequest['create_mail'])) {
			# Disable at AJAX due to a bug in reloading JS code
			if ($this->_aSession['userdata']['role'] == 0 &&
							RECAPTCHA_ENABLED === true &&
							AJAX_REQUEST === false &&
							MOBILE === false)
				return $this->_checkCaptcha();

			else
				return $this->_create(false);
		}
		else
			return $this->_showCreateMailTemplate($this->_aSession['userdata']['role'] == 0 ? true : false);
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
		$this->__autoload('User');

    $this->oSmarty->assign('contact', Model::getUserNamesAndEmail($this->_iId));
		$this->oSmarty->assign('content', isset($this->_aRequest['content']) ?
										(string) $this->_aRequest['content'] :
										'');

		$this->oSmarty->assign('email', isset($this->_aRequest['email']) ?
										(string) $this->_aRequest['email'] :
										$this->_aSession['userdata']['email']);

		$this->oSmarty->assign('subject', isset($this->_aRequest['subject']) ?
										(string) $this->_aRequest['subject'] :
										'');

		if ($bShowCaptcha === true && RECAPTCHA_ENABLED === true)
			$this->oSmarty->assign('_captcha_', recaptcha_get_html($this->_sRecaptchaPublicKey, $this->_sRecaptchaError));

    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $this->setDescription(I18n::get('global.contact'));
    $this->setTitle(I18n::get('global.contact'));

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'create');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'create');

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
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
        return $this->_create(true);

      else {
        $this->_aError['captcha'] = I18n::get('error.captcha.incorrect');
        return $this->_showCreateMailTemplate(true);
      }
    }
    else
      return Helper::errorMessage(I18n::get('error.captcha.loading'), '/mail/' . $this->_iId);
  }

	/**
	 * Check if required data is given or throw an error instead.
	 * If data is correct, send mail.
	 *
	 * @access protected
	 * @param boolean $bShowCaptcha Show the captcha?
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 *
	 */
  protected function _create($bShowCaptcha = true) {
		$this->_setError('content');
		$this->_setError('email');

		if (isset($this->_aError))
			return $this->_showCreateMailTemplate($bShowCaptcha);

    else {
      # Select user name and surname
      require_once 'app/models/User.model.php';
      $aRow = Model::getUserNamesAndEmail($this->_iId);

      $sSendersName = isset($this->_aSession['userdata']['name']) ?
              $this->_aSession['userdata']['name'] :
              I18n::get('global.system');

      $sSubject = isset($this->_aRequest['subject']) && !empty($this->_aRequest['subject']) ?
              Helper::formatInput($this->_aRequest['subject']) :
              str_replace('%u', $sSendersName, I18n::get('mail.subject.by'));

      # Mail to, Subject, Message, Reply to
      $bStatus = Mail::send(isset($aRow['email']) ? $aRow['email'] : WEBSITE_MAIL,
								$sSubject,
								Helper::formatInput($this->_aRequest['content']),
								Helper::formatInput($this->_aRequest['email']));

      if ($bStatus == true) {
        Log::insert($this->_aRequest['section'], 'create', (int) $this->_iId);
				return $this->_showSuccessMessage();
      }
			else
				Helper::errorMessage(I18n::get('error.mail.create'), '/');
    }
  }

  /**
	 * Show success message after mail is sent.
	 *
   * @access private
   * @return string HTML success page.
	 *
   */
  private function _showSuccessMessage() {
    $this->setTitle(I18n::get('mail.info.redirect'));

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'success');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'success');

    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
	 * Send a mail.
	 *
   * @param string $sTo email address to send mail to
   * @param string $sSubject mail subject
   * @param string $sMessage mail message
   * @param string $sReplyTo email address the user can reply to
   * @param string $sAttachment path to the attachment
   * @return type
	 *
   */
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

    if (SMTP_ENABLE == true)
      $oMail->IsSMTP();
    else
      $oMail->IsMail();

    try {
      if (SMTP_ENABLE == true) {
        if (WEBSITE_MODE == 'development') {
          $oMail->SMTPDebug = 1;
          $oMail->SMTPAuth  = false;
        }
        else {
          # disables SMTP debug information (for testing)
          $oMail->SMTPDebug = 0;
          $oMail->SMTPAuth  = true;
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