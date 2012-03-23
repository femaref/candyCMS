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

class Calendars extends Main {

  /**
	 * Get calendar data.
	 *
	 * @access private
   * @param integer $iId Id to work with
	 * @param boolean $bUpdate prepare data for update
	 * @return array data
	 *
	 */
	public function getData($iId = '', $bUpdate = false) {
		if (empty($iId) || (isset($this->_aRequest['action']) && 'archive' == $this->_aRequest['action'])) {
			try {
				if (isset($this->_aRequest['action']) && $this->_aRequest['action'] == 'archive') {
					$iYear = isset($this->_aRequest['id']) && !empty($this->_aRequest['id']) ?
									(int) $this->_aRequest['id'] :
									date('Y');

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
                                          " . SQL_PREFIX . "calendars c
                                        LEFT JOIN
                                          " . SQL_PREFIX . "users u
                                        ON
                                          c.author_id=u.id
																				WHERE
																					YEAR(c.start_date) = :year
                                        ORDER BY
                                          c.start_date ASC,
                                          c.title ASC");

					$oQuery->bindParam('year', $iYear, PDO::PARAM_INT);
				}
				else {
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
                                          " . SQL_PREFIX . "calendars c
                                        LEFT JOIN
                                          " . SQL_PREFIX . "users u
                                        ON
                                          c.author_id=u.id
																				WHERE
																					c.start_date > NOW()
                                        ORDER BY
                                          c.start_date ASC,
                                          c.title ASC");
				}

				$oQuery->execute();
				$aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
			}
      catch (\PDOException $p) {
        AdvancedException::reportBoth('0011 - ' . $p->getMessage());
        exit('SQL error.');
      }

			foreach ($aResult as $aRow) {
				$iId = $aRow['id'];
				$sMonth = I18n::get('global.months.' . $aRow['start_month']);
				$sYear = $aRow['start_year'];
				$sDate = $sMonth . $sYear;

				$this->_aData[$sDate]['month']	= $sMonth;
				$this->_aData[$sDate]['year']		= $sYear;

				$this->_aData[$sDate]['dates'][$iId] = $this->_formatForOutput($aRow);
				$this->_aData[$sDate]['dates'][$iId]['start_date'] = Helper::formatTimestamp($aRow['start_date'], 1);

				if ($aRow['end_date'] > 0)
					$this->_aData[$sDate]['dates'][$iId]['end_date'] = Helper::formatTimestamp($aRow['end_date'], 1);
			}
		}
		else {
			try {
				$oQuery = $this->_oDb->prepare("SELECT
                                          *,
                                          DATE_ADD(end_date, INTERVAL 1 DAY) as ics_end_date
                                        FROM
                                          " . SQL_PREFIX . "calendars
                                        WHERE
                                          id = :id");

				$oQuery->bindParam('id', $iId);
				$oQuery->execute();
				$aRow = & $oQuery->fetch(PDO::FETCH_ASSOC);
			}
      catch (\PDOException $p) {
        AdvancedException::reportBoth('0012 - ' . $p->getMessage());
        exit('SQL error.');
      }

			if($bUpdate === true)
        $this->_aData = $this->_formatForUpdate($aRow);

      else {
        $this->_aData = $this->_formatForOutput($aRow, 'calendar');

        # Overide for iCalendar specs
        $this->_aData['start_date'] = str_replace('-', '', $aRow['start_date']);
        $this->_aData['end_date']   = $aRow['end_date'] == '0000-00-00' ?
                str_replace('-', '', $this->_aData['start_date']) :
                str_replace('-', '', $aRow['ics_end_date']);
        $this->_aData['date']       = date('Ymd', $aRow['date']) . 'T' . date('His', $aRow['date']) . 'Z';
      }
		}

		return $this->_aData;
	}

	/**
	 * Create new calendar entry.
	 *
	 * @access public
	 * @return boolean status of query
	 *
	 */
	public function create() {
		try {
			$oQuery = $this->_oDb->prepare("INSERT INTO
																				" . SQL_PREFIX . "calendars
																				( author_id,
																					title,
																					content,
																					date,
																					start_date,
																					end_date)
																			VALUES
																				( :author_id,
																					:title,
																					:content,
																					:date,
																					:start_date,
																					:end_date)");

			$oQuery->bindParam('author_id', $this->_aSession['user']['id'], PDO::PARAM_INT);
			$oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title']), PDO::PARAM_STR);
			$oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']), PDO::PARAM_STR);
			$oQuery->bindParam('date', time(), PDO::PARAM_INT);
			$oQuery->bindParam('start_date', Helper::formatInput($this->_aRequest['start_date']), PDO::PARAM_STR, PDO::PARAM_INT);
			$oQuery->bindParam('end_date', Helper::formatInput($this->_aRequest['end_date']), PDO::PARAM_STR, PDO::PARAM_INT);

      $bReturn = $oQuery->execute();
      parent::$iLastInsertId = Helper::getLastEntry('calendars');

      return $bReturn;
		}
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0013 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0014 - ' . $p->getMessage());
      exit('SQL error.');
    }
	}

	/**
	 * Update a calendar entry.
	 *
	 * @access public
	 * @param integer $iId ID to update
	 * @return boolean status of query
	 *
	 */
	public function update($iId) {
		try {
			$oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "calendars
                                      SET
                                        author_id = :author_id,
                                        title = :title,
                                        content = :content,
                                        start_date = :start_date,
                                        end_date = :end_date
                                      WHERE
                                        id = :id");

			$oQuery->bindParam('id', $iId, PDO::PARAM_INT);
			$oQuery->bindParam('author_id', $this->_aSession['user']['id'], PDO::PARAM_INT);
			$oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title']), PDO::PARAM_STR);
			$oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']), PDO::PARAM_STR);
			$oQuery->bindParam('start_date', Helper::formatInput($this->_aRequest['start_date']), PDO::PARAM_STR, PDO::PARAM_INT);
			$oQuery->bindParam('end_date', Helper::formatInput($this->_aRequest['end_date']), PDO::PARAM_STR, PDO::PARAM_INT);

			return $oQuery->execute();
		}
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0015 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0016 - ' . $p->getMessage());
      exit('SQL error.');
    }
	}

	/**
	 * Destroy a calendar entry.
	 *
	 * @access public
	 * @param integer $iId ID to destroy
	 * @return boolean status of query
	 *
	 */
	public function destroy($iId) {
		try {
			$oQuery = $this->_oDb->prepare("DELETE FROM
                                        " . SQL_PREFIX . "calendars
                                      WHERE
                                        id = :id
                                      LIMIT
                                        1");

			$oQuery->bindParam('id', $iId, PDO::PARAM_INT);
			return $oQuery->execute();
		}
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0017 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0018 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }
}