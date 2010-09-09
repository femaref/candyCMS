<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/helpers/Addon.helper.php';

class Section extends Main {
  protected $_oObject;
  private function _getController() {
    if( file_exists('app/addon/' .(string)ucfirst($this->_aRequest['section']). '.controller.php') && ALLOW_ADDONS == true) {
      new Addon($this->_aRequest, $this->_aSession, $this->_aFile);
      $sClassName = 'Extension_' . (string) ucfirst($this->_aRequest['section']);
      $this->_oObject = new $sClassName($this->_aRequest, $this->_aSession, $this->_aFile);
    }
    elseif(file_exists('app/controllers/'	.(string)ucfirst($this->_aRequest['section']).	'.controller.php')) {
      require_once('app/controllers/'	.(string)ucfirst($this->_aRequest['section']).	'.controller.php');
      $this->_oObject = new $this->_aRequest['section']($this->_aRequest, $this->_aSession, $this->_aFile);
    }
    else
      throw new Exception('Module not found:' . 'app/controllers/'	.
              (string)ucfirst($this->_aRequest['section']).	'.controller.php');

    $this->_oObject->__init();
    return $this->_oObject;
  }

  public function getSection() {
    if (!isset($this->_aRequest['section']) || empty($this->_aRequest['section']))
      $this->_aRequest['section'] = 'blog';

    $this->_oObject = & $this->_getController();

    switch (strtolower((string) $this->_aRequest['section'])) {
      case 'blog':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create('create_blog'));
          parent::_setTitle(LANG_BLOG_TITLE_CREATE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update('update_blog'));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(), LANG_BLOG_TITLE_UPDATE));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy')
          parent::_setContent($this->_oObject->destroy());

        else {
          parent::_setContent($this->_oObject->show());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      case 'comment':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create('create_comment'));
          parent::_setTitle(LANG_COMMENT_TITLE_CREATE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setTitle(LANG_COMMENT_TITLE_DESTROY);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'show')
          parent::_setContent($this->_oObject->show());

        break;

      case 'content':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create('create_content'));
          parent::_setTitle(LANG_GLOBAL_CONTENTMANAGER . ': ' . LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update('update_content'));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(), LANG_CONTENT_TITLE_UPDATE));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setTitle($this->_oObject->getTitle());
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      case 'gallery':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create('create_gallery'));
          parent::_setTitle(LANG_GALLERY_ALBUM_CREATE_TITLE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'createfile') {
          parent::_setContent($this->_oObject->createFile());
          parent::_setTitle(LANG_GALLERY_FILE_CREATE_TITLE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update('update_gallery'));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(), LANG_GALLERY_ALBUM_UPDATE_TITLE));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'updatefile') {
          parent::_setContent($this->_oObject->updateFile());
          parent::_setTitle(LANG_GALLERY_FILE_UPDATE_TITLE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setTitle(LANG_GLOBAL_GALLERY);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroyfile') {
          parent::_setContent($this->_oObject->destroyFile());
          parent::_setTitle(LANG_GLOBAL_GALLERY);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      case 'mail':

        parent::_setContent($this->_oObject->create());
        parent::_setTitle(LANG_GLOBAL_CONTACT);

        break;

      case 'media':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create());
          parent::_setTitle(LANG_MEDIA_FILE_CREATE_TITLE);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setTitle(LANG_MEDIA_FILE_DESTROY_TITLE);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setTitle(LANG_GLOBAL_FILEMANAGER);
        }

        break;

      case 'newsletter':

        if( isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create' ) {
          parent::_setContent($this->_oObject->create());
          parent::_setTitle(LANG_NEWSLETTER_CREATE_TITLE);
        }
        else # CREATE and DESTROY functions
        {
          parent::_setContent($this->_oObject->handleNewsletter());
          parent::_setTitle(LANG_NEWSLETTER_HANDLE_TITLE);
        }

        break;

      case 'rss':

        parent::_setContent($this->_oObject->show());
        parent::_setTitle(LANG_GLOBAL_RSS);

        break;

      case 'search':

        if( isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create' ) {
          parent::_setContent($this->_oObject->create());
          parent::_setTitle(LANG_GLOBAL_LOGIN);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setTitle(LANG_GLOBAL_SEARCH);
        }

        break;

      case 'session':

        if( isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create' ) {
          parent::_setContent($this->_oObject->create());
          parent::_setTitle(LANG_GLOBAL_LOGIN);
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'resendpassword' ||
                $this->_aRequest['action'] == 'resendverification') {
          parent::_setContent($this->_oObject->createResendActions());
          parent::_setTitle($this->_oObject->getTitle());
        }
        else {
          parent::_setContent($this->_oObject->destroy());
          parent::_setTitle(LANG_GLOBAL_LOGOUT);
        }

        break;

      case 'static':

        $sTpl = isset($this->_aRequest['action']) ?
                (string)$this->_aRequest['action'] :
                LANG_ERROR_REQUEST_MISSING_ACTION;

        $oSmarty = new Smarty();
        $oSmarty->template_dir = '/skins/'	.PATH_TPL_STATIC.	'/tpl/static';
        parent::_setContent($oSmarty->fetch($sTpl.	'.tpl'));
        parent::_setTitle(ucfirst($sTpl));

        break;

      case 'user':

        if( isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update' ) {
          parent::_setContent($this->_oObject->update());
          parent::_setTitle(LANG_USER_UPDATE_TITLE);
        }
        elseif( isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create' ) {
          parent::_setContent($this->_oObject->create());
          parent::_setTitle(LANG_GLOBAL_REGISTRATION);
        }
        elseif( isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy' ) {
          parent::_setContent($this->_oObject->destroy());
          parent::_setTitle(LANG_GLOBAL_DESTROY);
        }
        elseif( isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'verification' ) {
          parent::_setContent($this->_oObject->verifyEmail());
          parent::_setTitle(LANG_GLOBAL_EMAIL_VERIFICATION);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;
    }
  }
}