<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Gallery.model.php';
require_once 'app/helpers/Page.helper.php';
require_once 'app/helpers/Upload.helper.php';
require_once 'app/helpers/Image.helper.php';

class Gallery extends Main {
  public function __init() {
    $this->_oModel = new Model_Gallery($this->_aRequest, $this->_aSession);
  }

  public final function show() {
    # Language
    $this->_oSmarty->assign('lang_no_files_uploaded', LANG_ERROR_GALLERY_NO_FILES_UPLOADED);

    # Specific gallery
    if( !empty($this->_iId) ) {
      # collect data array
      $sAlbumName	= Model_Gallery::getAlbumName($this->_iId);

      $this->_oSmarty->assign('files', $this->_oModel->getThumbs($this->_iId, LIMIT_ALBUM_IMAGES));
      $this->_oSmarty->assign('file_no', $this->_oModel->_iEntries);
      $this->_oSmarty->assign('gallery_name', $sAlbumName);
      $this->_oSmarty->assign('gallery_description', Model_Gallery::getAlbumDescription($this->_iId));

      # System variables
      $this->_oSmarty->assign('_album_pages_', $this->_oModel->oPage->showPages('gallery/'	.$this->_iId));

      # Language
      $this->_oSmarty->assign('lang_create_entry_headline', LANG_GALLERY_FILE_CREATE_TITLE);

      $this->_setTitle(Helper::removeSlahes(LANG_GLOBAL_GALLERY.	': '	. $sAlbumName));

      $this->_oSmarty->template_dir = Helper::getTemplateDir('galleries/showFiles');
      return $this->_oSmarty->fetch('galleries/showFiles.tpl');
    }
    # Overview
    else {
      $this->_setTitle(Helper::removeSlahes(LANG_GLOBAL_GALLERY));
      $this->_oSmarty->assign('albums', $this->_oModel->getData());

      # Language
      $this->_oSmarty->assign('lang_create_entry_headline', LANG_GALLERY_ALBUM_CREATE_TITLE);
      $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_GALLERY);

      $this->_oSmarty->template_dir = Helper::getTemplateDir('galleries/showAlbums');
      return $this->_oSmarty->fetch('galleries/showAlbums.tpl');
    }
  }

  # Create gallery album
  protected final function _create() {
    if (!isset($this->_aRequest['title']) || empty($this->_aRequest['title']))
      $this->_aError['title'] = LANG_ERROR_FORM_MISSING_TITLE;

    if (isset($this->_aError))
      return $this->_showFormTemplate(false);

    else {
			$sRedirect = '/gallery';

      if ($this->_oModel->create() === true) {
        Log::insert($this->_aRequest['section'], $this->_aRequest['action'],
												Helper::getLastEntry('gallery_albums'));

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
			$sRedirect = '/gallery/' . (int) $this->_aRequest['id'];

      if( $this->_oModel->update((int)$this->_aRequest['id']) === true) {
        Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_aRequest['id']);
        return Helper::successMessage(LANG_SUCCESS_UPDATE, $sRedirect);
      }

      else
        return Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
    }
  }

  # Destroy gallery album
  protected function _destroy() {
		$sRedirect = '/gallery';

    if($this->_oModel->destroy($this->_iId) === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], $this->_iId);
      return Helper::successMessage(LANG_SUCCESS_DESTROY, $sRedirect);
    }

    else {
      return Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
      unset($this->_iId);
    }
  }

  # Show gallery album form template
  protected final function _showFormTemplate($bUpdate = true) {
    if($bUpdate == true) {
      $this->_aData = $this->_oModel->getData($this->_iId, true);
      $this->_oSmarty->assign('title', $this->_aData['title']);
      $this->_oSmarty->assign('description', $this->_aData['description']);

      $this->_oSmarty->assign('_action_url_', '/gallery/'	.$this->_iId. '/update');
      $this->_oSmarty->assign('_formdata_', 'update_gallery');

      # Language
      $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
      $this->_oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);

      $this->_setTitle(Helper::removeSlahes($this->_aData['title']));
    }
    else {
      $sTitle = isset($this->_aRequest['title']) ?
              $this->_aRequest['title'] :
              '';

      $sDescription = isset($this->_aRequest['description']) ?
              $this->_aRequest['description'] :
              '';

      $this->_oSmarty->assign('_action_url_', '/gallery/create');
      $this->_oSmarty->assign('_formdata_', 'create_gallery');
      $this->_oSmarty->assign('title', $sTitle);
      $this->_oSmarty->assign('description', $sDescription);
      $this->_oSmarty->assign('_request_id_', '');

      # Language
      $this->_oSmarty->assign('lang_headline', LANG_GALLERY_ALBUM_CREATE_TITLE);
      $this->_oSmarty->assign('lang_submit', LANG_GALLERY_ALBUM_CREATE_TITLE);
    }

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->_oSmarty->assign('error_' . $sField, $sMessage);
    }

    $this->_oSmarty->template_dir = Helper::getTemplateDir('galleries/_form_album');
    return $this->_oSmarty->fetch('galleries/_form_album.tpl');
  }

  # Create gallery file
  public final function createFile() {
		if (USER_RIGHT < 3)
			return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);

		else {
			if (isset($this->_aRequest['create_file'])) {
				if ($this->_oModel->createFile() === true) {
					# Log uploaded image. Request ID = album id
					Log::insert($this->_aRequest['section'], 'createfile', (int) $this->_aRequest['id']);
					return $this->_oModel->getFilePath();
				}
			}
			else
				return $this->_showFormFileTemplate(false);
		}
	}

  public final function updateFile() {
    if( USER_RIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);

    else {
      if( isset($this->_aRequest['update_file']) ) {
        if( $this->_oModel->updateFile($this->_iId) === true) {
          Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId);
          return Helper::successMessage(LANG_SUCCESS_UPDATE, '/gallery');
        }
        else
          return Helper::errorMessage(LANG_ERROR_GLOBAL, '/gallery');
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
        Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId);
        return Helper::successMessage(LANG_SUCCESS_DESTROY, '/gallery');
        unset($this->_iId);
      }
			else
				return Helper::errorMessage(LANG_ERROR_GLOBAL_FILE_COULD_NOT_BE_DESTROYED, '/gallery');
    }
  }

  protected final function _showFormFileTemplate($bUpdate = false) {
    if($bUpdate === true) {
      $this->_oSmarty->assign('_action_url_', '/gallery/'	.$this->_iId. '/updatefile');
      $this->_oSmarty->assign('_formdata_', 'update_file');
      $this->_oSmarty->assign('description', Model_Gallery::getFileDescription($this->_iId));

      # Language
      $this->_oSmarty->assign('lang_headline', LANG_GALLERY_FILE_UPDATE_TITLE);
    }
    else {
      # See helper/Image.helper.php for details!
      $sDefault = isset($this->_aRequest['cut']) ?
              Helper::formatInput($this->_aRequest['cut']) :
              'c'; # r = resize, c = cut

      $this->_oSmarty->assign('_action_url_', '/gallery/'	.$this->_iId.	'/upload/' .session_id());
      $this->_oSmarty->assign('_formdata_', 'create_file');
      $this->_oSmarty->assign('default', $sDefault);
      $this->_oSmarty->assign('description', '');

      # Language
      $this->_oSmarty->assign('lang_create_file_cut', LANG_GALLERY_FILE_CREATE_LABEL_CUT);
      $this->_oSmarty->assign('lang_create_file_resize', LANG_GALLERY_FILE_CREATE_LABEL_RESIZE);
      $this->_oSmarty->assign('lang_file_choose', LANG_GALLERY_FILE_CREATE_LABEL_CHOOSE);
      $this->_oSmarty->assign('lang_headline', LANG_GALLERY_FILE_CREATE_TITLE);
      $this->_oSmarty->assign('lang_same_filetype', LANG_GALLERY_FILE_CREATE_INFO_SAME_FILETYPE);
    }

    $this->_oSmarty->template_dir = Helper::getTemplateDir('galleries/_form_file');
    return $this->_oSmarty->fetch('galleries/_form_file.tpl');
  }
}