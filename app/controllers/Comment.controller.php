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

  private $_sRecaptchaPublicKey   = RECAPTCHA_PUBLIC;
  private $_sRecaptchaPrivateKey  = RECAPTCHA_PRIVATE;
  private $_sRecaptchaResponse    = ''; # TODO: String ?!
  private $_sRecaptchaError       = ''; # TODO: String ?!

  public function __init($iEntries = '', $aParentData = '') {
    $this->_aParentData =& $aParentData;
    $this->_iEntries    =& $iEntries;

    $this->_oModel = new Model_Comment($this->_aRequest, $this->_aSession);

    # Define Blog as standard category
    /*
		 * Create a switch or if / else to add more categories!
		 * b = Blog
		 * c = Content
		 * g = Gallery
    */
    $this->_sParentCategory = 'b';
    $this->_sParentSection	= 'Blog';
    $this->_sAction         = '/Comment/create/'.	$this->_sParentCategory.	'/'	.$this->_iId;
  }

  public final function show() {
    if($this->_iId) {
      if(isset($this->_aRequest['ajax'])) {
        if( $this->_sParentCategory == 'b') {
          $this->__autoload('Blog');
          $oBlog = new Blog($this->_aRequest, $this->_aSession);
          $oBlog->__init();
          $this->_aParentData =& $oBlog->_oModel->getData($this->_iId);
          $this->_iEntries =& $this->_aParentData[1]['comment_sum'];
        }
      }

      $oSmarty = new Smarty();
      $oSmarty->assign('USER_RIGHT', USER_RIGHT);
      $oSmarty->assign('AJAX_REQUEST', AJAX_REQUEST);
      $oSmarty->assign('parent_id', $this->_iId);

      # Do only load comments, if they are avaiable
      $sReturn = '';
      if($this->_iEntries > 0) {
        # @Override __init here due to AJAX reasons
        $this->_oPages = new Pages($this->_aRequest, $this->_iEntries, LIMIT_COMMENTS, 1);
        $this->_oModel->__init($this->_iEntries, $this->_oPages->getOffset(), $this->_oPages->getLimit());
        $this->_aData = $this->_oModel->getData($this->_iId, $this->_sParentCategory);
        $oSmarty->assign('comments', $this->_aData);

        # Set author of blog entry
        $iAuthorID = (int)$this->_aParentData[1]['author_id'];
        $oSmarty->assign('author_id', $iAuthorID);

        # For correct information, do some math to display entries
        # NOTE: If you're admin, you can see all entries. That might bring pagination to your view, even
        # when other people don't see it
        $iCommentNumber = ($this->_oPages->getCurrentPage() * LIMIT_COMMENTS) - LIMIT_COMMENTS;
        $oSmarty->assign('comment_number', $iCommentNumber);

        # Do we need Pages?
        $sPages = $this->_oPages->showPages('Comment/' .$this->_sParentCategory. '/'	.$this->_iId, '');
        $oSmarty->assign('_comment_pages_', $sPages);

        # Language
        $oSmarty->assign('lang_author', LANG_GLOBAL_AUTHOR);
        $oSmarty->assign('lang_deleted_user', LANG_GLOBAL_DELETED_USER);
        $oSmarty->assign('lang_destroy', LANG_COMMENT_TITLE_DESTROY);
        $oSmarty->assign('lang_quote', LANG_GLOBAL_QUOTE);

        $oSmarty->template_dir = Helper::getTemplateDir('comment/show');
        $sReturn .= $oSmarty->fetch('comment/show.tpl');
      }

      # Does the user have enough rights to enter a comment?
      # Show createComment Template if we don't have an action - description below
      if( !empty($this->_iId) || isset($this->_aRequest['parent_category'])) {
        if(!isset($this->_aRequest['ajax']) && !empty($this->_aParentData[1]['title']))
          $sReturn .= $this->create('create_comment');
      }

      return $sReturn;
    }
  }

  # @Override
  # We must override the main method due to user right problems
  public final function create($sInputName) {
    if( isset($this->_aRequest[$sInputName]) ) {
      if( USER_RIGHT == 0 )
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
    if(	!isset($this->_aRequest['parent_category']) || empty($this->_aRequest['parent_category']) )
      $this->_aError['parent_category'] = LANG_ERROR_FORM_MISSING_CATEGORY;

    if(	!isset($this->_aRequest['parent_id']) || empty($this->_aRequest['parent_id']) )
      $this->_aError['parent_id'] = LANG_ERROR_GLOBAL_WRONG_ID;

    if(	!isset($this->_aRequest['content']) || empty($this->_aRequest['content']) )
      $this->_aError['content'] = LANG_ERROR_FORM_MISSING_CONTENT;

    if( USER_ID < 1) {
      if( !isset($this->_aRequest['name']) || empty($this->_aRequest['name']) )
        $this->_aError['name'] = LANG_ERROR_FORM_MISSING_NAME;
    }

    # Set new action for form template
    $this->_sAction = '/Comment/create/'	.$this->_aRequest['parent_category'].
            '/'	.(int)$this->_aRequest['parent_id']. '#'.
            (int)$this->_aRequest['parent_id'];

    if (isset($this->_aError))
      return $this->_showFormTemplate($bShowCaptcha);

    else {
      if ($this->_oModel->create() == true)
        return Helper::redirectTo('/' . $this->_sParentSection .
                '/' . (int) $this->_aRequest['parent_id']) . Helper::successMessage(LANG_SUCCESS_CREATE);
      else
        return Helper::errorMessage(LANG_ERROR_SQL_QUERY);
    }
  }

  protected final function _destroy() {
    if ($this->_oModel->destroy((int) $this->_aRequest['id']) == true)
      Header('Location:' . WEBSITE_URL . '/' . $this->_sParentSection .
                      '/' . (int) $this->_aRequest['parent_id']) . Helper::successMessage(LANG_SUCCESS_DESTROY);
    else
      return Helper::errorMessage(LANG_ERROR_SQL_QUERY);
  }

  protected final function _showFormTemplate($bShowCaptcha) {
    # We search for parent category
    if( empty($this->_sAction) )
      return Helper::errorMessage(__CLASS__, LANG_ERROR_REQUEST_MISSING_ACTION);

    else {
      $sName = isset($this->_aRequest['name']) ?
              (string)$this->_aRequest['name']:
              '';

      $sEmail = isset($this->_aRequest['email']) ?
              (string)$this->_aRequest['email']:
              '';

      $sContent = isset($this->_aRequest['content']) ?
              (string)$this->_aRequest['content']:
              '';

      $iParentId = isset($this->_aRequest['parent_id']) ?
              (int)$this->_aRequest['parent_id']:
              (int)$this->_iId;

      $oSmarty = new Smarty();
      $oSmarty->assign('USER_RIGHT', USER_RIGHT);
      $oSmarty->assign('USER_EMAIL', USER_EMAIL);
      $oSmarty->assign('USER_NAME', USER_NAME);
      $oSmarty->assign('USER_SURNAME', USER_SURNAME);
      $oSmarty->assign('_action_url_', $this->_sAction.$iParentId);
      $oSmarty->assign('content', $sContent);
      $oSmarty->assign('email', $sEmail);
      $oSmarty->assign('name', $sName);
      $oSmarty->assign('parent_id', $iParentId);

      if( $bShowCaptcha == true )
        $oSmarty->assign('_captcha_', recaptcha_get_html(	$this->_sRecaptchaPublicKey,
                $this->_sRecaptchaError) );
      else
        $oSmarty->assign('_captcha_', '');

      if (!empty($this->_aError)) {
        foreach ($this->_aError as $sField => $sMessage)
          $oSmarty->assign('error_' . $sField, $sMessage);
      }

      # Language
      $oSmarty->assign('lang_headline', LANG_COMMENT_TITLE_CREATE);
      $oSmarty->assign('lang_content', LANG_GLOBAL_CONTENT);
      $oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
      $oSmarty->assign('lang_name', LANG_GLOBAL_NAME);
      $oSmarty->assign('lang_optional', LANG_GLOBAL_OPTIONAL);
      $oSmarty->assign('lang_reset', LANG_GLOBAL_RESET);
      $oSmarty->assign('lang_submit', LANG_GLOBAL_CREATE_ENTRY);

      $oSmarty->template_dir = Helper::getTemplateDir('comment/_form');
      return $oSmarty->fetch('comment/_form.tpl');
    }
  }

  private function _checkCaptcha($bShowCaptcha = true) {
    if( isset($this->_aRequest['recaptcha_response_field']) ) {
      $this->_sRecaptchaResponse = recaptcha_check_answer (
              $this->_sRecaptchaPrivateKey,
              $_SERVER['REMOTE_ADDR'],
              $this->_aRequest['recaptcha_challenge_field'],
              $this->_aRequest['recaptcha_response_field']);

      if ($this->_sRecaptchaResponse->is_valid)
        return $this->_create($bShowCaptcha);

      else {
        $this->_sRecaptchaError = $this->_sRecaptchaResponse->error;
        $this->_aError['captcha'] = LANG_ERROR_MAIL_CAPTCHA_NOT_CORRECT;
        return $this->_showFormTemplate($bShowCaptcha);
      }
    }
    else
      return Helper::errorMessage(LANG_ERROR_MAIL_CAPTCHA_NOT_LOADED);
  }
}