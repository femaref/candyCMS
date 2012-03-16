<?php

/**
 * Route the application to the given controller and action.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Helper;

class Dispatcher {

	/**
	 * @var object
	 * @access public
	 *
	 */
  public $oController;

	/**
	 * Initialize the controller by adding input params.
	 *
	 * @access public
	 * @param array $aRequest alias for the combination of $_GET and $_POST
	 * @param array $aSession alias for $_SESSION
	 * @param array $aFile alias for $_FILE
	 * @param array $aCookie alias for $_COOKIE
	 *
	 */
	public function __construct(&$aRequest, &$aSession, &$aFile = '', &$aCookie = '') {
		$this->_aRequest	= & $aRequest;
		$this->_aSession	= & $aSession;
		$this->_aFile			= & $aFile;
		$this->_aCookie		= & $aCookie;
	}

  /**
   * Get the controller object.
   *
   * @access public
   * @return object $this->oController controller
   * @see app/controllers/Main.controller.php -> __autoload()
   *
   */
  public function getController() {
    $sController = & ucfirst(strtolower((string) $this->_aRequest['controller']));

    try {
      # Are addons for existing controllers available? If yes, use them.
      if (file_exists(PATH_STANDARD . '/addons/controllers/' . $sController . '.controller.php') &&
              (ALLOW_ADDONS === true || WEBSITE_MODE == 'development' || WEBSITE_MODE == 'test')) {
        require_once PATH_STANDARD . '/addons/controllers/' . $sController . '.controller.php';

        $sClassName = '\CandyCMS\Addon\Controller\Addon_' . $sController;
        $this->oController = new $sClassName($this->_aRequest, $this->_aSession, $this->_aFile, $this->_aCookie);
      }

      # There are no addons, so we use the default controllers
      elseif (file_exists(PATH_STANDARD . '/app/controllers/' . $sController . '.controller.php')) {
        require_once PATH_STANDARD . '/app/controllers/' . $sController . '.controller.php';

        $sClassName = '\CandyCMS\Controller\\' . $sController;
        $this->oController = new $sClassName($this->_aRequest, $this->_aSession, $this->_aFile, $this->_aCookie);
      }

      else {
        # Bugfix: Fix exceptions when upload file is missing
        if(substr(strtolower($sController), 0, 6) !== 'upload')
          throw new AdvancedException('Controller not found:' . PATH_STANDARD .
                  '/app/controllers/' . $sController . '.controller.php');
      }
    }
    catch (AdvancedException $e) {
      AdvancedException::reportBoth($e->getMessage());
      header('Status: 404 Not Found');
			header('HTTP/1.0 404 Not Found');
      Helper::redirectTo('/errors/404');
      exit('Controller not found.');
    }

    $this->oController->__init();
    return $this->oController;
  }

  /**
   * Handle the pre-defined actions.
   *
   * @access public
   *
   */
  public function getAction() {
    $sAction = isset($this->_aRequest['action']) ? strtolower((string) $this->_aRequest['action']) : 'show';

    switch ($sAction) {
      case 'create':

        $this->oController->setContent($this->oController->create('create_' . strtolower($this->_aRequest['controller'])));
        $this->oController->setTitle(I18n::get(strtolower($this->_aRequest['controller']) . '.title.create'));

        break;

      case 'destroy':

        $this->oController->setContent($this->oController->destroy());

        break;

      default:
      case 'show':

        $this->oController->setContent($this->oController->show());

        break;

      case 'update':

        $this->oController->setContent($this->oController->update('update_' . strtolower($this->_aRequest['controller'])));
        $this->oController->setTitle(str_replace('%p', $this->oController->getTitle(),
                I18n::get(strtolower($this->_aRequest['controller']) . '.title.update')));

        break;

      case 'xml':

        $this->oController->setContent($this->oController->showXML());

        break;
    }
  }
}