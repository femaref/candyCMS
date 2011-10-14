<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

namespace CandyCMS\Model;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Helper\Page as Page;
use PDO;

class Search extends Main {

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

        $aResult = $this->oQuery->fetchAll(PDO::FETCH_ASSOC);

        # Build table names and order them
        if ($sTable == 'gallery_albums') {
          $this->_aData[$sTable]['section'] = 'gallery';
          $this->_aData[$sTable]['title'] = I18n::get('global.gallery');
        }
        else {
          # Get table name from language files
          $iTableLen = strlen($sTable) - 1;
          $sTableSingular = substr($sTable, 0, $iTableLen);
          $this->_aData[$sTable]['section'] = $sTableSingular;
          $this->_aData[$sTable]['title'] = I18n::get('global.' . strtolower($sTableSingular));
        }

        foreach ($aResult as $aRow) {
          $iId = $aRow['id'];
          $this->_aData[$sTable][$iId] = array(
              'id'      => $aRow['id'],
              'title'   => Helper::formatOutput($aRow['title']),
              'date'    => Helper::formatTimestamp($aRow['date'], true),
              'datetime'=> Helper::formatTimestamp($aRow['date'])
          );
        }
      }
      catch (AdvancedException $e) {
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