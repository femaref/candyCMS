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

namespace CandyCMS\Core\Helpers;

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
   * @see vendor/candyCMS/core/controllers/Main.controller.php -> __autoload()
   *
   */
  public function getController() {
    $sController = ucfirst(strtolower((string) $this->_aRequest['controller']));

    try {
      # Are extensions for existing controllers available? If yes, use them.
      if (EXTENSION_CHECK && file_exists(PATH_STANDARD . '/app/extensions/controllers/' . $sController . '.controller.php')) {
        require_once PATH_STANDARD . '/app/extensions/controllers/' . $sController . '.controller.php';

        $sClassName = '\CandyCMS\Controller\Extension' . $sController;
        $this->oController = new $sClassName($this->_aRequest, $this->_aSession, $this->_aFile, $this->_aCookie);
      }

      # There are no extensions, so we use the default controllers
      elseif (file_exists(PATH_STANDARD . '/vendor/candyCMS/core/controllers/' . $sController . '.controller.php')) {
        require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/' . $sController . '.controller.php';

        $sClassName = '\CandyCMS\Core\controllers\\' . $sController;
        $this->oController = new $sClassName($this->_aRequest, $this->_aSession, $this->_aFile, $this->_aCookie);
      }

      else {
        # Bugfix: Fix exceptions when upload file is missing
        if(substr(strtolower($sController), 0, 6) !== 'upload')
          throw new AdvancedException('Controller not found:' . PATH_STANDARD .
                  '/vendor/candyCMS/core/controllers/' . $sController . '.controller.php');
      }
    }
    catch (AdvancedException $e) {
      AdvancedException::reportBoth($e->getMessage());
      Helper::redirectTo('/errors/404');
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
        $sController = strtolower($this->_aRequest['controller']);
        $this->oController->setContent($this->oController->update('update_' . $sController));
        $this->oController->setTitle(I18n::get($sController . '.title.update', $this->oController->getTitle()));
        break;

      case 'xml':

        $this->oController->setContent($this->oController->showXML());

        break;
    }
  }
}