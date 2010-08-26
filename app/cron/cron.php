<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

error_reporting(E_ALL);
ini_set('arg_separator.output', '&amp;');
ini_set('zlib.output_compression_level', 9);

require_once ('../Config.inc.php');
require_once ('../classes/helper/SqlConnect.class.php');
require_once ('../classes/helper/SqlQuery.class.php');
#require_once ('../classes/helper/Helper.class.php');

/* Cleanup Preview Thumbnails */
$sReturn = '<h2>1. Delete temp images</h2>';
$sTempPath = '../' . PATH_UPLOAD . '/temp/32';
$oDir = opendir($sTempPath);

$iI = 0;
while ($sFile = readdir($oDir)) {
  if ($sFile == '.' || $sFile == '..' || $sFile == '.htaccess')
    continue;
  unlink($sTempPath . '/' . $sFile);
  $iI++;
}

$sReturn .= $iI . ' Files deleted.';

/* Optimize Core Tables in Database */
$sReturn .= '<h2>2. Optimize MySQL Core</h2>';
SQLCONNECT::connect(SQL_HOST, SQL_USER, SQL_PASSWORD);
SQLCONNECT::selectDB(SQL_DB);

$oOptimize = new Query("OPTIMIZE TABLE
							`blog`,
							`comment`,
							`content`,
							`gallery_album`,
							`gallery_file`,
							`newsletter`,
							`user`");

$sReturn .= 'Database optimized.';
$sReturn .= '<h2>3. Backup Database</h2>';

$sBackupName = date('Y-m-d---Hi') . '.sql';
$sBackupFolder = '../backup';
$sBackupPath = $sBackupFolder . '/' . $sBackupName;

if (!is_dir($sBackupFolder))
  mkdir($sBackupFolder, 0777);

/* Create header information */
$sFileText = "#---------------------------------------------------------------#\r\n";
$sFileText .= "# Server OS: " . php_uname() . "\r\n";
$sFileText .= "# MySQL-Version: " . mysql_get_server_info() . "\r\n";
$sFileText .= "# PHP-Version: " . phpversion() . "\r\n";
$sFileText .= "# Database: " . SQL_DB . "\r\n";
$sFileText .= "# Time of Backup: " . date('Y-m-d H:i') . "\r\n";
$sFileText .= "#---------------------------------------------------------------#\r\n";
$sFileText .= "\r\n\r\n";
$sFileText .= "# Backup includes following tables:\r\n";

/* Get all tables and name them */
$iTables = 0;
$aTables = mysql_list_tables(SQL_DB);
for ($iI = 0; $iI < mysql_num_rows($aTables); $iI++) {
  $sTableName = mysql_tablename($aTables, $iI);

  /* Add Tables to information */
  $sFileText .= "# " . $iI . ". " . $sTableName . "\r\n";

  if ($sTableName <> '') {
    $aTable[$iTables] = $sTableName;
    $iTables++;
  }
}

flush();
unset($iI, $aTables, $sTableName);

/* Backup Tables */
for ($iI = 0; $iI < $iTables; $iI++) {
  $sTable = $aTable[$iI];
  $sFileText .= "#---------------------------------------------------------------#\r\n\r\n";
  $sFileText .= "# " . $sTable . "\r\n";
  $sFileText .= "#---------------------------------------------------------------#\r\n";
  $sFileText .= "\r\nCREATE TABLE `" . $sTable . "` (";

  $iColumns = 0; # ALSO USED FOR DATA BELOW
  $oGetFields = new Query("SHOW COLUMNS FROM " . $sTable);
  while ($aRow = $oGetFields->fetch()) {
    $iColumns++;
    $sFileText .= "\r\n`" . $aRow['Field'] . "` " . $aRow['Type'];

    if (!empty($aRow['Default']))
      $sFileText .= " NOT NULL default '" . $aRow['Default'] . "'";

    elseif ($aRow['Null'] !== 'YES')
      $sFileText .= ' NOT NULL';

    elseif ($aRow['Null'] == 'YES')
      $sFileText .= ' default NULL';

    if ($aRow['Extra'] != '') {
      $sFileText .= ' ' . $aRow['Extra'];

      if ($aRow['Extra'] == 'auto_increment')
        $aAutoIncrement = array(true, $aRow['Field']);
    }

    /* End of column */
    $sFileText .= ",";
  }

  /* Extra Structure (add at end of table) */
  $oGetKeys = new Query("SHOW KEYS FROM " . $sTable);
  while ($aRow = $oGetKeys->fetch()) {
    $sKey = & $aRow['Key_name'];

    if (($sKey != 'PRIMARY') && ($sKey['Non_unique'] == 0))
      $sKey = "UNIQUE|" . $sKey;

    /* Do we have keys? */
    $sFileText .= ",\n";
    if ($sKey == "PRIMARY")
      $sFileText .= " PRIMARY KEY (`" . $aRow['Column_name'] . "`)";

    elseif (substr($sKey, 0, 6) == "UNIQUE")
      $sFileText .= " UNIQUE " . substr($sKey, 7) . " (" . $aRow['Column_name'] . ")";

    else
      $sFileText .= " FULLTEXT KEY " . $sKey . " (`" . $aRow['Column_name'] . "`)";
  }

  $sFileText .= "\n)";
  $sFileText .= " ENGINE=MyISAM";

  if ($aAutoIncrement[0] == true) {
    $oGetLastEntry = new Query("SELECT
										id, " . $aAutoIncrement[1] . "
									FROM
										" . $sTable . "
									ORDER BY
										" . $aAutoIncrement[1] . " DESC
									LIMIT
										1");
    $aID = $oGetLastEntry->fetch();
    $aID['id'] = (int) $aID['id'];
    $sFileText .= " AUTO_INCREMENT=";
    $sFileText .= $aID['id']++;
  }

  $sFileText .= " ;\r\n\r\n";

  $oGetData = new Query("SELECT * FROM " . $sTable);
  $iEntries = $oGetData->numRows($oGetData);
  while ($aRow = $oGetData->fetchArray()) {
    $sFileText .= "INSERT INTO `" . $sTable . "` VALUES (";
    for ($iR = 0; $iR < $iColumns; $iR++) {
      if ($iR == ($iColumns - 1))
        $sFileText .= "'" . addslashes($aRow[$iR]) . "'";
      else
        $sFileText .= "'" . addslashes($aRow[$iR]) . "',";
    }
    $sFileText .= ");\r\n";
  }

  unset($aAutoIncrement);
}


/* Write into file */
$oFile = fopen($sBackupPath, 'a+');
fwrite($oFile, $sFileText);
fclose($oFile);
unset($aRow, $sKey);

if (ALLOW_GZIP_BACKUP == true) {
  $oData = implode('', file($sBackupPath));
  $oCompress = gzencode($oData, 9);
  unlink($sBackupPath);

  $sBackupPath = $sBackupPath . '.gz';
  $oF = fopen($sBackupPath, 'w+');
  fwrite($oF, $oCompress);
  fclose($oF);
}

$sReturn .= 'Backup successfull.';
#$sReturn .= '<h2>4. Sending per Mail</h2>';

$sMailHeader = "From:" . WEBSITE_NAME . "<" . WEBSITE_MAIL . ">\n";
$sMailHeader .= "MIME-Version: 1.0\n";
$sMailHeader .= "Content-Type: multipart/mixed\n";
$sMailHeader .= "Content-Type: text/plain\n";
$sMailHeader .= "Content-Transfer-Encoding: 8bit\n";
$sMailHeader .= $sFileText . "\n";

$oFile = fread(fopen($sBackupPath, "r"), filesize($sBackupPath));
$oFile = chunk_split(base64_encode($oFile));

$sMailHeader .= "Content-Type: application/octetstream; name='" . $sBackupPath . "'\n";
$sMailHeader .= "Content-Transfer-Encoding: base64\n";
$sMailHeader .= "Content-Disposition: attachment; filename='" . $sBackupPath . "'\n";
$sMailHeader .= $oFile . "\n";
#@mail(WEBSITE_MAIL_BACKUP, "Backup "	.date('Y-m-d'), $sFileText, $sMailHeader);
#$sReturn .= 'Backup successfully sent.';
echo $sReturn;
?>