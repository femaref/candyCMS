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

use CandyCMS\Controller\Index as Index;

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

# If this is an ajax request, no layout will be loaded.
define('AJAX_REQUEST', isset($_REQUEST['ajax']) && !empty($_REQUEST['ajax']) ? true : false);

# Clear cache if wanted.
define('CLEAR_CACHE', isset($_REQUEST['clearcache']) || isset($_REQUEST['template']) ? true : false);

# Start user session.
@session_start();

# Do we have a mobile device?
# *********************************************
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

# Allow mobile access
if(!isset($_REQUEST['mobile']))
  $_SESSION['mobile'] = isset($_SESSION['mobile']) ? $_SESSION['mobile'] : true;

# Override current session if there is a request.
else
  $_SESSION['mobile'] = (boolean) $_REQUEST['mobile'];

# Spread this information.
define('MOBILE', $bMobile === true && $_SESSION['mobile'] == true ? true : false);
define('MOBILE_DEVICE', $bMobile);

# Initialize software
# @todo try / catch
require PATH_STANDARD . '/config/Candy.inc.php';
require PATH_STANDARD . '/app/controllers/Index.controller.php';

# Define current url
define('CURRENT_URL', WEBSITE_URL . isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');

# Initialize software
$oIndex = new Index(array_merge($_GET, $_POST), $_SESSION, $_FILES, $_COOKIE);

# Redirect to landing page if we got no valid request.
if($_SERVER['HTTP_HOST'] !== WEBSITE_URL && WEBSITE_MODE == 'production' && $_SERVER['REQUEST_URI'] == '/')
  \CandyCMS\Helper\Helper::redirectTo(WEBSITE_URL . '/' . WEBSITE_LANDING_PAGE);

# Override the system variables in development mode.
if (WEBSITE_MODE == 'development') {
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
}
else {
  ini_set('display_errors', 0);
  error_reporting(E_NONE);
}

# If we are on a productive enviroment, make sure that we can't override the system.
# *********************************************
if (is_dir('install') && WEBSITE_MODE == 'production')
  exit('Please install software via <strong>install/</strong> and delete the folder afterwards.');

# Also disable tools to avoid system crashes.
# *********************************************
if (is_dir('tools') && WEBSITE_MODE == 'production')
  exit('Please delete the tools folder.');

# Disable tests on productive system.
# *********************************************
if (is_file('tests.php') && WEBSITE_MODE == 'production')
  exit('Please delete the tests enviroment (tests.php).');

# Print out HTML
echo $oIndex->show();

?>