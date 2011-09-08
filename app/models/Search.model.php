<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

namespace CandyCMS\Model;

class Search extends \CandyCMS\Model\Main {

  private function _setData($sSearch, $aTables) {

    foreach ($aTables as $sTable) {
      try {
        $this->oQuery = $this->_oDb->query("SELECT
                                              id, title, date
                                            FROM
                                              " . SQL_PREFIX . $sTable."
                                            WHERE
                                              title LIKE '%"  .$sSearch.  "%'
                                            OR
                                              content LIKE '%"  .$sSearch.  "%'
                                            ORDER BY
                                              date
                                            DESC");

        $aResult = $this->oQuery->fetchAll(\PDO::FETCH_ASSOC);

        # Build table names and order them
        if ($sTable == 'gallery_albums') {
          $this->_aData[$sTable]['section'] = 'gallery';
          $this->_aData[$sTable]['title'] = LANG_GLOBAL_GALLERY;
        }
        else {
          # Get table name from language files
          $iTableLen = strlen($sTable) - 1;
          $sTableSingular = substr($sTable, 0, $iTableLen);
          $this->_aData[$sTable]['section'] = $sTableSingular;
          $this->_aData[$sTable]['title'] = constant('LANG_GLOBAL_' . strtoupper($sTableSingular));
        }

        foreach ($aResult as $aRow) {
          $iId = $aRow['id'];
          $this->_aData[$sTable][$iId] = array(
              'id'      => $aRow['id'],
              'title'   => \CandyCMS\Helper\Helper::formatOutput($aRow['title']),
              'date'    => \CandyCMS\Helper\Helper::formatTimestamp($aRow['date'], true),
              'datetime'=> \CandyCMS\Helper\Helper::formatTimestamp($aRow['date'])
          );
        }
      }
      catch (\CandyCMS\Helper\AdvancedException $e) {
        $oDb->rollBack();
      }
    }

    return $this->_aData;
  }

  public function getData($sSearch, $aTables = '') {
    if (empty($aTables))
      $aTables = array('blogs', 'contents');

    return $this->_setData($sSearch, $aTables);
  }
}