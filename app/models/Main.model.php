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
use CandyCMS\Helper\AdvancedException as AdvancedException;
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
		$this->_aRequest	= & $aRequest;
		$this->_aSession	= & $aSession;
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

    # Bugfix
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
      $iTimestamp = $aData['date'];
      $aData['time'] = Helper::formatTimestamp($iTimestamp, 2);
      $aData['date'] = Helper::formatTimestamp($iTimestamp, 1);
      $aData['date_raw'] = (int) $iTimestamp;
      $aData['date_w3c'] = date('Y-m-d', $iTimestamp);

      $aData['datetime'] = Helper::formatTimestamp($iTimestamp);
      $aData['datetime_rss'] = date('D, d M Y H:i:s O', $iTimestamp);
      $aData['datetime_w3c'] = date('Y-m-d\TH:i:sP', $iTimestamp);

      # SEO optimization
      $iTimestampNow = time();
      # Entry is less than a day old
      if($iTimestampNow - $iTimestamp < 86400) {
        $aData['changefreq']  = 'hourly';
        $aData['priority']    = '1.0';
      }
      # Entry is younger than a week
      elseif($iTimestampNow - $iTimestamp < 86400 * 7) {
        $aData['changefreq']  = 'daily';
        $aData['priority']    = '0.9';
      }
      # Entry is younger than a month
      elseif($iTimestampNow - $iTimestamp < 86400 * 31) {
        $aData['changefreq']  = 'weekly';
        $aData['priority']    = '0.75';
      }
      # Entry is younger than three month
      elseif($iTimestampNow - $iTimestamp < 86400 * 90) {
        $aData['changefreq']  = 'monthly';
        $aData['priority']    = '0.6';
      }
      # Entry is younger than half a year
      elseif($iTimestampNow - $iTimestamp < 86400 * 180) {
        $aData['changefreq']  = 'monthly';
        $aData['priority']    = '0.4';
      }
      # Entry is younger than a year
      elseif($iTimestampNow - $iTimestamp < 86400 * 360) {
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
    $iUserId = isset($aData['author_id']) ? (int) $aData['author_id'] : (int) $aData['id'];

		# Create avatars
		if(isset($aData['author_email']))
			$sEmail = $aData['author_email'];

		elseif(isset($aData['email']))
			$sEmail = $aData['email'];

		else
			$sEmail = 'admin@example.com';

		Helper::createAvatarURLs($aData,
						$iUserId,
						$sEmail,
						isset($aData['use_gravatar']) ? (bool) $aData['use_gravatar'] : false);

    # Build full user name
    $aData['name']    = isset($aData['name']) ? (string) $aData['name'] : '';
    $aData['surname'] = isset($aData['surname']) ? (string) $aData['surname'] : '';
    $aData['full_name'] = trim($aData['name'] . ' ' . $aData['surname']);

    # Encode data for SEO
    $aData['encoded_full_name'] = urlencode($aData['full_name']);
    $aData['encoded_title']			= isset($aData['title']) ? urlencode($aData['title']) : '';

    # URL to entry
    $aData['url_clean']   = WEBSITE_URL . '/' . $sController . '/' . $aData['id'];
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
   * @param string $sClass name of model to load
   * @return string model name
   *
   */
  public static function __autoload($sClass) {
    $sClass = (string) ucfirst(strtolower($sClass));

		if (ADDON_CHECK && file_exists(PATH_STANDARD . '/addons/models/' . $sClass . '.model.php')) {
			require_once PATH_STANDARD . '/addons/models/' . $sClass . '.model.php';
			return '\CandyCMS\Addon\Model\Addon_' . $sClass;
		}
		elseif (file_exists(PATH_STANDARD . '/app/models/' . $sClass . '.model.php')) {
			require_once PATH_STANDARD . '/app/models/' . $sClass . '.model.php';
			return '\CandyCMS\Model\\' . $sClass;
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