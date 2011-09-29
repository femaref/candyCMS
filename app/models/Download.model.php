<?php

/**
 * Handle all download SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Model;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\Page as Page;
use CandyCMS\Helper\Upload as Upload;
use PDO;

require_once 'app/helpers/Upload.helper.php';

class Download extends Main {

  /**
   * Set download data.
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

        $this->_aData[$sCategory]['category'] = $sCategory; # Name category for overview
        $this->_aData[$sCategory]['files'][$iId] = $this->_formatForOutput($aRow, 'download');
        $this->_aData[$sCategory]['files'][$iId]['size'] = Helper::getFileSize(PATH_UPLOAD . '/download/' . $aRow['file']);
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

      $this->_aData = ($bUpdate == true) ? $this->_formatForUpdate($aRow) : $aRow;
    }

    return $this->_aData;
  }

  /**
   * Get download data.
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

  /**
   * Create new download.
   *
   * @access public
   * @return boolean status of query
   *
   */
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

  /**
   * Update a download.
   *
   * @access public
   * @param integer $iId ID to update
   * @return boolean status of query
   *
   */
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

  /**
   * Destroy a download and its file.
   *
   * @access public
   * @param integer $iId ID to update
   * @return boolean status of query
   *
   */
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

    # Get file name
    $aFile = $this->getData($iId);
    $sFile = $aFile['file'];

    if (is_file(PATH_UPLOAD . '/download/' . $sFile))
      unlink(PATH_UPLOAD . '/download/' . $sFile);

    return $bReturn;
  }

  /**
   * Updates a download count +1.
   *
   * @access public
   * @param integer $iId ID to update
   * @return boolean status of query
   *
   */
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