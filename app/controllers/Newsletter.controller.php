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
    $this->_oModel = new Model_Newsletter($this->_aRequest, $this->_aSession);
  }

  public final function handleNewsletter() {
    $sMsg = '';

    if (isset($this->_aRequest['email']) && ( Helper::checkEmailAddress($this->_aRequest['email']) == false ))
      $sMsg .= Helper::errorMessage(LANG_ERROR_WRONG_EMAIL_FORMAT);
    else {
      if(isset($this->_aRequest['email']) && !empty ($this->_aRequest['email'])) {
        $sQuery = Model_Newsletter::handleNewsletter(Helper::formatInput($this->_aRequest['email']));

        if ($sQuery == 'DESTROY')
          $sMsg .= Helper::successMessage(LANG_SUCCESS_DESTROY);
        elseif ($sQuery == 'INSERT') {
          $sMsg .= Helper::successMessage(LANG_SUCCESS_CREATE);

          Mail::send(Helper::formatInput($this->_aRequest['email']),
                          LANG_NEWSLETTER_CREATE_SUCCESS_SUBJECT,
                          LANG_NEWSLETTER_CREATE_SUCCESS_MESSAGE,
                          WEBSITE_MAIL_NOREPLY);
        }
        else
          return Helper::errorMessage(LANG_ERROR_DB_QUERY);
      }
    }

    $oSmarty = new Smarty();

    # Language
    $oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
    $oSmarty->assign('lang_headline', LANG_NEWSLETTER_CREATE_DESTROY);
    $oSmarty->assign('lang_description', LANG_NEWSLETTER_CREATE_DESTROY_DESCRIPTION);

    $oSmarty->template_dir = Helper::getTemplateDir('newsletter/newsletter');
    return $sMsg . $oSmarty->fetch('newsletter/newsletter.tpl');
  }

  public function create() {
    if (USER_RIGHT < 3)
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
    else {
      if (isset($this->_aRequest['send_newsletter']))
        return $this->_newsletterMail();
      else
        return $this->_showCreateNewsletterTemplate();
    }
  }

  private function _showCreateNewsletterTemplate() {
    $sSubject = isset($this->_aRequest['subject']) ?
            (string) $this->_aRequest['subject'] :
            '';

    $sContent = isset($this->_aRequest['content']) ?
            (string) $this->_aRequest['content'] :
            '';

    $oSmarty = new Smarty();
    $oSmarty->assign('subject', $sSubject);
    $oSmarty->assign('content', $sContent);

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $oSmarty->assign('error_' . $sField, $sMessage);
    }

    # Language
    $oSmarty->assign('lang_content', LANG_GLOBAL_CONTENT);
    $oSmarty->assign('lang_content_info', LANG_NEWSLETTER_CONTENT_INFO);
    $oSmarty->assign('lang_headline', LANG_NEWSLETTER_CREATE);
    $oSmarty->assign('lang_subject', LANG_GLOBAL_SUBJECT);
    $oSmarty->assign('lang_submit', LANG_NEWSLETTER_SUBMIT);

    $oSmarty->template_dir = Helper::getTemplateDir('newsletter/create');
    return $oSmarty->fetch('newsletter/create.tpl');
  }

  private function _newsletterMail() {
    $sError = '';

    if(	!isset($this->_aRequest['subject']) ||
            empty($this->_aRequest['subject']) )
       $this->_aError['subject'] = LANG_GLOBAL_SUBJECT;

    if(	!isset($this->_aRequest['content']) ||
            empty($this->_aRequest['content']) )
       $this->_aError['content'] = LANG_GLOBAL_CONTENT;

    if (isset($this->_aError))
      return $this->_showCreateNewsletterTemplate();
    else {
      # Deliver Newsletter to users
      $aResult = Model_Newsletter::getNewsletterRecipients('user');

      foreach ($aResult as $aRow) {
        $sReceiversName = $aRow['name'];
        $sReceiversMail = $aRow['email'];

        $sMailSubject = Helper::formatInput($this->_aRequest['subject']);
        $sMailContent = Helper::formatInput
                        (str_replace('%u', $sReceiversName, $this->_aRequest['content']),
                        false
        );

        $bStatusUser = Mail::send($sReceiversMail, $sMailSubject, $sMailContent);
      }

      # Deliver Newsletter to newsletter-subscripers
      $aResult = Model_Newsletter::getNewsletterRecipients('newsletter');

      foreach ($aResult as $aRow) {
        $sReceiversName = LANG_NEWSLETTER_DEFAULT_ADDRESS;
        $sReceiversMail = $aRow['email'];

        $sMailSubject = Helper::formatInput($this->_aRequest['subject']);
        $sMailContent = Helper::formatInput
                        (str_replace('%u', $sReceiversName, $this->_aRequest['content']),
                        false
        );

        $bStatusNewsletter = Mail::send($sReceiversMail, $sMailSubject, $sMailContent);
      }

      if($bStatusNewsletter == true && $bStatusUser == true)
        return Helper::successMessage( LANG_SUCCESS_MAIL_SENT );
      else
        return Helper::errorMessage(LANG_ERROR_MAIL_FAILED_SUBJECT);
    }
  }
}