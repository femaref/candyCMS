<?php

/**
 * Create search.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.5
 */

namespace CandyCMS\Model;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Helper\Pagination as Pagination;
use PDO;

class Searches extends Main {

  /**
   * Get search information from tables.
   *
   * @access public
   * @param string $sSearch query string to search
   * @param array $aTables tables to search in
   * @param string $sOrderBy how to order search
   * @return array $this->_aData search data
   *
   */
  public function getData($sSearch, $aTables = '', $sOrderBy = 'date DESC') {
    $aInts = array('id', 'author_id');

    if (empty($aTables))
      $aTables = array('blogs', 'contents');

    foreach ($aTables as $sTable) {
      try {
        $this->oQuery = $this->_oDb->query("SELECT
                                              *
                                            FROM
                                              " . SQL_PREFIX . $sTable . "
                                            WHERE
                                              title LIKE '%" . $sSearch . "%'
                                            OR
                                              content LIKE '%" . $sSearch . "%'
                                            ORDER BY
                                              " . (string) $sOrderBy);

        $aResult = $this->oQuery->fetchAll(PDO::FETCH_ASSOC);

        # Build table names and order them
        if ($sTable == 'gallery_albums') {
          $this->_aData[$sTable]['controller'] = 'galleries';
          $this->_aData[$sTable]['title'] = I18n::get('global.albums');
        }
        else {
          $this->_aData[$sTable]['controller'] = $sTable;
          $this->_aData[$sTable]['title'] = I18n::get('global.' . strtolower($sTable));
        }

				$iEntries = 0;
        foreach ($aResult as $aRow) {
          if (isset($aRow['published']) && $aRow['published'] == 0)
            continue;

          $iDate = $aRow['date'];
          $this->_aData[$sTable][$iDate] = $this->_formatForOutput($aRow, $aInts, null, $sTable);
					++$iEntries;
        }

				$this->_aData[$sTable]['entries'] = $iEntries;
      }
			catch (\PDOException $p) {
				AdvancedException::reportBoth('0071 - ' . $p->getMessage());
				exit('SQL error.');
			}
    }

    return $this->_aData;
  }

  /**
   * Ther is no delete in this Model
   *
   * @access public
   * @param integer $iId ID to delete
   * @return boolean status of query
   *
   */
  public function destroy($iId) {
    die(I18n::get('error.standard'));
  }
}