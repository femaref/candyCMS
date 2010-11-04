<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# This plugin gives users the opportunity to comment without registration.

require_once 'lib/facebook/facebook.php';

class FacebookCMS extends Facebook {

	/*public function getSessionStatus() {
		# TODO: Put into template
		if ($this->getSession())
			return '<a href="' . $this->getLogoutUrl() . '">' . LANG_GLOBAL_LOGOUT . '</a>';

		else
			return '<a href="' . $this->getLoginUrl(array('req_perms' => 'email')) . '">' . LANG_GLOBAL_LOGIN . '</a>';
	}*/

	public function getConnectButton() {
		$oSmarty = new Smarty();

		$oSmarty->assign('_url_', $this->getLoginUrl(array('req_perms' => 'email')));
		$oSmarty->assign('lang_login', LANG_GLOBAL_LOGIN);

		$oSmarty->cache_dir = CACHE_DIR;
		$oSmarty->compile_dir = COMPILE_DIR;
		$oSmarty->template_dir = 'public/skins/_plugins/facebook';
		return $oSmarty->fetch('button.tpl');
	}

	public function getUserData($sKey = '') {
		if ($this->getSession()) {
			try {
				$iUid = $this->getUser();
				$aApiCall = array(
						'method' => 'users.getinfo',
						'uids' => $iUid,
						'fields' => 'uid, first_name, last_name, pic_square, pic_big, pic, sex, locale, email, website'
				);

				$aData = $this->api($aApiCall);
				return!empty($sKey) ? $aData[$sKey] : $aData;
			}
			catch (AdvancedException $e) {
				die($e->getMessage());
			}
		}
	}

	public function getUserLocale() {
		return $this->getUserData('locale');
	}
}