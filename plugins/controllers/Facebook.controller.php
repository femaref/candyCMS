<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# This plugin gives users the opportunity to comment without registration.
# NOTE: This plugin slows down your page rapidly by sending a request to facebook each load!
# If you don't need it, keep it disabled.

namespace CandyCMS\Plugin;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use Smarty;

require_once 'lib/facebook/facebook.php';

class FacebookCMS extends Facebook {

  # @Override because of OAUTH - Bug
  protected function _restserver($params) {
    // generic application level parameters
    $params['api_key'] = $this->getAppId();
    $params['format'] = 'json-strings';

    $result = json_decode($this->_oauthRequest(
                    $this->getApiUrl($params['method']), $params
            ), true);

    // results are returned, errors are thrown
    if (is_array($result) && isset($result['error_code'])) {
      #throw new FacebookApiException($result);
    }
    return $result;
  }

  public function getConnectButton() {
    $oSmarty = new Smarty();

    $oSmarty->assign('_url_', $this->getLoginUrl(array('req_perms' => 'email', 'next'=> CURRENT_URL)));
    $oSmarty->assign('lang_login', LANG_GLOBAL_LOGIN);

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
    $oSmarty->template_dir = 'public/templates/_plugins/facebook';
    return $oSmarty->fetch('button.tpl');
  }

  public function getUserData($sKey = '') {
    if ($this->getAccessToken()) {
      try {
        $iUid = $this->getUser();
        $aApiCall = array(
            'method' => 'users.getinfo',
            'uids' => $iUid,
            'fields' => 'uid, first_name, last_name, profile_url, pic, pic_square_with_logo, locale, email, website'
        );

        $aData = $this->api($aApiCall);
        return !empty($sKey) ? $aData[$sKey] : $aData;
      }
      catch (AdvancedException $e) {
        die($e->getMessage());
      }
    }
  }

  public function getUserAvatar($sUids) {
    try {
      $aApiCall = array(
          'method' => 'users.getinfo',
          'uids' => $sUids,
          'fields' => 'pic_square_with_logo, profile_url'
      );

      return $this->api($aApiCall);
    }
    catch (AdvancedException $e) {
      die($e->getMessage());
    }
  }

  public function getUserLocale() {
    return $this->getUserData('locale');
  }
}