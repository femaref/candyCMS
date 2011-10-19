<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @todo documentation and refactoring
 */

namespace CandyCMS\Model;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\Page as Page;
use PDO;

class Newsletter extends Main {

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
      return ($this->_destroyFromNewsletter($sEmail) == true) ? 'DESTROY' : false;

    else
      return ($this->_createIntoNewsletter($sEmail) == true) ? 'INSERT' : false;
  }

  private function _createIntoNewsletter($sEmail) {
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

  private function _destroyFromNewsletter($sEmail) {
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