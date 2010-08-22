<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Model_Content extends Model_Main {

  private final function _setData($bUpdateEntry = false) {
    if (empty($this->_iID)) {
      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->query(" SELECT
                                    c.*,
                                    u.id AS uid,
                                    u.name,
                                    u.surname
                                  FROM
                                    content c
                                  LEFT JOIN
                                    user u
                                  ON
                                    c.authorID=u.id
                                  ORDER BY
                                    c.title ASC");

        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        $oDb = null;

      } catch (AdvancedException $e) {
        $oDb->rollBack();
        $e->getMessage();
        die();
      }
    } else {
      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->prepare(" SELECT
                                    c.*,
                                    u.id AS uid,
                                    u.name,
                                    u.surname
                                  FROM
                                    content c
                                  LEFT JOIN
                                    user u
                                  ON
                                    c.authorID=u.id
                                  WHERE
                                    c.id = :where
                                  ORDER BY
                                    c.title ASC
                                  LIMIT
                                    1");

        $oQuery->bindParam('where', $this->_iID);
        $oQuery->execute();

        # Fix for using it in the same template as overview
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        $oDb = null;

      } catch (AdvancedException $e) {
        $oDb->rollBack();
        $e->getMessage();
        die();
      }
    }

    foreach ($aResult as $aRow) {
      $iID = $aRow['id'];
      if ($bUpdateEntry == true) {

        $this->_aData = array(
            'id'        => $aRow['id'],
            'authorID'  => $aRow['authorID'],
            'title'     => Helper::removeSlahes($aRow['title']),
            'content'   => Helper::formatOutput($aRow['content']),
            'date'      => Helper::formatTimestamp($aRow['date'])
        );
        unset($sContent);

      } else {
        $this->_aData[$iID] = array(
            'id'        => $aRow['id'],
            'authorID'  => $aRow['authorID'],
            'title'     => Helper::formatOutput($aRow['title']),
            'content'   => Helper::formatOutput($aRow['content'], true),
            'date'      => Helper::formatTimestamp($aRow['date']),
            'uid'       => $aRow['uid'],
            'name'      => Helper::formatOutput($aRow['name']),
            'surname'   => Helper::formatOutput($aRow['surname']),
            'eTitle'    => Helper::formatOutput(urlencode($aRow['title']))
        );
      }
    }
  }

  public final function getData($iId = '', $bUpdateEntry = false) {
    if (!empty($iId))
      $this->_iID = (int) $iId;

    $this->_setData($bUpdateEntry);
    return $this->_aData;
  }

  public function create() {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare(" INSERT INTO
                                  content(authorID, title, content, date)
                                VALUES
                                  ( :user_id, :title, :content, :date )");

      $iUserId = USER_ID;
      $oQuery->bindParam('user_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false));
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false));
      $oQuery->bindParam('date', time());
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;

    } catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
      die();
    }
  }

  public function update($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	UPDATE
                                  content
                                SET
                                  title = :title,
                                  content = :content,
                                  date = :date,
                                  authorID = :user_id
                                WHERE
                                  id = :where");

      $iUserId = USER_ID;
      $oQuery->bindParam('user_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false));
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false));
      $oQuery->bindParam('date', time());
      $oQuery->bindParam('where', $iId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;

    } catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
      die();
    }
  }

  public function destroy($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	DELETE FROM
                                  content
                                WHERE
                                  id = :id
                                LIMIT
                                  1");

      $oQuery->bindParam('id', $iId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;

    } catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
      die();
    }
  }
}