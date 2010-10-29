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
    $this->_oModel = new Model_Content($this->_aRequest, $this->_aSession);
  }

  public final function show() {
    $this->_setTitle(LANG_GLOBAL_CONTENTMANAGER);

    $oSmarty = new Smarty();
    $oSmarty->assign('USER_RIGHT', USER_RIGHT);

    # Language
    $oSmarty->assign('lang_by', LANG_GLOBAL_BY);
    $oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);

    if(empty($this->_iId)) {
      $oSmarty->assign('content', $this->_oModel->getData());

      # Language
      $oSmarty->assign('lang_create_entry_headline', LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
      $oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY_ENTRY);
      $oSmarty->assign('lang_headline', LANG_GLOBAL_CONTENTMANAGER);

      $oSmarty->cache_dir = CACHE_DIR;
      $oSmarty->compile_dir = COMPILE_DIR;
      $oSmarty->template_dir = Helper::getTemplateDir('contents/overview');
      return $oSmarty->fetch('contents/overview.tpl');
    }
    else {
      $this->_aData = $this->_oModel->getData($this->_iId);

      # create output
      $oSmarty->assign('c', $this->_aData[$this->_iId]);
      $oSmarty->assign('URL', WEBSITE_URL);

      $this->_setTitle(Helper::removeSlahes($this->_aData[$this->_iId]['title']));

      # Language
      $oSmarty->assign('lang_add_bookmark', LANG_GLOBAL_ADD_BOOKMARK);
      $oSmarty->assign('lang_last_update', LANG_GLOBAL_LAST_UPDATE);
      $oSmarty->assign('lang_missing_entry', LANG_ERROR_GLOBAL_MISSING_ENTRY);
      $oSmarty->assign('lang_share', LANG_GLOBAL_SHARE);

      $oSmarty->cache_dir = CACHE_DIR;
      $oSmarty->compile_dir = COMPILE_DIR;
      $oSmarty->template_dir = Helper::getTemplateDir('contents/show');
      return $oSmarty->fetch('contents/show.tpl');
    }
  }

  protected final function _showFormTemplate($bUpdate = true) {
    $oSmarty = new Smarty();

    if($bUpdate == true) {
      $this->_aData = $this->_oModel->getData($this->_iId, true);
      $oSmarty->assign('_action_url_', '/Content/update');
      $oSmarty->assign('_formdata_', 'update_content');
      $oSmarty->assign('c', $this->_aData);
      $oSmarty->assign('id', $this->_iId);

      # Language
      $oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
      $oSmarty->assign('lang_reset', LANG_GLOBAL_RESET);
      $oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);

      $this->_setTitle(Helper::removeSlahes($this->_aData['title']));
    }
    # We create new content
    else {
      $sTitle = isset($this->_aRequest['title']) ?
              $this->_aRequest['title'] :
              '';

      $sContent = isset($this->_aRequest['content']) ?
              $this->_aRequest['content'] :
              '';

      $oSmarty->assign('_action_url_', '/Content/create');
      $oSmarty->assign('_formdata_', 'create_content');
      $aContent = array('title' => $sTitle, 'content' => $sContent);
      $oSmarty->assign('c', $aContent);
      $oSmarty->assign('id', '');

      # Language
      $oSmarty->assign('lang_headline', LANG_GLOBAL_CREATE_ENTRY);
      $oSmarty->assign('lang_submit', LANG_GLOBAL_CREATE_ENTRY);

      # Title comes from section helper
    }

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $oSmarty->assign('error_' . $sField, $sMessage);
    }

    $oSmarty->assign('_language_', substr(DEFAULT_LANGUAGE, 0, 2));
		$oSmarty->assign('_compress_files_suffix_', WEBSITE_COMPRESS_FILES == true ? '-min' : '');

    # More language
    $oSmarty->assign('lang_bb_help', LANG_GLOBAL_BBCODE_HELP);
    $oSmarty->assign('lang_content', LANG_GLOBAL_CONTENT);
    $oSmarty->assign('lang_currently', LANG_GLOBAL_CURRENTLY);
    $oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY_ENTRY);
    $oSmarty->assign('lang_last_update', LANG_GLOBAL_LAST_UPDATE);
    $oSmarty->assign('lang_title', LANG_GLOBAL_TITLE);

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
    $oSmarty->template_dir = Helper::getTemplateDir('contents/_form');
    return $oSmarty->fetch('contents/_form.tpl');
  }

  protected final function _create() {
		if (!isset($this->_aRequest['title']) || empty($this->_aRequest['title']))
			$this->_aError['title'] = LANG_ERROR_FORM_MISSING_TITLE;

		if (!isset($this->_aRequest['content']) || empty($this->_aRequest['content']))
			$this->_aError['content'] = LANG_ERROR_FORM_MISSING_CONTENT;

		if (isset($this->_aError))
			return $this->_showFormTemplate(false);

		else {
			if ($this->_oModel->create() === true) {
        Helper::log($this->_aRequest['section'], $this->_aRequest['action'], Helper::getLastEntry('contents'));
				return Helper::successMessage(LANG_SUCCESS_CREATE, '/Content');
      }
			else
				return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/Content');
		}
	}

  protected final function _update() {
		$sRedirect = '/Content/' . (int) $this->_aRequest['id'];
		if ($this->_oModel->update((int) $this->_aRequest['id']) === true) {
      Helper::log($this->_aRequest['section'], $this->_aRequest['action'],  (int) $this->_aRequest['id']);
			return Helper::successMessage(LANG_SUCCESS_UPDATE, $sRedirect);
    }
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
	}

	protected final function _destroy() {
		if ($this->_oModel->destroy((int) $this->_aRequest['id']) === true) {
      Helper::log($this->_aRequest['section'], $this->_aRequest['action'],  (int) $this->_aRequest['id']);
			return Helper::successMessage(LANG_SUCCESS_DESTROY, '/Content');
    }
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/Content');
	}
}