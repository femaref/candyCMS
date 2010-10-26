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
    $this->_oModel = new Model_Gallery($this->_aRequest, $this->_aSession);
  }

  public final function show() {
    $oSmarty = new Smarty();

    # Constants
    $oSmarty->assign('USER_RIGHT', USER_RIGHT);
    $oSmarty->assign('AJAX_REQUEST', AJAX_REQUEST);

    # Language
    $oSmarty->assign('lang_no_files_uploaded', LANG_ERROR_GALLERY_NO_FILES_UPLOADED);

    # Specific gallery
    if( !empty($this->_iId) ) {
      # collect data array
      $sAlbumName	= Model_Gallery::getAlbumName($this->_iId);

      $oSmarty->assign('id', $this->_iId);
      $oSmarty->assign('files', $this->_oModel->getThumbs($this->_iId, LIMIT_ALBUM_IMAGES));
      $oSmarty->assign('file_no', $this->_oModel->_iEntries);
      $oSmarty->assign('gallery_name', $sAlbumName);
      $oSmarty->assign('gallery_description', Model_Gallery::getAlbumDescription($this->_iId));

      # System variables
      $oSmarty->assign('_album_pages_', $this->_oModel->oPages->showPages('Gallery/'	.$this->_iId));
      $oSmarty->assign('_compress_files_suffix_', WEBSITE_COMPRESS_FILES == true ? '-min' : '');

      # Language
      $oSmarty->assign('lang_create_entry_headline', LANG_GALLERY_FILE_CREATE_TITLE);
      $oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY);
      $oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);
      $oSmarty->assign('lang_files', LANG_GLOBAL_FILES);
      $oSmarty->assign('lang_uploaded_at', LANG_GLOBAL_UPLOADED_AT);

      $this->_setTitle(Helper::removeSlahes(LANG_GLOBAL_GALLERY.	': '	.
              $sAlbumName));

      $oSmarty->cache_dir = CACHE_DIR;
      $oSmarty->compile_dir = COMPILE_DIR;
      $oSmarty->template_dir = Helper::getTemplateDir('galleries/showFiles');
      return $oSmarty->fetch('galleries/showFiles.tpl');
    }
    # Overview
    else {
      $this->_setTitle(Helper::removeSlahes(LANG_GLOBAL_GALLERY));
      $oSmarty->assign('albums', $this->_oModel->getData());

      # Language
      $oSmarty->assign('lang_create_entry_headline', LANG_GALLERY_ALBUM_CREATE_TITLE);
      $oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);
      $oSmarty->assign('lang_files', LANG_GLOBAL_FILES);
      $oSmarty->assign('lang_headline', LANG_GLOBAL_GALLERY);
      $oSmarty->assign('lang_no_entries', LANG_ERROR_GLOBAL_NO_ENTRIES);

      $oSmarty->cache_dir = CACHE_DIR;
      $oSmarty->compile_dir = COMPILE_DIR;
      $oSmarty->template_dir = Helper::getTemplateDir('galleries/showAlbums');
      return $oSmarty->fetch('galleries/showAlbums.tpl');
    }
  }

  # Create gallery album
  protected final function _create() {
    if (!isset($this->_aRequest['title']) || empty($this->_aRequest['title']))
      $this->_aError['title'] = LANG_ERROR_FORM_MISSING_TITLE;

    if (isset($this->_aError))
      return $this->_showFormTemplate(false);

    else {
			$sRedirect = '/Gallery';

      if ($this->_oModel->create() === true) {
        Helper::log('gallery_album', $this->_aRequest['action'], Helper::getLastEntry('gallery_albums'));
        return Helper::successMessage(LANG_SUCCESS_CREATE, $sRedirect);
      }
      else
        return Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
    }
  }

  # Update gallery album
  protected final function _update() {
    if(	!isset($this->_aRequest['title']) || empty($this->_aRequest['title']) )
      $this->_aError['title'] = LANG_ERROR_FORM_MISSING_TITLE;

    if (isset($this->_aError))
      return $this->_showFormTemplate(true);

    else {
			$sRedirect = '/Gallery/' . (int) $this->_aRequest['id'];

      if( $this->_oModel->update((int)$this->_aRequest['id']) === true) {
        Helper::log('gallery_album', $this->_aRequest['action'], (int)$this->_aRequest['id']);
        return Helper::successMessage(LANG_SUCCESS_UPDATE, $sRedirect);
      }
      else
        return Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
    }
  }

  # Destroy gallery album
  protected function _destroy() {
		$sRedirect = '/Gallery';

    if($this->_oModel->destroy($this->_iId) === true) {
      Helper::log('gallery_album', $this->_aRequest['action'], (int)$this->_aRequest['id']);
      return Helper::successMessage(LANG_SUCCESS_DESTROY, $sRedirect);
    }
    else {
      return Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
      unset($this->_iId);
    }
  }

  # Show gallery album form template
  protected final function _showFormTemplate($bUpdate = true) {
    $oSmarty = new Smarty();
    $oSmarty->assign('USER_RIGHT', USER_RIGHT);

    if($bUpdate == true) {
      $this->_aData = $this->_oModel->getData($this->_iId, true);
      $oSmarty->assign('title', $this->_aData['title']);
      $oSmarty->assign('description', $this->_aData['description']);

      $oSmarty->assign('_action_url_', '/Gallery/'	.$this->_iId. '/update');
      $oSmarty->assign('_formdata_', 'update_gallery');
      $oSmarty->assign('id', $this->_iId);

      # Language
      $oSmarty->assign('lang_destroy_entry', LANG_GLOBAL_DESTROY_ENTRY);
      $oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
      $oSmarty->assign('lang_reset', LANG_GLOBAL_RESET);
      $oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);

      $this->_setTitle(Helper::removeSlahes($this->_aData['title']));
    }
    else {
      $sTitle = isset($this->_aRequest['title']) ?
              $this->_aRequest['title'] :
              '';

      $sDescription = isset($this->_aRequest['description']) ?
              $this->_aRequest['description'] :
              '';

      $oSmarty->assign('_action_url_', '/Gallery/create');
      $oSmarty->assign('_formdata_', 'create_gallery');
      $oSmarty->assign('title', $sTitle);
      $oSmarty->assign('description', $sDescription);
      $oSmarty->assign('id', '');

      # Language
      $oSmarty->assign('lang_headline', LANG_GALLERY_ALBUM_CREATE_TITLE);
      $oSmarty->assign('lang_submit', LANG_GALLERY_ALBUM_CREATE_TITLE);
    }

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $oSmarty->assign('error_' . $sField, $sMessage);
    }

    $oSmarty->assign('lang_description', LANG_GLOBAL_DESCRIPTION);
    $oSmarty->assign('lang_title', LANG_GLOBAL_TITLE);

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
    $oSmarty->template_dir = Helper::getTemplateDir('galleries/_form_album');
    return $oSmarty->fetch('galleries/_form_album.tpl');
  }

  # Create gallery file
  public final function createFile() {
    if (USER_RIGHT < 3)
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);

    else {
      if (isset($this->_aRequest['create_file']))
        # TODO: Kick out damn path; log is in model...
        return $this->_oModel->createFile();
      else
        return $this->_showFormFileTemplate(false);
    }
  }

  public final function updateFile() {
    if( USER_RIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);

    else {
      if( isset($this->_aRequest['update_file']) ) {
        if( $this->_oModel->updateFile($this->_iId) === true)
          return Helper::successMessage(LANG_SUCCESS_UPDATE, '/Gallery');
        else
          return Helper::errorMessage(LANG_ERROR_GLOBAL, '/Gallery');
			}
      else
        return $this->_showFormFileTemplate(true);
    }
  }

  public final function destroyFile() {
    if( USER_RIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);

    else {
      if($this->_oModel->destroyFile($this->_iId) === true) {
        return Helper::successMessage(LANG_SUCCESS_DESTROY, '/Gallery');
        unset($this->_iId);
      }
			else
				return Helper::errorMessage(LANG_ERROR_GLOBAL_FILE_COULD_NOT_BE_DESTROYED, '/Gallery');
    }
  }

  protected final function _showFormFileTemplate($bUpdate = false) {
    $oSmarty = new Smarty();
    $oSmarty->assign('USER_RIGHT', USER_RIGHT);
    $oSmarty->assign('id', $this->_iId);

    if($bUpdate === true) {
      $oSmarty->assign('_action_url_', '/Gallery/'	.$this->_iId. '/updatefile');
      $oSmarty->assign('_formdata_', 'update_file');
      $oSmarty->assign('album_id', (int)$this->_aRequest['album_id']);
      $oSmarty->assign('description', Model_Gallery::getFileDescription($this->_iId));

      # Language
      $oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY);
      $oSmarty->assign('lang_headline', LANG_GALLERY_FILE_UPDATE_TITLE);
      $oSmarty->assign('lang_reset', LANG_GLOBAL_RESET);
    }
    else {
      # See helper/Image.helper.php for details!
      $sDefault = isset($this->_aRequest['cut']) ?
              Helper::formatInput($this->_aRequest['cut']) :
              'c'; # r = resize, c = cut

      $oSmarty->assign('_action_url_', '/Gallery/'	.$this->_iId.	'/upload/' .session_id());
      $oSmarty->assign('_formdata_', 'create_file');
      $oSmarty->assign('default', $sDefault);
      $oSmarty->assign('description', '');

      # Language
      $oSmarty->assign('lang_create_file_cut', LANG_GALLERY_FILE_CREATE_LABEL_CUT);
      $oSmarty->assign('lang_create_file_resize', LANG_GALLERY_FILE_CREATE_LABEL_RESIZE);
      $oSmarty->assign('lang_cut', LANG_GLOBAL_CUT);
      $oSmarty->assign('lang_file_choose', LANG_GALLERY_FILE_CREATE_LABEL_CHOOSE);
      $oSmarty->assign('lang_headline', LANG_GALLERY_FILE_CREATE_TITLE);
      $oSmarty->assign('lang_same_filetype', LANG_GALLERY_FILE_CREATE_INFO_SAME_FILETYPE);
    }

    # Language
    $oSmarty->assign('lang_description', LANG_GLOBAL_DESCRIPTION);

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
    $oSmarty->template_dir = Helper::getTemplateDir('galleries/_form_file');
    return $oSmarty->fetch('galleries/_form_file.tpl');
  }
}