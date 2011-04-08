<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# This is an example for extending a standard class.
class Addon_Mail extends Mail {

  public function create() {
    if( isset($this->_aRequest['send_mail']) )
        return $this->_standardMail(false);
    else
      return $this->_showCreateMailTemplate(false);
  }

  protected function _showCreateMailTemplate($bShowCaptcha = true) {
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

    $this->_oSmarty->assign('contact', Model_User::getUserNamesAndEmail($this->_iId));
    $this->_oSmarty->assign('content', $sContent);
    $this->_oSmarty->assign('email', $sEmail);
    $this->_oSmarty->assign('subject', $sSubject);

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->_oSmarty->assign('error_' . $sField, $sMessage);
    }

    # Language
    $this->_oSmarty->assign('lang_email', LANG_MAIL_GLOBAL_LABEL_OWN_EMAIL);
    $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_CONTACT);
		$this->_oSmarty->assign('lang_submit', LANG_GLOBAL_MAIL_SEND);

    $this->_oSmarty->template_dir = 'public/skins/_addons/mails';
    return $this->_oSmarty->fetch('create.tpl');
  }
}