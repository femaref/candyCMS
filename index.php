<?php

/*
* This software is copyright protected. Use only allowed on licensed
* websites. Contact author for further information or to receive a license.
*
* @link http://marcoraddatz.com
* @copyright 2007 - 2008 Marco Raddatz
* @author Marco Raddatz <mr at marcoraddatz dot com>
* @package CMS
* @version 1.0
*/

# List of userrights
#--------------------------------------------------
# 0 = Guests / Unregistered Users
# 1 = Members
# 2 = Special Members (VIPs, Paying Members, etc.)
# 3 = Moderators
# 4 = Administrators
#--------------------------------------------------

error_reporting  (E_ALL);
ini_set( 'arg_separator.output', '&amp;' );
ini_set( 'zlib.output_compression_level', 9);
date_default_timezone_set('Europe/Berlin');

try {
	#Load Parent
	if( !file_exists('app/models/Main.model.php') ||
			!file_exists('app/controllers/Main.controller.php') ||
			!file_exists('app/controllers/Index.controller.php') ||
			!file_exists('app/helpers/AdvancedException.helper.php') ||
			!file_exists('app/helpers/Section.helper.php') ||
			!file_exists('app/helpers/Helper.helper.php') ||
			!file_exists('app/helpers/SqlConnect.helper.php') ||
			!file_exists('app/helpers/SqlQuery.helper.php') ||
			!file_exists('lib/smarty/Smarty.class.php')
	)
		throw new Exception('Could not load required classes.');
	else {
		require_once 'app/models/Main.model.php';
		require_once 'app/controllers/Main.controller.php';
		require_once 'app/controllers/Index.controller.php';

		# All helpers
		require_once 'app/helpers/AdvancedException.helper.php';
		require_once 'app/helpers/Section.helper.php';
		require_once 'app/helpers/Helper.helper.php';
		require_once 'app/helpers/SqlConnect.helper.php';
		require_once 'app/helpers/SqlQuery.helper.php';

		# Smarty template parsing
		require_once 'lib/smarty/Smarty.class.php';
	}
}
catch (Exception $e)
{
	die($e->getMessage());
}

if (isset ($_FILES))
	$aFile =& $_FILES;
else
	$aFile = array();

session_start();

$oIndex = new Index($_REQUEST, $_SESSION, $aFile, $_COOKIE);
$oIndex->loadConfig();
$oIndex->checkURL();
$oIndex->setLanguage();
$oIndex->loadAddons();
$oIndex->connectDB();

$aUser =& $oIndex->setUser();
define( 'USERID',	(int)$aUser['id'] );
define( 'USERRIGHT', (int)$aUser['userright'] );

echo $oIndex->show();
?>