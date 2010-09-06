<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

$sDir = 'migrate/sql';
$oDir = opendir($sDir);

$iI = 1;
$aFiles = array();
while ($sFile = readdir($oDir)) {
  # Initial fix for older versions
  if (file_exists('migrate/sql/20100901_add_migrations_to_mysql.sql')) {
    $aFiles[0]['name'] = '20100901_add_migrations_to_mysql.sql';
    $aFiles[0]['query'] = '<strong>Execute this migration and reload page afterwards. If nothing changes,
      delete "/install/migrate/sql/20100901_add_migrations_to_mysql.sql" manually.</strong>';
    @unlink('migrate/sql/20100901_add_migrations_to_mysql.sql');
  }
  else {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("SELECT id FROM migrations WHERE file = :file");
      $oQuery->bindParam(':file', $sFile);

      $bReturn = $oQuery->execute();

      if ($bReturn == true)
        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);

      $oDb = null;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }

    $bAlreadyMigrated = isset($aResult['id']) && !empty($aResult['id']) ? true : false;

    if ($sFile == '20100901_add_migrations_to_mysql.sql')
      $bAlreadyMigrated = true;

    if (substr($sFile, 0, 1) == '.' || $sFile == '.svn' || $bAlreadyMigrated == true)
      continue;

    else {
      $oFo = fopen($sDir . '/' . $sFile, 'r');
      $sQuery = fread($oFo, filesize($sDir . '/' . $sFile));

      $aFiles[$iI]['name'] = $sFile;
      $aFiles[$iI]['query'] = $sQuery;
      $iI++;
    }

    unset($bAlreadyMigrated, $aResult);
  }
}

sort($aFiles);

$oSmarty->assign('files', $aFiles);
$oSmarty->assign('action', $_SERVER['PHP_SELF']);
$sHTML = $oSmarty->fetch('showStep1.tpl');
?>