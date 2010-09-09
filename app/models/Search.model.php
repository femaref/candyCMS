<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Model_Search extends Model_Main {

  # Get userdata; static function and direct return due to uncritical action
  private function _setData($sSearch, $aTables) {

    foreach ($aTables as $sTable) {
      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $oQuery = $oDb->query(" SELECT
                                  id, title, date
                                FROM
                                  " . $sTable."
                                WHERE
                                  title LIKE '%"  .$sSearch.  "%'
                                OR
                                  content LIKE '%"  .$sSearch.  "%'
                                ORDER BY
                                  date
                                DESC");

        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        $oDb = null;

        foreach ($aResult as $aRow) {
          $iId = $aRow['id'];
          $this->_aData[$sTable][$iId] = array(
              'id'      => $aRow['id'],
              'title'   => Helper::formatOutput($aRow['title']),
              'date'    => Helper::formatTimestamp($aRow['date'])
          );
        }
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
      }
    }
  }

  public function getData($sSearch, $aTables = '') {
    if (empty($aTables))
      $aTables = array('blogs', 'contents');

    $this->_setData($sSearch, $aTables);
    return $this->_aData;
  }
}