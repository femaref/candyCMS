<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

$sDir = 'sql/migrations';
$oDir = opendir($sDir);

$iI = 1;
$aFiles = array();
while ($sFile = readdir($oDir)) {
  if ($sFile == '.' || $sFile == '..' || $sFile == '.htaccess' || $sFile == '.svn')
    continue;

  $oFo = fopen($sDir. '/' .$sFile, 'r');
  $sQuery = fread($oFo, filesize($sDir. '/' .$sFile));

  $aFiles[$iI]['name'] = $sFile;
  $aFiles[$iI]['query'] = $sQuery;
  $iI++;
}

$oSmarty->assign('files', $aFiles);
$oSmarty->assign('action', $_SERVER['PHP_SELF']);
$sHTML = $oSmarty->fetch('show.tpl');
?>