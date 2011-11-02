<?php

/**
 * Handle all newsletter SQL requests.
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

class Newsletter extends Main {

  /**
   * Look for an existing email address and create or destroy user from mailing list.
   *
   * @access public
   * @param string $sEmail
   * @return boolean status of query
   *
   */
  public function handleNewsletter($sEmail) {
    try {
      $oQuery = $this->_oDb->prepare("SELECT
                                        email
                                      FROM
                                        " . SQL_PREFIX . "newsletters
                                      WHERE
                                        email = :email
                                      LIMIT
                                        1");

      $oQuery->bindParam('email', $sEmail);
      $oQuery->execute();
      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    if (isset($aResult) && !empty($aResult['email']))
      return ($this->_destroy($sEmail) === true) ? 'DESTROY' : false;

    else
      return ($this->_create($sEmail) === true) ? 'INSERT' : false;
  }

  /**
   * Add user to mailing list.
   *
   * @access private
   * @param string $sEmail email address of user to be added.
   * @return boolean status of query
   */
  private function _create($sEmail) {
    try {
      $oQuery = $this->_oDb->prepare("INSERT INTO
                                        " . SQL_PREFIX . "newsletters (email)
                                      VALUES
                                        ( :email )");

      $oQuery->bindParam('email', $sEmail);
      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  /**
   * Remove user from mailing list.
   *
   * @access private
   * @param string $sEmail email address of user to be removed from mailing list.
   * @return boolean status of query
   */
  private function _destroy($sEmail) {
    try {
      $oQuery = $this->_oDb->prepare("DELETE FROM
                                        " . SQL_PREFIX . "newsletters
                                      WHERE
                                        email = :email");

      $oQuery->bindParam('email', $sEmail);
      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  /**
   * Fetch all newsletter recipients.
   *
   * @access public
   * @param string $sMySqlTable table to fetch data from.
   * @return array recipients data
   */
  public function getNewsletterRecipients($sMySqlTable = 'newsletter') {
    if ($sMySqlTable == 'newsletter') {
      try {
        $oQuery = $this->_oDb->query("SELECT
                                        email
                                      FROM
                                        " . SQL_PREFIX . "newsletters");

        return $oQuery->fetchAll(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    }
    elseif ($sMySqlTable == 'user') {
      try {
        $oQuery = $this->_oDb->query("SELECT
                                        name, email
                                      FROM
                                        " . SQL_PREFIX . "users
                                      WHERE
                                        receive_newsletter = '1'");

        return $oQuery->fetchAll(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    }
  }
}