<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @todo refactor
 */

# The cronjob keeps your software backuped, fast and clean. Set up the execution
# intervals in the "config/Candy.inc.php" and lean back.
# Fix for install script

namespace CandyCMS\Plugin\Controller;

use CandyCMS\Controller\Mails as Mails;
use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Main as Main;
use CandyCMS\Controller\Logs as Logs;
use PDO;

final class Cronjob {

  /**
   * The Id of the Cronjobs Log Entry, that is created for the execution
   * @var int
   */
  private $_iLogId = -1;
  /**
   * The User Id of the user, that is running the Cronjob
   * @var int
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
    require_once PATH_STANDARD . '/app/controllers/Mails.controller.php';

    $this->_iUserId = $iUserId;

  }
   * Cleanup our temp folders.
   *
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
   * Create a SQL backup.
   *
   * @access public
   *
   */
  public final function backup() {
    $sBackupName      = date('Y-m-d_H-i');
    $sBackupFolder    = PATH_STANDARD . 'backup';
    $sBackupPath      = $sBackupFolder . '/' . $sBackupName . '.sql';

    Main::$_oDbStatic->beginTransaction();

    # Create header information
    $sFileText = "\r\n#---------------------------------------------------------------#\r\n";
    $sFileText .= "# Server OS: " . @php_uname();
    $sFileText .= "\r\n";
    $sFileText .= "# MySQL-Version: " . @mysql_get_server_info();
    $sFileText .= "\r\n";
    $sFileText .= "# PHP-Version: " . @phpversion();
    $sFileText .= "\r\n";
    $sFileText .= "# Database: " . SQL_DB;
    $sFileText .= "\r\n";
    $sFileText .= "# Time of backup: " . date('Y-m-d H:i');
    $sFileText .= "\r\n#---------------------------------------------------------------#\r\n";
    $sFileText .= "\r\n#---------------------------------------------------------------#\r\n";
    $sFileText .= "# Backup includes following tables:";
    $sFileText .= "\r\n#---------------------------------------------------------------#\r\n";
    $sFileText .= "\r\n";

    # Get all tables and name them
    try {
      $oQuery = Main::$_oDbStatic->query("SHOW TABLES FROM " . SQL_DB . '_' . WEBSITE_MODE);
      $aResult = $oQuery->fetchAll();
    }
    catch (AdvancedException $e) {
      # @todo exception
    }

    # Show all tables
    foreach ($aResult as $aTable) {
      $sFileText .= "# " . SQL_PREFIX . $aTable[0] . "\r\n";
    }

    # Now backup them
    foreach ($aResult as $aTable) {
      $sTable = SQL_PREFIX . $aTable[0];

      try {
        $oQuery = Main::$_oDbStatic->query("SHOW COLUMNS FROM " . $sTable);
        $aColumns = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        $iColumns = count($aColumns);
      }
      catch (AdvancedException $e) {
        # @todo exception
      }

      $sFileText .= "\r\n#---------------------------------------------------------------#\r\n";
      $sFileText .= "# Table: " . $sTable . ", Columns: " . $iColumns;
      $sFileText .= "\r\n#---------------------------------------------------------------#\r\n";
      $sFileText .= "\r\n";
      $sFileText .= "DROP TABLE IF EXISTS `" . $sTable . "`;";
      $sFileText .= "\r\n";
      $sFileText .= "CREATE TABLE `" . $sTable . "` (";

      foreach ($aColumns as $aColumn) {
        $sFileText .= "\r\n`" . $aColumn['Field'] . "` " . $aColumn['Type'];

        if (!empty($aColumn['Default']))
          $sFileText .= " NOT NULL default '" . $aColumn['Default'] . "'";

        elseif ($aColumn['Null'] !== 'YES')
          $sFileText .= ' NOT NULL';

        elseif ($aColumn['Null'] == 'YES')
          $sFileText .= ' default NULL';

        $sFileText .= ",";
      }

      $sFileText .= "\n";

      # Show extras like auto_increment etc
      try {
        $oQuery = Main::$_oDbStatic->query("SHOW KEYS FROM " . $sTable);
        $aKeys = $oQuery->fetchAll(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        # @todo exception
      }

      $iKey = 1;
      foreach ($aKeys as $aKey) {
        $sKey = & $aKey['Key_name'];

        if (($sKey != 'PRIMARY') && ($sKey['Non_unique'] == 0))
          $sKey = "UNIQUE|" . $sKey;

        # Do we have keys?
        if ($sKey == 'PRIMARY')
          $sFileText .= " PRIMARY KEY (`" . $aKey['Column_name'] . "`)";

        elseif (substr($sKey, 0, 6) == "UNIQUE")
          $sFileText .= " UNIQUE " . substr($sKey, 7) . " (`" . $aKey['Column_name'] . "`)";

        else
          $sFileText .= " FULLTEXT KEY " . $sKey . " (`" . $aKey['Column_name'] . "`)";

        if (count($aKeys) !== $iKey)
          $sFileText .= ",\n";

        ++$iKey;
      }

      # Closing bracket
      $sFileText .= "\n)";

      try {
        $oQuery = Main::$_oDbStatic->query(" SELECT
                                  id
                                FROM
                                  " . $sTable . "
                                ORDER BY
                                  id DESC
                                LIMIT
                                  1");

        $aRow = $oQuery->fetch();
      }
      catch (AdvancedException $e) {
        # @todo exception
      }

      # We also use this as count for data entries
      $iRows = (int) $aRow['id'];
      $sFileText .= " AUTO_INCREMENT=";
      $sFileText .= $iRows + 1;
      $sFileText .= " DEFAULT CHARSET=utf8;";
      $sFileText .= "\r\n";

      # Now fetch content
      try {
        $oQuery = Main::$_oDbStatic->query("SELECT * FROM " . $sTable);
        $aRows = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        $iRows = count($aRows);
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
      }

      $sFileText .= "\r\n#---------------------------------------------------------------#\r\n";
      $sFileText .= "# Data: " . $sTable . ", Rows: " . $iRows;
      $sFileText .= "\r\n#---------------------------------------------------------------#\r\n";
      $sFileText .= "\r\n";

      foreach ($aRows as $aRow) {
        $sFileText .= "INSERT INTO `" . $sTable . "` VALUES (";

        $iEntries = 1;
        foreach ($aRow as $sEntry) {
          $sFileText .= "'" . addslashes($sEntry) . "'";

          if ($iEntries !== $iColumns)
            $sFileText .= ",";

          $iEntries++;
        }

        $sFileText .= ");\r\n";
      }
    }

    # Write into file
    $oFile = @fopen($sBackupPath, 'a+');
    @fwrite($oFile, $sFileText);
    @fclose($oFile);

    if (CRONJOB_GZIP_BACKUP === true) {
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
    if (class_exists('Mails') && CRONJOB_SEND_PER_MAIL === true)
      Mails::send(WEBSITE_MAIL,
              str_replace('%s', $sBackupName, LANG_MAIL_CRONJOB_CREATE_SUBJECT),
              LANG_MAIL_CRONJOB_CREATE_BODY,
              WEBSITE_MAIL_NOREPLY,
              $sBackupPath);

    # @todo return status of backup?!
    # Write into backup log
    try {
      $oQuery = Main::$_oDbStatic->prepare("INSERT INTO
                                              " . SQL_PREFIX . "logs
                                              ( controller_name,
                                                action_name,
                                                action_id,
                                                time_start,
                                                time_end,
                                                user_id)
                                            VALUES
                                              ( :controller_name,
                                                :action_name,
                                                :action_id,
                                                :time_start,
                                                :time_end,
                                                :user_id)");

      $sControllerName  = 'cronjob';
      $sActionName      = 'create';
      $iActionId        = 0;

      $oQuery->bindParam('controller_name', $sControllerName, PDO::PARAM_STR);
      $oQuery->bindParam('action_name', $sActionName, PDO::PARAM_STR);
      $oQuery->bindParam('action_id', $iActionId, PDO::PARAM_INT);
      $oQuery->bindParam('time_start', $iBackupStartTime, PDO::PARAM_INT);
      $oQuery->bindParam('time_end', time(), PDO::PARAM_INT);
      $oQuery->bindParam('user_id', $iUserId, PDO::PARAM_INT);
      return $oQuery->execute();

    # rollback, since we did only read
    Main::$_oDbStatic->rollBack();
    }
    catch (AdvancedException $e) {
      Main::$_oDbStatic->rollBack();
      # @todo exception
    }
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
    $iInterval = !empty($iInterval) ? $iInterval : CRONJOB_UPDATE_INTERVAL;

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