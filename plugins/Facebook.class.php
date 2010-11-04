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

	public function getSessionStatus() {
		if ($this->getSession())
			return '<a href="' . $this->getLogoutUrl() . '">' . LANG_GLOBAL_LOGIN . '</a>';

		else
			return '<a href="' . $this->getLoginUrl() . '">' . LANG_GLOBAL_LOGOUT . '</a>';
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

	public function getUserLanguage() {
		return $this->getUserData('locale');
	}
}