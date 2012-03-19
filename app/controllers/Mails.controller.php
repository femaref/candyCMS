<?php

/**
 * Handle all mail stuff.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 * @todo test caching
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;

require PATH_STANDARD . '/lib/recaptcha/recaptchalib.php';

class Mails extends Main {

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
      Helper::redirectTo('/' . $this->_aRequest['controller'] . '/1');
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
			return	$this->_aSession['user']['role'] == 0 &&
							RECAPTCHA_ENABLED === true &&
							MOBILE === false &&
							WEBSITE_MODE !== 'test' ?
							$this->_checkCaptcha() :
							$this->_create(false);
		}
		else
			return $this->_showCreateMailTemplate($this->_aSession['user']['role'] == 0 ? true : false);
	}

	/**
	 * Create a mail template.
	 *
	 * Show the create mail form and check data for correct information.
	 *
	 * @access protected
	 * @param boolean $bShowCaptcha show captcha or not.
	 * @return string HTML content
   * @todo rename to _show?
	 *
	 */
  protected function _showCreateMailTemplate($bShowCaptcha) {
    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], 'create');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'create');

		$oUser = $this->__autoload('Users', true);
    $aUser = $oUser::getUserNamesAndEmail($this->_iId);

    $this->oSmarty->assign('contact', $aUser);
		$this->oSmarty->assign('content', isset($this->_aRequest['content']) ?
										(string) $this->_aRequest['content'] :
										'');

		$this->oSmarty->assign('email', isset($this->_aRequest['email']) ?
										(string) $this->_aRequest['email'] :
										$this->_aSession['user']['email']);

		$this->oSmarty->assign('subject', isset($this->_aRequest['subject']) ?
										(string) $this->_aRequest['subject'] :
										'');

		if ($bShowCaptcha === true && RECAPTCHA_ENABLED === true && WEBSITE_MODE !== 'test')
			$this->oSmarty->assign('_captcha_', recaptcha_get_html($this->_sRecaptchaPublicKey, $this->_sRecaptchaError));

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->setTitle(I18n::get('global.contact') . ' ' . $aUser['name'] . ' ' . $aUser['surname'])
            ->setDescription(str_replace('%u', $aUser['name'] . ' ' . $aUser['surname'], I18n::get('mails.description.show')));
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
      return Helper::errorMessage(I18n::get('error.captcha.loading'), '/' .
              $this->_aRequest['controller'] . '/' . $this->_iId);
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
		$this->_setError('content')->_setError('email');

		if (isset($this->_aError))
			return $this->_showCreateMailTemplate($bShowCaptcha);

    else {
      # Select user name and surname
      $oClass = $this->__autoload('Users', true);
      $oModel = & new $oClass($this->_aRequest, $this->_aSession);
      $aRow = $oModel::getUserNamesAndEmail($this->_iId);

      $sSendersName = isset($this->_aSession['user']['name']) ?
              $this->_aSession['user']['name'] :
              I18n::get('global.system');

      $sSubject = isset($this->_aRequest['subject']) && !empty($this->_aRequest['subject']) ?
              Helper::formatInput($this->_aRequest['subject']) :
              str_replace('%u', $sSendersName, I18n::get('mail.subject.by'));

      # Mail to, Subject, Message, Reply to
      $bStatus = Mails::send(isset($aRow['email']) ? $aRow['email'] : WEBSITE_MAIL,
							$sSubject,
							Helper::formatInput($this->_aRequest['content']),
							Helper::formatInput($this->_aRequest['email']));

      if ($bStatus == true) {
        Logs::insert($this->_aRequest['controller'], 'create', (int) $this->_iId);
				return $this->_showSuccessPage();
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
  private function _showSuccessPage() {
    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], 'success');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'success');

    $this->setTitle(I18n::get('mail.success_page.title'));
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

    $oMail = & new \PHPMailer(true);

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

      if($sAttachment)
        $oMail->AddAttachment($sAttachment);

      return $oMail->Send();
    }
    catch (phpmailerException $e) {
      return $e->errorMessage();
    }
  }
}