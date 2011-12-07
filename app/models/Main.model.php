<?php

/**
 * Parent class for most other models. Handles also DB insertations.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.5
 */

namespace CandyCMS\Model;

use CandyCMS\Helper\Helper as Helper;
use PDO;

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
   * Returned data from models.
   *
   * @var array
   * @access protected
   */
  protected $_aData = array();

  /**
   * ID to process.
   *
   * @var integer
   * @access protected
   */
  protected $_iId;

  /**
   * PDO object.
   *
   * @var object
   * @access protected
   */
  protected $_oDb;

  /**
   * Page object.
   *
   * @var object
   * @access public
   */
  public $oPage;

  /**
   * Database connection.
   *
   * @var object
   * @access protected
   *
   */
  static $_oDbStatic;

  /**
	 * Initialize the model by adding input params, set default id connect to database.
   *
   * @access public
	 * @param array $aRequest alias for the combination of $_GET and $_POST
	 * @param array $aSession alias for $_SESSION
	 * @param array $aFile alias for $_FILE
   *
   */
  public function __construct($aRequest = '', $aSession = '', $aFile = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aSession  = & $aSession;
    $this->_aFile     = & $aFile;

    # Set ID if needed (fix for detailed user view)
    $this->_iId = isset($this->_aRequest['id']) && !isset($this->_iId) ? (int) $this->_aRequest['id'] : '';

    $this->_oDb = $this->_connectToDatabase();
  }

  /**
   * Close DB connection.
   *
   * @access public
   * @return string null
   *
   */
  public function __destruct() {
    return $this->_disconnectFromDatabase();
  }

  /**
   * Connect to database.
   *
   * @access protected
   * @return object PDO
   *
   */
  protected function _connectToDatabase() {
    if (empty(self::$_oDbStatic)) {
      self::$_oDbStatic = new PDO('mysql:host=' . SQL_HOST . ';port=' . SQL_PORT . ';dbname=' . SQL_DB,
                                  SQL_USER,
                                  SQL_PASSWORD,
                                  array(PDO::ATTR_PERSISTENT => true));
      self::$_oDbStatic->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return self::$_oDbStatic;
  }

  /**
   * Disconnect from database.
   *
   * @return boolean
   *
   */
  protected function _disconnectFromDatabase() {
    return self::$_oDbStatic = null;
  }

  /**
   * Remove slashes from content for update purposes.
   *
   * @access protected
   * @param array $aRow array with data to update
   * @return array $aData data witout slashes
   *
   */
  protected function _formatForUpdate($aRow) {
    foreach ($aRow as $sColumn => $sData)
      $aData[$sColumn] = Helper::removeSlahes($sData);

    return $aData;
  }

  /**
   * Format data correctly.
   *
   * @access protected
   * @param array $aRow array with data to format
   * @param string $sSection name of the section we are working in
	 * @param boolean $bNl2br format string to br
   * @return array $aData rebuild data
   *
   */
  protected function _formatForOutput($aRow, $sSection, $bNl2br = false) {
    foreach ($aRow as $sColumn => $sData)
      $aData[$sColumn] = is_int($sData) ? (int) $sData : Helper::formatOutput($sData);

    # Format data
    if (isset($aRow['date'])) {
      $aData['time'] = Helper::formatTimestamp($aRow['date'], 2);
      $aData['date'] = Helper::formatTimestamp($aRow['date'], 1);
      $aData['datetime'] = Helper::formatTimestamp($aRow['date']);
      $aData['date_raw'] = (int) $aRow['date'];
      $aData['date_rss'] = date('D, d M Y H:i:s O', $aRow['date']);
      $aData['date_w3c'] = date('Y-m-d\TH:i:sP', $aRow['date']);
    }

    # Build user ID
    $iUserId = isset($aRow['author_id']) ? $aRow['author_id'] : $aRow['id'];

    if(isset($this->_aRequest['section']) && 'log' !== $this->_aRequest['section']) {
			$sEmail = isset($aRow['email']) ? $aRow['email'] : '';

      $aData['avatar_32']			= Helper::getAvatar(32, $iUserId, $sEmail);
      $aData['avatar_64']			= Helper::getAvatar(64, $iUserId, $sEmail);
      $aData['avatar_100']		= Helper::getAvatar(100, $iUserId, $sEmail);
      $aData['avatar_popup']	= Helper::getAvatar('popup', $iUserId, $sEmail);
    }

    # Build full user name
    $aData['name']    = isset($aData['name']) ? (string) $aData['name'] : '';
    $aData['surname'] = isset($aData['surname']) ? (string) $aData['surname'] : '';
    $aData['full_name'] = trim($aData['name'] . ' ' . $aData['surname']);

    # Encode data for SEO
    $aData['encoded_full_name'] = urlencode($aData['full_name']);
    $aData['encoded_title'] = isset($aRow['title']) ? urlencode($aRow['title']) : '';

    # URL to entry
    $aData['url_clean'] = '/' . $sSection . '/' . $aRow['id'];
    $aData['url'] = $aData['url_clean'] . '/' . $aData['encoded_title'];
    $aData['encoded_url'] = urlencode($aData['url']);

    # Do we need to highlight text?
    $sHighlight = isset($this->_aRequest['highlight']) && !empty($this->_aRequest['highlight']) ?
            $this->_aRequest['highlight'] :
            '';

    # Highlight text for search results
    if(!empty($sHighlight)) {
      $aData['title'] = isset($aData['title']) ? Helper::formatOutput($aData['title'], $sHighlight) : '';
      $aData['teaser'] = isset($aData['teaser']) ? Helper::formatOutput($aData['teaser'], $sHighlight) : '';
      $aData['content'] = Helper::formatOutput($aData['content'], $sHighlight);
    }

		if ($bNl2br == true)
			nl2br($aData['content']);

    return $aData;
  }
}