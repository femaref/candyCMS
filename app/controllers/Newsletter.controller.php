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
		if (isset($this->_aRequest['email'])) {
			if (isset($this->_aRequest['email']) && ( Helper::checkEmailAddress($this->_aRequest['email']) == false ))
				$this->_aError['email'] = LANG_ERROR_GLOBAL_WRONG_EMAIL_FORMAT;

			if (!isset($this->_aRequest['email']) || empty($this->_aRequest['email']))
				$this->_aError['email'] = LANG_ERROR_FORM_MISSING_EMAIL;

			if (isset($this->_aError))
				return $this->_showHandleNewsletterTemplate();

			else {
				$sQuery = Model_Newsletter::handleNewsletter(Helper::formatInput($this->_aRequest['email']));

				if ($sQuery == 'DESTROY')
					return Helper::successMessage(LANG_SUCCESS_DESTROY, '/newsletter');

				elseif ($sQuery == 'INSERT') {
					Mail::send(Helper::formatInput($this->_aRequest['email']),
													LANG_MAIL_NEWSLETTER_CREATE_SUBJECT,
													LANG_MAIL_NEWSLETTER_CREATE_BODY,
													WEBSITE_MAIL_NOREPLY);

					return Helper::successMessage(LANG_SUCCESS_CREATE, '/newsletter');
				}
				else
					return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/newsletter');
			}
		}
		else
			return $this->_showHandleNewsletterTemplate();
	}

  private function _showHandleNewsletterTemplate() {
    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->_oSmarty->assign('error_' . $sField, $sMessage);
    }

    # Language
    $this->_oSmarty->assign('lang_headline', LANG_NEWSLETTER_HANDLE_TITLE);
    $this->_oSmarty->assign('lang_description', LANG_NEWSLETTER_HANDLE_INFO);

    $this->_oSmarty->template_dir = Helper::getTemplateDir('newsletters/newsletter');
    return $this->_oSmarty->fetch('newsletters/newsletter.tpl');
  }

  # @Override
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

    $this->_oSmarty->template_dir = Helper::getTemplateDir('newsletters/create');
    return $this->_oSmarty->fetch('newsletters/create.tpl');
  }

  private function _newsletterMail() {
    if(	!isset($this->_aRequest['subject']) || empty($this->_aRequest['subject']) )
       $this->_aError['subject'] = LANG_ERROR_FORM_MISSING_SUBJECT;

    if(	!isset($this->_aRequest['content']) || empty($this->_aRequest['content']) )
       $this->_aError['content'] = LANG_ERROR_FORM_MISSING_CONTENT;

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
        $sReceiversName = LANG_NEWSLETTER_SHOW_DEFAULT_NAME;
        $sReceiversMail = $aRow['email'];

        $sMailSubject = Helper::formatInput($this->_aRequest['subject']);
        $sMailContent = Helper::formatInput
                        (str_replace('%u', $sReceiversName, $this->_aRequest['content']),
                        false
        );

        $bStatusNewsletter = Mail::send($sReceiversMail, $sMailSubject, $sMailContent);
      }

      if(isset($bStatusNewsletter) || isset($bStatusUser)) {
        Log::insert($this->_aRequest['section'], $this->_aRequest['action']);
        return Helper::successMessage( LANG_SUCCESS_MAIL_SENT, '/start' );
      }
      else
        return Helper::errorMessage(LANG_ERROR_MAIL_ERROR, '/start');
    }
  }
}