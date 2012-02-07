<?php

/**
 * Provide many helper methods.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Helper;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Plugin\Bbcode as Bbcode;
use CandyCMS\Plugin\FormatTimestamp as FormatTimestamp;
use PDO;

class Helper {

  /**
   * Display a success message after an action is done.
   *
	 * @static
   * @access public
	 * @param string $sMessage message to provide
	 * @param string $sRedirectTo site to redirect to
   * @return boolean true
   *
   */
  public static function successMessage($sMessage, $sRedirectTo = '') {
    $_SESSION['flash_message']['type']      = 'success';
    $_SESSION['flash_message']['message']   = $sMessage;
    $_SESSION['flash_message']['headline']  = '';
    $_SESSION['flash_message']['show']      = '0';

    if(!empty($sRedirectTo))
      Helper::redirectTo ($sRedirectTo);

    return true;
  }

  /**
   * Display an error message after an action is done.
   *
	 * @static
   * @access public
	 * @param string $sMessage message to provide
	 * @param string $sRedirectTo site to redirect to
   * @return boolean false
   *
   */
  public static function errorMessage($sMessage, $sRedirectTo = '') {
    $_SESSION['flash_message']['type']      = 'error';
    $_SESSION['flash_message']['message']   = $sMessage;
    $_SESSION['flash_message']['headline']  = I18n::get('error.standard');
    $_SESSION['flash_message']['show']			= '0';

    if(!empty($sRedirectTo))
      Helper::redirectTo ($sRedirectTo);

    return false;
  }

  /**
   * Redirect user to a specified page.
   *
	 * @static
   * @access public
	 * @param string $sUrl URL to redirect the user to
   *
   */
  public static function redirectTo($sUrl) {
		header('Location:' . $sUrl);
		exit();
	}

  /**
   * Check if the provided email address is in a correct format.
   *
	 * @static
   * @access public
	 * @param string $sMail email address to check
   * @return boolean true if correct format
   *
   */
  public static function checkEmailAddress($sMail) {
		if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $sMail))
			return true;
	}

  /**
   * Create a random charset.
   *
	 * @static
   * @access public
	 * @param integer $iLength length of the charset
	 * @param boolean $bIntegerOnly create a charset of numbers
   * @return string $sString created random charset
   *
   */
  public static function createRandomChar($iLength, $bIntegerOnly = false) {
		$sChars = 'ABCDEFGHJKLMNOPQRSTUVWXYZabcedfghijkmnopqrstuvwxyz123456789';

		if ($bIntegerOnly == true)
			$sChars = '0123456789';

		$sString = '';
		for ($iI = 1; $iI <= $iLength; $iI++) {
			$iTemp = rand(1, strlen($sChars));
			$iTemp--;
			$sString .= $sChars[$iTemp];
		}

		return $sString;
	}

  /**
   * Create a simple link with provided params.
   *
	 * @static
   * @access public
	 * @param string $sUrl URL to create a link with
	 * @param boolean $bInternal Is this an absolute URL?
   * @return string HTML code with anchor
   *
   */
  public static function createLinkTo($sUrl, $bExternal = false) {
    return $bExternal == true ? '<a href="' . $sUrl . '">' . $sUrl . '</a>' :
            '<a href="' . WEBSITE_URL . '/' . $sUrl . '">' . WEBSITE_URL . '/' . $sUrl . '</a>';
  }

  /**
	 * Return the URL of the user avatar.
	 *
	 * @static
	 * @access public
	 * @param string $sSize avatar size
	 * @param integer $iUserId user ID
	 * @param string $sEmail email address to search gravatar for
   * @param boolean $bUseGravatar do we want to use gravatar?
	 * @return string URL of the avatar
	 *
	 */
  public static function getAvatar($sSize, $iUserId, $sEmail = '', $bUseGravatar = false) {
		$sFilePath = PATH_UPLOAD . '/user/' . $sSize . '/' . $iUserId;
    $sFilePath = Helper::removeSlash($sFilePath);

		if (is_file($sFilePath . '.jpg') && $bUseGravatar == false)
			return '/' . $sFilePath . '.jpg';

		elseif (is_file($sFilePath . '.png') && $bUseGravatar == false)
			return '/' . $sFilePath . '.png';

		elseif (is_file($sFilePath . '.gif') && $bUseGravatar == false)
			return '/' . $sFilePath . '.gif';

		else {
      if (!is_int($sSize))
        $sSize = POPUP_DEFAULT_X;

      $sMail = isset($sEmail) ? $sEmail : WEBSITE_MAIL;
      return 'http://www.gravatar.com/avatar/' . md5($sMail) . '.jpg?s=' . $sSize . '&d=mm';
		}
  }

  /**
	 * Count the file size.
	 *
	 * @static
	 * @access public
	 * @param string $sPath path of the file
	 * @return string size of the file plus ending
	 *
	 */
  public static function getFileSize($sPath) {
		$iSize = @filesize($sPath);

		if ($iSize > 1024 && $iSize < 1048576)
			$sReturn = round(($iSize / 1024), 2) . ' KB';

		elseif ($iSize >= 1048576 && $iSize < 1073741824)
			$sReturn = round(($iSize / 1048576), 2) . ' MB';

		elseif ($iSize >= 1073741824)
			$sReturn = round(($iSize / 1073741824), 2) . ' GB';

		else
			$sReturn = round($iSize, 2) . ' Byte';

		return $sReturn;
	}

	/**
	 * Get the template dir. Check if there are addon files and use them if avaiable.
	 *
	 * @static
	 * @access public
	 * @param string $sDir dir of the templates
	 * @param string $sFile file name of the template
	 * @return string path of the chosen template
	 *
	 */
  public static function getTemplateDir($sDir, $sFile) {
		try {
			# Addons
			if (file_exists('addons/views/' . $sDir . '/' . $sFile . '.tpl') && ALLOW_ADDONS == true)
				return 'addons/views/' . $sDir;

			# Template use
			elseif (file_exists('public/templates/' . PATH_TEMPLATE . '/views/' . $sDir . '/' . $sFile . '.tpl'))
				return 'public/templates/' . PATH_TEMPLATE . '/views/' . $sDir;

			# Standard views
			else {

				if (!file_exists('app/views/' . $sDir . '/' . $sFile . '.tpl'))
					throw new AdvancedException('This template does not exist.');

				else
					return 'app/views/' . $sDir;
			}
		}
		catch (Exception $e) {
			$e->getMessage();
		}
	}

	/**
	 * Get the template type. Check if mobile template is avaiable and return standard if not.
	 *
	 * @static
	 * @access public
	 * @param string $sDir dir of the templates
	 * @param string $sFile file name of the template
	 * @param boolea $bPath show the path
	 * @return string path of the chosen template
	 *
	 */
  public static function getTemplateType($sDir, $sFile, $bPath = true) {
		$sDir = $bPath == true ? $sDir . '/' : '';

		try {
			# Mobile device.
			if (file_exists($sDir . '/' . $sFile . '.mob') && MOBILE === true)
				return $sDir . $sFile . '.mob';

			# Standard template
			else
				return $sDir . $sFile . '.tpl';
		}
		catch (Exception $e) {
			$e->getMessage();
		}
	}

	/**
	 * Get the template file. Check if there is a mobile device.
	 *
	 * @static
	 * @access public
	 * @param string $sDir dir of the templates
	 * @param string $sFile file name of the template
	 * @return string path of the chosen template
	 *
	 */
  public static function getPluginTemplateDir($sDir, $sFile) {
		try {
			# Template
			if (file_exists('public/templates/' . PATH_TEMPLATE . '/views/' . $sDir . '/' . $sFile . '.tpl'))
				return 'public/templates/' . PATH_TEMPLATE . '/views/' . $sDir;

			# Standard views
			else {
				if (!file_exists('plugins/views/' . $sDir . '/' . $sFile . '.tpl'))
					throw new AdvancedException('This template does not exist.');

				else
					return 'plugins/views/' . $sDir;
			}
		}
		catch (Exception $e) {
			$e->getMessage();
		}
	}

	/**
	 * Remove slashes of provided string.
	 *
	 * @static
	 * @access public
	 * @param string $sStr string to remove slashes from
	 * @return string string without slashes
	 *
	 */
  public static function removeSlahes($sStr) {
		$sStr = str_replace('\&quot;', '"', $sStr);
		$sStr = str_replace('\"', '"', $sStr);
		$sStr = str_replace("\'", "'", $sStr);
		return $sStr;
	}

	/**
	 * Check the input to avoid XSS and SQL injections.
	 *
	 * @static
	 * @access public
	 * @param string $sStr string to check
	 * @param boolean $bDisableHTML remove HTML code
	 * @return string cleaned input
	 *
	 */
  public static function formatInput($sStr, $bDisableHTML = true) {
    try {
      if (is_string($sStr) == false && is_int($sStr) == false && $bDisableHTML == true)
        throw new AdvancedException('Input seems not valid.');

      if ($bDisableHTML == true)
        $sStr = htmlspecialchars($sStr);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    return trim($sStr);
  }

	/**
	 * Format the linux timestamp into a user friendly format.
	 *
	 * If the "FormatTimestamp" plugin is enabled, load plugin and do some advanced work.
	 *
	 * Options:
	 * 0 = default dates
	 * 1 = date only
	 * 2 = time only
	 *
	 * @static
	 * @access public
	 * @param integer $iTime timestamp
	 * @param integer $iOptions options see above
	 * @see plugins/controllers/FormatTimestamp.controller.php
	 * @return string formatted timestamp
	 *
	 */
  public static function formatTimestamp($iTime, $iOptions = 0) {
    # Fallback for unit testing
    if(!constant('WEBSITE_LOCALE'))
      define('WEBSITE_LOCALE', 'de_DE');

		# Set active locale
		setlocale(LC_ALL, WEBSITE_LOCALE);

		if (class_exists('\CandyCMS\Plugin\FormatTimestamp') == true) {
			$oDate = new FormatTimestamp();
			return $oDate->getDate($iTime, $iOptions);
		}
		else {
			if ($iOptions == 1)
				return strftime(DEFAULT_DATE_FORMAT, $iTime);

			elseif($iOptions == 2)
				return strftime(DEFAULT_TIME_FORMAT, $iTime);

			else
				return strftime(DEFAULT_DATE_FORMAT . ', ' . DEFAULT_TIME_FORMAT, $iTime);
		}
	}

	/**
	 * Format HTML output .
	 *
	 * If the "Bbcode" plugin is enabled, load plugin do some advanced work.
	 *
	 * @static
	 * @access public
	 * @param string $sStr string to format
	 * @param string $sHighlight enable string highlighting
	 * @see plugins/controllers/Bbcode.controller.php
	 * @return string formatted string
	 *
	 */
  public static function formatOutput($sStr, $sHighlight = false) {
    $sStr = trim($sStr);
    $sStr = preg_replace('/\S{500}/', '\0 ', $sStr);

    # Remove Slashes
    $sStr = Helper::removeSlahes($sStr);

    # Highlight string
    if ($sHighlight == true)
      $sStr = str_ireplace($sHighlight, '<mark>' . $sHighlight . '</mark>', $sStr);

    if (class_exists('\CandyCMS\Plugin\Bbcode') == true) {
      $oBbcode = new Bbcode();
      return $oBbcode->getFormatedText($sStr);
    }
    else
      return $sStr;
  }

	/**
	 * Fetch the last entry from database.
	 *
	 * @static
	 * @access public
	 * @param string $sTable table to fetch data from
	 * @return integer latest ID
	 *
	 */
  public static function getLastEntry($sTable) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->query(" SELECT id FROM " . SQL_PREFIX . $sTable . " ORDER BY id DESC LIMIT 1");
      $aRow = $oQuery->fetch();
      return $aRow['id'];
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

	/**
	 * Replace non alphachars with predefined values.
	 *
	 * @static
	 * @access public
	 * @param string $sStr string to replace chars
	 * @return string string with formatted chars
	 *
	 */
  public static function replaceNonAlphachars($sStr) {
    $sStr = str_replace('"', '', $sStr);
    $sStr = str_replace('Ä', 'Ae', $sStr);
    $sStr = str_replace('ä', 'ae', $sStr);
    $sStr = str_replace('Ü', 'Ue', $sStr);
    $sStr = str_replace('ü', 'ue', $sStr);
    $sStr = str_replace('Ö', 'Oe', $sStr);
    $sStr = str_replace('ö', 'oe', $sStr);
    $sStr = str_replace('ß', 'ss', $sStr);
    $sStr = str_replace(' ', '_', $sStr);
    $sStr = strtolower($sStr);
    return $sStr;
  }

  /**
   * Removes first slash at dirs.
   *
   * @static
   * @access public
   * @param string $sStr
   * @return string without slash
   *
   */
  public static function removeSlash($sStr, $bReverse = false) {
    return substr($sStr, 0, 1) == '/' ? substr($sStr, 1) : $sStr;
  }

  /**
   * Adds slash for dirs.
   *
   * @static
   * @access public
   * @param string $sStr
   * @return string with slash
   *
   */
  public static function addSlash($sStr) {
    return substr($sStr, 0, 1) == '/' ? $sStr : '/' . $sStr;
  }
}