<?php

/**
 * Parent class for most other models. Handles also DB insertations.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.5
 *
 */

namespace CandyCMS\Core\Models;

use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\AdvancedException;
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
  public function __construct(&$aRequest = '', &$aSession = '', &$aFile = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aSession  = & $aSession;
    $this->_aFile     = & $aFile;

    $this->_iId = isset($this->_aRequest['id']) && !isset($this->_iId) ? (int) $this->_aRequest['id'] : '';
    $this->_oDb = $this->connectToDatabase();
  }

  /**
   * Close DB connection.
   *
   * @access public
   * @return null
   *
   */
  public function __destruct() {
    # not unsetting the database, because it is unset by index.controller
  }

  /**
   * Get a Singleton database PDO Object.
   *
   * @static
   * @access public
   * @return object PDO
   *
   */
  public static function connectToDatabase() {
    if (empty(self::$_oDbStatic)) {
      try {
        self::$_oDbStatic = new PDO('mysql:host=' . SQL_HOST . ';port=' . SQL_PORT . ';dbname=' . SQL_DB . '_' . WEBSITE_MODE,
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
   * @access public
   * @return boolean
   *
   */
  public static function disconnectFromDatabase() {
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
   * @param array $aData array with data to format
   * @param array $aInts identifiers, which should be cast to int
   * @param array $aBools identifiers, which should be cast to bool
   * @param string $sController name of the controller we are working in
   * @return array $aData rebuild data
   *
   */
  protected function _formatForOutput(&$aData, $aInts = array('id'), $aBools = null, $sController = '') {
    $sController = !$sController ? $this->_aRequest['controller'] : $sController;

    foreach ($aData as $sColumn => $mData)
      $aData[$sColumn] = Helper::formatOutput($mData);

    # Bugfix: Set types
    if ($aInts)
      foreach ($aInts as $sIdent)
        if (isset($aData[$sIdent]))
          $aData[$sIdent] = (int) $aData[$sIdent];

    if ($aBools)
      foreach ($aBools as $sIdent)
        if (isset($aData[$sIdent]))
          $aData[$sIdent] = (bool) $aData[$sIdent];

    # Format data
    if (isset($aData['date'])) {
      $aData['date_raw'] = (int) $aData['date'];
      $aData['date_w3c'] = date('Y-m-d', $aData['date_raw']);
      $aData['time']     = Helper::formatTimestamp($aData['date_raw'], 2);
      $aData['date']     = Helper::formatTimestamp($aData['date_raw'], 1);

      $aData['datetime'] = Helper::formatTimestamp($aData['date_raw']);
      $aData['datetime_rss'] = date('D, d M Y H:i:s O', $aData['date_raw']);
      $aData['datetime_w3c'] = date('Y-m-d\TH:i:sP', $aData['date_raw']);

      $iTimestampNow = time();

      # Entry is less than a day old
      if($iTimestampNow - $aData['date_raw'] < 86400) {
        $aData['changefreq']  = 'hourly';
        $aData['priority']    = '1.0';
      }
      # Entry is younger than a week
      elseif($iTimestampNow - $aData['date_raw'] < 86400 * 7) {
        $aData['changefreq']  = 'daily';
        $aData['priority']    = '0.9';
      }
      # Entry is younger than a month
      elseif($iTimestampNow - $aData['date_raw'] < 86400 * 31) {
        $aData['changefreq']  = 'weekly';
        $aData['priority']    = '0.75';
      }
      # Entry is younger than three month
      elseif($iTimestampNow - $aData['date_raw'] < 86400 * 90) {
        $aData['changefreq']  = 'monthly';
        $aData['priority']    = '0.6';
      }
      # Entry is younger than half a year
      elseif($iTimestampNow - $aData['date_raw'] < 86400 * 180) {
        $aData['changefreq']  = 'monthly';
        $aData['priority']    = '0.4';
      }
      # Entry is younger than a year
      elseif($iTimestampNow - $aData['date_raw'] < 86400 * 360) {
        $aData['changefreq']  = 'monthly';
        $aData['priority']    = '0.25';
      }
      # Entry older than half year
      else {
        $aData['changefreq']  = 'yearly';
        $aData['priority']    = '0.1';
      }
    }

    # build the user data
    if ($aData['user_id'] != 0) {
      $aUserData = array(
          'email'        => $aData['user_email'],
          'id'           => $aData['user_id'],
          'use_gravatar' => isset($aData['use_gravatar']) ? (bool) $aData['use_gravatar'] : false,
          'name'         => $aData['user_name'],
          'surname'      => $aData['user_surname'],
          'facebook_id'  => isset($aData['author_facebook_id']) ? $aData['author_facebook_id'] : '',
          'ip'           => isset($aData['author_ip']) ? $aData['author_ip'] : '',
      );
    }
    else {
      # we dont have a user (comments) and format the user given data instead
      $aUserData = array(
          'email'        => isset($aData['author_email']) ? $aData['author_email'] : WEBSITE_MAIL,
          'id'           => isset($aData['author_id']) ? $aData['author_id'] : 0,
          'use_gravatar' => isset($aData['use_gravatar']) ? (bool) $aData['use_gravatar'] : true,
          'name'         => isset($aData['author_name']) ? $aData['author_name'] : '',
          'surname'      => '',
          'facebook_id'  => isset($aData['author_facebook_id']) ? $aData['author_facebook_id'] : '',
          'ip'           => isset($aData['author_ip']) ? $aData['author_ip'] : '',
      );
    }
    $aData['author'] = $this->_formatForUserOutput($aUserData);

    # Encode data for SEO
    $aData['encoded_title'] = isset($aData['title']) ? urlencode($aData['title']) : $aData['author']['encoded_full_name'];

    # URL to entry
    $aData['url_clean']   = WEBSITE_URL . '/' . $sController . '/' . $aData['id'];
    $aData['url']         = $aData['url_clean'] . '/' . $aData['encoded_title'];
    $aData['encoded_url'] = urlencode($aData['url']); #SEO
    $aData['url_destroy'] = $aData['url_clean'] . '/destroy';
    $aData['url_update']  = $aData['url_clean'] . '/update';

    # Do we need to highlight text?
    $sHighlight = isset($this->_aRequest['highlight']) ? $this->_aRequest['highlight'] : '';

    # Highlight text for search results
    if(!empty($sHighlight)) {
      $aData['title']   = isset($aData['title']) ? Helper::formatOutput($aData['title'], $sHighlight) : '';
      $aData['teaser']  = isset($aData['teaser']) ? Helper::formatOutput($aData['teaser'], $sHighlight) : '';
      $aData['content'] = Helper::formatOutput($aData['content'], $sHighlight);
    }

    return $aData;
  }

  /**
   * Formats / adds all relevant Information for displaying a user.
   *
   * @access protected
   * @param array $aData array of given userdata, required fields are 'email', 'id', 'name', 'surname' and 'use_gravatar'
   * @return array $aData returns reference of $aData
   * @todo tests
   *
   */
  protected function _formatForUserOutput(&$aData) {
    # Create avatars
    Helper::createAvatarURLs($aData,
            $aData['id'],
            isset($aData['email']) ? $aData['email'] : WEBSITE_MAIL,
            isset($aData['use_gravatar']) ? (bool) $aData['use_gravatar'] : false);

    # Build full user name
    $aData['name']      = isset($aData['name']) ? (string) $aData['name'] : '';
    $aData['surname']   = isset($aData['surname']) ? (string) $aData['surname'] : '';
    $aData['full_name'] = trim($aData['name'] . ' ' . $aData['surname']);

    # Encode data for SEO
    $aData['encoded_full_name'] = urlencode($aData['full_name']);

    # URL to entry
    $aData['url_clean']   = WEBSITE_URL . '/users/' . $aData['id'];
    $aData['url']         = $aData['url_clean'] . '/' . $aData['encoded_full_name'];
    $aData['encoded_url'] = urlencode($aData['url']);

    $aData['url_destroy'] = $aData['url_clean'] . '/destroy';
    $aData['url_update']  = $aData['url_clean'] . '/update';

    if (isset($aData['date'])) {
      $aData['date_raw'] = (int) $aData['date'];
      $aData['date_w3c'] = date('Y-m-d', $aData['date_raw']);

      $aData['datetime'] = Helper::formatTimestamp($aData['date_raw']);
      $aData['datetime_rss'] = date('D, d M Y H:i:s O', $aData['date_raw']);
      $aData['datetime_w3c'] = date('Y-m-d\TH:i:sP', $aData['date_raw']);

      $aData['time'] = Helper::formatTimestamp($aData['date_raw'], 2);
      $aData['date'] = Helper::formatTimestamp($aData['date_raw'], 1);
    }

    return $aData;
  }

  /**
   * Return last inserted ID.
   *
   * @static
   * @access public
   * @return integer self::$iLastInsertId last inserted ID.
   *
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

      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);

      $sString = '';
      foreach ($aResult as $aRow) {
        if ($bSplit === true) {
          $aItems = explode(',', $aRow[$sColumn]);

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
        parent::$_oDbStatic->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0099 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0100 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Dynamically load models.
   *
   * @static
   * @access public
   * @param string $sClass name of model to load
   * @return string model name
   *
   */
  public static function __autoload($sClass) {
    $sClass = (string) ucfirst(strtolower($sClass));

    if (EXTENSION_CHECK && file_exists(PATH_STANDARD . '/app/extensions/models/' . $sClass . '.model.php')) {
      require_once PATH_STANDARD . '/app/extensions/models/' . $sClass . '.model.php';
      return '\CandyCMS\Models\\' . $sClass;
    }
    elseif (file_exists(PATH_STANDARD . '/vendor/candyCMS/core/models/' . $sClass . '.model.php')) {
      require_once PATH_STANDARD . '/vendor/candyCMS/core/models/' . $sClass . '.model.php';
      return '\CandyCMS\Core\Models\\' . $sClass;
    }
  }

  /**
   * Destroy an entry.
   *
   * @access public
   * @param integer $iId ID to destroy
   * @return boolean status of query
   *
   */
  public function destroy($iId) {
    try {
      $oQuery = $this->_oDb->prepare("DELETE FROM
                                        " . SQL_PREFIX . $this->_aRequest['controller'] . "
                                      WHERE
                                        id = :id
                                      LIMIT
                                        1");

      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
      return $oQuery->execute();
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0112 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0113 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }
}