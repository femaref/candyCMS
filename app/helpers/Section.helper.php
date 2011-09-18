<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

namespace CandyCMS\Helper;

use CandyCMS\Addon\Addon as Addon;

require_once 'addons/controllers/Addon.controller.php';

class Section extends \CandyCMS\Controller\Main {

  protected $_oObject;

  private function _getController() {
    # Are addons for existing controllers avaiable? If yes, use them
    if (file_exists('addons/controllers/' . (string) ucfirst($this->_aRequest['section']) . '.controller.php') && ALLOW_ADDONS === true) {
      require_once 'addons/controllers/' . (string) ucfirst($this->_aRequest['section']) . '.controller.php';
      $oAddon = new Addon($this->_aRequest, $this->_aSession, $this->_aFile);

      $sClassName = 'Addon_' . (string) ucfirst($this->_aRequest['section']);
      $this->_oObject = new $sClassName($this->_aRequest, $this->_aSession, $this->_aFile);
    }

    # There are no addons, so we use the default controllers
    elseif (file_exists('app/controllers/' . (string) ucfirst($this->_aRequest['section']) . '.controller.php')) {
      require_once('app/controllers/' . (string) ucfirst($this->_aRequest['section']) . '.controller.php');

      $sClassName = '\CandyCMS\Controller\\' . (string) ucfirst($this->_aRequest['section']);
      $this->_oObject = new $sClassName($this->_aRequest, $this->_aSession, $this->_aFile);
    }

    # Some files are missing. Quit work!
    else
      throw new Exception('Module not found:' . 'app/controllers/' .
              (string) ucfirst($this->_aRequest['section']) . '.controller.php');

    $this->_oObject->__init();
    return $this->_oObject;
  }

  # Handle the pre-defined sections
  public function getSection() {
    if (!isset($this->_aRequest['section']) || empty($this->_aRequest['section']))
      $this->_aRequest['section'] = '404';

    if ((string) strtolower($this->_aRequest['section']) !== 'static')
      $this->_oObject = & $this->_getController();

    switch (strtolower((string) $this->_aRequest['section'])) {

      case 'blog':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create('create_blog'));
          parent::_setDescription(LANG_BLOG_TITLE_CREATE);
          parent::_setTitle(LANG_BLOG_TITLE_CREATE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update('update_blog'));
          parent::_setDescription(str_replace('%p', $this->_oObject->getTitle(), LANG_BLOG_TITLE_UPDATE));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(), LANG_BLOG_TITLE_UPDATE));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy')
          parent::_setContent($this->_oObject->destroy());

        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription($this->_oObject->getDescription());
          parent::_setKeywords($this->_oObject->getKeywords());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      case 'comment':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(LANG_COMMENT_TITLE_DESTROY);
          parent::_setTitle(LANG_COMMENT_TITLE_DESTROY);
        }

        break;

      case 'content':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create('create_content'));
          parent::_setDescription(LANG_GLOBAL_CONTENTMANAGER . ': ' . LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
          parent::_setTitle(LANG_GLOBAL_CONTENTMANAGER . ': ' . LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update('update_content'));
          parent::_setDescription(str_replace('%p', $this->_oObject->getTitle(), LANG_CONTENT_TITLE_UPDATE));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(), LANG_CONTENT_TITLE_UPDATE));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription($this->_oObject->getDescription());
          parent::_setTitle($this->_oObject->getTitle());
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription($this->_oObject->getDescription());
          parent::_setKeywords($this->_oObject->getKeywords());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      case 'download':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create('create_download'));
          parent::_setDescription(LANG_DOWNLOAD_TITLE_CREATE);
          parent::_setTitle(LANG_DOWNLOAD_TITLE_CREATE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update('update_download'));
          parent::_setDescription(LANG_DOWNLOAD_TITLE_UPDATE);
          parent::_setTitle(LANG_DOWNLOAD_TITLE_UPDATE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(LANG_GLOBAL_DOWNLOAD);
          parent::_setTitle(LANG_GLOBAL_DOWNLOAD);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription(LANG_GLOBAL_DOWNLOAD);
          parent::_setTitle(LANG_GLOBAL_DOWNLOAD);
        }

        break;

      case 'error':

        if (isset($this->_aRequest['id']) && $this->_aRequest['id'] == '404') {
          parent::_setContent($this->_oObject->show404());
          parent::_setDescription(LANG_ERROR_GLOBAL_404_INFO);
          parent::_setTitle(LANG_ERROR_GLOBAL_404_TITLE);
        }

        break;

      case 'gallery':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create('create_gallery'));
          parent::_setDescription(LANG_GALLERY_ALBUM_CREATE_TITLE);
          parent::_setTitle(LANG_GALLERY_ALBUM_CREATE_TITLE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'createfile') {
          parent::_setContent($this->_oObject->createFile());
          parent::_setDescription(LANG_GALLERY_FILE_CREATE_TITLE);
          parent::_setTitle(LANG_GALLERY_FILE_CREATE_TITLE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update('update_gallery'));
          parent::_setDescription($this->_oObject->getDescription(str_replace('%p', $this->_oObject->getTitle(), LANG_GALLERY_ALBUM_UPDATE_TITLE)));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(), LANG_GALLERY_ALBUM_UPDATE_TITLE));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'updatefile') {
          parent::_setContent($this->_oObject->updateFile());
          parent::_setDescription(LANG_GALLERY_FILE_UPDATE_TITLE);
          parent::_setTitle(LANG_GALLERY_FILE_UPDATE_TITLE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(LANG_GLOBAL_GALLERY);
          parent::_setTitle(LANG_GLOBAL_GALLERY);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroyfile') {
          parent::_setContent($this->_oObject->destroyFile());
          parent::_setDescription(LANG_GLOBAL_GALLERY);
          parent::_setTitle(LANG_GLOBAL_GALLERY);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription($this->_oObject->getDescription());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      case 'log':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(LANG_GLOBAL_LOGS);
          parent::_setTitle(LANG_GLOBAL_LOGS);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription(LANG_GLOBAL_LOGS);
          parent::_setTitle(LANG_GLOBAL_LOGS);
        }

        break;

      case 'mail':

        parent::_setContent($this->_oObject->create());
        parent::_setDescription($this->_oObject->getDescription());
        parent::_setTitle($this->_oObject->getTitle());

        break;

      case 'media':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create());
          parent::_setDescription(LANG_MEDIA_FILE_CREATE_TITLE);
          parent::_setTitle(LANG_MEDIA_FILE_CREATE_TITLE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(LANG_MEDIA_FILE_DESTROY_TITLE);
          parent::_setTitle(LANG_MEDIA_FILE_DESTROY_TITLE);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription(LANG_GLOBAL_FILEMANAGER);
          parent::_setTitle(LANG_GLOBAL_FILEMANAGER);
        }

        break;

      case 'newsletter':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create());
          parent::_setTitle(LANG_NEWSLETTER_CREATE_TITLE);
        }
        else { # CREATE and DESTROY functions
          parent::_setContent($this->_oObject->handleNewsletter());
          parent::_setDescription($this->_oObject->getDescription());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      case 'rss':

        parent::_setContent($this->_oObject->show());

        break;

      case 'search':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create());
          parent::_setTitle(LANG_GLOBAL_LOGIN);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription($this->_oObject->getDescription());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      case 'sitemap':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'xml')
          parent::_setContent($this->_oObject->showXML());

        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription(LANG_GLOBAL_SITEMAP);
          parent::_setTitle(LANG_GLOBAL_SITEMAP);
        }

        break;

      case 'session':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create());
          parent::_setDescription($this->oI18n->get('global.login'));
          parent::_setTitle($this->oI18n->get('global.login'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'resendpassword' ||
                $this->_aRequest['action'] == 'resendverification') {
          parent::_setContent($this->_oObject->createResendActions());
          parent::_setDescription($this->_oObject->getDescription());
          parent::_setTitle($this->_oObject->getTitle());
        }
        else {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription($this->oI18n->get('global.logout'));
          parent::_setTitle($this->oI18n->get('global.logout'));
        }

        break;

      case 'static':

        $sTpl = isset($this->_aRequest['template']) ?
                (string) $this->_aRequest['template'] :
                LANG_ERROR_GLOBAL_NO_TEMPLATE;

        parent::_setContent($this->_oSmarty->fetch(PATH_STATIC_TEMPLATES . '/' . $sTpl . '.tpl'));
        parent::_setDescription(ucfirst($sTpl));
        parent::_setTitle(ucfirst($sTpl));

        break;

      case 'user':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update());
          parent::_setDescription(LANG_USER_UPDATE_TITLE);
          parent::_setTitle(LANG_USER_UPDATE_TITLE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create());
          parent::_setDescription(LANG_GLOBAL_REGISTRATION);
          parent::_setTitle(LANG_GLOBAL_REGISTRATION);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(LANG_GLOBAL_DESTROY);
          parent::_setTitle(LANG_GLOBAL_DESTROY);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'verification') {
          parent::_setContent($this->_oObject->verifyEmail());
          parent::_setDescription(LANG_GLOBAL_EMAIL_VERIFICATION);
          parent::_setTitle(LANG_GLOBAL_EMAIL_VERIFICATION);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription($this->_oObject->getDescription());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;
    }
  }
}