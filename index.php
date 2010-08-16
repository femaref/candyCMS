<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
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
		require_once 'app/helpers/AdvancedException.helper.php';
		require_once 'app/helpers/Section.helper.php';
		require_once 'app/helpers/Helper.helper.php';
		require_once 'app/helpers/SqlConnect.helper.php';
		require_once 'app/helpers/SqlQuery.helper.php';

		# Smarty template parsing
		require_once 'lib/smarty/Smarty.class.php';
	}
} catch (Exception $e) {
	die($e->getMessage());
}

if (isset ($_FILES))
	$aFile =& $_FILES;
else
	$aFile = array();

@session_start();

$oIndex = new Index($_REQUEST, $_SESSION, $aFile, $_COOKIE);
$oIndex->loadConfig();
$oIndex->checkURL();
$oIndex->setLanguage();
$oIndex->loadAddons();
$oIndex->loadPlugins();
$oIndex->connectDB();

$aUser =& $oIndex->setUser();
define( 'USER_ID',	(int)$aUser['id'] );
define( 'USER_NAME', (string)$aUser['name'] );
define( 'USER_RIGHT', (int)$aUser['userright'] );
define( 'USER_SURNAME', (string)$aUser['surname'] );

$iAjax = isset($_REQUEST['ajax']) ? 1 : 0;
define( 'AJAX_REQUEST', (int)$iAjax );

echo $oIndex->show();
?>