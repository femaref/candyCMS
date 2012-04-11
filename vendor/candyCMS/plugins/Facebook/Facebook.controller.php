<?php

/**
 * This plugin gives users the opportunity to comment without registration.
 *
 * NOTE: This plugin slows down your page rapidly by sending a request to facebook each load!
 * If you don't need it, keep it disabled.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Plugins;

use CandyCMS\Core\Helpers\AdvancedException;
use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\SmartySingleton;
use Facebook;

final class FacebookCMS extends Facebook {

  /**
   * Identifier for Template Replacements
   *
   * @var contant
   *
   */
  const IDENTIFIER = 'facebook';

  /**
   * Get user data.
   *
   * @final
   * @access public
   * @param string $sKey
   * @return array
   *
   */
  public final function getUserData($sKey = '') {
    if ($this->getAccessToken()) {
      try {
        $iUid = $this->getUser();
        $aApiCall = array(
            'method'  => 'users.getinfo',
            'uids'    => $iUid,
            'fields'  => 'uid, first_name, last_name, profile_url, pic, pic_square_with_logo, locale, email, website'
        );

        $aData = $this->api($aApiCall);
        return !empty($sKey) ? $aData[$sKey] : $aData;
      }
      catch (AdvancedException $e) {
        AdvancedException::reportBoth($e->getMessage());
        exit('Error');
      }
    }
  }

  /**
   *
   * Get the Facebook avatar Info for all given Uids, load from cache, if cache is specified
   *
   * @final
   * @access public
   * @param array $aUids
   * @param array $aSession
   * @return array
   *
   */
  public final function getUserAvatars($aUids, &$aSession = null) {
    try {
      $aFacebookAvatarCache = $aSession ? $aSession['facebookavatars'] : array();

      # only query for ids we don't know
      $sUids = '';
      foreach ($aUids as $sUid)
        if (!isset($aFacebookAvatarCache[$sUid]))
          $sUids .= $sUid . ',';

      # do the facebook call with all new $sUids
      if (strlen($sUids) > 1) {
        $aApiCall = array(
            'method' => 'users.getinfo',
            'uids' => substr($sUids, 0, -1),
            'fields' => 'pic_square_with_logo, profile_url'
        );

        $aFacebookAvatars = $this->api($aApiCall);

        # we read the response and add to the cache
        foreach ($aFacebookAvatars as $aFacebookAvatar) {
          $sUid = $aFacebookAvatar['uid'];
          $aFacebookAvatarCache[$sUid]['pic_square_with_logo'] = $aFacebookAvatar['pic_square_with_logo'];
          $aFacebookAvatarCache[$sUid]['profile_url']          = $aFacebookAvatar['profile_url'];
        }
      }

      #we return the cache
      return $aFacebookAvatarCache;
    }
    catch (AdvancedException $e) {
      AdvancedException::reportBoth($e->getMessage());
      exit('Error');
    }
  }

  /**
   * Show FB JavaScript code.
   *
   * @final
   * @access public
   * @param array $aRequest
   * @param array $aSession
   * @return string HTML
   *
   */
  public final function show(&$aRequest, &$aSession) {
    $sTemplateDir   = Helper::getPluginTemplateDir('facebook', 'show');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'show');

    $oSmarty = SmartySingleton::getInstance();
    $oSmarty->setTemplateDir($sTemplateDir);
    $oSmarty->setCaching(SmartySingleton::CACHING_LIFETIME_SAVED);

    $sCacheId = WEBSITE_MODE . '|plugins|' . WEBSITE_LOCALE . '|facebook';
    if (!$oSmarty->isCached($sTemplateFile, $sCacheId)) {
      $oSmarty->assign('PLUGIN_FACEBOOK_APP_ID', defined('PLUGIN_FACEBOOK_APP_ID') ? PLUGIN_FACEBOOK_APP_ID : '');
      $oSmarty->assign('WEBSITE_LOCALE', WEBSITE_LOCALE);
    }

    return $oSmarty->fetch($sTemplateFile, $sCacheId);
  }
}