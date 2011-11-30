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
use MCAPI;

require_once 'lib/mailchimp/MCAPI.class.php';

class Newsletter extends Main {

  /**
   * Handle newsletter actions. Decide whether to add or remove an entry.
   *
   * @access public
   * @return string status message
   *
   */
  public function createSubscription() {
    $this->_setError('email');

    if (isset($this->_aError))
      return $this->_showCreateSubscriptionTemplate();

    else {
      # Subscribe to MailChimp
      require_once 'config/Mailchimp.inc.php';

      $oMCAPI = new MCAPI(MAILCHIMP_API_KEY);
      $oMCAPI->listSubscribe(MAILCHIMP_LIST_ID, Helper::formatInput($this->_aRequest['email']));

      if ($oMCAPI->errorCode)
        return Helper::errorMessage($oMCAPI->errorMessage, '/newsletter');
      else
        return Helper::successMessage($this->oI18n->get('success.create'), '/newsletter');
    }
  }

  /**
   * Build newsletter template to insert or delete an email address.
   *
   * @access protected
   * @return string HTML content
   *
   */
  private function _showCreateSubscriptionTemplate() {
    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $this->_setDescription($this->oI18n->get('newsletter.info.handle'));
    $this->_setTitle($this->oI18n->get('newsletter.title.handle'));

    $sTemplateDir = Helper::getTemplateDir('newsletters', 'subscribe');
    $this->oSmarty->template_dir = $sTemplateDir;
    return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'subscribe'));
  }
}