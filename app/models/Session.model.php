<?php

/**
 * Handle all blog SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Model;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\Page as Page;
use PDO;

class Session extends Main {

  /**
   * Fetch all user data of active session.
   *
   * @static
   * @access public
   * @return array $aResult user data
   * @see app/controllers/Index.controller.php
   */
  public static function getUserDataBySession() {
    if (empty(parent::$_oDbStatic))
      parent::_connectToDatabase();

    try {
      $oQuery = parent::$_oDbStatic->prepare("SELECT
                                                u.*
                                              FROM
                                                " . SQL_PREFIX . "users AS u
																							LEFT JOIN
																								" . SQL_PREFIX . "sessions AS s
																							ON
																								u.id = s.user_id
                                              WHERE
                                                s.session = :session_id
                                              AND
                                                s.ip = :ip
                                              LIMIT
                                                1");

      $oQuery->bindParam('session_id', session_id(), PDO::PARAM_STR);
      $oQuery->bindParam('ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $bReturn = $oQuery->execute();

      if ($bReturn === false)
        $this->destroy();

      return $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      parent::$_oDbStatic->rollBack();
    }
  }

	/**
	 * Return aggregated data.
	 *
	 * @access public
	 * @return array $this->_aData
	 */
  public function getData() {
    return $this->_aData;
  }

  /**
   * Create a user session.
   *
   * @access public
	 * @param array $aUser optional user data.
   * @return boolean status of query
   */
  public function create($aUser = '') {
		require_once 'app/models/User.model.php';

		if (empty($aUser)) {
			$oModel = new User($this->_aRequest, $this->_aSession);
			$aUser	= $oModel->getLoginData();
		}

    # User did verify and has id, so log in!
    if (isset($aUser['id']) && !empty($aUser['id']) && empty($aUser['verification_code'])) {
			try {
				$oQuery = $this->_oDb->prepare("INSERT INTO
																					" . SQL_PREFIX . "sessions
																					(	user_id,
																						session,
																						ip,
																						date)
																				VALUES
																					( :user_id,
																						:session,
																						:ip,
																						:date)");

				$oQuery->bindParam('user_id', $aUser['id'], PDO::PARAM_INT);
				$oQuery->bindParam('session', session_id(), PDO::PARAM_STR);
				$oQuery->bindParam('ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
				$oQuery->bindParam('date', time(), PDO::PARAM_INT);

				return $oQuery->execute();
			}
			catch (AdvancedException $e) {
				$this->_oDb->rollBack();
			}
		}
	}

  /**
   * Resend password of verification code.
   *
   * @access public
   * @param string $sNewPasswordSecure
   * @return boolean status of query
	 * @todo move to user model
   */
  public function createResendActions($sNewPasswordSecure = '') {
    require_once 'app/controllers/Mail.controller.php';
    $bResult = false;

    if ($this->_aRequest['action'] == 'resendpassword') {
      try {
        $oQuery = $this->_oDb->prepare("SELECT name FROM " . SQL_PREFIX . "users WHERE email = :email");
        $oQuery->bindParam(':email', Helper::formatInput($this->_aRequest['email']), PDO::PARAM_STR);
        $bResult = $oQuery->execute();

        $this->_aData = $oQuery->fetch(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

      if (empty($this->_aData['name']) || $bResult == false)
        return false;

      else {
        # Set new password
        try {
          $oQuery = $this->_oDb->prepare("UPDATE " . SQL_PREFIX . "users SET password = :password WHERE email = :email");
          $oQuery->bindParam(':password', $sNewPasswordSecure, PDO::PARAM_STR);
          $oQuery->bindParam(':email', Helper::formatInput($this->_aRequest['email']), PDO::PARAM_STR);

          return $oQuery->execute();
        }
        catch (AdvancedException $e) {
          $this->_oDb->rollBack();
        }
      }
    }
    elseif ($this->_aRequest['action'] == 'resendverification') {
      try {
        $oQuery = $this->_oDb->prepare("SELECT name, verification_code FROM " . SQL_PREFIX . "users WHERE email = :email");
        $oQuery->bindParam(':email', Helper::formatInput($this->_aRequest['email']), PDO::PARAM_STR);
        $bResult = $oQuery->execute();

        $this->_aData = $oQuery->fetch(PDO::FETCH_ASSOC);

        if (empty($this->_aData['verification_code']) || $bResult == false)
          return false;
        else
          return $bResult;
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    }
    else
      return false;
  }

  /**
   * Destroy a user session and logout.
   *
   * @access public
   * @return boolean status of query
   */
  public function destroy() {
    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "sessions
                                      SET
                                        session = :session_null
                                      WHERE
                                        session = :session_id");

      $sNull = 'NULL';
      $iSessionId = session_id();
      $oQuery->bindParam('session_null', $sNull, PDO::PARAM_NULL);
      $oQuery->bindParam('session_id', $iSessionId, PDO::PARAM_STR);

      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }
}