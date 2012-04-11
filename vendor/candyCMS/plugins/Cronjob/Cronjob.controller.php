<?php

/**
 * The cronjob keeps your software backuped, fast and clean. Set up the execution
 * intervals in the "app/config/Candy.inc.php" and lean back.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @todo documentation
 * @license MIT
 * @since 1.5
 *
 */

namespace CandyCMS\Plugins;

use CandyCMS\Core\Controllers\Logs;
use CandyCMS\Core\Controllers\Mails;
use CandyCMS\Core\Helpers\AdvancedException;
use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\I18n;
use CandyCMS\Core\Models\Main;
use PDO;

final class Cronjob {

  /**
   * The Id of the Cronjobs log entry, that is created for the execution
   *
   * @access private
   * @var int
   *
   */
  private $_iLogId = -1;

  /**
   * The user id of the user, that is running the Cronjob.
   *
   * @access private
   * @var int
   *
   */
  private $_iUserId;

  /**
   * Create a new Cronjob Object
   *
   * @access public
   * @param integer $iUserId user ID who updates the database.
   *
   */
  public function __construct($iUserId) {
    $this->_iUserId = $iUserId;

    $this->_startCronjob();
  }

  /**
   * Finish up the execution of the Cronjob
   *
   * @access public
   *
   */
  public function __destruct() {
    Logs::updateEndTime($this->_iLogId);
  }

  /**
   * start the execution of the Cronjob, so there wont be other simultanious executions
   *
   * @final
   * @access private
   *
   */
  private final function _startCronjob() {
    $oPDO = Main::$_oDbStatic;
    $oPDO->beginTransaction();

    # Create log entry, so other calls won't start aswell.
    $iTime = time();
    Logs::insert('cronjob', 'execute', 0, $this->_iUserId, $iTime, $iTime);

    # save the id, so we can update at end
    $this->_iLogId = Main::getLastInsertId();

    $oPDO->commit();
  }

  /**
   * Cleanup our temp folders.
   *
   * @final
   * @access public
   * @param array $aFolders temp folders to clean
   *
   */
  public final function cleanup($aFolders) {
    foreach ($aFolders as $sFolder) {
      $sTempPath = Helper::removeSlash(PATH_UPLOAD . '/temp/' . $sFolder);
      $oDir = opendir($sTempPath);

      while ($sFile = readdir($oDir)) {
        if (substr($sFile, 0, 1) == '.' || filemtime($sTempPath . '/' . $sFile) > strtotime("-10 days"))
          continue;

        unlink($sTempPath . '/' . $sFile);
      }
    }
  }


  /**
   * Optimize tables.
   *
   * @access public
   * @todo clean up sessions table and remove old entries
   *
   */
  public final function optimize() {
    try {
      Main::$_oDbStatic->query("OPTIMIZE TABLE
                                " . SQL_PREFIX . "blogs,
                                " . SQL_PREFIX . "comments,
                                " . SQL_PREFIX . "calendars,
                                " . SQL_PREFIX . "contents,
                                " . SQL_PREFIX . "downloads,
                                " . SQL_PREFIX . "gallery_albums,
                                " . SQL_PREFIX . "gallery_files,
                                " . SQL_PREFIX . "migrations,
                                " . SQL_PREFIX . "logs,
                                " . SQL_PREFIX . "sessions,
                                " . SQL_PREFIX . "users");

    }
    catch (AdvancedException $e) {
      Main::$_oDbStatic->rollBack();
      AdvancedException::reportBoth('0109 - ' . $e->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Create a Backup of one Table
   *
   * @final
   * @access private
   * @param string $sTable the Table Name
   * @param string $sFileText the string to write the backup to
   * @return integer number of Columns
   * @throws AdvancedException
   *
   */
  private final function _backupTableInfo($sTable, &$sFileText) {
    $oQuery = Main::$_oDbStatic->query('SHOW COLUMNS FROM ' . $sTable);
    $aColumns = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    $iColumns = count($aColumns);

    $sFileText .= <<<EOD
#---------------------------------------------------------------#
# Table: {$sTable}, Columns: {$iColumns}
#---------------------------------------------------------------#

EOD;

    $sFileText .= 'DROP TABLE IF EXISTS `' . $sTable . '`;';
    $sFileText .= "\r\n";
    $sFileText .= 'CREATE TABLE `' . $sTable . '` (';

    foreach ($aColumns as $aColumn) {
      $sFileText .= "\r\n`" . $aColumn['Field'] . '` ' . $aColumn['Type'];

      if (!empty($aColumn['Default']))
        $sFileText .= " NOT NULL default '" . $aColumn['Default'] . "'";

      elseif ($aColumn['Null'] !== 'YES')
        $sFileText .= ' NOT NULL';

      elseif ($aColumn['Null'] == 'YES')
        $sFileText .= ' default NULL';

      $sFileText .= ',';
    }
    $sFileText .= "\r\n";

    # Show extras like auto_increment etc
    try {
      $oQuery = Main::$_oDbStatic->query('SHOW KEYS FROM ' . $sTable);
      $aKeys = $oQuery->fetchAll(PDO::FETCH_ASSOC);

      $iKey = 1;
      foreach ($aKeys as $aKey) {
        $sKey = & $aKey['Key_name'];

        if (($sKey != 'PRIMARY') && ($sKey['Non_unique'] == 0))
          $sKey = 'UNIQUE|' . $sKey;

        # Do we have keys?
        if ($sKey == 'PRIMARY')
          $sFileText .= ' PRIMARY KEY (`' . $aKey['Column_name'] . '`)';

        elseif (substr($sKey, 0, 6) == "UNIQUE")
          $sFileText .= ' UNIQUE ' . substr($sKey, 7) . ' (`' . $aKey['Column_name'] . '`)';

        else
          $sFileText .= ' FULLTEXT KEY ' . $sKey . ' (`' . $aKey['Column_name'] . '`)';

        if (count($aKeys) !== $iKey)
          $sFileText .= ",\n";
        ++$iKey;
      }
    }
    catch (AdvancedException $e) {
      # @todo exception
    }

    # Closing bracket
    $sFileText .= "\n)";

    try {
      # select last id
      $oQuery = Main::$_oDbStatic->query('SELECT
                                            id
                                          FROM
                                            ' . $sTable . '
                                          ORDER BY
                                            id DESC
                                          LIMIT
                                            1');

      $aRow = $oQuery->fetch(PDO::FETCH_ASSOC);
      $iRows = (int) $aRow['id'];
      $sFileText .= ' AUTO_INCREMENT=';
      $sFileText .= $iRows + 1;
    }
    catch (AdvancedException $e) {
      # @todo exception
    }

    # We also use this as count for data entries
    $sFileText .= ' DEFAULT CHARSET=utf8;';
    $sFileText .= "\r\n\r\n";

    return $iColumns;
  }

  /**
   * Get the table data and write it to $sFileText
   *
   * @final
   * @access public
   * @param string $sTable the table to get the data from
   * @param string $sFileText the result string
   * @return int number of rows backed up
   * @throws AdvancedException
   *
   */
  private final function _backupTableData($sTable, &$sFileText, $iColumns) {
    # fetch content
    $oQuery = Main::$_oDbStatic->query('SELECT * FROM ' . $sTable);
    $aRows = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    $iRows = count($aRows);

    $sFileText .= <<<EOD
#---------------------------------------------------------------#
# Data: {$sTable}, Rows: {$iRows}
#---------------------------------------------------------------#

EOD;

    foreach ($aRows as $aRow) {
      $sFileText .= 'INSERT INTO `' . $sTable . '` VALUES (';

      $iEntries = 1;
      foreach ($aRow as $sEntry) {
        $sFileText .= "'" . addslashes($sEntry) . "'";

        if ($iEntries !== $iColumns)
          $sFileText .= ',';

        $iEntries++;
      }

      $sFileText .= ");\r\n";
    }
    $sFileText .= "\r\n";
    return $iRows;
  }


  /**
   * Create a SQL backup.
   *
   * @final
   * @access public
   *
   */
  public final function backup() {
    $sBackupName      = date('Y-m-d_H-i');
    $sBackupFolder    = PATH_STANDARD . '/app/backup';
    $sBackupPath      = $sBackupFolder . '/' . $sBackupName . '.sql';

    Main::$_oDbStatic->beginTransaction();

    $sFileText = "#---------------------------------------------------------------#\r\n";
    $sFileText .= '# Server OS: '.@php_uname()."\r\n";
    $sFileText .= "#\r\n";
    $sFileText .= '# MySQL-Version: '.@mysql_get_server_info()."\r\n";
    $sFileText .= "#\r\n";
    $sFileText .= '# PHP-Version: '.@phpversion()."\r\n";
    $sFileText .= "#\r\n";
    $sFileText .= '# Database: ' . SQL_DB."\r\n";
    $sFileText .= "#\r\n";
    $sFileText .= "# Time of backup: ".date('Y-m-d H:i')."\r\n";
    $sFileText .= "#---------------------------------------------------------------#\r\n";
    $sFileText .= "\r\n";
    $sFileText .= "#---------------------------------------------------------------#\r\n";
    $sFileText .= "# Backup includes following tables:\r\n";
    $sFileText .= "#---------------------------------------------------------------#\r\n";

    # Get all tables and name them
    try {
      $oQuery = Main::$_oDbStatic->query("SHOW TABLES FROM " . SQL_DB . '_' . WEBSITE_MODE);
      $aResult = $oQuery->fetchAll();

      # Show all tables
      foreach ($aResult as $aTable) {
        $sFileText .= '# ' . $aTable[0] . "\r\n";
      }
      $sFileText .= "\r\n\r\n";

      # Now back them up
      foreach ($aResult as $aTable) {
        try {
          $iColumns = $this->_backupTableInfo($aTable[0], $sFileText);
          $this->_backupTableData($aTable[0], $sFileText, $iColumns);
        }
        catch (AdvancedException $e) {
          # @todo exception?
          continue;
        }
      }
    }
    catch (AdvancedException $e) {
      # @todo exception
    }
    # Write into file
    $oFile = @fopen($sBackupPath, 'a+');
    @fwrite($oFile, $sFileText);
    @fclose($oFile);

    if (PLUGIN_CRONJOB_GZIP_BACKUP === true) {
      $oData = implode('', file($sBackupPath));
      $oCompress = gzencode($oData, 9);
      unlink($sBackupPath);

      $sBackupPath = $sBackupPath . '.gz';
      $oF = fopen($sBackupPath, 'w+');
      fwrite($oF, $oCompress);
      fclose($oF);
    }

    # Send the backup via mail
    # @todo test if this works
    if (PLUGIN_CRONJOB_SEND_PER_MAIL === true) {
      $sMails = \CandyCMS\Core\Controller\Main::__autoload('Mails');
      $sMails::send(WEBSITE_MAIL,
              I18n::get('cronjob.mail.subject', $sBackupName),
              I18n::get('cronjob.mail.body'),
              WEBSITE_MAIL_NOREPLY,
              $sBackupPath);
    }

    # @todo return status of backup?!

    # rollback, since we did only read statements
    Main::$_oDbStatic->rollBack();
  }

  /**
   * Return the status if we want to execute the cronjob.
   *
   * @static
   * @access public
   * @param integer $iInterval time in seconds that the cronjob should be executed
   * @return boolean update status
   *
   */
  public static function getNextUpdate($iInterval = '') {
    $iInterval = !empty($iInterval) ? $iInterval : PLUGIN_CRONJOB_UPDATE_INTERVAL;

    if (empty(Main::$_oDbStatic))
      Main::connectToDatabase();

    try {
      $oQuery = Main::$_oDbStatic->prepare("SELECT
                                              time_end
                                            FROM
                                              " . SQL_PREFIX . "logs
                                            WHERE
                                              controller_name = 'cronjob'
                                            ORDER BY
                                              time_end DESC
                                            LIMIT
                                              1");
      $oQuery->execute();
      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      # no previous cronjob execution found
      if (!$aResult)
        return true;
      else
        return (int)$aResult['time_end'] + $iInterval < time();
    }
    catch (AdvancedException $e) {
      AdvancedException::reportBoth('0108 - ' . $e->getMessage());
      exit('SQL error.');
    }
  }
}