<?php

/*
 * This software is copyright protected. Use only allowed on licensed
 * websites. Contact author for further information or to receive a license.
 *
 * @link http://marcoraddatz.com
 * @copyright 2007 - 2008 Marco Raddatz
 * @author Marco Raddatz <mr at marcoraddatz dot com>
 * @package CMS
 * @version 1.0
 */

/*require_once 'app/controllers/Comment.controller.php';*/
require_once 'app/controllers/Mail.controller.php';

class Newsletter {
	public function __init() {}

	public final function newsletter() {
		$oSmarty = new Smarty();

		# TODO: Into Model
		# TODO: Parse for Mail, avoid Spam
		if( isset($this->m_aRequest['email']) && !empty($this->m_aRequest['email']) ) {
			$oGetData = new Query("	SELECT
																email
															FROM
																newsletter
															WHERE
																email ='"	.Helper::formatHTMLCode($this->m_aRequest['email']).	"'
															LIMIT
																1");

			if($oGetData->numRows() == 1) {
				new Query("	DELETE FROM
											`newsletter`
										WHERE
											`email` = '"	.Helper::formatHTMLCode($this->m_aRequest['email']).	"'
										LIMIT 1");
        # TODO: BETTER MESSAGE
				return Helper::successMessage(LANG_SUCCESS_DESTROY);
			}
			else {
				new Query("	INSERT INTO
											newsletter(email)
										VALUES(
											'"	.Helper::formatHTMLCode($this->m_aRequest['email']).	"')");
        # TODO: BETTER MESSAGE
				return Helper::successMessage(LANG_SUCCESS_CREATE);
			}
		}

		# Language
		$oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
		$oSmarty->assign('lang_headline', LANG_NEWSLETTER_CREATE_DESTROY);
		$oSmarty->assign('lang_description', LANG_NEWSLETTER_CREATE_DESTROY_DESCRIPTION);

		$oSmarty->template_dir = Helper::templateDir('newsletter/newsletter');
		return $oSmarty->fetch('newsletter/newsletter.tpl');
	}

	public function create() {
		if( USERRIGHT < 3 )
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