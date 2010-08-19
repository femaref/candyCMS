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
  private $_sParentCat;
  private $_sParentSection;
  private $_sAction;

  private $_sRecaptchaPublicKey = RECAPTCHA_PUBLIC;
  private $_sRecaptchaPrivateKey = RECAPTCHA_PRIVATE;
  private $_sRecaptchaResponse = '';
  private $_sRecaptchaError = '';

  public function __init($iEntries = '', $aParentData = '') {
    $this->_aParentData =& $aParentData;
    $this->_iEntries	=& $iEntries;

    $this->_oModel = new Model_Comment($this->m_aRequest, $this->m_oSession);

    # Define Blog as standard category
    /*
		 * Create a switch or if / else to add more categories!
		 * b = Blog
		 * c = Content
		 * g = Gallery
    */
    $this->_sParentCat		= 'b';
    $this->_sParentSection	= 'Blog';
    $this->_sAction			= '/Comment/create/'.	$this->_sParentCat.	'/'	.$this->_iID;
  }

  public final function show() {
    if($this->_iID) {
      if(isset($this->m_aRequest['ajax'])) {
        if( $this->_sParentCat == 'b') {
          $this->__autoload('Blog');
          $oBlog = new Blog($this->m_aRequest, $this->m_oSession);
          $oBlog->__init();
          $this->_aParentData =& $oBlog->_oModel->getData($this->_iID);
          $this->_iEntries =& $this->_aParentData[1]['comment_sum'];
        }
      }

      $oSmarty = new Smarty();
      $oSmarty->assign('USER_RIGHT', USER_RIGHT);
      $oSmarty->assign('AJAX_REQUEST', AJAX_REQUEST);
      $oSmarty->assign('parentID', $this->_iID);

      # Do only load comments, if they are avaiable
      $sReturn = '';
      if($this->_iEntries > 0) {
        # @Override __init here due to AJAX reasons
        $this->_oPages = new Pages($this->m_aRequest, $this->_iEntries, LIMIT_COMMENTS, 1);
        $this->_oModel->__init($this->_iEntries, $this->_oPages->getOffset(), $this->_oPages->getLimit());
        $this->_aData = $this->_oModel->getData($this->_iID, $this->_sParentCat);
        $oSmarty->assign('comments', $this->_aData);

        # Set author of blog entry
        $iAuthorID = (int)$this->_aParentData[1]['authorID'];
        $oSmarty->assign('authorID', $iAuthorID);

        # For correct information, do some math to display entries
        # NOTE: If you're admin, you can see all entries. That might bring pagination to your view, even
        # when other people don't see it
        $iCommentNumber = ($this->_oPages->getCurrentPage() * LIMIT_COMMENTS) - LIMIT_COMMENTS;
        $oSmarty->assign('comment_number', $iCommentNumber);

        # Do we need Pages?
        $sPages = $this->_oPages->showPages('Comment/' .$this->_sParentCat. '/'	.$this->_iID, '');
        $oSmarty->assign('_comment_pages_', $sPages);

        # Language
        $oSmarty->assign('lang_author', LANG_GLOBAL_AUTHOR);
        $oSmarty->assign('lang_deleted_user', LANG_GLOBAL_DELETED_USER);
        $oSmarty->assign('lang_destroy', LANG_COMMENT_DESTROY);
        $oSmarty->assign('lang_quote', LANG_GLOBAL_QUOTE);

        $oSmarty->template_dir = Helper::getTemplateDir('comment/show');
        $sReturn .= $oSmarty->fetch('comment/show.tpl');
      }

      # Does the user have enough rights to enter a comment?
      # Show createComment Template if we don't have an action - description below
      if( ($this->_iID !== '' && !isset($this->m_aRequest['action']) ) ||
              isset($this->m_aRequest['parentcat']) && $this->_iID !== '') {
        if(!isset($this->m_aRequest['ajax']))
          $sReturn .= $this->create('create_comment');
      }

      return $sReturn;
    }
  }

  public final function create($sInputName) {
    if( isset($this->m_aRequest[$sInputName]) ) {
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
    $sError = '';

    if(	!isset($this->m_aRequest['parentcat']) ||
            empty($this->m_aRequest['parentcat']) )
      $sError .= LANG_GLOBAL_CATEGORY.	'<br />';

    if(	!isset($this->m_aRequest['parentid']) ||
            empty($this->m_aRequest['parentid']) )
      $sError .= 'ID<br />';

    if(	!isset($this->m_aRequest['content']) ||
            empty($this->m_aRequest['content']) )
      $sError .= LANG_GLOBAL_CONTENT.	'<br />';

    if( USER_ID < 1) {
      if( !isset($this->m_aRequest['name']) ||
              empty($this->m_aRequest['name']) )
        $sError .= LANG_GLOBAL_NAME.	'<br />';
    }

    /* Set new Action */
    $this->_sAction = '/Comment/create/'	.$this->m_aRequest['parentcat'].
            '/'	.(int)$this->m_aRequest['parentid']. '#'.
            (int)$this->m_aRequest['parentid'];

    if( !empty($sError) ) {
      $sReturn  = Helper::errorMessage($sError, LANG_ERROR_GLOBAL_CHECK_FIELDS);
      $sReturn .= $this->_showFormTemplate($bShowCaptcha);
      return $sReturn;
    }
    else {
      # We receive the CommentID after creating an entry
      # TODO: Success Message to Controller
      $iComment = $this->_oModel->create();
      Helper::redirectTo('/'	.$this->_sParentSection.
              '/'	.(int)$this->m_aRequest['parentid'].	'#'	.$iComment);
    }
  }

  protected final function _destroy() {
    # TODO: Error Message
    if($this->_oModel->destroy((int)$this->m_aRequest['id']) == true)
      Header('Location:'	.WEBSITE_URL.	'/'	.$this->_sParentSection.
              '/'	.(int)$this->m_aRequest['parentid']);
  }

  protected final function _showFormTemplate($bShowCaptcha) {
    if( empty($this->_sAction) )
      return Helper::errorMessage(__CLASS__, LANG_ERROR_ACTION_NOT_SPECIFIED);
    else {
      $sName = isset($this->m_aRequest['name']) ?
              (string)$this->m_aRequest['name']:
              '';

      $sEmail = isset($this->m_aRequest['email']) ?
              (string)$this->m_aRequest['email']:
              '';

      $sContent = isset($this->m_aRequest['content']) ?
              (string)$this->m_aRequest['content']:
              '';

      $iParentId = isset($this->m_aRequest['parentID']) ?
              (int)$this->m_aRequest['parentID']:
              '';

      $oSmarty = new Smarty();
      $oSmarty->assign('USER_RIGHT', USER_RIGHT);
      $oSmarty->assign('USER_EMAIL', USER_EMAIL);
      $oSmarty->assign('USER_NAME', USER_NAME);
      $oSmarty->assign('USER_SURNAME', USER_SURNAME);
      $oSmarty->assign('action', $this->_sAction.$iParentId);
      $oSmarty->assign('content', $sContent);
      $oSmarty->assign('email', $sEmail);
      $oSmarty->assign('name', $sName);
      $oSmarty->assign('parentID', $iParentId);

      if( $bShowCaptcha == true )
        $oSmarty->assign('_captcha_', recaptcha_get_html(	$this->_sRecaptchaPublicKey,
                $this->_sRecaptchaError) );
      else
        $oSmarty->assign('_captcha_', '');

      # Language
      $oSmarty->assign('lang_headline', LANG_COMMENT_CREATE);
      $oSmarty->assign('lang_bb_help', LANG_GLOBAL_BBCODE_HELP);
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
    if( isset($this->m_aRequest['recaptcha_response_field']) ) {
      $this->_sRecaptchaResponse = recaptcha_check_answer (
              $this->_sRecaptchaPrivateKey,
              $_SERVER['REMOTE_ADDR'],
              $this->m_aRequest['recaptcha_challenge_field'],
              $this->m_aRequest['recaptcha_response_field']);

      if ($this->_sRecaptchaResponse->is_valid)
        return $this->_create($bShowCaptcha);
      else {
        $this->_sRecaptchaError = $this->_sRecaptchaResponse->error;
        return Helper::errorMessage(LANG_ERROR_MAIL_CAPTCHA_NOT_CORRECT).
                $this->_showFormTemplate($bShowCaptcha);
      }
    }
    else
      return Helper::errorMessage(LANG_ERROR_MAIL_CAPTCHA_NOT_LOADED);
  }
}