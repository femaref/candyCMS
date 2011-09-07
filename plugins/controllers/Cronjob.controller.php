<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# The cronjob keeps your software backuped, fast and clean. Set up the execution
# intervals in the "config/Candy.inc.php" and lean back.
# Fix for install script
if (file_exists('app/controllers/Mail.controller.php'))
  require_once 'app/controllers/Mail.controller.php';

final class Cronjob {

  public static final function cleanup() {

    $aFolders = array('media', 'bbcode');

    foreach ($aFolders as $sFolder) {
      $sTempPath = PATH_UPLOAD . '/temp/' . $sFolder;
      $oDir = opendir($sTempPath);

      while ($sFile = readdir($oDir)) {
        if (substr($sFile, 0, 1) == '.' || filemtime($sTempPath . '/' . $sFile) > strtotime("-10 days"))
          continue;

        unlink($sTempPath . '/' . $sFile);
      }
    }
  }

  public static final function optimize() {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->query(" OPTIMIZE TABLE
                                " . SQL_PREFIX . "blogs,
                                " . SQL_PREFIX . "comments,
                                " . SQL_PREFIX . "contents,
                                " . SQL_PREFIX . "gallery_albums,
                                " . SQL_PREFIX . "gallery_files,
                                " . SQL_PREFIX . "migrations,
                                " . SQL_PREFIX . "logs,
                                " . SQL_PREFIX . "newsletters,
                                " . SQL_PREFIX . "users");

      $oDb = null;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

  public static final function backup($iUserId, $sPath = '') {
    $sBackupName = date('Y-m-d_H-i');
    $sBackupFolder = $sPath . 'backup';
    $sBackupPath = $sBackupFolder . '/' . $sBackupName . '.sql';
    $iBackupStartTime = time();

    if (!is_dir($sBackupFolder))
      mkdir($sBackupFolder, 0777);

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
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
                  PDO::ATTR_PERSISTENT => true));
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->query("SHOW TABLES FROM " . SQL_DB);
      $aResult = $oQuery->fetchAll();
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    # Show all tables
    foreach ($aResult as $aTable) {
      $sTable = $aTable[0];
      $sFileText .= "# " . SQL_PREFIX . $sTable . "\r\n";
    }

    # Now backup them
    foreach ($aResult as $aTable) {
      $sTable = SQL_PREFIX . $aTable[0];

      try {
        $oQuery = $oDb->query("SHOW COLUMNS FROM " . $sTable);
        $aColumns = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        $iColumns = count($aColumns);
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
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
        $oQuery = $oDb->query("SHOW KEYS FROM " . $sTable);
        $aKeys = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        $iKeys = count($aKeys);
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
      }

      $iKey = 1;
      foreach ($aKeys as $aKey) {
        $sKey = & $aKey['Key_name'];

        if (($sKey != 'PRIMARY') && ($sKey['Non_unique'] == 0))
          $sKey = "UNIQUE|" . $sKey;

        # Do we have keys?
        if ($sKey == "PRIMARY")
          $sFileText .= " PRIMARY KEY (`" . $aKey['Column_name'] . "`)";

        elseif (substr($sKey, 0, 6) == "UNIQUE")
          $sFileText .= " UNIQUE " . substr($sKey, 7) . " (`" . $aKey['Column_name'] . "`)";

        else
          $sFileText .= " FULLTEXT KEY " . $sKey . " (`" . $aKey['Column_name'] . "`)";

        if ($iKeys !== $iKey)
          $sFileText .= ",\n";

        $iKey++;
      }

      # Closing bracket
      $sFileText .= "\n)";

      try {
        $oQuery = $oDb->query(" SELECT
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
        $oDb->rollBack();
      }

      # We also use this as count for data entries
      $iRows = (int) $aRow['id'];
      $sFileText .= " AUTO_INCREMENT=";
      $sFileText .= $iRows + 1;
      $sFileText .= " DEFAULT CHARSET=utf8;";
      $sFileText .= "\r\n";

      # Now fetch content
      try {
        $oQuery = $oDb->query("SELECT * FROM " . $sTable);
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

    if (CRONJOB_GZIP_BACKUP == true) {
      $oData = implode('', file($sBackupPath));
      $oCompress = gzencode($oData, 9);
      unlink($sBackupPath);

      $sBackupPath = $sBackupPath . '.gz';
      $oF = fopen($sBackupPath, 'w+');
      fwrite($oF, $oCompress);
      fclose($oF);
    }

    # Send the backup via mail
    if (class_exists('Mail') && CRONJOB_SEND_PER_MAIL == true)
      Mail::send(WEBSITE_MAIL, str_replace('%d', $sBackupName, LANG_MAIL_CRONJOB_CREATE_SUBJECT), LANG_MAIL_CRONJOB_CREATE_BODY, WEBSITE_MAIL_NOREPLY, $sBackupPath);

    # Write into backup log
    try {
      $oQuery = $oDb->prepare(" INSERT INTO
                                  " . SQL_PREFIX . "logs(section_name, action_name, action_id, time_start, time_end, user_id)
                                VALUES
                                  ( :section_name, :action_name, :action_id, :time_start, :time_end, :user_id)");

      $sSectionName = 'cronjob';
      $sActionName = 'create';
      $iActionId = 0;
      $oQuery->bindParam('section_name', $sSectionName);
      $oQuery->bindParam('action_name', $sActionName);
      $oQuery->bindParam('action_id', $iActionId, PDO::PARAM_INT);
      $oQuery->bindParam('time_start', $iBackupStartTime);
      $oQuery->bindParam('time_end', time());
      $oQuery->bindParam('user_id', $iUserId);
      $bResult = $oQuery->execute();

      $oDb = null;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

  public static function getNextUpdate($iInterval = '') {
    $iInterval = !empty($iInterval) ? $iInterval : CRONJOB_UPDATE_INTERVAL;

    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->query(" SELECT
                                time_end
                              FROM
                                " . SQL_PREFIX . "logs
                              WHERE
                                section_name = 'cronjob'
                              ORDER BY
                                time_end DESC
                              LIMIT
                                1");

      $aResult = $oQuery->fetch();
      $iTimeEnd = $aResult['time_end'];

      $oDb = null;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    if ($iTimeEnd + $iInterval < time())
      return true;
    else
      return false;
  }
}