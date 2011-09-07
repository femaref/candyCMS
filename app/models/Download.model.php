<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/helpers/Upload.helper.php';

class Model_Download extends Model_Main {

  private function _setData($bUpdate) {

    if (empty($this->_iId)) {
      try {
        $oQuery = $this->_oDb->prepare("SELECT
                                          d.*,
                                          u.id AS uid,
                                          u.name,
                                          u.surname
                                        FROM
                                          " . SQL_PREFIX . "downloads d
                                        LEFT JOIN
                                          " . SQL_PREFIX . "users u
                                        ON
                                          d.author_id=u.id
                                        ORDER BY
                                          d.category ASC,
                                          d.title ASC");

        $oQuery->execute();
        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

      foreach ($aResult as $aRow) {
        $iId = $aRow['id'];
        $sCategory = $aRow['category'];

        # Set SEO friendly user names
        $sName      = Helper::formatOutput($aRow['name']);
        $sSurname   = Helper::formatOutput($aRow['surname']);
        $sFullName  = $sName . ' ' . $sSurname;

        $sEncodedTitle = Helper::formatOutput(urlencode($aRow['title']));
        $sUrl = WEBSITE_URL . '/download/' . $aRow['id'];

        # Do we need to highlight text?
        $sHighlight = isset($this->_aRequest['highlight']) && !empty($this->_aRequest['highlight']) ?
                $this->_aRequest['highlight'] :
                '';

        # Name category for overview
        $this->_aData[$sCategory]['category'] = $sCategory;

        # Fetch normal data
        $this->_aData[$sCategory]['files'][$iId] = array(
                'id'                => $aRow['id'],
                'title'             => Helper::formatOutput($aRow['title'], $sHighlight),
                'content'           => Helper::formatOutput($aRow['content'], $sHighlight),
                'category'          => Helper::formatOutput($aRow['category']),
                'file'              => Helper::formatOutput($aRow['file']),
                'extension'         => Helper::formatOutput($aRow['extension']),
                'downloads'         => (int) $aRow['downloads'],
                'date'              => Helper::formatTimestamp($aRow['date'], true),
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
                'url_clean'         => $sUrl,
                'size'              => Helper::getFileSize(PATH_UPLOAD . '/download/' . $aRow['file'])
        );
      }
    }
    else {
      try {
        $oQuery = $this->_oDb->prepare("SELECT
                                          *
                                        FROM
                                          " . SQL_PREFIX . "downloads
                                        WHERE
                                          id = :id");

        $oQuery->bindParam('id', $this->_iId);
        $oQuery->execute();
        $aRow = & $oQuery->fetch(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

      # we want to edit an entry
      if ($bUpdate == true) {
        $this->_aData = array(
            'id'        => $aRow['id'],
            'title'     => Helper::removeSlahes($aRow['title']),
            'content'   => Helper::removeSlahes($aRow['content']),
            'category'  => Helper::removeSlahes($aRow['category']),
            'downloads' => (int) $aRow['downloads']
        );
      }
      # We do only need the file name
      else
        $this->_aData = & $aRow;
    }

    return $this->_aData;
  }

  public function getData($iId = '', $bUpdate = false) {
    if (!empty($iId))
      $this->_iId = (int) $iId;

    $this->_setData($bUpdate);
    return $this->_aData;
  }

  public function create() {
    # Set up upload helper and rename file to title
    $oUploadFile = new Upload($this->_aRequest, $this->_aFile, Helper::formatInput($this->_aRequest['title']));

    # File is up so insert data into database
    if($oUploadFile->uploadFile('download') == true) {
      try {
        $oQuery = $this->_oDb->prepare("INSERT INTO
                                          " . SQL_PREFIX . "downloads
                                          ( author_id,
                                            title,
                                            content,
                                            category,
                                            file,
                                            extension,
                                            date)
                                        VALUES
                                          ( :author_id,
                                            :title,
                                            :content,
                                            :category,
                                            :file,
                                            :extension,
                                            :date )");

        $iUserId = USER_ID;
        $oQuery->bindParam('author_id', $iUserId);
        $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title']));
        $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']));
        $oQuery->bindParam('category', Helper::formatInput($this->_aRequest['category']));
        $oQuery->bindParam('file', $oUploadFile->getId(false));
        $oQuery->bindParam('extension', $oUploadFile->getExtension());
        $oQuery->bindParam('date', time());

        return $oQuery->execute();
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    }
  }

  public function update($iId) {
    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "downloads
                                      SET
                                        author_id = :author_id,
                                        title = :title,
                                        category = :category,
                                        content = :content,
                                        downloads = :downloads
                                      WHERE
                                        id = :id");

      $iUserId = USER_ID;
      $oQuery->bindParam('author_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title']));
      $oQuery->bindParam('category', Helper::formatInput($this->_aRequest['category']));
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']));
      $oQuery->bindParam('downloads', Helper::formatInput($this->_aRequest['downloads']));
      $oQuery->bindParam('id', $iId);

      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  public function destroy($iId) {
    try {
      $oQuery = $this->_oDb->prepare("DELETE FROM
                                        " . SQL_PREFIX . "downloads
                                      WHERE
                                        id = :id
                                      LIMIT
                                        1");

      $oQuery->bindParam('id', $iId);
      $bReturn = $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    $aFile = $this->getData($iId);
    $sFile = $aFile['file'];

    if (is_file(PATH_UPLOAD . '/download/' . $sFile))
      unlink(PATH_UPLOAD . '/download/' . $sFile);

    return $bReturn;
  }

  public static function updateDownloadCount($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("  UPDATE
                                  " . SQL_PREFIX . "downloads
                                SET
                                  downloads = downloads + 1
                                WHERE
                                  id = :id");

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