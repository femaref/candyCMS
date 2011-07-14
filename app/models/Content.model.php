<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Model_Content extends Model_Main {

  private final function _setData($bUpdateEntry = false) {
    if (empty($this->_iId)) {
      try {
        $oQuery = $this->_oDb->query("SELECT
																				c.*,
																				u.id AS uid,
																				u.name,
																				u.surname
																			FROM
																				" . SQL_PREFIX . "contents c
																			LEFT JOIN
																				" . SQL_PREFIX . "users u
																			ON
																				c.author_id=u.id
																			ORDER BY
																				c.title ASC");

        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
      } catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    } else {
      try {
        $oQuery = $this->_oDb->prepare("SELECT
																					c.*,
																					u.id AS uid,
																					u.name,
																					u.surname
																				FROM
																					" . SQL_PREFIX . "contents c
																				LEFT JOIN
																					" . SQL_PREFIX . "users u
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

      } catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    }

    foreach ($aResult as $aRow) {
      $iId = $aRow['id'];
      if ($bUpdateEntry == true) {

        $this->_aData = array(
            'id'        => $aRow['id'],
            'author_id' => $aRow['author_id'],
            'content'   => Helper::removeSlahes($aRow['content']),
            'keywords'  => Helper::removeSlahes($aRow['keywords']),
            'teaser'    => Helper::removeSlahes($aRow['teaser']),
            'title'     => Helper::removeSlahes($aRow['title']),
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
        $sUrl = WEBSITE_URL . '/content/' . $aRow['id'];

        # Do we need to highlight text?
        $sHighlight = isset($this->_aRequest['highlight']) && !empty($this->_aRequest['highlight']) ?
                $this->_aRequest['highlight'] :
                '';

        $this->_aData[$iId] = array(
            'id'                => $aRow['id'],
            'author_id'         => $aRow['author_id'],
            'title'             => Helper::formatOutput($aRow['title'], $sHighlight),
            'teaser'            => Helper::formatOutput($aRow['teaser']),
            'keywords'          => Helper::formatOutput($aRow['keywords']),
            'content'           => Helper::formatOutput($aRow['content'], $sHighlight),
            'date'              => Helper::formatTimestamp($aRow['date']),
            'datetime'          => Helper::formatTimestamp($aRow['date']),
            'date_raw'          => $aRow['date'],
            'date_rss'          => date('D, d M Y H:i:s O', $aRow['date']),
            'date_w3c'          => date('Y-m-d\TH:i:sP', $aRow['date']),
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
      $oQuery = $this->_oDb->prepare("INSERT INTO
																				" . SQL_PREFIX . "contents(author_id, title, teaser, keywords, content, date)
																			VALUES
																				( :author_id, :title, :teaser, :keywords, :content, :date )");

      $iUserId = USER_ID;
      $oQuery->bindParam('author_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false));
      $oQuery->bindParam('teaser', Helper::formatInput($this->_aRequest['teaser']));
      $oQuery->bindParam('keywords', Helper::formatInput($this->_aRequest['keywords']));
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false));
      $oQuery->bindParam('date', time());

      $bResult = $oQuery->execute();
      return $bResult;
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  public function update($iId) {
    try {
      $oQuery = $this->_oDb->prepare("UPDATE
																				" . SQL_PREFIX . "contents
																			SET
																				title = :title,
																				teaser = :teaser,
																				keywords = :keywords,
																				content = :content,
																				date = :date,
																				author_id = :user_id
																			WHERE
																				id = :where");

      $iUserId = USER_ID;
      $oQuery->bindParam('user_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false));
      $oQuery->bindParam('teaser', Helper::formatInput($this->_aRequest['teaser']));
      $oQuery->bindParam('keywords', Helper::formatInput($this->_aRequest['keywords']));
      $oQuery->bindParam('content', Helper::removeSlahes($this->_aRequest['content'], false));
      $oQuery->bindParam('date', time());
      $oQuery->bindParam('where', $iId);

      $bResult = $oQuery->execute();
      return $bResult;
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  public function destroy($iId) {
    try {
      $oQuery = $this->_oDb->prepare("DELETE FROM
																				" . SQL_PREFIX . "contents
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