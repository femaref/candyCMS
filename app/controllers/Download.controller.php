<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Download.model.php';

class Download extends Main {

  public function __init() {
    $this->_oModel = new Model_Download($this->_aRequest, $this->_aSession, $this->_aFile);
  }

  public function show() {

    # Direct download for this id
    if (!empty($this->_iId)) {
      $aData = $this->_oModel->getData($this->_iId);
      $sFile = $aData['file'];

      # Update download count
      Model_Download::updateDownloadCount($this->_iId);

      # Send file directly
      header('Content-Disposition: attachment; filename="' . $sFile . '"');
      readfile(PATH_UPLOAD . '/download/' . $sFile);

      # No more actions down here
      exit();
    }

    # Show overview
    else {
      $this->_oSmarty->assign('download', $this->_oModel->getData());

      $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_DOWNLOAD);

      $this->_oSmarty->template_dir = Helper::getTemplateDir('downloads', 'show');
      return $this->_oSmarty->fetch('show.tpl');
    }
  }

  protected function _showFormTemplate($bUpdate = true) {

    if ($bUpdate == true) {
      $this->_aData = $this->_oModel->getData($this->_iId, true);

      foreach($this->_aData as $sColumn => $sData)
        $this->_oSmarty->assign($sColumn, $sData);

      $this->_oSmarty->assign('_action_url_', '/download/' . $this->_iId . '/update');
      $this->_oSmarty->assign('_formdata_', 'update_download');

      $this->_oSmarty->assign('lang_headline', LANG_DOWNLOAD_TITLE_UPDATE);
    }
    else {
      $this->_oSmarty->assign('_action_url_', '/download/create');
      $this->_oSmarty->assign('_formdata_', 'create_download');

      $this->_oSmarty->assign('category', '');
      $this->_oSmarty->assign('content', '');
      $this->_oSmarty->assign('title', '');

      $this->_oSmarty->assign('lang_file_choose', LANG_DOWNLOAD_FILE_CREATE_TITLE);
      $this->_oSmarty->assign('lang_headline', LANG_DOWNLOAD_TITLE_CREATE);
    }

		if (!empty($this->_aError)) {
			foreach ($this->_aError as $sField => $sMessage)
				$this->_oSmarty->assign('error_' . $sField, $sMessage);
		}

    $this->_oSmarty->template_dir = Helper::getTemplateDir('downloads', '_form');
    return $this->_oSmarty->fetch('_form.tpl');
  }

  protected function _create() {

    if (!isset($this->_aRequest['title']) || empty($this->_aRequest['title']))
      $this->_aError['title'] = LANG_ERROR_FORM_MISSING_TITLE;

    if (!isset($this->_aFile['file']) || empty($this->_aFile['file']['name']))
      $this->_aError['file'] = LANG_ERROR_FORM_MISSING_FILE;

    if (isset($this->_aError))
      return $this->_showFormTemplate(false);

    elseif ($this->_oModel->create() === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], Helper::getLastEntry('downloads'));
      return Helper::successMessage(LANG_SUCCESS_CREATE, '/download');
    }
    else
      return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/download');
  }

  protected function _update() {
		if ($this->_oModel->update((int) $this->_aRequest['id']) === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'],  (int) $this->_aRequest['id']);
			return Helper::successMessage(LANG_SUCCESS_UPDATE, '/download');
    }
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/download');
  }

  protected function _destroy() {
		if ($this->_oModel->destroy((int) $this->_aRequest['id']) === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'],  (int) $this->_aRequest['id']);
			return Helper::successMessage(LANG_SUCCESS_DESTROY, '/download');
    }
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/download');
  }
}