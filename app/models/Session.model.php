<?php

/**
 * Handle all blog SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 * @todo documentation and refactoring
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
   * @return array $aResult user data
   */
  public static function getSessionData() {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT * FROM " . SQL_PREFIX . "users WHERE session = :session_id AND ip = :ip LIMIT 1");

      $oQuery->bindParam('session_id', session_id(), PDO::PARAM_STR);
      $oQuery->bindParam('ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $bReturn = $oQuery->execute();

      if ($bReturn === false)
        $this->destroy();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;

      return $aResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

  /**
   * Override session for current user.
   *
   * @static
   * @param integer $iId ID of user
   * @return boolean $bResult status of query
   */
  public static function setActiveSession($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("  UPDATE
                                  " . SQL_PREFIX . "users
                                SET
                                  session = :session,
                                  ip = :ip,
                                  last_login = :last_login
                                WHERE
                                  id = :id");

      $oQuery->bindParam('session', session_id(), PDO::PARAM_STR);
      $oQuery->bindParam('ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $oQuery->bindParam('last_login', time(), PDO::PARAM_INT);
      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

  # Create session
  public function create() {
    try {
      $oQuery = $this->_oDb->prepare("SELECT
                                        id, verification_code
                                      FROM
                                        " . SQL_PREFIX . "users
                                      WHERE
                                        email = :email
                                      AND
                                        password = :password
                                      LIMIT
                                        1");

      $sPassword = md5(RANDOM_HASH . Helper::formatInput($this->_aRequest['password']));
      $oQuery->bindParam('email', Helper::formatInput($this->_aRequest['email']), PDO::PARAM_STR);
      $oQuery->bindParam('password', $sPassword, PDO::PARAM_STR);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    # Check if user did not verify
    if (isset($aResult['verification_code']) && !empty($aResult['verification_code']))
      return false;

    # User did verify his and has id, so log in!
    elseif (isset($aResult['id']) && !empty($aResult['id']))
      return Session::setActiveSession($aResult['id']);

    else
      return false;
  }

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
  }

  public function getData() {
    return $this->_aData;
  }

  public function destroy() {
    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "users
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