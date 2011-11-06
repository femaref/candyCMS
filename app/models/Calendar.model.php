<?php

/**
 * Handle all calendar	 SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Model;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use DateTime;
use PDO;

require_once 'app/helpers/Upload.helper.php';

class Calendar extends Main {

  /**
   * Set calendar data.
   *
   * @access private
   * @param boolean $bUpdate prepare data for update
   * @return array data
   *
   */
  private function _setData($bUpdate) {

    if (empty($this->_iId)) {
      try {
				$oQuery = $this->_oDb->prepare("SELECT
                                          c.*,
																					MONTH(c.start_date) AS start_month,
																					YEAR(c.start_date) AS start_year,
																					UNIX_TIMESTAMP(c.start_date) AS start_date,
																					UNIX_TIMESTAMP(c.end_date) AS end_date,
                                          u.id AS uid,
                                          u.name,
                                          u.surname
                                        FROM
                                          " . SQL_PREFIX . "calendar c
                                        LEFT JOIN
                                          " . SQL_PREFIX . "users u
                                        ON
                                          c.author_id=u.id
																				WHERE
																					start_date > NOW()
                                        ORDER BY
                                          c.start_date ASC,
                                          c.title ASC");

				$oQuery->execute();
				$aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
			}
			catch (AdvancedException $e) {
				$this->_oDb->rollBack();
			}

			foreach ($aResult as $aRow) {
				$iId		= $aRow['id'];
				$sMonth = I18n::get('global.months.' . $aRow['start_month']);
				$sYear	= $aRow['start_year'];
				$sDate	= $sMonth . $sYear;

				$this->_aData[$sDate]['month']	= $sMonth;
				$this->_aData[$sDate]['year']		= $sYear;

				$this->_aData[$sDate]['dates'][$iId] = $this->_formatForOutput($aRow, 'calendar');
				$this->_aData[$sDate]['dates'][$iId]['start_date']	= Helper::formatTimestamp($aRow['start_date'], 1);

				if(date('His', $aRow['start_date']) !== '000000')
					$this->_aData[$sDate]['dates'][$iId]['start_time']	= Helper::formatTimestamp($aRow['start_date'], 2);

				if($aRow['end_date'] > 0) {
					$this->_aData[$sDate]['dates'][$iId]['end_date'] = Helper::formatTimestamp($aRow['end_date'], 1);

					if(date('His', $aRow['end_date']) !== '000000')
						$this->_aData[$sDate]['dates'][$iId]['end_time'] = Helper::formatTimestamp($aRow['end_date'], 2);
				}
			}
    }
    else {
      try {
        $oQuery = $this->_oDb->prepare("SELECT
                                          *
                                        FROM
                                          " . SQL_PREFIX . "calendar
                                        WHERE
                                          id = :id");

        $oQuery->bindParam('id', $this->_iId);
        $oQuery->execute();
        $aRow = & $oQuery->fetch(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

      $this->_aData = ($bUpdate == true) ? $this->_formatForUpdate($aRow) : $aRow;
    }

    return $this->_aData;
	}

  /**
   * Get calendar data.
   *
   * @access public
   * @param integer $iId ID to get data from
   * @param boolean $bUpdate prepare data for update
   * @return array data
   *
   */
  public function getData($iId = '', $bUpdate = false) {
    if (!empty($iId))
      $this->_iId = (int) $iId;

    return $this->_setData($bUpdate);
  }
}