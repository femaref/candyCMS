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
    $_SESSION['flash_message'] = array(
        'type'    => 'success',
        'message' => $sMessage,
        'headline'=> '');

    return !empty($sRedirectTo) ? Helper::redirectTo ($sRedirectTo) : true;
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
    $_SESSION['flash_message'] = array(
        'type'    => 'error',
        'message' => $sMessage,
        'headline'=> I18n::get('error.standard'));

    return !empty($sRedirectTo) ? Helper::redirectTo ($sRedirectTo) : false;
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
   * @return boolean
   *
   */
  public static function checkEmailAddress($sMail) {
    return preg_match("/^([a-zA-Z0-9])+(\.?[a-zA-Z0-9_-]+)*@([a-zA-Z0-9_-]+\.)+[a-zA-Z]{2,6}$/", $sMail) ? true : false;
	}

  /**
   * Create a random charset.
   *
	 * @static
   * @access public
	 * @param integer $iLength length of the charset
	 * @param boolean $bSpeakable charset is speakable by humans (every second char is a vocal)
   * @return string $sString created random charset
   *
   */
  public static function createRandomChar($iLength, $bSpeakable = false) {
    $sChars   = 'BCDFGHJKLMNPQRSTVWXZbcdfghjkmnpqrstvwxz';
    $sVocals  = 'AaEeiOoUuYy';
    $sNumbers = '123456789';

    $sString = '';

    if ($bSpeakable === false) {
      $sChars .= $sVocals . $sNumbers;
      for ($iI = 1; $iI <= $iLength; $iI++) {
        $iTemp = rand(0, strlen($sChars) - 1);
        $sString .= $sChars[$iTemp];
      }
    }
    else {
      $iI = 1;

      while ($iI < $iLength) {
        if ($iI % 5 == 0) {
          $sString .= $sNumbers[rand(0, strlen($sNumbers) - 1)];
          $iI++;
        }
        else {
          # Vocal
          $sString .= $sChars[rand(0, strlen($sChars) - 1)];

          # If we have more chars to put, use a vocal, otherwise use numbers to fill the string
          if ($iI < $iLength - 1)
            $sString .= $sVocals[rand(0, strlen($sVocals) - 1)];

          elseif ($iI < $iLength)
            $sString .= $sNumbers[rand(0, strlen($sNumbers) - 1)];

          else
            $iI--;

          $iI += 2;
        }
      }
    }

    return $sString;
  }

  /**
   * Create a simple link with provided params.
   *
	 * @static
   * @access public
	 * @param string $sUrl URL to create a link with
	 * @param boolean $bExternal display a link to an external / absolute URL?
   * @return string HTML code with anchor
   *
   */
  public static function createLinkTo($sUrl, $bExternal = false) {
		return	$bExternal === true ?
						'<a href="' . $sUrl . '" rel="external">' . $sUrl . '</a>' :
						'<a href="' . WEBSITE_URL . '/' . $sUrl . '">' . WEBSITE_URL . '/' . $sUrl . '</a>';
	}

  /**
	 * Return the URL of the user avatar.
	 *
	 * @static
	 * @access public
	 * @param integer $iSize avatar size
	 * @param integer $iUserId user ID
	 * @param string $sEmail email address to search gravatar for
   * @param boolean $bUseGravatar do we want to use gravatar?
	 * @return string URL of the avatar
	 *
	 */
  public static function getAvatar($iSize, $iUserId, $sEmail = '', $bUseGravatar = false) {
    $sFilePath = Helper::removeSlash(PATH_UPLOAD . '/user/' . $iSize . '/' . $iUserId);

		if ($bUseGravatar == false && file_exists($sFilePath . '.jpg'))
			return '/' . $sFilePath . '.jpg';

		elseif ($bUseGravatar == false && file_exists($sFilePath . '.png'))
			return '/' . $sFilePath . '.png';

		elseif ($bUseGravatar == false && file_exists($sFilePath . '.gif'))
			return '/' . $sFilePath . '.gif';

		else {
      if (!is_int($iSize))
        $iSize = POPUP_DEFAULT_X;

      return 'http://www.gravatar.com/avatar/' . md5($sEmail) . '.jpg?s=' . $iSize . '&d=mm';
		}
  }

  /**
   * Add the avatar_* entries to $aData
   *
   * @static
   * @access public
   * @param array $aData array of userdata
   * @param integer $iUserId user ID
   * @param string $sEmail email address to search gravatar for
   * @param boolean $bUseGravatar do we want to use gravatar?
   * @return array $aData with all avatarURLs added
   *
   */
  public static function createAvatarURLs(&$aData, $iUserId, $sEmail, $bUseGravatar = false) {
    $aData['avatar_32']     = Helper::getAvatar(32, $iUserId, $sEmail, $bUseGravatar);
    $aData['avatar_64']     = Helper::getAvatar(64, $iUserId, $sEmail, $bUseGravatar);
    $aData['avatar_100']    = Helper::getAvatar(100, $iUserId, $sEmail, $bUseGravatar);
    $aData['avatar_popup']  = Helper::getAvatar('popup', $iUserId, $sEmail, $bUseGravatar);

    return $aData;
  }

  /**
	 * Count the file size.
	 *
	 * @static
	 * @access public
	 * @param string $sPath path of the file
	 * @return string size of the file plus hardcoded ending
	 *
	 */
  public static function getFileSize($sPath) {
    $iSize = filesize(Helper::removeSlash($sPath));

    if ($iSize > 1024 && $iSize < 1048576)
      return round(($iSize / 1024), 2) . ' KB';

    elseif ($iSize >= 1048576 && $iSize < 1073741824)
      return round(($iSize / 1048576), 2) . ' MB';

    elseif ($iSize >= 1073741824)
      return round(($iSize / 1073741824), 2) . ' GB';

    elseif($iSize > 0)
      return round($iSize, 2) . ' Byte';

    #else
    #  throw new AdvancedException('File does not exist:' . $sPath);
  }

	/**
	 * Get the template dir. Check if there are addon files and use them if available.
	 *
	 * @static
	 * @access public
	 * @param string $sFolder dir of the templates
	 * @param string $sFile file name of the template
	 * @return string path of the chosen template
	 *
	 */
  public static function getTemplateDir($sFolder, $sFile) {
		try {
			# Addons
			if (file_exists(PATH_STANDARD . '/addons/views/' . $sFolder . '/' . $sFile . '.tpl') &&
							(ALLOW_ADDONS === true || WEBSITE_MODE == 'development' || WEBSITE_MODE == 'test'))
				return PATH_STANDARD . '/addons/views/' . $sFolder;

			# Template use
			elseif (file_exists(PATH_STANDARD . '/public/templates/' . PATH_TEMPLATE . '/views/' . $sFolder . '/' . $sFile . '.tpl'))
				return PATH_STANDARD . '/public/templates/' . PATH_TEMPLATE . '/views/' . $sFolder;

			# Standard views
			else {
				if (!file_exists(PATH_STANDARD . '/app/views/' . $sFolder . '/' . $sFile . '.tpl'))
					throw new AdvancedException('This template does not exist.');

				else
					return PATH_STANDARD . '/app/views/' . $sFolder;
			}
		}
		catch (AdvancedException $e) {
			$e->getMessage();
		}
	}

	/**
	 * Get the template type. Check if mobile template is available and return standard if not.
	 *
	 * @static
	 * @access public
	 * @param string $sDir dir of the templates
	 * @param string $sFile file name of the template
	 * @return string path of the chosen template
	 *
	 */
  public static function getTemplateType($sDir, $sFile) {
		try {
			# Mobile device.
			if (file_exists($sDir . '/' . $sFile . '.mob') && MOBILE === true)
				return $sFile . '.mob';

			# Standard template
			else
				return $sFile . '.tpl';
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
	 * @param string $sFolder dir of the templates
	 * @param string $sFile file name of the template
	 * @return string path of the chosen template
	 *
	 */
  public static function getPluginTemplateDir($sFolder, $sFile) {
		try {
			# Template
			if (file_exists(PATH_STANDARD . '/public/templates/' . PATH_TEMPLATE . '/views/' . $sFolder . '/' . $sFile . '.tpl'))
				return PATH_STANDARD . '/public/templates/' . PATH_TEMPLATE . '/views/' . $sFolder;

			# Standard views
			else {
				if (!file_exists(PATH_STANDARD . '/plugins/' . ucfirst($sFolder) . '/views/' . $sFile . '.tpl'))
					throw new AdvancedException('This template does not exist.');

				else
					return PATH_STANDARD . '/plugins/' . ucfirst($sFolder) . '/views';
			}
		}
		catch (Exception $e) {
			$e->getMessage();
		}
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

      if ($bDisableHTML === true)
        $sStr = & htmlspecialchars($sStr);
    }
    catch (AdvancedException $e) {
      AdvancedException::reportBoth($e->getMessage());
    }

    # Fix quotes to avoid problems with inputs
    return trim(str_replace('"', "&quot;", $sStr));
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
    if(!defined('WEBSITE_LOCALE'))
      define('WEBSITE_LOCALE', 'en_US');

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
	 * @param string $sHighlight string to highlight
	 * @see plugins/controllers/Bbcode.controller.php
	 * @return string $sStr formatted string
	 *
	 */
  public static function formatOutput($sStr, $sHighlight = '') {
    if (!empty($sHighlight))
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
			$oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB . '_' . WEBSITE_MODE, SQL_USER, SQL_PASSWORD);
			$oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$oQuery = $oDb->query("SELECT id FROM " . SQL_PREFIX . $sTable . " ORDER BY id DESC LIMIT 1");
			$aRow = $oQuery->fetch();
			$oDb = null;

			return $aRow['id'];
		}
		catch (AdvancedException $e) {
			AdvancedException::reportBoth('0103 - ' . $e->getMessage());
			exit('SQL error.');
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

		# Remove non alpha chars
		$sStr = preg_replace("/[^a-zA-Z0-9\s]/", '', $sStr);

		# Remove spaces
    $sStr = str_replace(' ', '_', $sStr);

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
  public static function removeSlash($sStr) {
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