<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Gallery.model.php';
require_once 'app/helpers/Pages.helper.php';
require_once 'app/helpers/Upload.helper.php';
require_once 'app/helpers/Image.helper.php';

class Gallery extends Main {
  public function __init() {
    $this->_oModel = new Model_Gallery($this->m_aRequest, $this->m_oSession);
  }

  public final function show() {
    $oSmarty = new Smarty();
    $oSmarty->assign('UR', USERRIGHT);
    $oSmarty->assign('AJAX', AJAX);

    # Language
    $oSmarty->assign('lang_no_files_yet', LANG_GALLERY_NO_FILES_YET);

    # Specific gallery
    if( !empty($this->_iID) ) {
      # collect data array
      $sAlbumName	= $this->_oModel->getAlbumName($this->_iID);

      $oSmarty->assign('id', $this->_iID);
      $oSmarty->assign('files',
              $this->_oModel->getThumbs($this->_iID, LIMIT_ALBUM_IMAGES));
      $oSmarty->assign('gallery_name', $sAlbumName);
      $oSmarty->assign('gallery_description',
              $this->_oModel->getAlbumDescription($this->_iID));
      $oSmarty->assign('file_no', $this->_oModel->_iEntries);
      $oSmarty->assign('albumPages',
              $this->_oModel->_oPages->showPages('Gallery/'	.$this->_iID));
      $oSmarty->assign('dev', WEBSITE_DEV);
      $oSmarty->assign('popup_path', POPUP_DEFAULT_X);

      # Language
      $oSmarty->assign('lang_create_entry_headline', LANG_GALLERY_CREATE_FILE);
      $oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY);
      $oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);
      $oSmarty->assign('lang_files', LANG_GLOBAL_FILES);
      $oSmarty->assign('lang_uploaded_at', LANG_GLOBAL_UPLOADED_AT);

      $this->_setTitle(Helper::removeSlahes(LANG_GLOBAL_GALLERY.	': '	.
              $sAlbumName));

      $oSmarty->template_dir = Helper::templateDir('gallery/showFiles');
      return $oSmarty->fetch('gallery/showFiles.tpl');
    }
    # Overview
    else {
      $this->_setTitle(Helper::removeSlahes(LANG_GLOBAL_GALLERY));
      $oSmarty->assign('albums', $this->_oModel->getData());

      # Language
      $oSmarty->assign('lang_create_entry_headline', LANG_GALLERY_CREATE_ALBUM);
      $oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);
      $oSmarty->assign('lang_files', LANG_GLOBAL_FILES);
      $oSmarty->assign('lang_headline', LANG_GLOBAL_GALLERY);
      $oSmarty->assign('lang_no_entries', LANG_ERROR_GLOBAL_NO_ENTRIES);

      if( is_file(PATH_TPL_ADDON.	'/gallery/showAlbums.tpl') )
        $oSmarty->template_dir = PATH_TPL_ADDON;

      return $oSmarty->fetch('gallery/showAlbums.tpl');
    }
  }

  protected final function _create() {
    if(	!isset($this->m_aRequest['title']) ||
            empty($this->m_aRequest['title']) )
      $sError = LANG_GLOBAL_TITLE.	'<br />';

    if( !empty($sError) ) {
      $sReturn  = Helper::errorMessage($sError, LANG_ERROR_GLOBAL_CHECK_FIELDS);
      $sReturn .= $this->_showFormTemplate(false);
      return $sReturn;
    }
    else {
      if($this->_oModel->create() == true)
        return Helper::successMessage(LANG_SUCCESS_CREATE).
                $this->show($this->_oModel->getId());
      else
        return Helper::errorMessage(LANG_ERROR_DB_QUERY);
    }
  }

  protected final function _update() {
    if(	!isset($this->m_aRequest['title']) ||
            empty($this->m_aRequest['title']) )
      $sError = LANG_GLOBAL_TITLE.	'<br />';

    if( !empty($sError) ) {
      $sReturn  = Helper::errorMessage($sError, LANG_ERROR_GLOBAL_CHECK_FIELDS);
      $sReturn .= $this->_showFormTemplate(true);
      return $sReturn;
    }
    else {
      if( $this->_oModel->update((int)$this->m_aRequest['id']) == true)
        return Helper::successMessage(LANG_SUCCESS_UPDATE).
                $this->show();
      else
        return Helper::errorMessage(LANG_ERROR_DB_QUERY);
    }
  }

  protected function _destroy() {
    if($this->_oModel->destroy($this->_iID) == true) {
      unset($this->_iID);
      return Helper::successMessage(LANG_SUCCESS_DESTROY).
              $this->show();
    }
    else {
      unset($this->_iID);
      return Helper::errorMessage(LANG_ERROR_DB_QUERY);
    }
  }

  protected final function _showFormTemplate($bUpdate = true) {
    $oSmarty = new Smarty();
    $oSmarty->assign('UR', USERRIGHT);

    if($bUpdate == true) {
      $this->_aData = $this->_oModel->getData($this->_iID, true);
      $oSmarty->assign('title', $this->_aData['title']);
      $oSmarty->assign('description', $this->_aData['description']);

      $oSmarty->assign('action', '/Gallery/update/'	.$this->_iID);
      $oSmarty->assign('formdata', 'update_gallery');
      $oSmarty->assign('id', $this->_iID);

      # Language
      $oSmarty->assign('lang_destroy_entry', LANG_GLOBAL_DESTROY_ENTRY);
      $oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
      $oSmarty->assign('lang_reset', LANG_GLOBAL_RESET);
      $oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);

      $this->_setTitle(Helper::removeSlahes($this->_aData['title']));
    }
    else {
      $sTitle = isset($this->m_aRequest['title']) ?
              $this->m_aRequest['title'] :
              '';

      $sDescription = isset($this->m_aRequest['description']) ?
              $this->m_aRequest['description'] :
              '';

      $oSmarty->assign('title', $sTitle);
      $oSmarty->assign('description', $sDescription);
      $oSmarty->assign('action', '/Gallery/create');
      $oSmarty->assign('formdata', 'create_gallery');
      $oSmarty->assign('id', '');

      # Language
      $oSmarty->assign('lang_headline', LANG_GALLERY_CREATE_ALBUM);
      $oSmarty->assign('lang_submit', LANG_GALLERY_CREATE_ALBUM);
    }

    $oSmarty->assign('lang_description', LANG_GLOBAL_DESCRIPTION);
    $oSmarty->assign('lang_title', LANG_GLOBAL_TITLE);

    $oSmarty->template_dir = Helper::templateDir('gallery/_form_album');
    return $oSmarty->fetch('gallery/_form_album.tpl');
  }


  public final function createFile() {
    if( USERRIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
    else {
      if( isset($this->m_aRequest['create_file']) )
        return $this->_oModel->createFile();
      else
        return $this->_showFormFileTemplate(false);
    }
  }

  public final function updateFile() {
    if( USERRIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
    else {
      if( isset($this->m_aRequest['update_file']) )
        if( $this->_oModel->updateFile($this->_iID) == true)
          return Helper::successMessage(LANG_SUCCESS_UPDATE).
                  $this->show();
        else
          return Helper::errorMessage(LANG_ERROR_GLOBAL);
      else
        return $this->_showFormFileTemplate(true);
    }
  }

  public final function destroyFile() {
    if( USERRIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
    else {
      if($this->_oModel->destroyFile($this->_iID) == true) {
        unset($this->_iID);
        Helper::redirectTo('/Gallery');
        return Helper::successMessage(LANG_MEDIA_FILE_DELETE_SUCCESS).$this->show();
        ;
      }
    }
  }

  protected final function _showFormFileTemplate($bUpdate = false) {
    $oSmarty = new Smarty();
    $oSmarty->assign('UR', USERRIGHT);
    $oSmarty->assign('id', $this->_iID);

    if($bUpdate == true) {
      $oGetFileData = new Query("	SELECT
																		description
																	FROM
																		gallery_file
																	WHERE
																		id = '"	.$this->_iID.	"'");

      $aRow = $oGetFileData->fetch();
      $oSmarty->assign('description', $aRow['description']);
      $oSmarty->assign('formdata', 'update_file');
      $oSmarty->assign('action', '/Gallery/updatefile/'	.$this->_iID);

      # Language
      $oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY);
      $oSmarty->assign('lang_headline', LANG_GALLERY_UPDATE_FILE);
      $oSmarty->assign('lang_reset', LANG_GLOBAL_RESET);
    }
    else {
      # See helper/Image.helper.php for Details!
      $sDefault = isset($this->m_aRequest['cut']) ?
              Helper::formatHTMLCode($this->m_aRequest['cut']) :
              'r'; # r = resize, c = cut

      $oSmarty->assign('default', $sDefault);
      $oSmarty->assign('description', '');
      $oSmarty->assign('formdata', 'create_file');
      $oSmarty->assign('action', '/Gallery/upload/'	.$this->_iID.	'/' .session_id());

      # Language
      $oSmarty->assign('lang_create_file_cut', LANG_GALLERY_CREATE_FILE_CUT);
      $oSmarty->assign('lang_create_file_resize', LANG_GALLERY_CREATE_FILE_RESIZE);
      $oSmarty->assign('lang_cut', LANG_GLOBAL_CUT);
      $oSmarty->assign('lang_file_choose', LANG_MEDIA_FILE_CHOOSE);
      $oSmarty->assign('lang_headline', LANG_GALLERY_CREATE_FILE);
      $oSmarty->assign('lang_same_filetype', LANG_GALLERY_SAME_FILETYPE);
    }

    # Language
    $oSmarty->assign('lang_description', LANG_GLOBAL_DESCRIPTION);

    $oSmarty->template_dir = Helper::templateDir('gallery/_form_file');
    return $oSmarty->fetch('gallery/_form_file.tpl');
  }
}