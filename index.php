<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# List of user rights
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

define('VERSION', '20101019');

try {
	#Load Parent
	if( !file_exists('app/models/Main.model.php') ||
			!file_exists('app/controllers/Main.controller.php') ||
			!file_exists('app/controllers/Search.controller.php') ||
			!file_exists('app/controllers/Session.controller.php') ||
			!file_exists('app/controllers/Index.controller.php') ||
			!file_exists('app/helpers/AdvancedException.helper.php') ||
			!file_exists('app/helpers/Section.helper.php') ||
			!file_exists('app/helpers/Helper.helper.php') ||
			!file_exists('lib/smarty/Smarty.class.php')
	)
		throw new Exception('Could not load required classes.');
	else {
		require_once 'app/models/Main.model.php';
		require_once 'app/controllers/Main.controller.php';
		require_once 'app/controllers/Search.controller.php';
		require_once 'app/controllers/Session.controller.php';
		require_once 'app/controllers/Index.controller.php';
		require_once 'app/helpers/AdvancedException.helper.php';
		require_once 'app/helpers/Section.helper.php';
		require_once 'app/helpers/Helper.helper.php';

		# Smarty template parsing
		require_once 'lib/smarty/Smarty.class.php';
	}
} catch (AdvancedException $e) {
	die($e->getMessage());
}

@session_start();
$aFile = isset($_FILES) ? $_FILES : array();
$oIndex = new Index($_REQUEST, $_SESSION, $aFile, $_COOKIE);
$oIndex->loadConfig();
$oIndex->setBasicConfiguration();
$oIndex->setLanguage();
$oIndex->loadAddons();
$oIndex->loadPlugins();

$aUser =& $oIndex->setActiveUser();
define( 'USER_ID',	(int)$aUser['id'] );
define( 'USER_EMAIL', (string)$aUser['email'] );
define( 'USER_NAME', (string)$aUser['name'] );
define( 'USER_RIGHT', (int)$aUser['user_right'] );
define( 'USER_SURNAME', (string)$aUser['surname'] );

# Load cronjob
$oIndex->loadCronjob();

$iAjax = isset($_REQUEST['ajax']) ? 1 : 0;
define( 'AJAX_REQUEST', (int)$iAjax );

echo $oIndex->show();
?>