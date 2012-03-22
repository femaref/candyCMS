<?php

/**
 * Parent class for most other controllers and provides most language variables.
 *
 * @abstract
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Plugin\Bbcode as Bbcode;
use CandyCMS\Plugin\FacebookCMS as FacebookCMS;
use CandyCMS\Helper\SmartySingleton as SmartySingleton;
use MCAPI;

require_once PATH_STANDARD . '/app/helpers/Helper.helper.php';

abstract class Main {

	/**
	 * Alias for $_REQUEST
	 *
	 * @var array
	 * @access protected
	 */
	protected $_aRequest = array();

	/**
	 * Alias for $_SESSION
	 *
	 * @var array
	 * @access protected
	 */
	protected $_aSession = array();

	/**
	 * Alias for $_FILE
	 *
	 * @var array
	 * @access protected
	 */
	protected $_aFile;

	/**
	 * Alias for $_COOKIE
	 *
	 * @var array
	 * @access protected
	 */
	protected $_aCookie;

	/**
	 * ID to process.
	 *
	 * @var integer
	 * @access protected
	 */
	protected $_iId;

	/**
	 * Fetches all error messages in an array.
	 *
	 * @var array
	 * @access protected
	 */
	protected $_aError;

	/**
	 * The controller claimed model.
	 *
	 * @var object
	 * @access protected
	 */
	protected $_oModel;

	/**
	 * Returned data from models.
	 *
	 * @var array
	 * @access protected
	 */
	protected $_aData = array();

	/**
	 * Final HTML-Output.
	 *
	 * @var string
	 * @access private
	 */
	private $_sContent;

	/**
	 * Meta description.
	 *
	 * @var string
	 * @access private
	 */
	private $_sDescription;

	/**
	 * Meta keywords.
	 *
	 * @var string
	 * @access private
	 */
	private $_sKeywords;

	/**
	 * Page title.
	 *
	 * @var string
	 * @access private
	 */
	private $_sTitle;

  /**
   * Name of the templates folder.
   *
   * @var string
   * @access protected
   *
   */
  protected $_sTemplateFolder;

	/**
	 * Smarty object.
	 *
	 * @var object
	 * @access public
	 */
	public $oSmarty;

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
	public function __construct(&$aRequest, &$aSession, &$aFile = '', &$aCookie = '') {
		$this->_aRequest	= & $aRequest;
		$this->_aSession	= & $aSession;
		$this->_aFile			= & $aFile;
		$this->_aCookie		= & $aCookie;

    # Load config files if not already done (important for unit testing)
    if (!defined('WEBSITE_URL'))
      require PATH_STANDARD . '/config/Candy.inc.php';

		if (!defined('WEBSITE_LOCALE'))
			define('WEBSITE_LOCALE', 'en_US');

		$this->_iId = isset($this->_aRequest['id']) ? (int) $this->_aRequest['id'] : '';

    $this->_setSmarty();
	}

  /**
   * Destructor.
   *
   * @access public
   *
   */
  public function __destruct() {
  }

  /**
   * Dynamically load classes.
   *
   * @static
   * @param string $sClass name of class to load
   * @param boolean $bModel load a model file
   * @return string class name
   *
   */
  public static function __autoload($sClass, $bModel = false) {
    $sClass = (string) ucfirst(strtolower($sClass));

    if ($bModel === true) {
      if (file_exists(PATH_STANDARD . '/addons/models/' . $sClass . '.model.php')) {
        require_once PATH_STANDARD . '/addons/models/' . $sClass . '.model.php';
        return '\CandyCMS\Addon\Model\Addon_' . $sClass;
      }
      elseif (file_exists(PATH_STANDARD . '/app/models/' . $sClass . '.model.php')) {
        require_once PATH_STANDARD . '/app/models/' . $sClass . '.model.php';
        return '\CandyCMS\Model\\' . $sClass;
      }
    }
    else {
      if (file_exists(PATH_STANDARD . '/addons/controllers/' . $sClass . '.controller.php')) {
        require_once PATH_STANDARD . '/addons/controllers/' . $sClass . '.controller.php';
        return '\CandyCMS\Addon\Controller\Addon_' . $sClass;
      }
      else {
        require_once PATH_STANDARD . '/app/controllers/' . $sClass . '.controller.php';
        return '\CandyCMS\Controller\\' . $sClass;
      }
    }
  }

	/**
	 * Method to include the model files.
	 *
	 * @access public
	 *
	 */
  public function __init() {
    $sModel = $this->__autoload($this->_aRequest['controller'], true);

    if ($sModel)
      $this->_oModel = & new $sModel($this->_aRequest, $this->_aSession);
  }

	/**
	 * Set up smarty.
	 *
	 * @access proteced
	 * @return object $this->oSmarty
	 *
	 */
	protected function _setSmarty() {
		# Initialize smarty
		$this->oSmarty = SmartySingleton::getInstance();

    # Clear cache on development mode or when we force it via a request.
    if (isset($this->_aRequest['clearcache']) || WEBSITE_MODE == 'development' || WEBSITE_MODE == 'test') {
      $this->oSmarty->clearAllCache();
      $this->oSmarty->clearCompiledTemplate();
    }

    # Global variables
		$this->oSmarty->assign('_REQUEST', $this->_aRequest);
		$this->oSmarty->assign('_SESSION', $this->_aSession);

		return $this->oSmarty;
	}

	/**
	 * Set meta description.
	 *
	 * @access public
	 * @param string $sDescription description to be set.
	 *
	 */
	public function setDescription($sDescription = '') {
    if ($sDescription && !$this->_sDescription)
      $this->_sDescription = & $sDescription;
  }

	/**
	 * Give back the meta description.
	 *
	 * @access public
	 * @return string meta description
	 *
	 */
	public function getDescription() {
    if(!$this->_sDescription) {
      # Show default description if this is our landing page or we got no descrption.
      if ($this->_aRequest['controller'] == $_SESSION['routes']['/'])
        $this->setDescription(I18n::get('website.description'));

      elseif (!$this->_sDescription)
        $this->setDescription($this->getTitle());
    }

    return $this->_sDescription;
  }

	/**
	 * Set meta keywords.
	 *
	 * @access public
	 * @param string $sKeywords keywords to be set.
	 *
	 */
	public function setKeywords($sKeywords = '') {
    if ($sKeywords && !$this->_sKeywords)
      $this->_sKeywords = & $sKeywords;
	}

	/**
	 * Give back the meta keywords.
	 *
	 * @access public
	 * @return string meta keywords
	 *
	 */
	public function getKeywords() {
		return $this->_sKeywords ? $this->_sKeywords : I18n::get('website.keywords');
	}

	/**
	 * Set meta keywords.
	 *
	 * @access public
	 * @param string $sTitle title to be set.
	 *
	 */
	public function setTitle($sTitle = '') {
    if ($sTitle && !$this->_sTitle)
      $this->_sTitle = & $sTitle;
  }

	/**
	 * Give back the page title.
	 *
	 * @access public
	 * @return string page title
	 *
	 */
	public function getTitle() {
    if(!$this->_sTitle) {
      if ($this->_aRequest['controller'] == 'errors')
        $this->setTitle(I18n::get('error.' . $this->_aRequest['id'] . '.title'));

      else
        $this->setTitle($this->_sTitle ? $this->_sTitle :
                        I18n::get('global.' . strtolower(Helper::singleize($this->_aRequest['controller']))));
    }

    return $this->_sTitle;
  }

	/**
	 * Set the page content.
	 *
	 * @access public
	 * @param string $sContent HTML content
	 * @see app/helpers/Dispatcher.helper.php
	 *
	 */
	public function setContent($sContent) {
		$this->_sContent = & $sContent;
	}

	/**
	 *
	 * Give back the page content (HTML).
	 *
	 * @access public
	 * @return string $this->_sContent
	 */
	public function getContent() {
		return $this->_sContent;
	}

	/**
	 * Give back ID.
	 *
	 * @access public
	 * @return integer $this->_iId
	 *
	 */
	public function getId() {
		return $this->_iId;
	}

	/**
	 * Quick hack for displaying title without html tags.
	 *
   * @static
	 * @access protected
	 * @param string $sTitle title to modifiy
	 * @return string modified title
	 *
	 */
	protected static function _removeHighlight($sTitle) {
		$sTitle = str_replace('<mark>', '', $sTitle);
		$sTitle = str_replace('</mark>', '', $sTitle);
		return $sTitle;
	}

	/**
	 * Set error messages.
	 *
	 * @access protected
	 * @param string $sField field to be checked
	 * @param string $sMessage error to be displayed
   * @return object $this due to method chaining
	 *
	 */
	protected function _setError($sField, $sMessage = '') {
    if ($sField === 'file' || $sField === 'image') {
      if (!isset($this->_aFile[$sField]) || empty($this->_aFile[$sField]['name']))
          $this->_aError[$sField] = $sMessage ?
                $sMessage :
                I18n::get('error.form.missing.file');
    }
    else {
      if (!isset($this->_aRequest[$sField]) || empty($this->_aRequest[$sField]))
          $sError = I18n::get('error.form.missing.' . strtolower($sField)) ?
                I18n::get('error.form.missing.' . strtolower($sField)) :
                I18n::get('error.form.missing.standard');

      if ('email' == $sField && !Helper::checkEmailAddress($this->_aRequest['email']))
          $sError = $sError ? $sError : I18n::get('error.mail.format');

      if ($sError) $this->_aError[$sField] = !$sMessage ?
                $sError :
                $sMessage;
    }
    return $this;
  }

  /**
   * Show a entry.
   *
   * @access public
   * @return string HTML
   *
   */
  public function show() {
    $this->oSmarty->setCaching(SmartySingleton::CACHING_LIFETIME_SAVED);
    return $this->_show();
  }

  /**
   * Show a entry as XML.
   *
   * @access public
   * @return string HTML
   *
   */
  public function showXML() {
    $this->oSmarty->setCaching(SmartySingleton::CACHING_LIFETIME_SAVED);
    return $this->_showXML();
  }

	/**
	 * Create an action.
	 *
	 * Create entry or show form template if we have enough rights.
	 *
	 * @access public
	 * @param string $sInputName sent input name to verify action
	 * @param integer $iUserRole required user right
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 *
	 */
	public function create($sInputName, $iUserRole = 3) {
    $this->oSmarty->setCaching(false);

		if ($this->_aSession['user']['role'] < $iUserRole)
			return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

		else
			return isset($this->_aRequest[$sInputName]) ? $this->_create() : $this->_showFormTemplate();
	}

	/**
	 * Update entry or show form template if we have enough rights.
	 *
	 * @access public
	 * @param string $sInputName sent input name to verify action
	 * @param integer $iUserRole required user right
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 *
	 */
	public function update($sInputName, $iUserRole = 3) {
    $this->oSmarty->setCaching(false);

		if ($this->_aSession['user']['role'] < $iUserRole)
			return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

		else
			return isset($this->_aRequest[$sInputName]) ? $this->_update() : $this->_showFormTemplate();
	}

	/**
   * Delete entry if we have enough rights.
   *
   * @access public
   * @param integer $iUserRole required user right
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function destroy($iUserRole = 3) {
    $this->oSmarty->setCaching(false);

    return $this->_aSession['user']['role'] < $iUserRole ?
            Helper::errorMessage(I18n::get('error.missing.permission'), '/') :
            $this->_destroy();
  }

  /**
   * Create an entry.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, activate the model, insert them into the database and redirect afterwards.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create() {
		$this->_setError('title');

		if ($this->_aError)
			return $this->_showFormTemplate();

		elseif ($this->_oModel->create() === true) {
      $this->oSmarty->clearCacheForController($this->_aRequest['controller']);

			Logs::insert(	$this->_aRequest['controller'],
										$this->_aRequest['action'],
										$this->_oModel->getLastInsertId($this->_aRequest['controller']),
										$this->_aSession['user']['id']);

			return Helper::successMessage(I18n::get('success.create'), '/' . $this->_aRequest['controller']);
		}
		else
			return Helper::errorMessage(I18n::get('error.sql.query'), '/' . $this->_aRequest['controller']);
	}

  /**
   * Update an entry.
   *
   * Activate model, insert data into the database and redirect afterwards.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _update() {
    $this->_setError('title');

    if ($this->_aError)
      return $this->_showFormTemplate();

    elseif ($this->_oModel->update((int) $this->_aRequest['id']) === true) {
			$this->oSmarty->clearCacheForController($this->_aRequest['controller']);

      Logs::insert(	$this->_aRequest['controller'],
										$this->_aRequest['action'],
										(int) $this->_aRequest['id'],
										$this->_aSession['user']['id']);

      return Helper::successMessage(I18n::get('success.update'),
              '/' . $this->_aRequest['controller'] . '/' . (int) $this->_aRequest['id']);
    }

    else
      return Helper::errorMessage(I18n::get('error.sql'),
              '/' . $this->_aRequest['controller'] . '/' . (int) $this->_aRequest['id']);
  }

  /**
   * Destroy an entry.
   *
   * Activate model, delete data from database and redirect afterwards.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _destroy() {
    if($this->_oModel->destroy($this->_iId) === true) {
			$this->oSmarty->clearCacheForController($this->_aRequest['controller']);

      Logs::insert(	$this->_aRequest['controller'],
										$this->_aRequest['action'],
										$this->_iId,
										$this->_aSession['user']['id']);

      return Helper::successMessage(I18n::get('success.destroy'), '/' . $this->_aRequest['controller']);
    }

    else
      return Helper::errorMessage(I18n::get('error.sql'), '/' . $this->_aRequest['controller']);
  }

	/**
   * Subscribe to newsletter list.
   *
   * @static
   * @access protected
   * @param array $aData user data
   * @return boolean status of subscription
	 *
   */
  protected static function _subscribeToNewsletter($aData, $bDoubleOptIn = false) {
    require_once PATH_STANDARD . '/lib/mailchimp/MCAPI.class.php';

    $oMCAPI = new MCAPI(MAILCHIMP_API_KEY);
    return $oMCAPI->listSubscribe(MAILCHIMP_LIST_ID,
              $aData['email'],
              array('FNAME' => $aData['name'], 'LNAME' => $aData['surname']),
              '',
              $bDoubleOptIn);
  }

  /**
   * Remove from newsletter list
   *
   * @static
   * @access private
   * @param string $sEmail
   * @return boolean status of action
   *
   */
  protected static function _unsubscribeFromNewsletter($sEmail) {
    require_once PATH_STANDARD . '/lib/mailchimp/MCAPI.class.php';

    $oMCAPI = new MCAPI(MAILCHIMP_API_KEY);
    return $oMCAPI->listUnsubscribe(MAILCHIMP_LIST_ID, $sEmail, '', '', false, false);
  }
}
