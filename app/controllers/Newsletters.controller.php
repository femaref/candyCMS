<?php

/**
 * Send newsletter to receipients or users.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;

class Newsletters extends Main {

  /**
   * Redirect to create method due to logic at the dispatcher.
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    return $this->create('subscribe_newsletter', 0);
  }

  /**
   * Create a newsletter subscription. Send email information to mailchimp servers.
   *
   * @access public
   * @return string HTML content
   *
   */
  public function _create() {
		$this->_setError('email');

		if (isset($this->_aError))
			return $this->_showFormTemplate();

		else
			return $this->_subscribeToNewsletter($this->_aRequest, true) === true ?
							Helper::successMessage(I18n::get('success.newsletter.create'), '/') :
							Helper::errorMessage(I18n::get('error.standard'), '/' . $this->_aRequest['controller']);
	}

  /**
   * Show a form for email subscription.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showFormTemplate() {
    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'subscribe');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'subscribe');

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }
}