<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/models/Comment.model.php';
require_once 'app/helpers/Pages.helper.php';
require_once 'lib/recaptcha/recaptchalib.php';

final class Comment extends Main {
  private $_aParentData;
	private $_oPages;
	private $_iEntries;
	private $_sParentCategory;
	private $_sParentSection;
	private $_sAction;
	private $_sRecaptchaPublicKey = RECAPTCHA_PUBLIC;
	private $_sRecaptchaPrivateKey = RECAPTCHA_PRIVATE;
	private $_oRecaptchaResponse = '';
	private $_sRecaptchaError = '';

  public function __init($iEntries = '', $aParentData = '') {
    $this->_iEntries    =& $iEntries;
    $this->_aParentData =& $aParentData;

    $this->_oModel = new Model_Comment($this->_aRequest, $this->_aSession);

    # Define Blog as standard category
    # b = blog
    $this->_sParentCategory = 'b';
    $this->_sParentSection	= 'Blog';
    $this->_sAction         = '/Comment/create/'.	$this->_sParentCategory.	'/'	.$this->_iId;
  }

  public final function show() {
		if ($this->_iId) {
			if (isset($this->_aRequest['ajax'])) {
				if ($this->_sParentCategory == 'b') {
					$this->__autoload('Blog');
					$oBlog = new Blog($this->_aRequest, $this->_aSession);
					$oBlog->__init();

					$this->_aParentData = & $oBlog->_oModel->getData($this->_iId);
					$this->_iEntries = & $this->_aParentData[1]['comment_sum'];
				}
			}

			# Do only load comments, if they are avaiable
			$sReturn = '';
			if ($this->_iEntries > 0) {
				# @Override __init here due to AJAX reasons
				$this->_oPages = new Pages($this->_aRequest, $this->_iEntries, LIMIT_COMMENTS, 1);
				$this->_oModel->__init($this->_iEntries, $this->_oPages->getOffset(), $this->_oPages->getLimit());
				$this->_aData = $this->_oModel->getData($this->_iId, $this->_sParentCategory);
				$this->_oSmarty->assign('comments', $this->_aData);

				# Set author of blog entry
				$iAuthorId = (int) $this->_aParentData[1]['author_id'];
				$this->_oSmarty->assign('author_id', $iAuthorId);

				# For correct information, do some math to display entries
				# NOTE: If you're admin, you can see all entries. That might bring pagination to your view, even
				# when other people don't see it
				$iCommentNumber = ($this->_oPages->getCurrentPage() * LIMIT_COMMENTS) - LIMIT_COMMENTS;
				$this->_oSmarty->assign('comment_number', $iCommentNumber);

				# Do we need Pages?
				$sPages = $this->_oPages->showPages('Comment/' . $this->_sParentCategory . '/' . $this->_iId, '');
				$this->_oSmarty->assign('_comment_pages_', $sPages);

				# Language
				$this->_oSmarty->assign('lang_destroy', LANG_COMMENT_TITLE_DESTROY);

				$this->_oSmarty->template_dir = Helper::getTemplateDir('comments/show');
				$sReturn .= $this->_oSmarty->fetch('comments/show.tpl');
			}

			# Does the user have enough rights to enter a comment?
			# Show createComment Template if we don't have an action - description below
			if (!empty($this->_iId) || isset($this->_aRequest['parent_category'])) {
				if (!isset($this->_aRequest['ajax']) && !empty($this->_aParentData[1]['title']))
					$sReturn .= $this->create('create_comment');
			}

			return $sReturn;
		}
	}

  # @Override
	# We must override the main method due to user right problems
	public final function create($sInputName) {
		if (isset($this->_aRequest[$sInputName])) {
			if (USER_RIGHT == 0)
				return $this->_checkCaptcha(true);

			else
				return $this->_create(false);
		}

		else {
			$bShowCaptcha = ( USER_RIGHT == 0 ) ? true : false;
			return $this->_showFormTemplate($bShowCaptcha);
		}
	}

  protected final function _create($bShowCaptcha = false) {
    if (!isset($this->_aRequest['parent_category']) || empty($this->_aRequest['parent_category']))
			$this->_aError['parent_category'] = LANG_ERROR_FORM_MISSING_CATEGORY;

		if (!isset($this->_aRequest['parent_id']) || empty($this->_aRequest['parent_id']))
			$this->_aError['parent_id'] = LANG_ERROR_GLOBAL_WRONG_ID;

		if (!isset($this->_aRequest['content']) || empty($this->_aRequest['content']))
			$this->_aError['content'] = LANG_ERROR_FORM_MISSING_CONTENT;

		if (USER_ID < 1) {
			if (!isset($this->_aRequest['name']) || empty($this->_aRequest['name']))
				$this->_aError['name'] = LANG_ERROR_FORM_MISSING_NAME;
		}

    # Set new action for form template
    $this->_sAction = '/Comment/create/' . $this->_aRequest['parent_category'] .
						'/' . (int) $this->_aRequest['parent_id'] . '#' .
						(int) $this->_aRequest['parent_id'];

		if (isset($this->_aError))
			return $this->_showFormTemplate($bShowCaptcha);

		else {
			$iLastComment = Helper::getLastEntry('comments') + 1;

			$sRedirect = '/' . $this->_sParentSection .
							'/' . (int) $this->_aRequest['parent_id'] . '#' . $iLastComment;

			if ($this->_oModel->create() === true) {
				Helper::log($this->_aRequest['section'], $this->_aRequest['action'], $iLastComment);
				return Helper::successMessage(LANG_SUCCESS_CREATE, $sRedirect);
			}

			else
				return Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
		}
  }

  protected final function _destroy() {
		$sRedirect = '/' . $this->_sParentSection . '/' . (int) $this->_aRequest['parent_id'];

		if ($this->_oModel->destroy((int) $this->_aRequest['id']) === true) {
      Helper::log($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_aRequest['id']);
			return Helper::successMessage(LANG_SUCCESS_DESTROY, $sRedirect);
    }

		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
	}

  protected final function _showFormTemplate($bShowCaptcha) {
    # Do we know, in which parent category we want to post?
    if( empty($this->_sAction) )
      return Helper::errorMessage(LANG_ERROR_REQUEST_MISSING_ACTION);

    else {
      $sName = isset($this->_aRequest['name']) ?
							(string) $this->_aRequest['name'] :
							'';

			$sEmail = isset($this->_aRequest['email']) ?
							(string) $this->_aRequest['email'] :
							'';

			$sContent = isset($this->_aRequest['content']) ?
							(string) $this->_aRequest['content'] :
							'';

			$iParentId = isset($this->_aRequest['parent_id']) ?
							(int) $this->_aRequest['parent_id'] :
							(int) $this->_iId;

			$this->_oSmarty->assign('_action_url_', $this->_sAction);
			$this->_oSmarty->assign('_parent_id_', $iParentId);
			$this->_oSmarty->assign('content', $sContent);
			$this->_oSmarty->assign('email', $sEmail);
			$this->_oSmarty->assign('name', $sName);

      if ($bShowCaptcha === true)
				$this->_oSmarty->assign('_captcha_', recaptcha_get_html($this->_sRecaptchaPublicKey,
												$this->_sRecaptchaError));

			if (!empty($this->_aError)) {
				foreach ($this->_aError as $sField => $sMessage)
					$this->_oSmarty->assign('error_' . $sField, $sMessage);
			}

			# Language
			$this->_oSmarty->assign('lang_headline', LANG_COMMENT_TITLE_CREATE);

			$this->_oSmarty->template_dir = Helper::getTemplateDir('comments/_form');
			return $this->_oSmarty->fetch('comments/_form.tpl');
    }
  }

  private function _checkCaptcha($bShowCaptcha = true) {
    if (isset($this->_aRequest['recaptcha_response_field'])) {
			$this->_oRecaptchaResponse = recaptcha_check_answer(
											$this->_sRecaptchaPrivateKey,
											$_SERVER['REMOTE_ADDR'],
											$this->_aRequest['recaptcha_challenge_field'],
											$this->_aRequest['recaptcha_response_field']);

			if ($this->_oRecaptchaResponse->is_valid)
				return $this->_create($bShowCaptcha);

			else {
				$this->_aError['captcha'] = LANG_ERROR_MAIL_CAPTCHA_NOT_CORRECT;
				return $this->_showFormTemplate($bShowCaptcha);
			}
		}

		else
			return Helper::errorMessage(LANG_ERROR_MAIL_CAPTCHA_NOT_LOADED);
	}
}