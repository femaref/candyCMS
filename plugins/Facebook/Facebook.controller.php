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

namespace CandyCMS\Plugin;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use Facebook;
use Smarty;

require_once PATH_STANDARD . '/lib/facebook/facebook.php';

final class FacebookCMS extends Facebook {

	/**
	 *
	 * @param type $sKey
	 * @return type
	 *
	 */
	public final function getUserData($sKey = '') {
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

	/**
	 *
	 * @param type $sUids
	 * @return type
	 *
	 */
	public final function getUserAvatar($sUids) {
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

	public final function show() {

	}
}