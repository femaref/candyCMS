<?php

/**
 * Minify all JS and CSS files.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

function compress($sPath, $sType) {
  $sHtml = '';

  $oPathDir = opendir($sPath);
  while ($sDir = readdir($oPathDir)) {
    if (substr($sDir, 0, 1) == '.')
      continue;


    $sPathFile = $sPath . '/' . $sDir;
    $oPathFile = opendir($sPathFile);
    $sHtml .= '<h1>' . $sPathFile . '</h1>';

    while ($sFile = readdir($oPathFile)) {
      if (substr($sFile, 0, 1) == '.' || preg_match('/min/', $sFile))
        continue;

      $sFileUrl = $sPathFile . '/' . $sFile;
      $sFileUrlMin = $sPathFile . '/' . substr($sFile, 0, strlen($sFile) - (strlen($sType) + 1)) . '.min.' . $sType;

      $sCmd = 'java -jar ' . __DIR__ . '/build/yuicompressor-2.4.6.jar --type ' . $sType . ' --charset UTF-8 ' . $sFileUrl . ' -o ' . $sFileUrlMin;
      #exec($sCmd);
      $sHtml .= $sCmd . '<br />';
    }

    closedir($oPathFile);
  }

  closedir($oPathDir);

  echo $sHtml;
}

# Standard paths
compress($_SERVER['DOCUMENT_ROOT'] . '/public/js', 'js');
compress($_SERVER['DOCUMENT_ROOT'] . '/public/css', 'css');

# Templates
$sPath = $_SERVER['DOCUMENT_ROOT'] . '/public/templates';
$oPathDir = opendir($sPath);
while ($sDir = readdir($oPathDir)) {
  if (substr($sDir, 0, 1) == '.')
    continue;

  compress($sPath . '/' . $sDir . '/css', 'css');
  compress($sPath . '/' . $sDir . '/js', 'js');
}
closedir($oPathDir);

?>