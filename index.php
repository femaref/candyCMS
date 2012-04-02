<?php

/**
 * Website entry.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @version 2.0
 * @since 1.0
 *
 */

namespace CandyCMS;

# Override separator due to W3C compatibility.
ini_set('arg_separator.output', '&amp;');

# Compress output.
ini_set('zlib.output_compression', "On");
ini_set('zlib.output_compression_level', 9);

# Set standard timezone for PHP5.
date_default_timezone_set('Europe/Berlin');

# Current version we are working with.
define('VERSION', '20111114');

# Define a standard path
define('PATH_STANDARD', dirname(__FILE__));

# Initialize software
try {
  require PATH_STANDARD . '/config/Candy.inc.php';
  require PATH_STANDARD . '/app/controllers/Index.controller.php';
}
catch (Exception $e) {
  die($e->getMessage());
}

# Override the system variables in development mode.
if (WEBSITE_MODE == 'test') {
  ini_set('display_errors', 0);
	ini_set('error_reporting', 0);
	ini_set('log_errors', 1);
}
else {
  ini_set('display_errors', 1);
	ini_set('error_reporting', 1);
	ini_set('log_errors', 1);
}

# Define current url
define('CURRENT_URL', isset($_SERVER['REQUEST_URI']) ? WEBSITE_URL . $_SERVER['REQUEST_URI'] : WEBSITE_URL);

# Start user session.
@session_start();

# Do we have a mobile device?
# *********************************************
if(isset($_SERVER['HTTP_USER_AGENT'])) {
	$bMobile    = preg_match('/Opera Mini/i', $_SERVER['HTTP_USER_AGENT']) ||
								preg_match('/Symb/i', $_SERVER['HTTP_USER_AGENT']) ||
								preg_match('/Windows CE/i', $_SERVER['HTTP_USER_AGENT']) ||
								preg_match('/IEMobile/i', $_SERVER['HTTP_USER_AGENT']) ||
								preg_match('/iPhone/i', $_SERVER['HTTP_USER_AGENT']) ||
								preg_match('/iPod/i', $_SERVER['HTTP_USER_AGENT']) ||
								preg_match('/Blackberry/i', $_SERVER['HTTP_USER_AGENT']) ||
								preg_match('/Android/i', $_SERVER['HTTP_USER_AGENT']) ?
								true :
								false;
}
else
	$bMobile = false;

# Allow mobile access
if(!isset($_REQUEST['mobile']))
  $_SESSION['mobile'] = isset($_SESSION['mobile']) ? $_SESSION['mobile'] : true;

# Override current session if there is a request.
else
  $_SESSION['mobile'] = (boolean) $_REQUEST['mobile'];

define('MOBILE', $bMobile === true && $_SESSION['mobile'] == true ? true : false);
define('MOBILE_DEVICE', $bMobile);

# page called by crawler?
define('CRAWLER', defined('CRAWLERS') ?
              preg_match('/' . CRAWLERS . '/', $_SERVER['HTTP_USER_AGENT']) > 0 :
              false);

# check for addons?
define('ADDON_CHECK', ALLOW_ADDONS === true || WEBSITE_MODE == 'development' || WEBSITE_MODE == 'test');

# Initialize software
$oIndex = new \CandyCMS\Controller\Index(array_merge($_GET, $_POST), $_SESSION, $_FILES, $_COOKIE);

# If we are on a productive enviroment, make sure that we can't override the system.
if (is_dir('install') && WEBSITE_MODE == 'production')
  exit('Please install software via <strong>install/</strong> and delete the folder afterwards.');

# Also disable tools to avoid system crashes.
if (is_dir('tools') && WEBSITE_MODE == 'production')
  exit('Please delete the tools folder.');

# Disable tests on productive system.
if (is_dir('tests') && WEBSITE_MODE == 'production')
  exit('Please delete the tests enviroment (tests.php).');

# Print out HTML
echo $oIndex->show();

?>