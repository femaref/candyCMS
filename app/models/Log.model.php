<?php

/**
 * Handle all log SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

class Model_Log extends Model_Main {

	/**
	 * Set log overview data.
	 *
	 * @access private
	 * @param integer $iLimit blog post limit
	 * @return array data
	 *
	 */
	private function _setData($iLimit) {
    # Count entries
    try {
      $oQuery = $this->_oDb->query("SELECT COUNT(*) FROM " . SQL_PREFIX . "logs");
      $iResult = $oQuery->fetchColumn();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    $this->oPage = new Page($this->_aRequest, $iResult, $iLimit);

		try {
			$oQuery = $this->_oDb->prepare("SELECT
                                        l.*,
                                        u.id AS uid,
                                        u.name,
                                        u.surname
                                      FROM
                                        " . SQL_PREFIX . "logs l
                                      LEFT JOIN
                                        " . SQL_PREFIX . "users u
                                      ON
                                        l.user_id=u.id
                                      ORDER BY
                                        l.time_end DESC
                                      LIMIT
                                        :offset,
                                        :limit");

      $oQuery->bindParam('limit', $this->oPage->getLimit(), PDO::PARAM_INT);
      $oQuery->bindParam('offset', $this->oPage->getOffset(), PDO::PARAM_INT);
      $oQuery->execute();

			$aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (AdvancedException $e) {
			$this->_oDb->rollBack();
		}

		foreach ($aResult as $aRow) {
			$iId = $aRow['id'];

			$this->_aData[$iId] = $this->_formatForOutput($aRow, 'log');
			$this->_aData[$iId]['time_start'] = Helper::formatTimestamp($aRow['time_start']);
			$this->_aData[$iId]['time_end '] = Helper::formatTimestamp($aRow['time_end']);
		}

    return $this->_aData;
	}

	/**
	 * Get log overview data.
	 *
	 * @access public
	 * @param integer $iLimit blog post limit
	 * @return array data from _setData
	 *
	 */
	public function getData($iLimit = 50) {
		return $this->_setData($iLimit);
	}

	/**
	 * Get log overview data.
	 *
	 * @static
	 * @access public
	 * @param string $sSectionName name of section
	 * @param string $sActionName name of action (CRUD)
	 * @param integer $iActionId ID of the row that is affected
	 * @param integer $iUserId ID of the acting user
	 * @param integer $iTimeStart starting timestamp of the entry
	 * @param integer $iTimeEnd ending timestamp of the entry
	 * @return boolean status of query
	 *
	 */
	public static function insert($sSectionName, $sActionName, $iActionId, $iUserId, $iTimeStart, $iTimeEnd) {
		$iTimeStart = empty($iTimeStart) ? time() : $iTimeStart;
		$iTimeEnd   = empty($iTimeEnd) ? time() : $iTimeEnd;

		try {
			$oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
			$oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare(" INSERT INTO
                                  " . SQL_PREFIX . "logs
                                  ( section_name,
                                    action_name,
                                    action_id,
                                    time_start,
                                    time_end,
                                    user_id)
                                VALUES
                                  ( :section_name,
                                    :action_name,
                                    :action_id,
                                    :time_start,
                                    :time_end,
                                    :user_id)");

			$oQuery->bindParam('section_name', strtolower($sSectionName));
			$oQuery->bindParam('action_name', strtolower($sActionName));
			$oQuery->bindParam('action_id', $iActionId, PDO::PARAM_INT);
			$oQuery->bindParam('time_start', $iTimeStart);
			$oQuery->bindParam('time_end', $iTimeEnd);
			$oQuery->bindParam('user_id', $iUserId);

			$bResult = $oQuery->execute();
			$oDb = null;

			return $bResult;
		}
		catch (AdvancedException $e) {
			$oDb->rollBack();
		}
	}

	/**
	 * Delete a log entry.
	 *
	 * @access public
	 * @param integer $iId ID to delete
	 * @return boolean status of query
	 * @override app/models/Main.model.php
	 *
	 */
	public function destroy($iId) {
    try {
      $oQuery = $this->_oDb->prepare("DELETE FROM
																				" . SQL_PREFIX . "logs
																			WHERE
																				id = :id
																			LIMIT
																				1");

      $oQuery->bindParam('id', $iId);
      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }
}