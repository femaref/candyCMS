<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# List of user rights
#--------------------------------------------------
# 0 = Guests / Unregistered Users
# 1 = Members
# 2 = Facebook users
# 3 = Moderators
# 4 = Administrators
#--------------------------------------------------

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('arg_separator.output', '&amp;');
ini_set('zlib.output_compression_level', 9);
date_default_timezone_set('Europe/Berlin');

define('VERSION', '20110714');

try {
	if (!file_exists('app/models/Main.model.php') ||
					!file_exists('app/controllers/Main.controller.php') ||
					!file_exists('app/controllers/Session.controller.php') ||
					!file_exists('app/controllers/Index.controller.php') ||
					!file_exists('app/controllers/Log.controller.php') ||
					!file_exists('app/helpers/AdvancedException.helper.php') ||
					!file_exists('app/helpers/Section.helper.php') ||
					!file_exists('app/helpers/Helper.helper.php') ||
					!file_exists('lib/smarty/Smarty.class.php')
	)
		throw new Exception('Could not load required classes.');
	else {
		require_once 'app/models/Main.model.php';
		require_once 'app/controllers/Main.controller.php';
		#require_once 'app/controllers/Search.controller.php';
		require_once 'app/controllers/Session.controller.php';
		require_once 'app/controllers/Index.controller.php';
		require_once 'app/controllers/Log.controller.php';
		require_once 'app/helpers/AdvancedException.helper.php';
		require_once 'app/helpers/Section.helper.php';
		require_once 'app/helpers/Helper.helper.php';
		require_once 'lib/smarty/Smarty.class.php';
	}
}
catch (AdvancedException $e) {
	die($e->getMessage());
}

@session_start();

# We avoid the $_REQUEST due to problems with $_COOKIE
$aRequest = array_merge($_POST, $_GET);
$aFiles = isset($_FILES) ? $_FILES : array();
$oIndex = new Index($aRequest, $_SESSION, $aFiles, $_COOKIE);
$oIndex->loadConfig();
$oIndex->setBasicConfiguration();
$oIndex->setSkin();
$oIndex->setLanguage();
$oIndex->loadPlugins();

$aUser = & $oIndex->getActiveUser();

# Check whether we use facebook or CMS data
define('USER_ID', (int) $aUser['id']);
define('USER_PASSWORD', isset($aUser['password']) ? $aUser['password'] : '');

# If we use the facebook plugin and are not logged in, fetch user data
if(USER_ID == 0) {
  $oFacebook = $oIndex->loadFacebookPlugin();
  if ($oFacebook == true)
    $aFacebookData = $oFacebook->getUserData();
}

define('USER_RIGHT', isset($aFacebookData[0]['uid']) ?
								2 :
								(int) $aUser['user_right']);

define('USER_FACEBOOK_ID', isset($aFacebookData[0]['uid']) ?
								$aFacebookData[0]['uid'] :
								'');

define('USER_EMAIL', isset($aFacebookData[0]['email']) ?
								$aFacebookData[0]['email'] :
								$aUser['email']);

define('USER_NAME', isset($aFacebookData[0]['first_name']) ?
								$aFacebookData[0]['first_name'] :
								$aUser['name']);

define('USER_SURNAME', isset($aFacebookData[0]['last_name']) ?
								$aFacebookData[0]['last_name'] :
								$aUser['surname']);

define('USER_FULL_NAME', USER_NAME . ' ' . USER_SURNAME);

# Load cronjob if plugin if enabled
$oIndex->loadCronjob();

# If this is an ajax request, no layout is loaded
$iAjax = isset($_REQUEST['ajax']) ? 1 : 0;
define('AJAX_REQUEST', (int) $iAjax);

define('CURRENT_URL', WEBSITE_URL . $_SERVER['REQUEST_URI']);

echo $oIndex->show();
?>