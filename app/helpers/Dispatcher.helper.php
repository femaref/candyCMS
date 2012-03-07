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
use Smarty;

class Dispatcher {

	/**
	 * Saves the object.
	 *
	 * @var object
	 * @access public
	 *
	 */
  public $oController;

	/**
	 * Initialize the controller by adding input params, set default id and start template engine.
	 *
	 * @access public
	 * @param array $aRequest alias for the combination of $_GET and $_POST
	 * @param array $aSession alias for $_SESSION
	 * @param array $aFile alias for $_FILE
	 * @param array $aCookie alias for $_COOKIE
	 *
	 */
	public function __construct($aRequest, $aSession, $aFile = '', $aCookie = '') {
		$this->_aRequest	= & $aRequest;
		$this->_aSession	= & $aSession;
		$this->_aFile			= & $aFile;
		$this->_aCookie		= & $aCookie;
	}

  /**
   * Get the controller.
   *
   * @access public
   * @return object created object
   *
   */
  public function getController() {
    require PATH_STANDARD . '/addons/controllers/Addon.controller.php';

    # Are addons for existing controllers available? If yes, use them
    if (file_exists(PATH_STANDARD . '/addons/controllers/' . (string) ucfirst($this->_aRequest['section']) .
                    '.controller.php') && (ALLOW_ADDONS === true || WEBSITE_MODE == 'development' || WEBSITE_MODE == 'test')) {
      require_once PATH_STANDARD . '/addons/controllers/' . (string) ucfirst($this->_aRequest['section']) .
              '.controller.php';

      $sClassName = '\CandyCMS\Addon\Controller\Addon_' . (string) ucfirst($this->_aRequest['section']);
      $this->oController = new $sClassName($this->_aRequest, $this->_aSession, $this->_aFile);
    }

    # There are no addons, so we use the default controllers
    elseif (file_exists(PATH_STANDARD . '/app/controllers/' . (string) ucfirst($this->_aRequest['section']) . '.controller.php')) {
      require_once PATH_STANDARD . '/app/controllers/' .
              (string) ucfirst($this->_aRequest['section']) . '.controller.php';

      $sClassName = '\CandyCMS\Controller\\' . (string) ucfirst($this->_aRequest['section']);
      $this->oController = new $sClassName($this->_aRequest, $this->_aSession, $this->_aFile);
    }

    # Some files are missing. Quit work!
    else
      throw new AdvancedException('Controller not found:' . 'app/controllers/' .
              (string) ucfirst($this->_aRequest['section']) . '.controller.php');

    $this->oController->__init();
    return $this->oController;
  }

  /**
   * Handle the pre-defined sections.
   *
   * @access public
   *
   */
  public function getAction() {
		$sAction = isset($this->_aRequest['action']) ? strtolower((string) $this->_aRequest['action']) : 'show';

		if ((string) strtolower($this->_aRequest['section']) !== 'static') {

			switch ($sAction) {

				case 'create':

					$this->oController->setContent($this->oController->create('create_' . strtolower($this->_aRequest['section'])));
					$this->oController->setDescription(I18n::get(strtolower($this->_aRequest['section']) . '.title.create'));
					$this->oController->setKeywords($this->oController->getKeywords());
					$this->oController->setTitle(I18n::get(strtolower($this->_aRequest['section']) . '.title.create'));

					break;

				case 'destroy':

					$this->oController->setContent($this->oController->destroy());
					$this->oController->setDescription(I18n::get(strtolower($this->_aRequest['section']) . '.title.destroy'));
					$this->oController->setKeywords($this->oController->getKeywords());
					$this->oController->setTitle(I18n::get(strtolower($this->_aRequest['section']) . '.title.destroy'));

					break;

				default:
				case 'show':

					$this->oController->setContent($this->oController->show());
					$this->oController->setDescription($this->oController->getDescription());
					$this->oController->setKeywords($this->oController->getKeywords());
					$this->oController->setTitle($this->oController->getTitle());

					break;

				case 'update':

					$this->oController->setContent($this->oController->update('update_' . strtolower($this->_aRequest['section'])));
					$this->oController->setDescription(
									str_replace('%p',
													$this->oController->getTitle(),
													I18n::get(strtolower($this->_aRequest['section']) . '.title.update')));
					$this->oController->setKeywords($this->oController->getKeywords());
					$this->oController->setTitle(
									str_replace('%p',
													$this->oController->getTitle(),
													I18n::get(strtolower($this->_aRequest['section']) . '.title.update')));

					break;

				case 'xml':

					$this->oController->setContent($this->oController->showXML());
					$this->oController->setDescription($this->oController->getDescription());
					$this->oController->setKeywords($this->oController->getKeywords());
					$this->oController->setTitle($this->oController->getTitle());

					break;
			}
		}
		else {

		}

    /*switch (strtolower((string) $this->_aRequest['section'])) {


      case 'gallery':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->oController->create('create_gallery'));
          parent::_setDescription(I18n::get('gallery.albums.title.create'));
          parent::_setTitle(I18n::get('gallery.albums.title.create'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'createfile') {
          parent::_setContent($this->oController->createFile());
          parent::_setDescription(I18n::get('gallery.files.title.create'));
          parent::_setTitle(I18n::get('gallery.files.title.update'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'update') {
          parent::_setContent($this->oController->update('update_gallery'));
          parent::_setDescription($this->oController->getDescription(str_replace('%p', $this->oController->getTitle(),
									I18n::get('gallery.albums.title.update'))));
          parent::_setTitle(str_replace('%p', $this->oController->getTitle(),
									I18n::get('gallery.albums.title.update')));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'updatefile') {
          parent::_setContent($this->oController->updateFile());
          parent::_setDescription(I18n::get('gallery.files.title.update'));
          parent::_setTitle(I18n::get('gallery.files.title.update'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->oController->destroy());
          parent::_setDescription(I18n::get('gallery.albums.title.destroy'));
          parent::_setTitle(I18n::get('gallery.albums.title.destroy'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroyfile') {
          parent::_setContent($this->oController->destroyFile());
          parent::_setDescription(I18n::get('gallery.files.title.destroy'));
          parent::_setTitle(I18n::get('gallery.files.title.destroy'));
        }
        else {
          parent::_setContent($this->oController->show());
          parent::_setDescription($this->oController->getDescription());
          parent::_setTitle($this->oController->getTitle());
        }

        break;


      case 'mail':

        parent::_setContent($this->oController->create());
        parent::_setDescription($this->oController->getDescription());
        parent::_setTitle($this->oController->getTitle());

        break;

      case 'media':

        else {
          parent::_setContent($this->oController->show());
          parent::_setDescription(I18n::get('global.manager.media'));
          parent::_setTitle(I18n::get('global.manager.media'));
        }

        break;

      case 'newsletter':

        parent::_setContent($this->oController->createSubscription());
        parent::_setDescription(I18n::get('newsletter.title.subscribe'));
        parent::_setTitle(I18n::get('newsletter.title.subscribe'));

        break;

      case 'session':

        if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->oController->create());
          parent::_setDescription(I18n::get('global.login'));
          parent::_setTitle(I18n::get('global.login'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'password') {
					parent::_setContent($this->oController->resendPassword());
					parent::_setDescription($this->oController->getDescription());
					parent::_setTitle($this->oController->getTitle());
				}
				elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'verification') {
					parent::_setContent($this->oController->resendVerification());
					parent::_setDescription($this->oController->getDescription());
					parent::_setTitle($this->oController->getTitle());
				}
        else {
          parent::_setContent($this->oController->destroy());
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
          parent::_setContent($this->oController->update());
          parent::_setDescription(I18n::get('user.title.update'));
          parent::_setTitle(I18n::get('user.title.update'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'create') {
          parent::_setContent($this->oController->create());
          parent::_setDescription(I18n::get('global.registration'));
          parent::_setTitle(I18n::get('global.registration'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'destroy') {
          parent::_setContent($this->oController->destroy());
          parent::_setDescription(I18n::get('user.title.destroy'));
          parent::_setTitle(I18n::get('user.title.destroy'));
        }
        elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'verification') {
          parent::_setContent($this->oController->verifyEmail());
          parent::_setDescription(I18n::get('global.email.verification'));
          parent::_setTitle(I18n::get('global.email.verification'));
        }
				elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'token') {
					parent::_setContent($this->oController->getToken());
					parent::_setDescription(I18n::get('global.api_token'));
					parent::_setTitle(I18n::get('global.api_token'));
				}
				elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'password') {
					parent::_setContent($this->oController->updatePassword());
					parent::_setDescription(I18n::get('user.title.password'));
					parent::_setTitle(I18n::get('user.title.password'));
				}
				elseif (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'avatar') {
					parent::_setContent($this->oController->updateAvatar());
					parent::_setDescription(I18n::get('user.title.avatar'));
					parent::_setTitle(I18n::get('user.title.avatar'));
				}
        else {
          parent::_setContent($this->oController->show());
          parent::_setDescription($this->oController->getDescription());
          parent::_setTitle($this->oController->getTitle());
        }

        break;
    }*/
  }
}