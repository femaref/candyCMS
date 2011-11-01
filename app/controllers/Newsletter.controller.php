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
        return Helper::successMessage($this->oI18n->get('success.destroy'), '/newsletter');

      elseif ($sQuery == 'INSERT') {
        Mail::send(
                Helper::formatInput($this->_aRequest['email']),
                $this->oI18n->get('newsletter.mail.subject'),
                $this->oI18n->get('newsletter.mail.body'),
                WEBSITE_MAIL_NOREPLY);

        return Helper::successMessage($this->oI18n->get('success.create'), '/newsletter');
      }
      else
        return Helper::errorMessage($this->oI18n->get('error.sql'), '/newsletter');
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
    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $this->_setDescription($this->oI18n->get('newsletter.info.handle'));
    $this->_setTitle($this->oI18n->get('newsletter.title.handle'));


    $sTemplateDir = Helper::getTemplateDir('newsletters', 'newsletter');
    $this->oSmarty->template_dir = $sTemplateDir;
    return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'newsletter'));
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
      return Helper::errorMessage($this->oI18n->get('error.missing.permission'), '/');

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

    $this->oSmarty->assign('subject', $sSubject);
    $this->oSmarty->assign('content', $sContent);

    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir = Helper::getTemplateDir('newsletters', 'create');
    $this->oSmarty->template_dir = $sTemplateDir;
    return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'create'));
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
        $sReceiversName = $this->oI18n->get('newsletter.receipients');
        $sReceiversMail = $aRow['email'];

        $sMailSubject = Helper::formatInput($this->_aRequest['subject']);
        $sMailContent = Helper::formatInput
                        (str_replace('%u', $sReceiversName, $this->_aRequest['content']), false
        );

        $bStatusNewsletter = Mail::send($sReceiversMail, $sMailSubject, $sMailContent);
      }

      if(isset($bStatusNewsletter) || isset($bStatusUser)) {
        Log::insert($this->_aRequest['section'], $this->_aRequest['action']);
        return Helper::successMessage( $this->oI18n->get('success.mail.create'), '/' );
      }
      else
        return Helper::errorMessage($this->oI18n->get('error.mail.create'), '/');
    }
  }
}