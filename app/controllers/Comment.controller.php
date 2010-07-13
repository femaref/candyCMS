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

require_once 'app/models/Comment.model.php';
require_once 'app/helpers/Pages.helper.php';

final class Comment extends Main {
  private $_aParentData;
  private $_oPages;
  private $_iEntries;
  private $_sParentCat;
  private $_sParentSection;
  private $_sAction;

  public function __init($iEntries = '', $aParentData = '') {
    $this->_aParentData =& $aParentData;
    $this->_iEntries	=& $iEntries;

    $this->_oModel = new Model_Comment($this->m_aRequest, $this->m_oSession);

    # Define Blog as standard category
    # TODO: more comment-sections
    /*
		 * Create a switch or if / else to add more categories!
		 * b = Blog
		 * c = Content
		 * g = Gallery
    */
    $this->_sParentCat		= 'b';
    $this->_sParentSection	= 'Blog';
    $this->_sAction			= '/CreateComment/'.	$this->_sParentCat.	'/'	.$this->_iID;
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
      $oSmarty->assign('uid', USERID);
      $oSmarty->assign('UR', USERRIGHT);

      # Do only load comments, if they are avaiable
      $sReturn = '';
      if($this->_iEntries > 0) {
        # __init here due to AJAX reasons
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
        $oSmarty->assign('commentNumber', $iCommentNumber);

        # Do we need Pages?
        $sPages = $this->_oPages->showPages('Comment/' .$this->_sParentCat. '/'	.$this->_iID, '');
        $oSmarty->assign('commentPages', $sPages);

        # Language
        $oSmarty->assign('lang_author', LANG_GLOBAL_AUTHOR);
        $oSmarty->assign('lang_deleted_user', LANG_GLOBAL_DELETED_USER);
        $oSmarty->assign('lang_destroy', LANG_COMMENT_DESTROY);
        $oSmarty->assign('lang_quote', LANG_GLOBAL_QUOTE);

        $oSmarty->template_dir = Helper::templateDir('comment/show');
        $sReturn .= $oSmarty->fetch('comment/show.tpl');
      }

      # Does the user have enough rights to enter a comment?
      # Show createComment Template if we don't have an action - description below
      if( ($this->_iID !== '' && USERID > 0 && !isset($this->m_aRequest['action']) ) ||
              isset($this->m_aRequest['parentcat']) && $this->_iID !== '' && USERID > 0) {
        if(!isset($this->m_aRequest['ajax']))
          $sReturn .= $this->create('create_comment');
      }
      
      # Due to privacy options, _showFormTemplate is forced to return
      # a simple template instead of handling action requests
      elseif($this->_iID !== '' || USERID == 0 ||
              !(isset($this->m_aRequest['action']) && 'tags' ==
                      isset($this->m_aRequest['action']) ) ) {
        if(!isset($this->m_aRequest['ajax']))
          $sReturn .= LANG_COMMENT_LOGIN_FIRST;
      }

      return $sReturn;
    }
  }

  protected final function _create() {
    if(	!isset($this->m_aRequest['parentcat']) ||
            empty($this->m_aRequest['parentcat']) )
      $sError .= LANG_GLOBAL_CATEGORY.	'<br />';

    if(	!isset($this->m_aRequest['parentid']) ||
            empty($this->m_aRequest['parentid']) )
      $sError .= 'ID<br />';

    if(	!isset($this->m_aRequest['content']) ||
            empty($this->m_aRequest['content']) )
      $sError .= LANG_GLOBAL_CONTENT.	'<br />';

    /* Set new Action */
    $this->_sAction = '/CreateComment/'	.$this->m_aRequest['parentcat'].
            '/'	.(int)$this->m_aRequest['parentid']. '#'.
            (int)$this->m_aRequest['parentid'];

    if( !empty($sError) ) {
      $sReturn  = Helper::errorMessage($sError, LANG_ERROR_GLOBAL_CHECK_FIELDS);
      $sReturn .= $this->_showFormTemplate();
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

  protected final function _showFormTemplate($bEdit = false) {
    if( empty($this->_sAction) )
      return Helper::errorMessage(__CLASS__, LANG_ERROR_ACTION_NOT_SPECIFIED);
    else {
      $oSmarty = new Smarty();
      $oSmarty->assign('UR', USERRIGHT);
      $oSmarty->assign('action', $this->_sAction);

      # Language
      $oSmarty->assign('lang_headline', LANG_COMMENT_CREATE);
      $oSmarty->assign('lang_bb_help', LANG_GLOBAL_BBCODE_HELP);
      $oSmarty->assign('lang_submit', LANG_GLOBAL_CREATE_ENTRY);

      $oSmarty->template_dir = Helper::templateDir('comment/_form');
      return $oSmarty->fetch('comment/_form.tpl');
    }
  }
}