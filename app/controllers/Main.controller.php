<?php

/**
 * Parent class for most other controllers and provides most language variables.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Model\Session as Session;
use CandyCMS\Plugin\Adsense as Adsense;
use CandyCMS\Plugin\Archive as Archive;
use CandyCMS\Plugin\Bbcode as Bbcode;
use CandyCMS\Plugin\FacebookCMS as FacebookCMS;
use CandyCMS\Plugin\Headlines as Headlines;
use CandyCMS\Plugin\Teaser as Teaser;
use MCAPI;
use Smarty;

abstract class Main {

	/**
	 * Alias for $_REQUEST
	 *
	 * @var array
	 * @access protected
	 */
	protected $_aRequest;

	/**
	 * Alias for $_SESSION
	 *
	 * @var array
	 * @access protected
	 */
	protected $_aSession;

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
	 * i18n object.
	 *
	 * @var object
	 * @access public
	 */
	public $oI18n;

	/**
	 * Smarty object.
	 *
	 * @var object
	 * @access public
	 */
	public $oSmarty;

	/**
	 * Initialize the software by adding input params, set default id and start template engine.
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

    if (!isset($this->_aRequest['section'])) {
      Helper::redirectTo('/' . WEBSITE_LANDING_PAGE);
      exit();
    }

		$this->_iId = isset($this->_aRequest['id']) ? (int) $this->_aRequest['id'] : '';

    $this->_setI18n();
    $this->_setSmarty();
	}

  /**
   * Dynamically load classes.
   *
   * @param string $sClassName name of class to load
   */
  public function __autoload($sClassName) {
    require_once 'app/controllers/' .$sClassName. '.controller.php';
  }

	/**
	 * Method to include the model files.
	 *
	 * @access public
	 *
	 */
  public function __init() {

  }

  /**
   * @access public
   *
   */
  public function __call($sMethod, $Args) {

  }

	/**
	 * Set up i18n.
	 *
	 * @access proteced
	 * @return obj $this->oI18n
	 *
	 */
  protected function _setI18n() {
    $this->oI18n = new I18n(WEBSITE_LANGUAGE);
    return $this->oI18n;
  }

	/**
	 * Set up smarty.
	 *
	 * @access proteced
	 * @return obj $this->oSmarty
	 *
	 */
	protected function _setSmarty() {
		# Initialize smarty
		$this->oSmarty = new Smarty();
		$this->oSmarty->cache_dir = CACHE_DIR;
		$this->oSmarty->compile_dir = COMPILE_DIR;

    if (CLEAR_CACHE == true)
      $this->oSmarty->clearAllCache();

		# Define constants
		$this->oSmarty->assign('AJAX_REQUEST', AJAX_REQUEST);
		$this->oSmarty->assign('CURRENT_URL', CURRENT_URL);
		$this->oSmarty->assign('FACEBOOK_ADMIN_ID', FACEBOOK_ADMIN_ID); # required for meta only
		$this->oSmarty->assign('FACEBOOK_APP_ID', FACEBOOK_APP_ID); # required for facebook actions
		$this->oSmarty->assign('MOBILE', MOBILE);
		$this->oSmarty->assign('MOBILE_DEVICE', MOBILE_DEVICE);
		$this->oSmarty->assign('THUMB_DEFAULT_X', THUMB_DEFAULT_X);
		$this->oSmarty->assign('USER_EMAIL', USER_EMAIL);
		$this->oSmarty->assign('USER_FACEBOOK_ID', USER_FACEBOOK_ID);
		$this->oSmarty->assign('USER_FULL_NAME', USER_FULL_NAME);
		$this->oSmarty->assign('USER_ID', USER_ID);
		$this->oSmarty->assign('USER_NAME', USER_NAME);
		$this->oSmarty->assign('USER_RIGHT', USER_RIGHT);
		$this->oSmarty->assign('USER_SURNAME', USER_SURNAME);
		$this->oSmarty->assign('VERSION', VERSION);
		$this->oSmarty->assign('WEBSITE_COMPRESS_FILES', WEBSITE_COMPRESS_FILES);
		$this->oSmarty->assign('WEBSITE_LANGUAGE', WEBSITE_LANGUAGE);
		$this->oSmarty->assign('WEBSITE_LOCALE', WEBSITE_LOCALE);
		$this->oSmarty->assign('WEBSITE_NAME', WEBSITE_NAME);
		$this->oSmarty->assign('WEBSITE_URL', WEBSITE_URL);
		$this->oSmarty->assign('WEBSITE_TRACKING_CODE', WEBSITE_TRACKING_CODE);

		# Define system variables
		$this->oSmarty->assign('_date_', date('Y-m-d'));
		$this->oSmarty->assign('_compress_files_suffix_', WEBSITE_COMPRESS_FILES == true ? '.min' : '');
		$this->oSmarty->assign('_facebook_plugin_', class_exists('FacebookCMS') ? true : false);
		$this->oSmarty->assign('_json_language_', $this->oI18n->getJson());
		$this->oSmarty->assign('_pubdate_', date('r'));
		$this->oSmarty->assign('_request_id_', $this->_iId);

		# Include Google Adsense
		if (class_exists('\CandyCMS\Plugin\Adsense')) {
			$oAdsense = new Adsense();
			$this->oSmarty->assign('_plugin_adsense_', $oAdsense->show());
		}

		# Include news archive
		if (class_exists('\CandyCMS\Plugin\Archive')) {
			$oArchive = new Archive($this->_aRequest, $this->_aSession);
			$this->oSmarty->assign('_plugin_archive_', $oArchive->show());
		}

		# Include latest headlines
		if (class_exists('\CandyCMS\Plugin\Headlines')) {
			$oHeadlines = new Headlines($this->_aRequest, $this->_aSession);
			$this->oSmarty->assign('_plugin_headlines_', $oHeadlines->show());
		}

		# Include latest teaser
		if (class_exists('\CandyCMS\Plugin\Teaser')) {
			$oTeaser = new Teaser($this->_aRequest, $this->_aSession);
			$this->oSmarty->assign('_plugin_teaser_', $oTeaser->show());
		}

		# Set up language
		$this->oSmarty->assign('lang', $this->oI18n->getArray());

		return $this->oSmarty;
	}

	/**
	 * Set meta description.
	 *
	 * @access protected
	 * @param string $sDescription description to be set.
	 *
	 */
	protected function _setDescription($sDescription = '') {
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
		# Show default description if this is our landing page or we got no descrption.
		if (WEBSITE_LANDING_PAGE == substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI'])) || empty($this->_sDescription))
			return $this->oI18n->get('website.description');

		# We got no description. Fall back to default description.
		else
			return $this->_sDescription;
	}

	/**
	 * Set meta keywords.
	 *
	 * @access protected
	 * @param string $sKeywords keywords to be set.
	 *
	 */
	protected function _setKeywords($sKeywords = '') {
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
		return !empty($this->_sKeywords) ? Helper::removeSlahes($this->_sKeywords) : $this->oI18n->get('website.keywords');
	}

	/**
	 * Set meta keywords.
	 *
	 * @access protected
	 * @param string $sTitle title to be set.
	 *
	 */
	protected function _setTitle($sTitle = '') {
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
		return !empty($this->_sTitle) ? Helper::removeSlahes($this->_sTitle) : $this->oI18n->get('error.404.title');
	}

	/**
	 * Set the page content.
	 *
	 * @access protected
	 * @param string $sContent html content
	 * @see app/helpers/Section.helper.php
	 *
	 */
	protected function _setContent($sContent) {
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
	 * @return integer id
	 *
	 */
	public function getId() {
		return !empty($this->_iId) ? $this->_iId : '';
	}

	/**
	 * Quick hack for displaying title without html tags.
	 *
	 * @access protected
	 * @param string $sTitle title to modifiy
	 * @return string modified title
	 *
	 */
	protected function _removeHighlight($sTitle) {
		$sTitle = Helper::removeSlahes($sTitle);
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
	 *
	 */
	protected function _setError($sField, $sMessage = '') {
		if (!isset($this->_aRequest[$sField]) || empty($this->_aRequest[$sField]))
			$this->_aError['error'][$sField] = empty($sMessage) ? $this->oI18n->get('error.form.missing.' . strtoupper($sField)) : $sMessage;

		if (isset($this->_aRequest['email']) && ( Helper::checkEmailAddress($this->_aRequest['email']) == false ) &&
						'blog' !== $this->_aRequest['section'])
			$this->_aError['error']['email'] = $this->oI18n->get('error.form.missing.email');
	}

	/**
	 * Create an action.
	 *
	 * Create entry or show form template if we have enough rights.
	 *
	 * @access public
	 * @param string $sInputName sent input name to verify action
	 * @param integer $iUserRight required user right
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 *
	 */
	public function create($sInputName, $iUserRight = 3) {
		if (USER_RIGHT < $iUserRight)
			return Helper::errorMessage($this->oI18n->get('error.missing.permission'), '/');

		else {
			Log::insert($this->_aRequest['section'], $this->_aRequest['action'], $this->_iId);
			return isset($this->_aRequest[$sInputName]) ? $this->_create() : $this->_showFormTemplate();
		}
	}

	/**
	 * Update an action.
	 *
	 * Update entry or show form template if we have enough rights.
	 *
	 * @access public
	 * @param string $sInputName sent input name to verify action
	 * @param integer $iUserRight required user right
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 *
	 */
	public function update($sInputName, $iUserRight = 3) {
		if (USER_RIGHT < $iUserRight)
			return Helper::errorMessage($this->oI18n->get('error.missing.permission'), '/');

		else {
			Log::insert($this->_aRequest['section'], $this->_aRequest['action'], $this->_iId);
			return isset($this->_aRequest[$sInputName]) ? $this->_update() : $this->_showFormTemplate();
		}
	}

	/**
	 * Delete an action.
	 *
	 * Delete entry if we have enough rights.
	 *
	 * @access public
	 * @param integer $iUserRight required user right
	 * @return string|boolean HTML content (string) or returned status of model action (boolean).
	 *
	 */
	public function destroy($iUserRight = 3) {
		Log::insert($this->_aRequest['section'], $this->_aRequest['action'], $this->_iId);
		return (USER_RIGHT < $iUserRight) ? Helper::errorMessage($this->oI18n->get('error.missing.permission'), '/') : $this->_destroy();
	}

	/**
	 * Subscribe to newsletter list.
	 *
	 * @access protected
	 * @param array $aData user data
	 * @return boolean status of subscription
	 */
	protected function _subscribeToNewsletter($aData, $bDoubleOptIn = false) {
		require_once 'config/Mailchimp.inc.php';
		$aVars = array('FNAME' => $aData['name'], 'LNAME' => $aData['surname']);

		$oMCAPI = new MCAPI(MAILCHIMP_API_KEY);
		return $oMCAPI->listSubscribe(MAILCHIMP_LIST_ID, $aData['email'], $aVars, '', $bDoubleOptIn);
	}

  /**
   * Remove from newsletter list
   *
   * @access private
   * @param string $sEmail
   * @return boolean status of action
   *
   */
  protected function _unsubscribeFromNewsletter($sEmail) {
      require_once 'config/Mailchimp.inc.php';

    $oMCAPI = new MCAPI(MAILCHIMP_API_KEY);
    return $oMCAPI->listUnsubscribe(MAILCHIMP_LIST_ID, $sEmail, '', '', false, false);
  }
}