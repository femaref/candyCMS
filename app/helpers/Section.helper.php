<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Section extends Main {
  protected $_oObject;

  private function _getController() {
    if( file_exists('app/addon/' .(string)ucfirst($this->m_aRequest['section']). '.controller.php') && ALLOW_ADDONS == true) {
      new Addon($this->m_aRequest, $this->m_oSession, $this->m_aFile);
      $sClassName = (string)ucfirst($this->m_aRequest['section']). 'Extended';
      $this->_oObject = new $sClassName($this->m_aRequest, $this->m_oSession, $this->m_aFile);
    }
    elseif(file_exists('app/controllers/'	.(string)ucfirst($this->m_aRequest['section']).	'.controller.php')) {
      require_once('app/controllers/'	.(string)ucfirst($this->m_aRequest['section']).	'.controller.php');
      $this->_oObject = new $this->m_aRequest['section']($this->m_aRequest, $this->m_oSession, $this->m_aFile);
    }
    else
      throw new Exception('Module not found:' . 'app/controllers/'	.
              (string)ucfirst($this->m_aRequest['section']).	'.controller.php');

    $this->_oObject->__init();
    return $this->_oObject;
  }

  public function getSection() {
    if( !isset($this->m_aRequest['section']) || empty($this->m_aRequest['section']) )
      $this->m_aRequest['section'] = 'blog';

    $this->_oObject =& $this->_getController();

    switch( strtolower( (string)$this->m_aRequest['section']) ) {
      case 'blog':

        if( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'create' ) {
          parent::_setContent($this->_oObject->create('create_blog'));
          parent::_setTitle(LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'update' ) {
          parent::_setContent($this->_oObject->update('update_blog'));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(), LANG_BLOG_UPDATE));
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'destroy' )
          parent::_setContent($this->_oObject->destroy());

        else {
          parent::_setContent($this->_oObject->show());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      case 'comment':

        if( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'create' ) {
          parent::_setContent($this->_oObject->create('create_comment'));
          parent::_setTitle(LANG_COMMENT_CREATE);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'destroy' ) {
          parent::_setContent($this->_oObject->destroy());
          parent::_setTitle(LANG_COMMENT_DESTROY);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'show' )
          parent::_setContent($this->_oObject->show());

        break;

      case 'content':

        if( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'create' ) {
          parent::_setContent($this->_oObject->create('create_content'));
          parent::_setTitle(LANG_GLOBAL_CONTENTMANAGER.	': '	.LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'update' ) {
          parent::_setContent($this->_oObject->update('update_content'));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(), LANG_CONTENT_UPDATE));
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'destroy' )
          parent::_setContent($this->_oObject->destroy());

        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'overview' ) {
          parent::_setContent($this->_oObject->overview());
          parent::_setTitle(LANG_GLOBAL_CONTENTMANAGER);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      /* Should be excluded */
      case 'cron':

        require_once '../cronjob/cron.php';

        break;

      case 'gallery':

        if( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'create' ) {
          parent::_setContent($this->_oObject->create('create_gallery'));
          parent::_setTitle(LANG_GALLERY_CREATE_ALBUM);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'createfile' ) {
          parent::_setContent($this->_oObject->createFile());
          parent::_setTitle(LANG_GALLERY_CREATE_FILE);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'update' ) {
          parent::_setContent($this->_oObject->update('update_gallery'));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(), LANG_CONTENT_UPDATE));
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'updatefile' ) {
          parent::_setContent($this->_oObject->updateFile());
          parent::_setTitle(LANG_GALLERY_UPDATE_FILE);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'destroy' )
          parent::_setContent($this->_oObject->destroy());

        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'destroyfile' )
          parent::_setContent($this->_oObject->destroyFile());

        else {
          parent::_setContent($this->_oObject->show());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      case 'login':

        if( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'createsession' ) {
          parent::_setContent($this->_oObject->createSession());
          parent::_setTitle(LANG_GLOBAL_LOGIN);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'createinvite' ) {
          parent::_setContent($this->_oObject->createInvite());
          parent::_setTitle(LANG_LOGIN_INVITATION_HEADLINE);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'createnewpassword' ) {
          parent::_setContent($this->_oObject->createNewPassword());
          parent::_setTitle(LANG_LOGIN_PASSWORD_LOST);
        }
        else {
          parent::_setContent($this->_oObject->destroySession());
          parent::_setTitle(LANG_GLOBAL_LOGOUT);
        }

        break;

      case 'mail':

        parent::_setContent($this->_oObject->createMail());
        parent::_setTitle(LANG_GLOBAL_CONTACT);

        break;

      case 'media':

        if( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'create' ) {
          parent::_setContent($this->_oObject->create());
          parent::_setTitle(LANG_MEDIA_FILE_CREATE);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'destroy' ) {
          parent::_setContent($this->_oObject->destroy());
          parent::_setTitle(LANG_MEDIA_FILE_DESTROY);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setTitle(LANG_GLOBAL_FILEMANAGER);
        }

        break;

      case 'newsletter':

        if( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'create' ) {
          parent::_setContent($this->_oObject->create());
          parent::_setTitle(LANG_NEWSLETTER_CREATE);
        }
        else # CREATE and DESTROY functions
        {
          parent::_setContent($this->_oObject->handleNewsletter());
          parent::_setTitle(LANG_NEWSLETTER_CREATE_DESTROY);
        }

        break;

      case 'rss':

        parent::_setContent($this->_oObject->show());
        parent::_setTitle(LANG_GLOBAL_RSS);

        break;

      /* This function is used to GZIP contents via .htaccess */
      case 'static':

        $sTpl = isset($this->m_aRequest['action']) ?
                (string)$this->m_aRequest['action'] :
                LANG_ERROR_ACTION_NOT_SPECIFIED;

        $oSmarty = new Smarty();
        $oSmarty->template_dir = 'static/skins/'	.SKIN_TPL.	'/tpl/static';
        parent::_setContent($oSmarty->fetch($sTpl.	'.tpl'));
        parent::_setTitle(ucfirst($sTpl));

        break;

      case 'user':

        if( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'update' ) {
          parent::_setContent($this->_oObject->update());
          parent::_setTitle(LANG_USER_SETTINGS_HEADLINE);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'create' ) {
          parent::_setContent($this->_oObject->create());
          parent::_setTitle(LANG_GLOBAL_REGISTRATION);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'destroy' ) {
          parent::_setContent($this->_oObject->destroy());
          parent::_setTitle(LANG_GLOBAL_DESTROY);
        }
        elseif( isset($this->m_aRequest['action']) && $this->m_aRequest['action'] == 'overview' ) {
          parent::_setContent($this->_oObject->overview());
          parent::_setTitle(LANG_USER_OVERVIEW);
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setTitle(LANG_USER_DETAILS.	': '	.$this->_oObject->getTitle());
        }

        break;
    }
  }
}