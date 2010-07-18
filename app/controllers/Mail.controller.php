<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'lib/recaptcha/recaptchalib.php';
require_once 'app/controllers/User.controller.php';

class Mail extends Main {
  private $_sRecaptchaPublicKey = RECAPTCHA_PUBLIC;
  private $_sRecaptchaPrivateKey = RECAPTCHA_PRIVATE;
  private $_sRecaptchaResponse = '';
  private $_sRecaptchaError = '';

  public final function __init() {

  }

  public final function createMail() {
    if( isset($this->m_aRequest['send_mail']) ) {
      if( USERRIGHT == 0 )
        return $this->_captchaProtectedMail();
      else
        return $this->_standardMail(false);
    }
    else {
      $bShowCaptcha = ( USERRIGHT == 0 ) ? true : false;
      return $this->_showCreateMailTemplate($bShowCaptcha);
    }
  }

  private function _showCreateMailTemplate($bShowCaptcha = true) {
    # Look for existing E-Mail address
    if( isset($this->m_aRequest['email']))
      $sEmail = (string)$this->m_aRequest['email'];
    elseif( isset($this->m_oSession['userdata']['email']) )
      $sEmail = $this->m_oSession['userdata']['email'];
    else
      $sEmail = '';

    $sSubject = isset($this->m_aRequest['subject']) ?
            (string)$this->m_aRequest['subject']:
            '';

    $sContent = isset($this->m_aRequest['content']) ?
            (string)$this->m_aRequest['content']:
            '';

    $oSmarty = new Smarty();
    $oSmarty->assign('id', $this->_iID);
    $oSmarty->assign('content', $sContent);
    $oSmarty->assign('email', $sEmail);
    $oSmarty->assign('subject', $sSubject);

    if( $bShowCaptcha == true )
      $oSmarty->assign('captcha', recaptcha_get_html(	$this->_sRecaptchaPublicKey,
              $this->_sRecaptchaError) );
    else
      $oSmarty->assign('captcha', '');

    # Language
    $oSmarty->assign('lang_headline', LANG_GLOBAL_CONTACT);
    $oSmarty->assign('lang_content', LANG_GLOBAL_CONTENT);
    $oSmarty->assign('lang_email', LANG_MAIL_OWN_EMAIL);
    $oSmarty->assign('lang_subject', LANG_GLOBAL_SUBJECT);

    if( isset( $this->m_aRequest['subject'] ) &&
            'Bugreport' == $this->m_aRequest['subject'] )
      $oSmarty->assign('lang_submit', LANG_GLOBAL_REPORT_ERROR);
    else
      $oSmarty->assign('lang_submit', LANG_GLOBAL_MAIL_SEND);

    $oSmarty->template_dir = Helper::templateDir('mail/create');
    return $oSmarty->fetch('mail/create.tpl');
  }

  private function _captchaProtectedMail() {
    if( isset($this->m_aRequest['recaptcha_response_field']) ) {
      $this->_sRecaptchaResponse = recaptcha_check_answer (
              $this->_sRecaptchaPrivateKey,
              $_SERVER['REMOTE_ADDR'],
              $this->m_aRequest['recaptcha_challenge_field'],
              $this->m_aRequest['recaptcha_response_field']);

      if ($this->_sRecaptchaResponse->is_valid)
        return $this->_standardMail(true);
      else {
        $this->_sRecaptchaError = $this->_sRecaptchaResponse->error;
        return Helper::errorMessage(LANG_ERROR_MAIL_CAPTCHA_NOT_CORRECT).
                $this->_showCreateMailTemplate();
      }
    }
    else
      return Helper::errorMessage(LANG_ERROR_MAIL_CAPTCHA_NOT_LOADED);
  }

  private function _standardMail($bShowCaptcha = true) {
    $sError = '';

    if(	!isset($this->m_aRequest['email']) ||
            empty($this->m_aRequest['email']) )
      $sError .= LANG_GLOBAL_EMAIL.	'<br />';

    if(	!isset($this->m_aRequest['content']) ||
            empty($this->m_aRequest['content']) )
      $sError .= LANG_GLOBAL_CONTENT.	'<br />';

    if( !empty($sError) ) {
      $sReturn  = Helper::errorMessage($sError, LANG_ERROR_GLOBAL_CHECK_FIELDS);
      $sReturn .= $this->_showCreateMailTemplate($bShowCaptcha);
      return $sReturn;
    }
    else {
      $oGetUser = new Query("	SELECT
																name, email
															FROM
																user
															WHERE
																id = '"	.$this->_iID.	"'
															LIMIT
																1" );
      $aRow = $oGetUser->fetch();
      $sMailTo = $aRow['email'];

      $sSendersMail = isset($this->m_aRequest['email']) &&
                      !empty($this->m_aRequest['email']) ?
              Helper::formatHTMLCode($this->m_aRequest['email']):
              WEBSITE_MAIL_NOREPLY;

      $sSendersName = isset($this->m_oSession['userdata']['name']) ?
              $this->m_oSession['userdata']['name'] :
              LANG_GLOBAL_SYSTEMBOT;

      $sMailSubject = isset($this->m_aRequest['subject']) &&
                      !empty($this->m_aRequest['subject']) ?
              Helper::formatHTMLCode($this->m_aRequest['subject']) :
              str_replace('%u', $sSendersName, LANG_MAIL_SUBJECT_BY_USER);

      $sMailMessage = Helper::formatHTMLCode($this->m_aRequest['content']);

      # Redirect to User Profile
      #$oController = new User($this->m_aRequest, $this->m_oSession);

      $bStatus = Mail::send(	$sMailTo,
              $sMailSubject,
              $sMailMessage,
              true,
              $SendersMail);

      if($bStatus == true)
        return Helper::successMessage(LANG_SUCCESS_MAIL_SENT);
    }
  }

  public static function send($sTo, $sSubject, $sMessage, $sReplyTo = WEBSITE_MAIL) {
    # If you're developing, avoid Mails to User
    if(WEBSITE_DEV == 0) {
      $sHeader  =	"From:"	.WEBSITE_NAME.	" <"	.WEBSITE_MAIL.	">\n";
      $sHeader .=	"Reply-To: "	.$sReplyTo.	"\n";
      $sHeader .=	"X-Mailer: PHP/" . phpversion(). "\n";
      $sHeader .=	"X-Sender-IP: "	.$_SERVER['REMOTE_ADDR'].	"\n";
      $sHeader .=	'Content-Type: text/html; charset=UTF-8';

      if(@mail(trim($sTo), $sSubject, nl2br($sMessage), $sHeader))
        return true;
      else
        return false;

    }
    else {
      # DEBUG MODE
      return Helper::errorMessage('<div style=\'text-align:left\'>'
              .LANG_GLOBAL_BY.	': '	.WEBSITE_NAME. '<br />'
              .LANG_GLOBAL_EMAIL.	': '	.$sReplyTo. '<br />'
              .LANG_GLOBAL_SUBJECT.	': '	.$sSubject. '<br />'
              .LANG_GLOBAL_CONTENT.	': '	.$sMessage.	'</div>',
              'DEBUG MODE' );
    }
  }
}