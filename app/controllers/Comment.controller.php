<?php

/**
 * CRD action of comments.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Comment as Model;

require_once 'app/models/Comment.model.php';
require_once 'lib/recaptcha/recaptchalib.php';

class Comment extends Main {

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
    $this->_oModel = new Model($this->_aRequest, $this->_aSession);
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
      $aData = $this->_oModel->getData($this->_iId, (int) $this->_aParentData[1]['comment_sum'], LIMIT_COMMENTS);
      $this->oSmarty->assign('comments', $aData);

      # Set author of blog entry
      $this->oSmarty->assign('author_id', (int) $this->_aParentData[1]['author_id']);

      # For correct information, do some math to display entries.
      # NOTE: If you're admin, you can see all entries. That might bring pagination to your view, even
      # when other people don't see it
      $this->oSmarty->assign('comment_number', ($this->_oModel->oPagination->getCurrentPage() * LIMIT_COMMENTS) - LIMIT_COMMENTS);

      # Do we need pages?
      $this->oSmarty->assign('_pages_', $this->_oModel->oPagination->showPages('/blog/' . $this->_iId));

      $sTemplateDir = Helper::getTemplateDir('comments', 'show');
      $this->oSmarty->template_dir = $sTemplateDir;
      return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'show')) . $this->create('create_comment');
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
    $sName      = isset($this->_aRequest['name']) ? (string) $this->_aRequest['name'] : '';
    $sEmail     = isset($this->_aRequest['email']) ? (string) $this->_aRequest['email'] : '';
    $sContent   = isset($this->_aRequest['content']) ? (string) $this->_aRequest['content'] : '';

    $this->oSmarty->assign('content', $sContent);
    $this->oSmarty->assign('email', $sEmail);
    $this->oSmarty->assign('name', $sName);

    if ($bShowCaptcha === true && RECAPTCHA_ENABLED === true)
      $this->oSmarty->assign('_captcha_', recaptcha_get_html($this->_sRecaptchaPublicKey, $this->_sRecaptchaError));

    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir = Helper::getTemplateDir('comments', '_form');
    $this->oSmarty->template_dir = $sTemplateDir;
    return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, '_form'));
  }

  /**
   * Create entry, check for captcha or show form template if we have enough roles.
   * We must override the main method due to a diffent required user role.
   *
   * @access public
   * @param string $sInputName sent input name to verify action
   * @return string HTML content
   * @override app/controllers/Main.controller.php
   *
   */
  public function create($sInputName) {
    if (isset($this->_aRequest[$sInputName])) {
      if ($this->_aSession['userdata']['role'] == 0 && RECAPTCHA_ENABLED == true && MOBILE == false)
        return $this->_checkCaptcha();

      else
        return $this->_create(false);
    }
    else {
      $bShowCaptcha = $this->_aSession['userdata']['role'] == 0 ? true : false;
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
   * @param boolean $bShowCaptcha show captcha?
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create($bShowCaptcha = true) {
    $this->_setError('parent_id', $this->oI18n->get('error.missing.id'));
    $this->_setError('content');

    if ($this->_aSession['userdata']['id'] < 1)
      $this->_setError('name');

    if (isset($this->_aError))
      return $this->_showFormTemplate($bShowCaptcha);

    else {
      $iLastComment = Helper::getLastEntry('comments') + 1;
      $sRedirect = '/blog/' . (int) $this->_aRequest['parent_id'];

      if ($this->_oModel->create() === true) {
        Log::insert('comment', 'create', $iLastComment, $this->_aSession['userdata']['id']);
        return Helper::successMessage($this->oI18n->get('success.create'), $sRedirect);
      }
      else
        return Helper::errorMessage($this->oI18n->get('error.sql'), $sRedirect);
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
      Log::insert('comment', 'destroy', (int) $this->_aRequest['id'], $this->_aSession['userdata']['id']);
      return Helper::successMessage($this->oI18n->get('success.destroy'), $sRedirect);
    }
    else
      return Helper::errorMessage($this->oI18n->get('error.sql'), $sRedirect);
  }

  /**
   * Check if the entered captcha is correct.
   *
   * @access protected
   * @return boolean|string status of create action (boolean),
   * status of error message (boolean) or HTML content of form template (string).
   *
   */
  protected function _checkCaptcha() {
    if (isset($this->_aRequest['recaptcha_response_field'])) {
      $this->_oRecaptchaResponse = recaptcha_check_answer (
              $this->_sRecaptchaPrivateKey,
              $_SERVER['REMOTE_ADDR'],
              $this->_aRequest['recaptcha_challenge_field'],
              $this->_aRequest['recaptcha_response_field']);

      if ($this->_oRecaptchaResponse->is_valid)
        return $this->_create(true);

      else {
        $this->_aError['captcha'] = $this->oI18n->get('error.captcha.incorrect');
        return Helper::errorMessage($this->oI18n->get('error.captcha.incorrect')) . $this->_showFormTemplate(true);
      }
    }
    else
      return Helper::errorMessage($this->oI18n->get('error.captcha.loading'), '/blog/' . $this->_iId);
  }
}