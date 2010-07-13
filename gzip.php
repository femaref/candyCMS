<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

error_reporting  (E_ALL);
ini_set( 'arg_separator.output', '&amp;' );
ini_set( 'zlib.output_compression_level', 9);

require_once ('config/Config.inc.php');
if( WEBSITE_URL == 'http://'	.$_SERVER['SERVER_NAME']) {
	if( isset($_GET['file'])) {
		$sPath = (isset($_GET['path']) && 'images' == $_GET['path']) ?
				PATH_IMAGES :
				PATH_PUBLIC;

		$sFile = (string)$_GET['file'];
		$sFile =  substr(strrchr($sPath, '/'), 1).	'/'	.$sFile;

		if( !is_file($sFile) ) {
			header('Status: 404 Not Found');
			die('<h3>Error 404 - Not found</h3>');
		}

		#$iFileSize = (int)filesize($sFile);
		$sFileExtension = strtolower(substr(strrchr($sFile, '.'), 1) );

		switch( $sFileExtension ) {
			case 'css': $sType = 'text/css'; break;
			case 'js':	$sType = 'text/javascript'; break;
			case 'png': $sType = 'image/png'; break;
			case 'jpeg':
			case 'jpg': $sType = 'image/jpg'; break;
			case 'gif': $sType = 'image/gif'; break;
			default: $sType = 'application/octet-stream'; break;
		}

		require_once( $sFile );
	}
}
else
	die('Websites do not match!');

?>