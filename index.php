<?php

/**
 * Website entry.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @version 2.0
 * @since 1.0
 */


namespace CandyCMS;

use CandyCMS\Controller\Index as Index;

/**
 * Set how to handle PHP error messages.
 */
error_reporting(E_ALL);

/**
 * Override separator due to W3C compatibility.
 */
ini_set('arg_separator.output', '&amp;');

/**
 * Compress output.
 */
ini_set('zlib.output_compression', "On");
ini_set('zlib.output_compression_level', 9);

/**
 * Set standard timezone for PHP5.
 */
date_default_timezone_set('Europe/Berlin');

/**
 * Current version we are working with.
 */
define('VERSION', '20111114');

/**
 * Display error messages when in development mode.
 */
ini_set('display_errors', 1);

/*
 * Load main classes.
 */
try {
  if (!file_exists('app/models/Main.model.php') ||
      !file_exists('app/controllers/Main.controller.php') ||
      !file_exists('app/controllers/Session.controller.php') ||
      !file_exists('app/controllers/Index.controller.php') ||
      !file_exists('app/controllers/Log.controller.php') ||
      !file_exists('app/helpers/AdvancedException.helper.php') ||
      !file_exists('app/helpers/Section.helper.php') ||
      !file_exists('app/helpers/Helper.helper.php') ||
      !file_exists('app/helpers/I18n.helper.php') ||
      !file_exists('lib/smarty/Smarty.class.php')
  )
    throw new \Exception('Could not load required classes.');
  else {
    require_once 'app/models/Main.model.php';
    require_once 'app/controllers/Main.controller.php';
    require_once 'app/controllers/Session.controller.php';
    require_once 'app/controllers/Index.controller.php';
    require_once 'app/controllers/Log.controller.php';
    require_once 'app/helpers/AdvancedException.helper.php';
    require_once 'app/helpers/Section.helper.php';
    require_once 'app/helpers/Helper.helper.php';
    require_once 'app/helpers/I18n.helper.php';
    require_once 'lib/smarty/Smarty.class.php';
  }
}
catch (\CandyCMS\Helper\AdvancedException $e) {
  die($e->getMessage());
}

# If this is an ajax request, no layout is loaded
$iAjax = isset($_REQUEST['ajax']) ? 1 : 0;
define('AJAX_REQUEST', (int) $iAjax);

# Clear cache if needed
define('CLEAR_CACHE', isset($_REQUEST['clearcache']) ? true : false);

@session_start();

# Initialize software
$oIndex = new Index(array_merge($_GET, $_POST), $_SESSION, $_FILES, $_COOKIE);

$oIndex->getConfigFiles(array('Candy', 'Plugins', 'Facebook', 'Mailchimp'));
$oIndex->getPlugins(ALLOW_PLUGINS);
$oIndex->getLanguage();
$oIndex->getCronjob();
$oIndex->getFacebookExtension();
$oIndex->setUser();
$oIndex->setTemplate();

# Define current url
define('CURRENT_URL', WEBSITE_URL . $_SERVER['REQUEST_URI']);

# If we are on a productive enviroment, make sure that we can't override the system.
# *********************************************
if (is_dir('install') && WEBSITE_DEV == false)
  exit('Please install software via <strong>install/</strong> and delete the folder afterwards.');

# Also disable tools to avoid system crashes.
# *********************************************
if (is_dir('tools') && WEBSITE_DEV == false)
  exit('Please delete the tools folder.');

# Disable tests on productive system.
# *********************************************
if (is_file('tests.php') && WEBSITE_DEV == false)
  exit('Please delete the tests enviroment (tests.php).');

# Do we have a mobile device?
# *********************************************
$sUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$bMobile    = preg_match('/Opera Mini/i', $sUserAgent) ||
              preg_match('/Symb/i', $sUserAgent) ||
              preg_match('/Windows CE/i', $sUserAgent) ||
              preg_match('/IEMobile/i', $sUserAgent) ||
              preg_match('/iPhone/i', $sUserAgent) ||
              preg_match('/iPad/i', $sUserAgent) ||
              preg_match('/iPod/i', $sUserAgent) ||
              preg_match('/Blackberry/i', $sUserAgent) ||
              preg_match('/Android/i', $sUserAgent) ?
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

# Print out HTML
echo $oIndex->show();
unset($_SESSION)

?>