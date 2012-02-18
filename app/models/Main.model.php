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
  protected $_aRequest = array();

  /**
   * Alias for $_SESSION
   *
   * @var array
   * @access protected
   */
  protected $_aSession = array();

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
  public $oPagination;

  /**
   * Database connection.
   *
   * @var object
   * @access protected
   *
   */
  static $_oDbStatic;

  /**
   * Return ID of last inserted data.
   *
   * @var integer
   * @access public
   *
   */
  static $iLastInsertId;

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
		$this->_aRequest	= & $aRequest;
		$this->_aSession	= & $aSession;
    $this->_aFile     = & $aFile;

    # Set ID if needed (fix for detailed user view)
    $this->_iId = isset($this->_aRequest['id']) && !isset($this->_iId) ? (int) $this->_aRequest['id'] : '';

    $this->_oDb = $this->_connectToDatabase();
  }

  /**
   * Close DB connection.
   *
   * @access public
   * @return null
   *
   */
  public function __destruct() {
    return $this->_disconnectFromDatabase();
  }

  /**
   * Connect to database.
   *
   * @static
   * @access protected
   * @return object PDO
   *
   */
  protected static function _connectToDatabase() {
    if (empty(self::$_oDbStatic)) {
      try {
        self::$_oDbStatic = new PDO('mysql:host=' . SQL_HOST . ';port=' . SQL_PORT . ';dbname=' . SQL_DB,
                        SQL_USER,
                        SQL_PASSWORD,
                        array(PDO::ATTR_PERSISTENT => true));

        self::$_oDbStatic->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      catch (PDOException $p) {
        AdvancedException::reportBoth('0102 - ' . $p->getMessage());
        exit('SQL error.');
      }
    }

    return self::$_oDbStatic;
  }

  /**
   * Disconnect from database.
   *
   * @static
   * @return boolean
   *
   */
  protected static function _disconnectFromDatabase() {
    return self::$_oDbStatic = null;
  }

  /**
   * Remove slashes from content for update purposes.
   *
   * @static
   * @access protected
   * @param array $aRow array with data to update
   * @return array $aData data witout slashes
   *
   */
  protected static function _formatForUpdate($aRow) {
    $aData = array();

    foreach ($aRow as $sColumn => $sData)
      $aData[$sColumn] = $sData;

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
    $aData = '';

    foreach ($aRow as $sColumn => $mData)
      $aData[$sColumn] = is_int($mData) ? (int) $mData : Helper::formatOutput($mData);

    # Format data
    if (isset($aRow['date'])) {
      $aData['time'] = Helper::formatTimestamp($aRow['date'], 2);
      $aData['date'] = Helper::formatTimestamp($aRow['date'], 1);
      $aData['datetime'] = Helper::formatTimestamp($aRow['date']);
      $aData['date_raw'] = (int) $aRow['date'];
      $aData['date_rss'] = date('D, d M Y H:i:s O', $aRow['date']);
      $aData['date_w3c'] = date('Y-m-d\TH:i:sP', $aRow['date']);

      # SEO optimization
      # Entry is less than a day old
      if(time() - $aRow['date'] < 86400) {
        $aData['changefreq']  = 'hourly';
        $aData['priority']    = '1.0';
      }
      # Entry is younger than a week
      elseif(time() - $aRow['date'] < 86400 * 7) {
        $aData['changefreq']  = 'daily';
        $aData['priority']    = '0.9';
      }
      # Entry is younger than a month
      elseif(time() - $aRow['date'] < 86400 * 31) {
        $aData['changefreq']  = 'weekly';
        $aData['priority']    = '0.75';
      }
      # Entry is younger than three month
      elseif(time() - $aRow['date'] < 86400 * 90) {
        $aData['changefreq']  = 'monthly';
        $aData['priority']    = '0.6';
      }
      # Entry is younger than half a year
      elseif(time() - $aRow['date'] < 86400 * 180) {
        $aData['changefreq']  = 'monthly';
        $aData['priority']    = '0.4';
      }
      # Entry is younger than a year
      elseif(time() - $aRow['date'] < 86400 * 360) {
        $aData['changefreq']  = 'monthly';
        $aData['priority']    = '0.25';
      }
      # Entry older than half year
      else {
        $aData['changefreq']  = 'yearly';
        $aData['priority']    = '0.1';
      }
    }

    # Build user ID
    $iUserId = isset($aRow['author_id']) ? $aRow['author_id'] : $aRow['id'];

    if(isset($this->_aRequest['section']) && 'log' !== $this->_aRequest['section']) {
			$sEmail       = isset($aRow['email']) ? $aRow['email'] : '';
      $bUseGravatar = isset($aRow['use_gravatar']) ? (bool) $aRow['use_gravatar'] : false;

      $aData['avatar_32']			= Helper::getAvatar(32, $iUserId, $sEmail, $bUseGravatar);
      $aData['avatar_64']			= Helper::getAvatar(64, $iUserId, $sEmail, $bUseGravatar);
      $aData['avatar_100']		= Helper::getAvatar(100, $iUserId, $sEmail, $bUseGravatar);
      $aData['avatar_popup']	= Helper::getAvatar('popup', $iUserId, $sEmail, $bUseGravatar);
    }

    # Build full user name
    $aData['name']    = isset($aData['name']) ? (string) $aData['name'] : '';
    $aData['surname'] = isset($aData['surname']) ? (string) $aData['surname'] : '';
    $aData['full_name'] = trim($aData['name'] . ' ' . $aData['surname']);

    # Encode data for SEO
    $aData['encoded_full_name'] = urlencode($aData['full_name']);
    $aData['encoded_title'] = isset($aRow['title']) ? urlencode($aRow['title']) : '';

    # URL to entry
    $aData['url_clean']   = WEBSITE_URL . '/' . $sSection . '/' . $aRow['id'];
    $aData['url']         = $aData['url_clean'] . '/' . $aData['encoded_title'];
    $aData['encoded_url'] = urlencode($aData['url']);

    # Do we need to highlight text?
    $sHighlight = isset($this->_aRequest['highlight']) && !empty($this->_aRequest['highlight']) ?
            $this->_aRequest['highlight'] :
            '';

    # Highlight text for search results
    if(!empty($sHighlight)) {
      $aData['title']   = isset($aData['title']) ? Helper::formatOutput($aData['title'], $sHighlight) : '';
      $aData['teaser']  = isset($aData['teaser']) ? Helper::formatOutput($aData['teaser'], $sHighlight) : '';
      $aData['content'] = Helper::formatOutput($aData['content'], $sHighlight);
    }

		if ($bNl2br == true)
			nl2br($aData['content']);

    return $aData;
  }

  /**
   * Return last inserted ID.
   *
   * @static
   * @access public
   * @return integer last inserted ID.
   */
  public static function getLastInsertId() {
    return self::$iLastInsertId;
  }

  /**
   * Return data for autocompletion.
   *
   * @static
   * @access public
   * @param string $sTable table to get data from
   * @param string $sColumn column to get data from
   * @param boolean $bSplit split data by comma
   * @return string formatted data
   *
   */
  public static function getTypeaheadData($sTable, $sColumn, $bSplit = false) {
    try {
      $oQuery = self::$_oDbStatic->query("SELECT
                                              " . $sColumn . "
                                            FROM
                                              " . SQL_PREFIX . $sTable . "
                                            GROUP BY
                                              " . $sColumn);

      $aResult = & $oQuery->fetchAll(PDO::FETCH_ASSOC);

      $sString = '';
      foreach ($aResult as $aRow) {
        if ($bSplit == true) {
          $aItems = preg_split("/[\s]*[,][\s]*/", $aRow[$sColumn]);

          foreach ($aItems as $sItem)
            $sString .= '"' . $sItem . '",';
        }

        else
          $sString .= '"' . $aRow[$sColumn] . '",';
      }

      return '[' . substr($sString, 0, -1) . ']';
    }
    catch (\PDOException $p) {
      try {
        parent::rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0099 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0100 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }
}