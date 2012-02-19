<?php

/**
 * Route the application to the given section.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Helper;

use CandyCMS\Addon\Controller\Addon as Addon;
use CandyCMS\Controller\Main as Main;
use Smarty;

class Section extends Main {

	/**
	 * Saves the object.
	 *
	 * @var object
	 * @access protected
	 *
	 */
  protected $_oObject;

  /**
   * Get the controller.
   *
   * @access public
   * @return object created object
   *
   */
  private function _getController() {
    require PATH_STANDARD . '/addons/controllers/Addon.controller.php';

    # Are addons for existing controllers avaiable? If yes, use them
    if (file_exists(PATH_STANDARD . '/addons/controllers/' . (string) ucfirst($this->_aRequest['section']) .
                    '.controller.php') && (ALLOW_ADDONS === true || WEBSITE_MODE == 'development' || WEBSITE_MODE == 'test')) {
      require_once PATH_STANDARD . '/addons/controllers/' . (string) ucfirst($this->_aRequest['section']) .
              '.controller.php';

      $sClassName = '\CandyCMS\Addon\Controller\Addon_' . (string) ucfirst($this->_aRequest['section']);
      $this->_oObject = new $sClassName($this->_aRequest, $this->_aSession, $this->_aFile);
    }

    # There are no addons, so we use the default controllers
    elseif (file_exists(PATH_STANDARD . '/app/controllers/' . (string) ucfirst($this->_aRequest['section']) . '.controller.php')) {
      require_once PATH_STANDARD . '/app/controllers/' .
              (string) ucfirst($this->_aRequest['section']) . '.controller.php';

      $sClassName = '\CandyCMS\Controller\\' . (string) ucfirst($this->_aRequest['section']);
      $this->_oObject = new $sClassName($this->_aRequest, $this->_aSession, $this->_aFile);
    }

    # Some files are missing. Quit work!
    else
      throw new AdvancedException('Module not found:' . 'app/controllers/' .
              (string) ucfirst($this->_aRequest['section']) . '.controller.php');

    $this->_oObject->__init();
    return $this->_oObject;
  }

  /**
   * Handle the pre-defined sections.
   *
   * @access public
   *
   */
  public function getSection() {
    if (!isset($this->_aRequest['section']) || empty($this->_aRequest['section']))
      $this->_aRequest['section'] = 'Error';

    if ((string) strtolower($this->_aRequest['section']) !== 'static')
      $this->_oObject = & $this->_getController();

    switch (strtolower((string) $this->_aRequest['section'])) {

      case 'blog':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create('create_blog'));
          parent::_setDescription(I18n::get('blog.title.create'));
          parent::_setTitle(I18n::get('blog.title.create'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update('update_blog'));
          parent::_setDescription(str_replace('%p', $this->_oObject->getTitle(), I18n::get('blog.title.update')));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(), I18n::get('blog.title.update')));
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

      case 'calendar':

				if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
					parent::_setContent($this->_oObject->create('create_calendar'));
					parent::_setDescription(I18n::get('calendar.title.create'));
					parent::_setTitle(I18n::get('calendar.title.create'));
				}
				elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
					parent::_setContent($this->_oObject->update('update_calendar'));
					parent::_setDescription(I18n::get('calendar.title.update'));
					parent::_setTitle(I18n::get('calendar.title.update'));
				}
				elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
					parent::_setContent($this->_oObject->destroy());
					parent::_setDescription(I18n::get('global.calendar.destroy'));
					parent::_setTitle(I18n::get('global.calendar.destroy'));
				}
				else {
					parent::_setContent($this->_oObject->show());
					parent::_setDescription(I18n::get('global.calendar'));
					parent::_setTitle(I18n::get('global.calendar'));
				}

				break;

			case 'comment':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(I18n::get('comment.title.destroy'));
          parent::_setTitle(I18n::get('comment.title.destroy'));
        }

        break;

      case 'content':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create('create_content'));
          parent::_setDescription(I18n::get('content.title.create'));
          parent::_setTitle(I18n::get('content.title.create'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update('update_content'));
          parent::_setDescription(str_replace('%p', $this->_oObject->getTitle(), I18n::get('content.title.destroy')));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(), I18n::get('content.title.destroy')));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(I18n::get('global.content.destroy'));
          parent::_setTitle(I18n::get('global.content.destroy'));
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
          parent::_setDescription(I18n::get('download.title.create'));
          parent::_setTitle(I18n::get('download.title.create'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update('update_download'));
          parent::_setDescription(I18n::get('download.title.update'));
          parent::_setTitle(I18n::get('download.title.update'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(I18n::get('global.download.destroy'));
          parent::_setTitle(I18n::get('global.download.destroy'));
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription(I18n::get('global.download'));
          parent::_setTitle(I18n::get('global.download'));
        }

        break;

      case 'error':

        if (isset($this->_aRequest['id']) && $this->_aRequest['id'] == '404') {
          parent::_setContent($this->_oObject->show404());
          parent::_setDescription(I18n::get('error.404.info'));
          parent::_setTitle(I18n::get('error.404.title'));
        }

        break;

      case 'gallery':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create('create_gallery'));
          parent::_setDescription(I18n::get('gallery.albums.title.create'));
          parent::_setTitle(I18n::get('gallery.albums.title.create'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'createfile') {
          parent::_setContent($this->_oObject->createFile());
          parent::_setDescription(I18n::get('gallery.files.title.create'));
          parent::_setTitle(I18n::get('gallery.files.title.update'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update('update_gallery'));
          parent::_setDescription($this->_oObject->getDescription(str_replace('%p', $this->_oObject->getTitle(),
									I18n::get('gallery.albums.title.update'))));
          parent::_setTitle(str_replace('%p', $this->_oObject->getTitle(),
									I18n::get('gallery.albums.title.update')));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'updatefile') {
          parent::_setContent($this->_oObject->updateFile());
          parent::_setDescription(I18n::get('gallery.files.title.update'));
          parent::_setTitle(I18n::get('gallery.files.title.update'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(I18n::get('gallery.albums.title.destroy'));
          parent::_setTitle(I18n::get('gallery.albums.title.destroy'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroyfile') {
          parent::_setContent($this->_oObject->destroyFile());
          parent::_setDescription(I18n::get('gallery.files.title.destroy'));
          parent::_setTitle(I18n::get('gallery.files.title.destroy'));
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription($this->_oObject->getDescription());
          parent::_setTitle($this->_oObject->getTitle());
        }

        break;

      case 'log':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
					parent::_setContent($this->_oObject->destroy(4));
					parent::_setDescription(I18n::get('global.logs'));
					parent::_setTitle(I18n::get('global.logs'));
				}
				else {
					parent::_setContent($this->_oObject->show());
					parent::_setDescription(I18n::get('global.logs'));
					parent::_setTitle(I18n::get('global.logs'));
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
          parent::_setDescription(I18n::get('media.title.create'));
          parent::_setTitle(I18n::get('media.title.create'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(I18n::get('media.title.destroy'));
          parent::_setTitle(I18n::get('media.title.destroy'));
        }
        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription(I18n::get('global.manager.media'));
          parent::_setTitle(I18n::get('global.manager.media'));
        }

        break;

      case 'newsletter':

        parent::_setContent($this->_oObject->createSubscription());
        parent::_setDescription(I18n::get('newsletter.title.subscribe'));
        parent::_setTitle(I18n::get('newsletter.title.subscribe'));

        break;

      case 'rss':

        parent::_setContent($this->_oObject->show());

        break;

      case 'search':

        parent::_setContent($this->_oObject->show());
        parent::_setDescription($this->_oObject->getDescription());
        parent::_setTitle($this->_oObject->getTitle());

        break;

      case 'sitemap':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'xml')
          parent::_setContent($this->_oObject->showXML());

        else {
          parent::_setContent($this->_oObject->show());
          parent::_setDescription(I18n::get('global.sitemap'));
          parent::_setTitle(I18n::get('global.sitemap'));
        }

        break;

      case 'session':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create());
          parent::_setDescription(I18n::get('global.login'));
          parent::_setTitle(I18n::get('global.login'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'password') {
					parent::_setContent($this->_oObject->resendPassword());
					parent::_setDescription($this->_oObject->getDescription());
					parent::_setTitle($this->_oObject->getTitle());
				}
				elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'verification') {
					parent::_setContent($this->_oObject->resendVerification());
					parent::_setDescription($this->_oObject->getDescription());
					parent::_setTitle($this->_oObject->getTitle());
				}
        else {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(I18n::get('global.logout'));
          parent::_setTitle(I18n::get('global.logout'));
        }

        break;

      case 'static':
        $sTpl = isset($this->_aRequest['subsection']) ?
                (string) $this->_aRequest['subsection'] :
                die(I18n::get('error.missing.template'));

        $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
        $this->oSmarty->setCacheLifetime(300);

        parent::_setContent($this->oSmarty->fetch(PATH_STATIC_TEMPLATES . '/' . $sTpl . '.tpl'));
        parent::_setDescription(ucfirst($sTpl));
        parent::_setTitle(ucfirst($sTpl));

        break;

      case 'user':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->_oObject->update());
          parent::_setDescription(I18n::get('user.title.update'));
          parent::_setTitle(I18n::get('user.title.update'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->_oObject->create());
          parent::_setDescription(I18n::get('global.registration'));
          parent::_setTitle(I18n::get('global.registration'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->_oObject->destroy());
          parent::_setDescription(I18n::get('user.title.destroy'));
          parent::_setTitle(I18n::get('user.title.destroy'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'verification') {
          parent::_setContent($this->_oObject->verifyEmail());
          parent::_setDescription(I18n::get('global.email.verification'));
          parent::_setTitle(I18n::get('global.email.verification'));
        }
				elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'token') {
					parent::_setContent($this->_oObject->getToken());
					parent::_setDescription(I18n::get('global.api_token'));
					parent::_setTitle(I18n::get('global.api_token'));
				}
				elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'password') {
					parent::_setContent($this->_oObject->updatePassword());
					parent::_setDescription(I18n::get('user.title.password'));
					parent::_setTitle(I18n::get('user.title.password'));
				}
				elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'avatar') {
					parent::_setContent($this->_oObject->updateAvatar());
					parent::_setDescription(I18n::get('user.title.avatar'));
					parent::_setTitle(I18n::get('user.title.avatar'));
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