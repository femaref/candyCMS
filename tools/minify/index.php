<?php

/**
 * Minify all JS and CSS files.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

#require_once '../../config/Candy.inc.php';

function search($sPath, $sType) {
  $sHtml = '';
  $sPath = $sPath . '/' . $sType;
  $sFileContent = '';

  if (is_dir($sPath)) {

    # Get subfolders
    $oPathDir = opendir($sPath);
    while ($sDir = readdir($oPathDir)) {

      # Skip system files
      if (substr($sDir, 0, 1) == '.')
        continue;

      # Get into subfolder
      $sPathFile = $sPath . '/' . $sDir;
      $oPathFile = opendir($sPathFile);
      $sHtml .= '<h1>' . $sPathFile . '</h1>';

      # Get files
      while ($sFile = readdir($oPathFile)) {
        if (substr($sFile, 0, 1) == '.' || preg_match('/min/', $sFile))
          continue;

        $sFileUrl = $sPathFile . '/' . $sFile;
        $sFileUrlMin = $sPathFile . '/' . substr($sFile, 0, strlen($sFile) - (strlen($sType) + 1)) . '.min.' . $sType;

        # Delete existing minified files
        if (file_exists($sFileUrlMin))
          unlink($sFileUrlMin);

        $sHtml .= compress($sType, $sFileUrl, $sFileUrlMin);
      }

      closedir($oPathFile);

      # Clear function data
      $sFileContent = '';
    }

    closedir($oPathDir);
    echo $sHtml;
  }
}

function compress($sType, $sFileUrl, $sFileUrlMin) {
  $sCmd = 'java -jar ' . __DIR__ . '/build/yuicompressor-2.4.7.jar --type ' . $sType . ' --charset UTF-8 ' . $sFileUrl . ' -o ' . $sFileUrlMin;
  exec($sCmd);
  return $sCmd . '<br />';
}

# Standard paths
search($_SERVER['DOCUMENT_ROOT'] . '/public', 'js');
search($_SERVER['DOCUMENT_ROOT'] . '/public', 'css');

# Templates
$sPath = $_SERVER['DOCUMENT_ROOT'] . '/public/templates';
$oPathDir = opendir($sPath);
while ($sDir = readdir($oPathDir)) {
  if (substr($sDir, 0, 1) == '.')
    continue;

  search($sPath . '/' . $sDir, 'css');
  search($sPath . '/' . $sDir, 'js');
}

closedir($oPathDir);
?>