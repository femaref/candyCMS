<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/models/Newsletter.model.php';
require_once 'app/controllers/Mail.controller.php';

class Newsletter extends Main {
  public function __init() {
    $this->_oModel = new Model_Newsletter($this->m_aRequest, $this->m_oSession);
  }

  public final function handleNewsletter() {
    $sMsg = '';

    if( isset($this->m_aRequest['email']) &&  ( Helper::checkEmailAddress($this->m_aRequest['email']) == false ) )
      $sMsg .= Helper::errorMessage(LANG_ERROR_WRONG_EMAIL_FORMAT);
    else {
      $sQuery = $this->_oModel->handleNewsletter();
      if($sQuery == 'DESTROY')
        $sMsg .= Helper::successMessage(LANG_SUCCESS_DESTROY);
      elseif($sQuery == 'INSERT') {
        $sMsg .= Helper::successMessage(LANG_SUCCESS_CREATE);

        Mail::send(	Helper::formatHTMLCode($this->m_aRequest['email']),
                LANG_NEWSLETTER_CREATE_SUCCESS_SUBJECT,
                LANG_NEWSLETTER_CREATE_SUCCESS_MESSAGE,
                WEBSITE_MAIL_NOREPLY);

      }
    }

    $oSmarty = new Smarty();

    # Language
    $oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
    $oSmarty->assign('lang_headline', LANG_NEWSLETTER_CREATE_DESTROY);
    $oSmarty->assign('lang_description', LANG_NEWSLETTER_CREATE_DESTROY_DESCRIPTION);

    $oSmarty->template_dir = Helper::templateDir('newsletter/newsletter');
    return $sMsg.$oSmarty->fetch('newsletter/newsletter.tpl');
  }

  public function create() {
    if( USER_RIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
    else {
      if( isset($this->m_aRequest['send_newsletter']) )
        return $this->_newsletterMail();
      else
        return $this->_showCreateNewsletterTemplate();
    }
  }

  private function _showCreateNewsletterTemplate() {
    $sSubject = isset($this->m_aRequest['subject']) ?
            (string)$this->m_aRequest['subject']:
            '';

    $sContent = isset($this->m_aRequest['content']) ?
            (string)$this->m_aRequest['content']:
            '';

    $oSmarty = new Smarty();
    $oSmarty->assign('subject', $sSubject);
    $oSmarty->assign('content', $sContent);

    # Language
    $oSmarty->assign('lang_content', LANG_GLOBAL_CONTENT);
    $oSmarty->assign('lang_content_info', LANG_NEWSLETTER_CONTENT_INFO);
    $oSmarty->assign('lang_headline', LANG_NEWSLETTER_CREATE);
    $oSmarty->assign('lang_subject', LANG_GLOBAL_SUBJECT);
    $oSmarty->assign('lang_submit', LANG_NEWSLETTER_SUBMIT);

    $oSmarty->template_dir = Helper::templateDir('newsletter/create');
    return $oSmarty->fetch('newsletter/create.tpl');
  }

  private function _newsletterMail() {
    $sError = '';

    if(	!isset($this->m_aRequest['subject']) ||
            empty($this->m_aRequest['subject']) )
      $sError .= LANG_GLOBAL_SUBJECT.	'<br />';

    if(	!isset($this->m_aRequest['content']) ||
            empty($this->m_aRequest['content']) )
      $sError .= LANG_GLOBAL_CONTENT.	'<br />';

    if( !empty($sError) ) {
      $sReturn  = Helper::errorMessage($sError, LANG_ERROR_GLOBAL_CHECK_FIELDS);
      $sReturn .= $this->_showCreateNewsletterTemplate();
      return $sReturn;
    }
    else {
      # Deliver Newsletter to Users
      $oGetUser = new Query("	SELECT
																name, email
															FROM
																user
															WHERE
																newsletter_default = '1'" );

      while($aRow = $oGetUser->fetch()) {
        $sReceiversName = $aRow['name'];
        $sReceiversMail = $aRow['email'];

        $sMailSubject	= Helper::formatHTMLCode($this->m_aRequest['subject']);
        $sMailContent	= Helper::formatHTMLCode
                (	str_replace('%u', $sReceiversName, $this->m_aRequest['content']),
                false
        );

        Mail::send(	$sReceiversMail, $sMailSubject, $sMailContent);
      }

      # Deliver Newsletter to newsletter-subscripers
      $oGetUser = new Query("	SELECT
																email
															FROM
																newsletter" );

      while($aRow = $oGetUser->fetch()) {
        $sReceiversName = LANG_NEWSLETTER_DEFAULT_ADDRESS;
        $sReceiversMail = $aRow['email'];

        $sMailSubject	= Helper::formatHTMLCode($this->m_aRequest['subject']);
        $sMailContent	= Helper::formatHTMLCode
                (	str_replace('%u', $sReceiversName, $this->m_aRequest['content']),
                false
        );

        Mail::send(	$sReceiversMail, $sMailSubject, $sMailContent );
      }

      return Helper::successMessage( LANG_SUCCESS_MAIL_SENT );
    }
  }
}