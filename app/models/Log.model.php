<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Model_Log extends Model_Main {

	private function _setData() {
		try {
			$oQuery = $this->_oDb->query("SELECT
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
																			25");

			$aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
			return $aResult;

		} catch (AdvancedException $e) {
			$this->_oDb->rollBack();
		}
	}

  public function getData() {
		return $this->_setData();
  }

  public static function insert($sSectionName, $sActionName, $iActionId, $iUserId, $iTimeStart, $iTimeEnd) {

    $iTimeStart = empty($iTimeStart) ? time() : $iTimeStart;
    $iTimeEnd = empty($iTimeEnd) ? time() : $iTimeEnd;

    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare(" INSERT INTO
                                  " . SQL_PREFIX . "logs(section_name, action_name, action_id, time_start, time_end, user_id)
                                VALUES
                                  ( :section_name, :action_name, :action_id, :time_start, :time_end, :user_id)");

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

  public function destroy($iId) {
    try {
			$oQuery = $this->_oDb->prepare("DELETE FROM
																				" . SQL_PREFIX . "logs
																			WHERE
																				id = :id
																			LIMIT
																				1");

			$oQuery->bindParam('id', $iId);

			$bResult = $oQuery->execute();
			return $bResult;
		}
		catch (AdvancedException $e) {
			$this->_oDb->rollBack();
		}
	}
}