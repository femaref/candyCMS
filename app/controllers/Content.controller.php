<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/models/Content.model.php';

final class Content extends Main {
  public final function __init() {
    $this->_oModel = new Model_Content($this->m_aRequest, $this->m_oSession);
  }

  public final function show() {
    $oSmarty = new Smarty();
    $oSmarty->assign('USER_RIGHT', USER_RIGHT);

    # Language
    $oSmarty->assign('lang_by', LANG_GLOBAL_BY);
    $oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);

    if(!empty($this->_iID)) {
      $this->_aData = $this->_oModel->getData($this->_iID);

      # create output
      $oSmarty->assign('c', $this->_aData[$this->_iID]);
      $oSmarty->assign('URL', WEBSITE_URL);

      $this->_setTitle(Helper::removeSlahes($this->_aData[$this->_iID]['title']));

      # Language
      $oSmarty->assign('lang_add_bookmark', LANG_GLOBAL_ADD_BOOKMARK);
      $oSmarty->assign('lang_last_update', LANG_GLOBAL_LAST_UPDATE);
      $oSmarty->assign('lang_missing_entry', LANG_ERROR_GLOBAL_MISSING_ENTRY);
      $oSmarty->assign('lang_share', LANG_GLOBAL_SHARE);

      $oSmarty->template_dir = Helper::getTemplateDir('content/show');
      return $oSmarty->fetch('content/show.tpl');
    }
    else {
      $oSmarty->assign('content', $this->_oModel->getData());

      # Language
      $oSmarty->assign('lang_create_entry_headline', LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
      $oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY_ENTRY);
      $oSmarty->assign('lang_headline', LANG_GLOBAL_CONTENTMANAGER);

      $oSmarty->template_dir = Helper::getTemplateDir('content/overview');
      return $oSmarty->fetch('content/overview.tpl');
    }
  }

  protected final function _showFormTemplate($bUpdate = true) {
    $oSmarty = new Smarty();

    if($bUpdate == true) {
      $this->_aData = $this->_oModel->getData($this->_iID, true);
      $oSmarty->assign('c', $this->_aData[$this->_iID]);
      $oSmarty->assign('action', '/Content/update');
      $oSmarty->assign('formdata', 'update_content');
      $oSmarty->assign('id', $this->_iID);

      # Language
      $oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
      $oSmarty->assign('lang_reset', LANG_GLOBAL_RESET);
      $oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);

      $this->_setTitle(Helper::removeSlahes($this->_aData[$this->_iID]['title']));
    }
    else {
      $sTitle = isset($this->m_aRequest['title']) ?
              $this->m_aRequest['title'] :
              '';

      $sContent = isset($this->m_aRequest['content']) ?
              $this->m_aRequest['content'] :
              '';

      $aContent = array('title' => $sTitle, 'content' => $sContent);
      $oSmarty->assign('c', $aContent);
      $oSmarty->assign('action', '/Content/create');
      $oSmarty->assign('formdata', 'create_content');
      $oSmarty->assign('id', '');

      /* Language */
      $oSmarty->assign('lang_headline', LANG_GLOBAL_CREATE_ENTRY);
      $oSmarty->assign('lang_submit', LANG_GLOBAL_CREATE_ENTRY);
    }

    /* More language */
    $oSmarty->assign('lang_bb_help', LANG_GLOBAL_BBCODE_HELP);
    $oSmarty->assign('lang_content', LANG_GLOBAL_CONTENT);
    $oSmarty->assign('lang_currently', LANG_GLOBAL_CURRENTLY);
    $oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY_ENTRY);
    $oSmarty->assign('lang_last_update', LANG_GLOBAL_LAST_UPDATE);
    $oSmarty->assign('lang_title', LANG_GLOBAL_TITLE);

    $oSmarty->template_dir = Helper::getTemplateDir('content/_form');
    return $oSmarty->fetch('content/_form.tpl');
  }

  protected final function _create() {
    $sError = '';

    if(	!isset($this->m_aRequest['title']) ||
            empty($this->m_aRequest['title']) )
      $sError .= LANG_WEBSITE_TITLE.	'<br />';

    if(	!isset($this->m_aRequest['content']) ||
            empty($this->m_aRequest['content']) )
      $sError .= LANG_GLOBAL_CONTENT.	'<br />';

    if( !empty($sError) ) {
      $sReturn  = Helper::errorMessage($sError, LANG_ERROR_GLOBAL_CHECK_FIELDS);
      $sReturn .= $this->_showFormTemplate(false);
      return $sReturn;
    }
    else {
      if($this->_oModel->create() == true) {
        return Helper::successMessage(LANG_SUCCESS_CREATE).
                $this->show();
      }
      else
        return Helper::errorMessage(LANG_ERROR_DB_QUERY);
    }
  }

  protected final function _update() {
    if($this->_oModel->update((int)$this->m_aRequest['id']) == true)
      return Helper::successMessage(LANG_SUCCESS_UPDATE).$this->show();
    else
      return Helper::errorMessage(LANG_ERROR_DB_QUERY);
  }

  protected final function _destroy() {
    if($this->_oModel->destroy((int)$this->m_aRequest['id']) == true)
      return Helper::successMessage(LANG_SUCCESS_DESTROY).$this->show();
    else
      return Helper::errorMessage(LANG_ERROR_DB_QUERY);
  }
}