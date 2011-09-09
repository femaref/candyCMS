<?php

/**
 * Send newsletter to receipients or users.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Newsletter as Model;

require_once 'app/models/Newsletter.model.php';
require_once 'app/controllers/Mail.controller.php';

class Newsletter extends Main {

  /**
   * Include the newsletter model.
   *
   * @access public
   * @override app/controllers/Main.controller.php
   *
   */
  public function __init() {
    $this->_oModel = new Model($this->_aRequest, $this->_aSession);
  }

  /**
   * Handle newsletter actions. Decide whether to add or remove an entry.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function handleNewsletter() {
    # If there is no email request then show form template
    if (!isset($this->_aRequest['email']))
      return $this->_showHandleNewsletterTemplate();

    # Check format of email
    elseif(isset($this->_aRequest['email'])) {
      $this->_setError('email');
    }

    if (isset($this->_aError))
      return $this->_showHandleNewsletterTemplate();

    else {
      # Query the model and get back the status code of the action
      $sQuery = $this->_oModel->handleNewsletter(Helper::formatInput($this->_aRequest['email']));

      if ($sQuery == 'DESTROY')
        return Helper::successMessage(LANG_SUCCESS_DESTROY, '/newsletter');

      elseif ($sQuery == 'INSERT') {
        Mail::send(
                Helper::formatInput($this->_aRequest['email']),
                LANG_MAIL_NEWSLETTER_CREATE_SUBJECT,
                LANG_MAIL_NEWSLETTER_CREATE_BODY,
                WEBSITE_MAIL_NOREPLY);
        return Helper::successMessage(LANG_SUCCESS_CREATE, '/newsletter');
      }
      else
        return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/newsletter');
    }
  }

  /**
   * Build newsletter template to insert or delete an email address.
   *
   * @access protected
   * @return string HTML content
   *
   */
  private function _showHandleNewsletterTemplate() {
    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->_oSmarty->assign('error_' . $sField, $sMessage);
    }

    # Language
    $this->_oSmarty->assign('lang_headline', LANG_NEWSLETTER_HANDLE_TITLE);
    $this->_oSmarty->assign('lang_description', LANG_NEWSLETTER_HANDLE_INFO);

    $this->_setDescription(LANG_NEWSLETTER_HANDLE_INFO);
    $this->_setTitle(LANG_NEWSLETTER_HANDLE_TITLE);

    $this->_oSmarty->template_dir = Helper::getTemplateDir('newsletters', 'newsletter');
    return $this->_oSmarty->fetch('newsletter.tpl');
  }

  /**
   * Create a newsletter.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   * @override app/controllers/Main.controller.php
   *
   */
  public function create() {
    if (USER_RIGHT < 3)
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');

    else
      return isset($this->_aRequest['create_newsletter']) ? $this->_create() : $this->_showCreateNewsletterTemplate();
  }

  /**
   * Build newsletter form template to create an upload.
   *
   * @access private
   * @return string HTML content
   *
   */
  private function _showCreateNewsletterTemplate() {
    $sSubject = isset($this->_aRequest['subject']) ? (string) $this->_aRequest['subject'] : '';
    $sContent = isset($this->_aRequest['content']) ? (string) $this->_aRequest['content'] : '';

    $this->_oSmarty->assign('subject', $sSubject);
    $this->_oSmarty->assign('content', $sContent);

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->_oSmarty->assign('error_' . $sField, $sMessage);
    }

    # Language
    $this->_oSmarty->assign('lang_content_info', LANG_NEWSLETTER_CREATE_INFO);
    $this->_oSmarty->assign('lang_headline', LANG_NEWSLETTER_CREATE_TITLE);
    $this->_oSmarty->assign('lang_submit', LANG_NEWSLETTER_CREATE_LABEL_SUBMIT);

    $this->_oSmarty->template_dir = Helper::getTemplateDir('newsletters', 'create');
    return $this->_oSmarty->fetch('create.tpl');
  }

  /**
   * Send newsletter to all people in our newsletter table and all users that
   * accepted them.
   *
   * @access private
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  private function _create() {
    $this->_setError('subject');
    $this->_setError('content');

    $aResult = array();

    if (isset($this->_aError))
      return $this->_showCreateNewsletterTemplate();

    else {
      # Deliver newsletter to users
      $aResult = $this->_oModel->getNewsletterRecipients('user');

      foreach ($aResult as $aRow) {
        $sReceiversName = $aRow['name'];
        $sReceiversMail = $aRow['email'];

        $sMailSubject = Helper::formatInput($this->_aRequest['subject']);
        $sMailContent = Helper::formatInput
                        (str_replace('%u', $sReceiversName, $this->_aRequest['content']), false
        );

        $bStatusUser = Mail::send($sReceiversMail, $sMailSubject, $sMailContent);
      }

      # Deliver Newsletter to newsletter-subscripers
      $aResult = $this->_oModel->getNewsletterRecipients('newsletter');

      foreach ($aResult as $aRow) {
        $sReceiversName = LANG_NEWSLETTER_SHOW_DEFAULT_NAME;
        $sReceiversMail = $aRow['email'];

        $sMailSubject = Helper::formatInput($this->_aRequest['subject']);
        $sMailContent = Helper::formatInput
                        (str_replace('%u', $sReceiversName, $this->_aRequest['content']), false
        );

        $bStatusNewsletter = Mail::send($sReceiversMail, $sMailSubject, $sMailContent);
      }

      if(isset($bStatusNewsletter) || isset($bStatusUser)) {
        Log::insert($this->_aRequest['section'], $this->_aRequest['action']);
        return Helper::successMessage( LANG_SUCCESS_MAIL_SENT, '/' );
      }
      else
        return Helper::errorMessage(LANG_ERROR_MAIL_ERROR, '/');
    }
  }
}