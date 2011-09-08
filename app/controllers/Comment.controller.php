<?php

/**
 * CRD action of comments.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Controller;

require_once 'app/models/Comment.model.php';
require_once 'app/helpers/Page.helper.php';
require_once 'lib/recaptcha/recaptchalib.php';

class Comment extends \CandyCMS\Controller\Main {

  /**
   * The provided blog data.
   *
   * @var array
   * @access private
   */
  private $_aParentData;

  /**
   * ReCaptcha public key.
   *
   * @var string
   * @access protected
   * @see config/Candy.inc.php
   */
  protected $_sRecaptchaPublicKey = RECAPTCHA_PUBLIC;

  /**
   * ReCaptcha private key.
   *
   * @var string
   * @access protected
   * @see config/Candy.inc.php
   */
  protected $_sRecaptchaPrivateKey = RECAPTCHA_PRIVATE;

  /**
   * ReCaptcha object.
   *
   * @var object
   * @access protected
   */
  protected $_oRecaptchaResponse = '';

  /**
   * Provided ReCaptcha error message.
   *
   * @var string
   * @access protected
   */
  protected $_sRecaptchaError = '';

  /**
   * Include the content model.
   *
   * @access public
   * @param array $aParentData optionally provided blog data
   * @override app/controllers/Main.controller.php
   *
   */
  public function __init($aParentData = '') {
    $this->_aParentData =& $aParentData;
    $this->_oModel = new \CandyCMS\Model\Comment($this->_aRequest, $this->_aSession);
  }

  /**
   * Show comment entries.
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    if ($this->_iId) {
      $this->_oSmarty->assign('comments',
              $this->_oModel->getData($this->_iId, $this->_aParentData[1]['comment_sum'], LIMIT_COMMENTS));

      # Set author of blog entry
      $this->_oSmarty->assign('author_id', (int) $this->_aParentData[1]['author_id']);

      # For correct information, do some math to display entries.
      # NOTE: If you're admin, you can see all entries. That might bring pagination to your view, even
      # when other people don't see it
      $this->_oSmarty->assign('comment_number', ($this->_oModel->oPage->getCurrentPage() * LIMIT_COMMENTS) - LIMIT_COMMENTS);

      # Do we need pages?
      $this->_oSmarty->assign('_pages_', $this->_oModel->oPage->showPages('/blog/' . $this->_iId));

      # Language
      $this->_oSmarty->assign('lang_destroy', LANG_COMMENT_TITLE_DESTROY);

      $this->_oSmarty->template_dir = \CandyCMS\Helper\Helper::getTemplateDir('comments', 'show');
      return $this->_oSmarty->fetch('show.tpl') . $this->create('create_comment');
    }
  }

  /**
   * Build form template to create a comment.
   *
   * @access protected
   * @param boolean $bShowCaptcha force captcha or not
   * @return string HTML content
   *
   */
  protected function _showFormTemplate($bShowCaptcha) {
    $iParentId  = isset($this->_aRequest['parent_id']) ? (int) $this->_aRequest['parent_id'] : (int) $this->_iId;
    $sName      = isset($this->_aRequest['name']) ? (string) $this->_aRequest['name'] : '';
    $sEmail     = isset($this->_aRequest['email']) ? (string) $this->_aRequest['email'] : '';
    $sContent   = isset($this->_aRequest['content']) ? (string) $this->_aRequest['content'] : '';

    $this->_oSmarty->assign('_parent_id_', $iParentId);
    $this->_oSmarty->assign('content', $sContent);
    $this->_oSmarty->assign('email', $sEmail);
    $this->_oSmarty->assign('name', $sName);

    if ($bShowCaptcha === true && RECAPTCHA_ENABLED === true)
      $this->_oSmarty->assign('_captcha_', recaptcha_get_html($this->_sRecaptchaPublicKey, $this->_sRecaptchaError));

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->_oSmarty->assign('error_' . $sField, $sMessage);
    }

    # Language
    $this->_oSmarty->assign('lang_headline', LANG_COMMENT_TITLE_CREATE);

    $this->_oSmarty->template_dir = \CandyCMS\Helper\Helper::getTemplateDir('comments', '_form');
    return $this->_oSmarty->fetch('_form.tpl');
  }

  /**
   * Create entry, check for captcha or show form template if we have enough rights.
   * We must override the main method due to a diffent required user right.
   *
   * @access public
   * @param string $sInputName sent input name to verify action
   * @return string HTML content
   * @override app/controllers/Main.controller.php
   *
   */
  public function create($sInputName) {
    if (isset($this->_aRequest[$sInputName])) {
      if (USER_RIGHT == 0 && RECAPTCHA_ENABLED === true)
        return $this->_checkCaptcha();

      else
        return $this->_create(false);
    }
    else {
      $bShowCaptcha = ( USER_RIGHT == 0 ) ? true : false;
      return $this->_showFormTemplate($bShowCaptcha);
    }
  }

  /**
   * Create a blog entry.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, activate the model, insert them into the database and redirect afterwards.
   *
   * @access protected
   * @param
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create($bShowCaptcha = true) {
    $this->_setError('parent_id', LANG_ERROR_GLOBAL_WRONG_ID);
    $this->_setError('content');

    if (USER_ID < 1)
      $this->_setError('name');

    if (isset($this->_aError))
      return $this->_showFormTemplate($bShowCaptcha);

    else {
      $iLastComment = \CandyCMS\Helper\Helper::getLastEntry('comments') + 1;
      $sRedirect = '/blog/' . (int) $this->_aRequest['parent_id'] . '#' . $iLastComment;

      if ($this->_oModel->create() === true) {
        \CandyCMS\Controller\Log::insert('comment', 'create', $iLastComment);
        return \CandyCMS\Helper\Helper::successMessage(LANG_SUCCESS_CREATE, $sRedirect);
      }

      else
        return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
    }
  }

  /**
   * Delete a a comment.
   *
   * Activate model, delete data from database and redirect afterwards.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _destroy() {
    $sRedirect = '/blog/' . (int) $this->_aRequest['parent_id'];

    if ($this->_oModel->destroy((int) $this->_aRequest['id']) === true) {
      \CandyCMS\Controller\Log::insert('comment', 'destroy', (int) $this->_aRequest['id']);
      return \CandyCMS\Helper\Helper::successMessage(LANG_SUCCESS_DESTROY, $sRedirect);
    }
    else
      return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
  }

  /**
   * Check if the entered captcha is correct.
   *
   * @access protected
   * @return boolean|string status of create action (boolean),
   * status of error message (boolean) or HTML content of form template (string).
   *
   */
  private function _checkCaptcha() {
    if (isset($this->_aRequest['recaptcha_response_field'])) {
      $this->_oRecaptchaResponse = recaptcha_check_answer(
                      $this->_sRecaptchaPrivateKey,
                      $_SERVER['REMOTE_ADDR'],
                      $this->_aRequest['recaptcha_challenge_field'],
                      $this->_aRequest['recaptcha_response_field']);

      if ($this->_oRecaptchaResponse->is_valid)
        return $this->_create(true);

      else {
        $this->_aError['captcha'] = LANG_ERROR_MAIL_CAPTCHA_NOT_CORRECT;
        return $this->_showFormTemplate(true);
      }
    }
    else
      return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_MAIL_CAPTCHA_NOT_LOADED, '/blog/' . $this->_iId);
  }
}