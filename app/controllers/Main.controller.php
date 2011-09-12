<?php

/**
 * Parent class for most other controllers and provides most language variables.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
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
	 * Method to include the model files.
	 *
	 * @access public
	 *
	 */
  public function __init() {

  }

	/**
	 * Set up i18n.
	 *
	 * @access proteced
	 * @return obj $this->oI18n
	 *
	 */
  protected function _setI18n() {
    $this->oI18n = new I18n('languages/' . WEBSITE_LANGUAGE . '/' . WEBSITE_LANGUAGE . '.language.yml');
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

		# Define constants
		$this->oSmarty->assign('AJAX_REQUEST', AJAX_REQUEST);
		$this->oSmarty->assign('CURRENT_URL', CURRENT_URL);
		$this->oSmarty->assign('FACEBOOK_ADMIN_ID', FACEBOOK_ADMIN_ID); # required for meta only
		$this->oSmarty->assign('FACEBOOK_APP_ID', FACEBOOK_APP_ID); # required for facebook actions
		$this->oSmarty->assign('THUMB_DEFAULT_X', THUMB_DEFAULT_X);
		$this->oSmarty->assign('URL', WEBSITE_URL);
		$this->oSmarty->assign('USER_EMAIL', USER_EMAIL);
		$this->oSmarty->assign('USER_FACEBOOK_ID', USER_FACEBOOK_ID);
		$this->oSmarty->assign('USER_FULL_NAME', USER_FULL_NAME);
		$this->oSmarty->assign('USER_ID', USER_ID);
		$this->oSmarty->assign('USER_NAME', USER_NAME);
		$this->oSmarty->assign('USER_RIGHT', USER_RIGHT);
		$this->oSmarty->assign('USER_SURNAME', USER_SURNAME);
		$this->oSmarty->assign('VERSION', VERSION);
		$this->oSmarty->assign('WEBSITE_DESCRIPTION', $this->oI18n->get('website.description'));
		$this->oSmarty->assign('WEBSITE_NAME', WEBSITE_NAME);
		$this->oSmarty->assign('WEBSITE_URL', WEBSITE_URL);
		$this->oSmarty->assign('WEBSITE_TRACKING_CODE', WEBSITE_TRACKING_CODE);

		# Define system variables
		$this->oSmarty->assign('_compress_files_suffix_', WEBSITE_COMPRESS_FILES == true ? '.min' : '');
		$this->oSmarty->assign('_facebook_plugin_', class_exists('FacebookCMS') ? true : false);
		$this->oSmarty->assign('_language_', WEBSITE_LANGUAGE);
		$this->oSmarty->assign('_locale_', WEBSITE_LOCALE);
		$this->oSmarty->assign('_pubdate_', date('r'));
		$this->oSmarty->assign('_request_id_', $this->_iId);

		# Include Google Adsense
		if (class_exists('Adsense')) {
			$oAdsense = new Adsense();
			$this->oSmarty->assign('_plugin_adsense_', $oAdsense->show());
		}

		# Include news archive
		if (class_exists('Archive')) {
			$oArchive = new Archive($this->_aRequest, $this->_aSession);
			$this->oSmarty->assign('_plugin_archive_', $oArchive->show());
		}

		# Include latest headlines
		if (class_exists('Headlines')) {
			$oHeadlines = new Headlines($this->_aRequest, $this->_aSession);
			$this->oSmarty->assign('_plugin_headlines_', $oHeadlines->show());
		}

		# Include latest teaser
		if (class_exists('Teaser')) {
			$oTeaser = new Teaser($this->_aRequest, $this->_aSession);
			$this->oSmarty->assign('_plugin_teaser_', $oTeaser->show());
		}

		# Initialize language
		$this->oSmarty->assign('lang_about', LANG_GLOBAL_ABOUT);
		$this->oSmarty->assign('lang_add_bookmark', LANG_GLOBAL_ADD_BOOKMARK);
		$this->oSmarty->assign('lang_author', LANG_GLOBAL_AUTHOR);
		$this->oSmarty->assign('lang_bb_help', LANG_GLOBAL_BBCODE_HELP);
		$this->oSmarty->assign('lang_blog', LANG_GLOBAL_BLOG);
		$this->oSmarty->assign('lang_by', LANG_GLOBAL_BY);
		$this->oSmarty->assign('lang_category', LANG_GLOBAL_CATEGORY);
		$this->oSmarty->assign('lang_content', LANG_GLOBAL_CONTENT);
		$this->oSmarty->assign('lang_contentmanager', LANG_GLOBAL_CONTENTMANAGER);
		$this->oSmarty->assign('lang_create_entry_headline', LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
		$this->oSmarty->assign('lang_currently', LANG_GLOBAL_CURRENTLY);
		$this->oSmarty->assign('lang_cut', LANG_GLOBAL_CUT);
		$this->oSmarty->assign('lang_comments', LANG_GLOBAL_COMMENTS);
		$this->oSmarty->assign('lang_contact', LANG_GLOBAL_CONTACT);
		$this->oSmarty->assign('lang_cronjob_exec', LANG_GLOBAL_CRONJOB_EXEC);
		$this->oSmarty->assign('lang_date', LANG_GLOBAL_DATE);
		$this->oSmarty->assign('lang_deleted_user', LANG_GLOBAL_DELETED_USER);
		$this->oSmarty->assign('lang_description', LANG_GLOBAL_DESCRIPTION);
		$this->oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY);
		$this->oSmarty->assign('lang_destroy_entry', LANG_GLOBAL_DESTROY_ENTRY);
		$this->oSmarty->assign('lang_disclaimer', LANG_GLOBAL_DISCLAIMER);
		$this->oSmarty->assign('lang_disclaimer_read', LANG_GLOBAL_TERMS_READ);
		$this->oSmarty->assign('lang_download', LANG_GLOBAL_DOWNLOAD);
		$this->oSmarty->assign('lang_downloads', LANG_GLOBAL_DOWNLOADS);
		$this->oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
		$this->oSmarty->assign('lang_email_info', LANG_COMMENT_INFO_EMAIL);
		$this->oSmarty->assign('lang_file', LANG_GLOBAL_FILE);
		$this->oSmarty->assign('lang_files', LANG_GLOBAL_FILES);
		$this->oSmarty->assign('lang_filemanager', LANG_GLOBAL_FILEMANAGER);
		$this->oSmarty->assign('lang_gallery', LANG_GLOBAL_GALLERY);
		$this->oSmarty->assign('lang_logs', LANG_GLOBAL_LOGS);
		$this->oSmarty->assign('lang_message_close', LANG_GLOBAL_MESSAGE_CLOSE);
		$this->oSmarty->assign('lang_missing_entry', LANG_ERROR_GLOBAL_MISSING_ENTRY);
		$this->oSmarty->assign('lang_name', LANG_GLOBAL_NAME);
		$this->oSmarty->assign('lang_newsletter_handle', LANG_NEWSLETTER_HANDLE_TITLE);
		$this->oSmarty->assign('lang_newsletter_create', LANG_NEWSLETTER_CREATE_TITLE);
		$this->oSmarty->assign('lang_keywords', LANG_GLOBAL_KEYWORDS);
		$this->oSmarty->assign('lang_last_update', LANG_GLOBAL_LAST_UPDATE);
		$this->oSmarty->assign('lang_login', LANG_GLOBAL_LOGIN);
		$this->oSmarty->assign('lang_logout', LANG_GLOBAL_LOGOUT);
		$this->oSmarty->assign('lang_no_entries', LANG_ERROR_GLOBAL_NO_ENTRIES);
		$this->oSmarty->assign('lang_not_published', LANG_ERROR_GLOBAL_NOT_PUBLISHED);
		$this->oSmarty->assign('lang_optional', LANG_GLOBAL_OPTIONAL);
		$this->oSmarty->assign('lang_overview', LANG_GLOBAL_OVERVIEW);
		$this->oSmarty->assign('lang_password', LANG_GLOBAL_PASSWORD);
		$this->oSmarty->assign('lang_password_repeat', LANG_GLOBAL_PASSWORD_REPEAT);
		$this->oSmarty->assign('lang_published', LANG_GLOBAL_PUBLISHED);
		$this->oSmarty->assign('lang_quote', LANG_GLOBAL_QUOTE);
		$this->oSmarty->assign('lang_register', LANG_GLOBAL_REGISTER);
		$this->oSmarty->assign('lang_registration', LANG_GLOBAL_REGISTRATION);
		$this->oSmarty->assign('lang_report_error', LANG_GLOBAL_REPORT_ERROR);
		$this->oSmarty->assign('lang_reset', LANG_GLOBAL_RESET);
		$this->oSmarty->assign('lang_required', LANG_GLOBAL_REQUIRED);
		$this->oSmarty->assign('lang_search', LANG_GLOBAL_SEARCH);
		$this->oSmarty->assign('lang_settings', LANG_GLOBAL_SETTINGS);
		$this->oSmarty->assign('lang_share', LANG_GLOBAL_SHARE);
		$this->oSmarty->assign('lang_sitemap', LANG_GLOBAL_SITEMAP);
		$this->oSmarty->assign('lang_size', LANG_GLOBAL_SIZE);
		$this->oSmarty->assign('lang_subject', LANG_GLOBAL_SUBJECT);
		$this->oSmarty->assign('lang_submit', LANG_GLOBAL_CREATE_ENTRY);
		$this->oSmarty->assign('lang_surname', LANG_GLOBAL_SURNAME);
		$this->oSmarty->assign('lang_tags', LANG_GLOBAL_TAGS);
		$this->oSmarty->assign('lang_tags_info', LANG_GLOBAL_TAGS_INFO);
		$this->oSmarty->assign('lang_teaser', LANG_GLOBAL_TEASER);
		$this->oSmarty->assign('lang_title', LANG_GLOBAL_TITLE);
		$this->oSmarty->assign('lang_user', LANG_GLOBAL_USER);
		$this->oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);
		$this->oSmarty->assign('lang_update_show', LANG_GLOBAL_UPDATE_SHOW);
		$this->oSmarty->assign('lang_uploaded_at', LANG_GLOBAL_UPLOADED_AT);
		$this->oSmarty->assign('lang_user_right', LANG_GLOBAL_USERRIGHT);
		$this->oSmarty->assign('lang_user_right_1', LANG_GLOBAL_USERRIGHT_1);
		$this->oSmarty->assign('lang_user_right_2', LANG_GLOBAL_USERRIGHT_2);
		$this->oSmarty->assign('lang_user_right_3', LANG_GLOBAL_USERRIGHT_3);
		$this->oSmarty->assign('lang_user_right_4', LANG_GLOBAL_USERRIGHT_4);
		$this->oSmarty->assign('lang_usermanager', LANG_GLOBAL_USERMANAGER);
		$this->oSmarty->assign('lang_welcome', LANG_GLOBAL_WELCOME);

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
			return LANG_WEBSITE_DESCRIPTION;

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
		return !empty($this->_sKeywords) ? $this->_sKeywords : LANG_WEBSITE_KEYWORDS;
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
		return !empty($this->_sTitle) ? $this->_sTitle : LANG_ERROR_GLOBAL_404_TITLE;
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
			$this->_aError[$sField] = empty($sMessage) ? constant('LANG_ERROR_FORM_MISSING_' . strtoupper($sField)) : $sMessage;

		if (isset($this->_aRequest['email']) && ( Helper::checkEmailAddress($this->_aRequest['email']) == false ))
			$this->_aError['email'] = LANG_ERROR_GLOBAL_WRONG_EMAIL_FORMAT;
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
			return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');

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
			return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');

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
		return (USER_RIGHT < $iUserRight) ? Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/') : $this->_destroy();
	}
}