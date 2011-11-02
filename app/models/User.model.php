<?php

/**
 * Handle all user SQL requests.
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

require_once 'app/controllers/Session.controller.php';

class User extends Main {

  /**
   * Get user name, surname and email from user ID.
   *
	 * @static
   * @access public
   * @param integer $iId ID of the user
   * @return array data with user information
   *
   */
  public static final function getUserNamesAndEmail($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT name, surname, email FROM " . SQL_PREFIX . "users WHERE id = :id LIMIT 1");

      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;

      return $aResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

  /**
   * Check, if the user already with given email address exists.
   *
	 * @static
   * @access public
   * @param string $sEmail email address of user.
   * @return boolean status of user check
   *
   */
  public static function getExistingUser($sEmail) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT email FROM " . SQL_PREFIX . "users WHERE email = :email LIMIT 1");

      $oQuery->bindParam('email', $sEmail, PDO::PARAM_STR);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;

      if (isset($aResult['email']) && !empty($aResult['email']))
        return true;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

  /**
   * Set user entry or user overview data.
   *
   * @access private
   * @param boolean $bUpdate prepare data for update
   * @param integer $iLimit blog post limit
   * @return array data
   *
   */
  private function _setData($bUpdate, $iLimit) {
    if (empty($this->_iId)) {
      try {
        $oQuery = $this->_oDb->prepare("SELECT
                                          id,
                                          name,
                                          email,
                                          surname,
                                          last_login,
                                          date,
                                          use_gravatar
                                        FROM
                                          " . SQL_PREFIX . "users
                                        ORDER BY
                                          id ASC
                                        LIMIT
                                          :limit");

        $oQuery->bindParam('limit', $iLimit, PDO::PARAM_INT);
        $oQuery->execute();

        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

      foreach ($aResult as $aRow) {
        $iId = $aRow['id'];

        $this->_aData[$iId] = $this->_formatForOutput($aRow, 'user');
        $this->_aData[$iId]['last_login'] = Helper::formatTimestamp($aRow['last_login'], true);
      }

    }
    else {
      try {
        $oQuery = $this->_oDb->prepare("SELECT
                                          *
                                        FROM
                                          " . SQL_PREFIX . "users
                                        WHERE
                                          id = :id
                                        LIMIT 1");

        $oQuery->bindParam('id', $this->_iId, PDO::PARAM_INT);
        $oQuery->execute();

        $aRow = & $oQuery->fetch(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

      if ($bUpdate == true)
        $this->_aData = $this->_formatForUpdate($aRow);

      else {
        $this->_aData[1] = $this->_formatForOutput($aRow, 'user');
        $this->_aData[1]['last_login'] = Helper::formatTimestamp($aRow['last_login'], true);
      }
    }

    return $this->_aData;
  }

  /**
   * Get user entry or user overview data. Depends on avaiable ID.
   *
   * @access public
   * @param integer $iId ID to load data from. If empty, show overview.
   * @param boolean $bForceNoId Override ID to show user overview
   * @param boolean $bUpdate prepare data for update
   * @param integer $iLimit user overview limit
   * @return array data from _setData
   *
   */
  public function getData($iId = '', $bForceNoId = false, $bUpdate = false, $iLimit = 1000) {
    if (!empty($iId))
      $this->_iId = (int) $iId;

    if ($bForceNoId == true)
      $this->_iId = '';

    return $this->_setData($bUpdate, $iLimit);
  }

  /**
   * Create a user.
   *
   * @access public
	 * @param integer $iVerificationCode verification code that was sent to the user.
   * @return boolean status of query
   * @override app/models/Main.model.php
   *
   */
  public function create($iVerificationCode) {
    try {
      $oQuery = $this->_oDb->prepare("INSERT INTO
                                        " . SQL_PREFIX . "users
                                          (name, surname, password, email, date, verification_code)
                                      VALUES
                                        ( :name, :surname, :password, :email, :date, :verification_code )");

      $oQuery->bindParam('name', Helper::formatInput($this->_aRequest['name']), PDO::PARAM_STR);
      $oQuery->bindParam('surname', Helper::formatInput($this->_aRequest['surname']), PDO::PARAM_STR);
      $oQuery->bindParam('password', md5(RANDOM_HASH . $this->_aRequest['password']), PDO::PARAM_STR);
      $oQuery->bindParam('email', Helper::formatInput($this->_aRequest['email']), PDO::PARAM_STR);
      $oQuery->bindParam('date', time(), PDO::PARAM_INT);
      $oQuery->bindParam('verification_code', $iVerificationCode, PDO::PARAM_STR);

      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  /**
   * Get the encrypted password of a user. This is required for user update actions.
   *
   * @access public
   * @param integer $iId ID to get password from
   * @return string encrypted password
   *
   */
  private function _getPassword($iId) {
    try {
      $oQuery = $this->_oDb->prepare("SELECT password FROM " . SQL_PREFIX . "users WHERE id = :id LIMIT 1");
      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      return $aResult['password'];
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  /**
   * Update a user.
   *
   * @access public
   * @param integer $iId ID to update
   * @return boolean status of query
   *
   */
  public function update($iId) {
    $iReceiveNewsletter = isset($this->_aRequest['receive_newsletter']) ? 1 : 0;
    $iUseGravatar = isset($this->_aRequest['use_gravatar']) ? 1 : 0;

    # Set other peoples user right
    if (($iId !== USER_ID) && USER_RIGHT === 4)
      $iUserRight = isset($this->_aRequest['user_right']) && !empty($this->_aRequest['user_right']) ?
              (int) $this->_aRequest['user_right'] :
              1;
    else
      $iUserRight = USER_RIGHT;

    # Get my active password
    $sPassword = $this->_getPassword($iId);

    # Change passwords
    if (isset($this->_aRequest['password_new']) && !empty($this->_aRequest['password_new']) &&
            isset($this->_aRequest['password_old']) && !empty($this->_aRequest['password_old']) &&
            USER_ID === $iId)
      $sPassword = md5(RANDOM_HASH . $this->_aRequest['password_new']);

    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "users
                                      SET
                                        name = :name,
                                        surname = :surname,
                                        email = :email,
                                        content = :content,
                                        receive_newsletter = :receive_newsletter,
                                        use_gravatar = :use_gravatar,
                                        password = :password,
                                        user_right = :user_right
                                      WHERE
                                        id = :id");

      $oQuery->bindParam('name', Helper::formatInput($this->_aRequest['name']), PDO::PARAM_STR);
      $oQuery->bindParam('surname', Helper::formatInput($this->_aRequest['surname']), PDO::PARAM_STR);
      $oQuery->bindParam('email', Helper::formatInput($this->_aRequest['email']), PDO::PARAM_STR);
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']), PDO::PARAM_STR);
      $oQuery->bindParam('receive_newsletter', $iReceiveNewsletter, PDO::PARAM_INT);
      $oQuery->bindParam('use_gravatar', $iUseGravatar, PDO::PARAM_INT);
      $oQuery->bindParam('password', $sPassword, PDO::PARAM_STR);
      $oQuery->bindParam('user_right', $iUserRight, PDO::PARAM_INT);
      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);

      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  /**
   * Destroy a user and his avatar images.
   *
   * @access public
   * @param integer $iId ID to update
   * @return boolean status of query
   *
   */
  public function destroy($iId) {
    try {
      $oQuery = $this->_oDb->prepare("DELETE FROM
                                        " . SQL_PREFIX . "users
                                      WHERE
                                        id = :id
                                      LIMIT
                                        1");

      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  /**
   * Update a user account when verification link is clicked.
   *
   * @access public
   * @param integer $iVerificationCode Code to remove.
   * @return boolean status of query
   *
   */
  public function verifyEmail($iVerificationCode) {
    try {
      $oQuery = $this->_oDb->prepare("SELECT
                                        id
                                      FROM
                                        " . SQL_PREFIX . "users
                                      WHERE
                                        verification_code = :verification_code
                                      LIMIT 1");

      $oQuery->bindParam('verification_code', $iVerificationCode, PDO::PARAM_STR);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    if (!empty($aResult['id'])) {
      try {
        $oQuery = $this->_oDb->prepare("UPDATE
                                          " . SQL_PREFIX . "users
                                        SET
                                          verification_code = ''
                                        WHERE
                                          id = :id");

        $oQuery->bindParam('id', $aResult['id'], PDO::PARAM_INT);
        Session::update($aResult['id']);
        return $oQuery->execute();
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    }
  }
}