<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Model_Content extends Model_Main {

  private final function _setData($bUpdateEntry = false) {
    if (empty($this->_iId)) {
      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->query(" SELECT
                                  c.*,
                                  u.id AS uid,
                                  u.name,
                                  u.surname
                                FROM
                                  contents c
                                LEFT JOIN
                                  users u
                                ON
                                  c.author_id=u.id
                                ORDER BY
                                  c.title ASC");

        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        $oDb = null;

      } catch (AdvancedException $e) {
        $oDb->rollBack();
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
                                    contents c
                                  LEFT JOIN
                                    users u
                                  ON
                                    c.author_id=u.id
                                  WHERE
                                    c.id = :where
                                  ORDER BY
                                    c.title ASC
                                  LIMIT
                                    1");

        $oQuery->bindParam('where', $this->_iId);
        $oQuery->execute();

        # Fix for using it in the same template as overview
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        $oDb = null;

      } catch (AdvancedException $e) {
        $oDb->rollBack();
      }
    }

    foreach ($aResult as $aRow) {
      $iId = $aRow['id'];
      if ($bUpdateEntry == true) {

        $this->_aData = array(
            'id'        => $aRow['id'],
            'author_id' => $aRow['author_id'],
            'title'     => Helper::removeSlahes($aRow['title']),
            'content'   => Helper::removeSlahes($aRow['content']),
            'date'      => Helper::formatTimestamp($aRow['date'], true),
            'datetime'  => Helper::formatTimestamp($aRow['date'])
        );
        unset($sContent);

      } else {
        # Set SEO friendly user names
        $sName      = Helper::formatOutput($aRow['name']);
        $sSurname   = Helper::formatOutput($aRow['surname']);
        $sFullName  = $sName . ' ' . $sSurname;

        $sEncodedTitle = Helper::formatOutput(urlencode($aRow['title']));
        $sUrl = WEBSITE_URL . '/Content/' . $aRow['id'];

        # Do we need to highlight text?
        $sHighlight = isset($this->_aRequest['highlight']) && !empty($this->_aRequest['highlight']) ?
                $this->_aRequest['highlight'] :
                '';

        $this->_aData[$iId] = array(
            'id'                => $aRow['id'],
            'author_id'         => $aRow['author_id'],
            'title'             => Helper::formatOutput($aRow['title'], false, $sHighlight),
            'content'           => Helper::formatOutput($aRow['content'], true, $sHighlight),
            'date'              => Helper::formatTimestamp($aRow['date'], true),
            'datetime'          => Helper::formatTimestamp($aRow['date']),
            'date_raw'          => $aRow['date'],
            'date_rss'          => date('r', $aRow['date']),
            'uid'               => $aRow['uid'],
            'name'              => $sName,
            'surname'           => $sSurname,
            'full_name'         => $sFullName,
            'encoded_full_name' => urlencode($sFullName),
            'encoded_title'     => Helper::formatOutput(urlencode($aRow['title'])),
            'encoded_url'       => urlencode($sUrl),
            'url'               => $sUrl . '/' . $sEncodedTitle,
            'url_clean'         => $sUrl
        );
      }
    }
  }

  public final function getData($iId = '', $bUpdateEntry = false) {
    if (!empty($iId))
      $this->_iId = (int) $iId;

    $this->_setData($bUpdateEntry);
    return $this->_aData;
  }

  public function create() {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare(" INSERT INTO
                                  contents(author_id, title, content, date)
                                VALUES
                                  ( :author_id, :title, :content, :date )");

      $iUserId = USER_ID;
      $oQuery->bindParam('author_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false));
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false));
      $oQuery->bindParam('date', time());
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

  public function update($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	UPDATE
                                  contents
                                SET
                                  title = :title,
                                  content = :content,
                                  date = :date,
                                  author_id = :user_id
                                WHERE
                                  id = :where");

      $iUserId = USER_ID;
      $oQuery->bindParam('user_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false));
      $oQuery->bindParam('content', Helper::removeSlahes($this->_aRequest['content'], false));
      $oQuery->bindParam('date', time());
      $oQuery->bindParam('where', $iId);
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
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	DELETE FROM
                                  contents
                                WHERE
                                  id = :id
                                LIMIT
                                  1");

      $oQuery->bindParam('id', $iId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }
}