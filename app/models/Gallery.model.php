<?php

/**
 * Handle all gallery SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Model;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\Page as Page;
use CandyCMS\Helper\Upload as Upload;
use PDO;

class Gallery extends Main {
  private $_aThumbs;
  private $_sFilePath;

  /**
   * Set gallery album data.
   *
   * @access private
   * @param boolean $bUpdate prepare data for update
   * @param integer $iLimit blog post limit
   * @return array data
   *
   */
  private function _setData($bUpdate, $bAdvancedImageInformation, $iLimit) {
    $sWhere = '';
    $iResult = 1;

    if (empty($this->_iId)) {
      try {
        $oQuery = $this->_oDb->query("SELECT COUNT(*) FROM " . SQL_PREFIX . "gallery_albums " . $sWhere);
        $iResult = $oQuery->fetchColumn();
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    }

    else
      $sWhere = "WHERE a.id = '" . $this->_iId . "'";

    $this->oPage = new Page($this->_aRequest, (int) $iResult, $iLimit);

    try {
      $oQuery = $this->_oDb->query("SELECT
                                      a.*,
                                      u.id AS uid,
                                      u.name,
                                      u.surname,
                                      COUNT(f.id) AS files_sum
                                    FROM
                                      " . SQL_PREFIX . "gallery_albums a
                                    LEFT JOIN
                                      " . SQL_PREFIX . "users u
                                    ON
                                      a.author_id=u.id
                                    LEFT JOIN
                                      " . SQL_PREFIX . "gallery_files f
                                    ON
                                      f.album_id=a.id
                                    "  .$sWhere.  "
                                    GROUP BY
                                      a.id
                                    ORDER BY
                                      a.id DESC
                                    LIMIT
                                      " . $this->oPage->getOffset() . ",
                                      " . $this->oPage->getLimit());

      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    # Update a single entry. Fix it with 0 o
    if ($bUpdate == true)
      $this->_aData = $this->_formatForUpdate($aResult[0]);

    else {
      foreach ($aResult as $aRow) {
        $iId = $aRow['id'];

        $this->_aData[$iId] = $this->_formatForOutput($aRow, 'gallery');
        $this->_aData[$iId]['files'] = ($aRow['files_sum'] > 0) ? $this->getThumbs($iId, $bAdvancedImageInformation) : '';
      }
    }

    return $this->_aData;
  }

  /**
   * Get blog entry or blog overview data. Depends on avaiable ID.
   *
   * @access public
   * @param integer $iId Album-ID to load data from. If empty, show overview.
   * @param boolean $bUpdate prepare data for update
   * @param boolean $bAdvancedImageInformation provide image with advanced information (MIME_TYPE etc.)
   * @param integer $iLimit blog post limit
   * @return array data from _setData
   *
   */
  public function getData($iId = '', $bUpdate = false, $bAdvancedImageInformation = false, $iLimit = LIMIT_ALBUMS) {
    if (!empty($iId))
      $this->_iId = (int) $iId;

    return $this->_setData($bUpdate, $bAdvancedImageInformation, $iLimit);
  }

  public function getId() {
    return $this->_iId;
  }

  private function _setThumbs($iId, $bAdvancedImageInformation) {

    # Clear existing array (fix, when we got no images at a gallery
    if (!empty($this->_aThumbs))
      unset($this->_aThumbs);

    try {
      $oQuery = $this->_oDb->prepare("SELECT
                                        f.*,
                                        u.name,
                                        u.surname
                                      FROM
                                        " . SQL_PREFIX . "gallery_files f
                                      LEFT JOIN
                                        " . SQL_PREFIX . "users u
                                      ON
                                        f.author_id=u.id
                                      WHERE
                                        f.album_id= :album_id
                                      ORDER BY
                                        f.date ASC");

      $oQuery->bindParam('album_id', $iId);
      $oQuery->execute();

      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    $iLoop = 0;
    foreach ($aResult as $aRow) {
      $iId = $aRow['id'];

      # Set SEO friendly user names
      $sName      = Helper::formatOutput($aRow['name']);
      $sSurname   = Helper::formatOutput($aRow['surname']);
      $sFullName  = $sName . ' ' . $sSurname;

      $sUrlAlbum     = WEBSITE_URL . '/' . PATH_UPLOAD . '/gallery/' . $aRow['album_id'];
      $sUrl32        = $sUrlAlbum . '/32/' . $aRow['file'];
      $sUrlPopup     = $sUrlAlbum . '/popup/' . $aRow['file'];
      $sUrlOriginal  = $sUrlAlbum . '/original/' . $aRow['file'];
      $sUrlThumb     = $sUrlAlbum . '/' . THUMB_DEFAULT_X . '/' . $aRow['file'];

      $this->_aThumbs[$iId] = array(
          'id'            => $aRow['id'],
          'album_id'      => $aRow['album_id'],
          'file'          => $aRow['file'],
          'content'       => Helper::formatOutput($aRow['content']),
          'date'          => Helper::formatTimestamp($aRow['date'], true),
          'datetime'      => Helper::formatTimestamp($aRow['date']),
          'date_raw'      => $aRow['date'],
          'date_rss'      => date('D, d M Y H:i:s O', $aRow['date']),
          'date_w3c'      => date('Y-m-d\TH:i:sP', $aRow['date']),
          'url'           => WEBSITE_URL . '/gallery/' . $aRow['album_id'] . '/image/' . $iId,
          'url_32'        => $sUrl32,
          'url_album'     => $sUrlAlbum,
          'url_popup'     => $sUrlPopup,
          'url_original'  => $sUrlOriginal,
          'url_thumb'     => $sUrlThumb,
          'name'          => $sName,
          'surname'       => $sSurname,
          'full_name'     => $sFullName,
          'extension'     => $aRow['extension'],
          'thumb_width'   => THUMB_DEFAULT_X,
          'loop'          => $iLoop
      );

      # We want to get the image dimension of the original image
      # This function is not set to default due its long processing time
      if ($bAdvancedImageInformation == true) {
        $aPopupSize = getimagesize($sUrlPopup);
        $aThumbSize = getimagesize($sUrlThumb);
        $iImageSize = filesize(PATH_UPLOAD . '/gallery/' . $aRow['album_id'] . '/popup/' . $aRow['file']);

        $this->_aThumbs[$iId]['popup_width']  = $aPopupSize[0];
        $this->_aThumbs[$iId]['popup_height'] = $aPopupSize[1];
        $this->_aThumbs[$iId]['popup_size']   = $iImageSize;
        $this->_aThumbs[$iId]['popup_mime']   = $aPopupSize['mime'];
        $this->_aThumbs[$iId]['thumb_width']  = $aThumbSize[0];
        $this->_aThumbs[$iId]['thumb_height'] = $aThumbSize[1];
      }

      $iLoop++;
    }

    return $this->_aThumbs;
  }

  public function getThumbs($iId, $bAdvancedImageInformation = false) {
    return $this->_setThumbs($iId, $bAdvancedImageInformation);
  }

  public static function getAlbumName($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT title FROM " . SQL_PREFIX . "gallery_albums WHERE id = :album_id");
      $oQuery->bindParam('album_id', $iId);
      $bReturn = $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    # Do we need to highlight text?
    $sHighlight = isset($_REQUEST['highlight']) && !empty($_REQUEST['highlight']) ?
            $_REQUEST['highlight'] :
            '';

    if ($bReturn === true)
      return Helper::formatOutput($aResult['title'], $sHighlight);
  }

  public static function getAlbumContent($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT content FROM " . SQL_PREFIX . "gallery_albums WHERE id = :album_id");
      $oQuery->bindParam('album_id', $iId);
      $bReturn = $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    # Do we need to highlight text?
    $sHighlight = isset($_REQUEST['highlight']) && !empty($_REQUEST['highlight']) ?
            $_REQUEST['highlight'] :
            '';

    if ($bReturn === true)
      return Helper::formatOutput($aResult['content'], $sHighlight);
  }

  public static function getFileContent($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT content FROM " . SQL_PREFIX . "gallery_files WHERE id = :id");
      $oQuery->bindParam('id', $iId);
      $bReturn = $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    if ($bReturn === true)
      return Helper::formatOutput($aResult['content']);
  }

  public static function getFileData($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT * FROM " . SQL_PREFIX . "gallery_files WHERE id = :id");
      $oQuery->bindParam('id', $iId);
      $bReturn = $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    if ($bReturn === true)
      return $aResult;
  }

  public function create() {
    try {
      $oQuery = $this->_oDb->prepare("INSERT INTO
                                        " . SQL_PREFIX . "gallery_albums
                                        ( author_id,
                                          title,
                                          content,
                                          date)
                                      VALUES
                                        ( :author_id,
                                          :title,
                                          :content,
                                          :date )");

      $iUserId = USER_ID;
      $oQuery->bindParam('author_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title']));
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']));
      $oQuery->bindParam('date', time());
      $bResult = $oQuery->execute();

      $this->_iId = $this->_oDb->lastInsertId();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    if ($bResult === true) {
      $sPath = PATH_UPLOAD . '/gallery/' . (int) $this->_iId;

      $sPathThumbS = $sPath . '/32';
      $sPathThumbL = $sPath . '/' . THUMB_DEFAULT_X;
      $sPathThumbP = $sPath . '/popup';
      $sPathThumbO = $sPath . '/original';

      if (!is_dir($sPath))
        mkdir($sPath, 0755);

      if (!is_dir($sPathThumbS))
        mkdir($sPathThumbS, 0755);

      if (!is_dir($sPathThumbL))
        mkdir($sPathThumbL, 0755);

      if (!is_dir($sPathThumbP))
        mkdir($sPathThumbP, 0755);

      if (!is_dir($sPathThumbO))
        mkdir($sPathThumbO, 0755);
    }

    return $bResult;
  }

  public function update($iId) {
    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "gallery_albums
                                      SET
                                        title = :title,
                                        content = :content
                                      WHERE
                                        id = :id");

      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title']));
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']));
      $oQuery->bindParam('id', $iId);
      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  public function destroy($iId) {
    $sPath = PATH_UPLOAD . '/gallery/' . (int) $iId;

    try {
      $oQuery = $this->_oDb->prepare("SELECT file FROM " . SQL_PREFIX . "gallery_files WHERE album_id = :album_id");

      $oQuery->bindParam('album_id', $iId);
      $bReturn = $oQuery->execute();
      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    if ($bReturn === true) {
      foreach ($aResult as $aRow) {
        @unlink($sPath . '/32/' . $aRow['file']);
        @unlink($sPath . '/' . THUMB_DEFAULT_X . '/' . $aRow['file']);
        @unlink($sPath . '/popup/' . $aRow['file']);
        @unlink($sPath . '/original/' . $aRow['file']);
      }

      # Delete Folders
      @rmdir($sPath . '/32/');
      @rmdir($sPath . '/' . THUMB_DEFAULT_X);
      @rmdir($sPath . '/popup');
      @rmdir($sPath . '/original');
      @rmdir($sPath);

      try {
        $oQuery = $this->_oDb->prepare("DELETE FROM
                                          " . SQL_PREFIX . "gallery_files
                                        WHERE
                                          album_id = :album_id");

        $oQuery->bindParam('album_id', $iId);
        $bResult = $oQuery->execute();
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

      try {
        $oQuery = $this->_oDb->prepare("DELETE FROM
                                          " . SQL_PREFIX . "gallery_albums
                                        WHERE
                                          id = :album_id
                                        LIMIT
                                          1");

        $oQuery->bindParam('album_id', $iId);
        return $oQuery->execute();
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    }
  }

  public function createFile($aFile) {
    $oUploadFile = new Upload($this->_aRequest, $aFile);

    if($oUploadFile->uploadGalleryFile() == true) {
      try {
        $oQuery = $this->_oDb->prepare("INSERT INTO
                                          " . SQL_PREFIX . "gallery_files
                                            (album_id, author_id, file, extension, content, date)
                                        VALUES
                                          ( :album_id, :author_id, :file, :extension, :content, :date )");

        $iUserId = USER_ID;
        $oQuery->bindParam('album_id', $this->_aRequest['id']);
        $oQuery->bindParam('author_id', $iUserId);
        $oQuery->bindParam('file', $oUploadFile->getId());
        $oQuery->bindParam('extension', $oUploadFile->getExtension());
        $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']));
        $oQuery->bindParam('date', time());

        return $oQuery->execute();
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    }
  }

  public function getFilePath() {
    return $this->_sFilePath;
  }

  public function updateFile($iId) {
    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "gallery_files
                                      SET
                                        content = :content
                                      WHERE
                                        id = :id");

      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']));
      $oQuery->bindParam('id', $iId);

      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  public function destroyFile($iId) {
    try {
      $oQuery = $this->_oDb->prepare("SELECT file, album_id FROM " . SQL_PREFIX . "gallery_files WHERE id = :id");
      $oQuery->bindParam('id', $iId);
      $bReturn = $oQuery->execute();

      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    if ($bReturn === true) {
      foreach ($aResult as $aRow) {
        $sPath = PATH_UPLOAD . '/gallery/' . $aRow['album_id'];
        @unlink($sPath . '/32/' . $aRow['file']);
        @unlink($sPath . '/' . THUMB_DEFAULT_X . '/' . $aRow['file']);
        @unlink($sPath . '/popup/' . $aRow['file']);
        @unlink($sPath . '/original/' . $aRow['file']);
      }

      try {
        $oQuery = $this->_oDb->prepare("DELETE FROM
                                          " . SQL_PREFIX . "gallery_files
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
}