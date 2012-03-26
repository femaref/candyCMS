<?php

/**
 * Handle all gallery SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Model;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\Pagination as Pagination;
use CandyCMS\Helper\Upload as Upload;
use PDO;

require_once PATH_STANDARD . '/app/helpers/Pagination.helper.php';

class Galleries extends Main {

  /**
   *
   * @access private
   * @var array
   */
  private $_aThumbs;

  /**
   * Get blog entry or blog overview data. Depends on available ID.
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
    $sWhere   = '';
    $iResult  = 0;

    if (empty($iId)) {
      try {
        $oQuery = $this->_oDb->query("SELECT COUNT(*) FROM " . SQL_PREFIX . "gallery_albums");
        $iResult = $oQuery->fetchColumn();
      }
      catch (\PDOException $p) {
        AdvancedException::reportBoth('0042 - ' . $p->getMessage());
        exit('SQL error.');
      }

      # Bugfix: Set update to false when creating an entry to avoid offset warnings.
      $bUpdate = false;
    }

    # Single entry
    else
      $sWhere = "WHERE a.id = '" . $iId . "'";

    $this->oPagination = new Pagination($this->_aRequest, (int) $iResult, $iLimit);

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
                                      " . $this->oPagination->getOffset() . ",
                                      " . $this->oPagination->getLimit());

      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0044 - ' . $p->getMessage());
      exit('SQL error.');
    }

    # Update a single entry. Fix it with 0 o
    if ($bUpdate === true)
      $this->_aData = $this->_formatForUpdate($aResult[0]);

    else {
      $aInts = array('id', 'author_id', 'uid', 'files_sum');
      foreach ($aResult as $aRow) {
        $iId = $aRow['id'];

        $this->_aData[$iId] = $this->_formatForOutput($aRow, 'galleries', $aInts);
        $this->_aData[$iId]['files'] = ($aRow['files_sum'] > 0) ? $this->getThumbs($aRow['id'], $bAdvancedImageInformation) : '';
      }
    }

    return $this->_aData;
  }

  /**
   * Get thumbnail array.
   *
   * @access public
   * @param integer $iId album id to fetch images from
   * @param boolean $bAdvancedImageInformation fetch additional information like width, height etc.
   * @return array $this->_aThumbs processed array with image information
   *
   */
  public function getThumbs($iId, $bAdvancedImageInformation = false) {
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

      $oQuery->bindParam('album_id', $iId, PDO::PARAM_INT);
      $oQuery->execute();

      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0045 - ' . $p->getMessage());
      exit('SQL error.');
    }

    $iLoop = 0;
    $aInts = array('id', 'album_id', 'author_id');
    foreach ($aResult as $aRow) {
      $iId           = $aRow['id'];
      $sUrlUpload    = Helper::addSlash(PATH_UPLOAD . '/galleries/' . $aRow['album_id']);
      $sUrl32        = $sUrlUpload . '/32/' . $aRow['file'];
      $sUrlPopup     = $sUrlUpload . '/popup/' . $aRow['file'];
      $sUrlOriginal  = $sUrlUpload . '/original/' . $aRow['file'];
      $sUrlThumb     = $sUrlUpload . '/' . THUMB_DEFAULT_X . '/' . $aRow['file'];

      $this->_aThumbs[$iId]                 = $this->_formatForOutput($aRow, 'galleries', $aInts);
      $this->_aThumbs[$iId]['url']          = '/galleries/' . $aRow['album_id'] . '/image/' . $iId;
      $this->_aThumbs[$iId]['url_32']       = $sUrl32;
      $this->_aThumbs[$iId]['url_upload']   = $sUrlUpload;
      $this->_aThumbs[$iId]['url_popup']    = $sUrlPopup;
      $this->_aThumbs[$iId]['url_original'] = $sUrlOriginal;
      $this->_aThumbs[$iId]['url_thumb']    = $sUrlThumb;
      $this->_aThumbs[$iId]['thumb_width']  = THUMB_DEFAULT_X;
      $this->_aThumbs[$iId]['loop']         = $iLoop;

      # We want to get the image dimension of the original image
      # This function is not set to default due its long processing time
      if ($bAdvancedImageInformation == true) {
        $aPopupSize = getimagesize(Helper::removeSlash($sUrlPopup));
        $aThumbSize = getimagesize(Helper::removeSlash($sUrlThumb));
        $iImageSize = filesize(Helper::removeSlash(PATH_UPLOAD . '/galleries/' .
                $aRow['album_id'] . '/popup/' . $aRow['file']));

        $this->_aThumbs[$iId]['popup_width']  = $aPopupSize[0];
        $this->_aThumbs[$iId]['popup_height'] = $aPopupSize[1];
        $this->_aThumbs[$iId]['popup_size']   = $iImageSize;
        $this->_aThumbs[$iId]['popup_mime']   = $aPopupSize['mime'];
        $this->_aThumbs[$iId]['thumb_width']  = $aThumbSize[0];
        $this->_aThumbs[$iId]['thumb_height'] = $aThumbSize[1];
      }

      ++$iLoop;
    }

    return $this->_aThumbs;
  }

  /**
   * Return album name.
   *
   * @static
   * @access public
   * @param integer $iId album ID
   * @param array $aRequest current request
   * @return string album name
   *
   */
  public static function getAlbumName($iId, $aRequest = '') {
    if (empty(parent::$_oDbStatic))
      parent::connectToDatabase();

    try {
      $oQuery = parent::$_oDbStatic->prepare("SELECT title FROM " . SQL_PREFIX . "gallery_albums WHERE id = :album_id");
      $oQuery->bindParam('album_id', $iId, PDO::PARAM_INT);

      $bReturn = $oQuery->execute();
      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0046 - ' . $p->getMessage());
      exit('SQL error.');
    }

    # Do we need to highlight text?
    $sHighlight = isset($aRequest['highlight']) ?
            $aRequest['highlight'] :
            '';

    if ($bReturn === true)
      return Helper::formatOutput($aResult['title'], $sHighlight);
  }

  /**
   * Get the album content (former description).
   *
   * @static
   * @access public
   * @param integer $iId album ID
   * @param array $aRequest current request
   * @return string content/destription
   *
   */
  public static function getAlbumContent($iId, $aRequest = '') {
    if (empty(parent::$_oDbStatic))
      parent::connectToDatabase();

    try {
      $oQuery = parent::$_oDbStatic->prepare("SELECT content FROM " . SQL_PREFIX . "gallery_albums WHERE id = :album_id");
      $oQuery->bindParam('album_id', $iId, PDO::PARAM_INT);

      $bReturn = $oQuery->execute();
      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0047 - ' . $p->getMessage());
      exit('SQL error.');
    }

    # Do we need to highlight text?
    $sHighlight = isset($aRequest['highlight']) ?
            $aRequest['highlight'] :
            '';

    if ($bReturn === true)
      return Helper::formatOutput($aResult['content'], $sHighlight);
  }

  /**
   * Get file content (former description).
   *
   * @static
   * @access public
   * @param integer $iId album ID
   * @return array
   *
   */
  public static function getFileDetails($iId) {
    if (empty(parent::$_oDbStatic))
      parent::connectToDatabase();

    try {
      $oQuery = parent::$_oDbStatic->prepare("SELECT album_id, content FROM " . SQL_PREFIX . "gallery_files WHERE id = :id");
      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);

      $bReturn = $oQuery->execute();
      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0048 - ' . $p->getMessage());
      exit('SQL error.');
    }

    if ($bReturn === true)
      return $aResult;
  }

  /**
   * Get all file data.
   *
   * @static
   * @access public
   * @param integer $iId album ID
   * @return array file data
   *
   */
  public static function getFileData($iId) {
    if (empty(parent::$_oDbStatic))
      parent::connectToDatabase();

    try {
      $oQuery = parent::$_oDbStatic->prepare("SELECT * FROM " . SQL_PREFIX . "gallery_files WHERE id = :id");
      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
      $oQuery->execute();

      return $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0049 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Create a new album.
   *
   * @access public
   * @return boolean status of query
   *
   */
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

      $oQuery->bindParam('author_id', $this->_aSession['user']['id'], PDO::PARAM_INT);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title']), PDO::PARAM_STR);
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']), PDO::PARAM_STR);
      $oQuery->bindParam('date', time(), PDO::PARAM_INT);

      $bReturn = $oQuery->execute();
      parent::$iLastInsertId = Helper::getLastEntry('gallery_albums');

      return $bReturn;
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0050 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0051 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Update an album.
   *
   * @access public
   * @param integer $iId
   * @return boolean status of query
   *
   */
  public function update($iId) {
    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "gallery_albums
                                      SET
                                        title = :title,
                                        content = :content
                                      WHERE
                                        id = :id");

      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title']), PDO::PARAM_STR);
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']), PDO::PARAM_STR);
      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);

      return $oQuery->execute();
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0052 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0053 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Destroy a full album.
   *
   * @access public
   * @param integer $iId album ID
   * @return type
   *
   */
  public function destroy($iId) {
    $sPath = Helper::removeSlash(PATH_UPLOAD . '/' . $this->_aRequest['controller'] . '/' . (int) $iId);

    # Fetch all images
    try {
      $oQuery = $this->_oDb->prepare("SELECT
                                        file
                                      FROM
                                        " . SQL_PREFIX . "gallery_files
                                      WHERE
                                        album_id = :album_id");

      $oQuery->bindParam('album_id', $iId);

      $bReturn = $oQuery->execute();
      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0054 - ' . $p->getMessage());
      exit('SQL error.');
    }

    if ($bReturn === true) {
      # Destroy files from database
      try {
        $oQuery = $this->_oDb->prepare("DELETE FROM
                                          " . SQL_PREFIX . "gallery_files
                                        WHERE
                                          album_id = :album_id");

        $oQuery->bindParam('album_id', $iId);
        $oQuery->execute();

        $aSizes = array ('32', 'popup', 'original', THUMB_DEFAULT_X);
        foreach ($aSizes as $sSize) {
          # destroy files from disk
          foreach ($aResult as $aRow)
            @unlink($sPath . '/' . $sSize . '/' . $aRow['file']);

          # Destroy Folders
          @rmdir($sPath . '/' . $sSize);
        }
      }
      catch (\PDOException $p) {
        try {
          $this->_oDb->rollBack();
        }
        catch (\Exception $e) {
          AdvancedException::reportBoth('0055 - ' . $e->getMessage());
        }

        AdvancedException::reportBoth('0056 - ' . $p->getMessage());
        exit('SQL error.');
      }

      # Destroy albums from database
      try {
        $oQuery = $this->_oDb->prepare("DELETE FROM
                                          " . SQL_PREFIX . "gallery_albums
                                        WHERE
                                          id = :album_id
                                        LIMIT
                                          1");

        $oQuery->bindParam('album_id', $iId);
        $bReturn = $oQuery->execute();
        @rmdir($sPath);
        return $bReturn;
      }
      catch (\PDOException $p) {
        try {
          $this->_oDb->rollBack();
        }
        catch (\Exception $e) {
          AdvancedException::reportBoth('0057 - ' . $e->getMessage());
        }

        AdvancedException::reportBoth('0058 - ' . $p->getMessage());
        exit('SQL error.');
      }
    }
  }

  /**
   * Create a new file.
   *
   * @access public
   * @param string $sFile file name
   * @param string $sExtension file extension
   * @return boolean status of query
   *
   */
  public function createFile($sFile, $sExtension) {
    try {
      $oQuery = $this->_oDb->prepare("INSERT INTO
                                        " . SQL_PREFIX . "gallery_files
                                        ( album_id,
                                          author_id,
                                          file,
                                          extension,
                                          content,
                                          date)
                                      VALUES
                                        ( :album_id,
                                          :author_id,
                                          :file,
                                          :extension,
                                          :content,
                                          :date )");

      $oQuery->bindParam('album_id', $this->_aRequest['id'], PDO::PARAM_INT);
      $oQuery->bindParam('author_id', $this->_aSession['user']['id'], PDO::PARAM_INT);
      $oQuery->bindParam('file', $sFile, PDO::PARAM_STR);
      $oQuery->bindParam('extension', $sExtension, PDO::PARAM_STR);
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']), PDO::PARAM_STR);
      $oQuery->bindParam('date', time(), PDO::PARAM_INT);

      $bReturn = $oQuery->execute();
      parent::$iLastInsertId = Helper::getLastEntry('gallery_files');

      return $bReturn;
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0059 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0060 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Update a file.
   *
   * @access public
   * @param integer $iId file ID
   * @return boolean status of query
   *
   */
  public function updateFile($iId) {
    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "gallery_files
                                      SET
                                        content = :content
                                      WHERE
                                        id = :id");

      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']), PDO::PARAM_STR);
      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);

      return $oQuery->execute();
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0061 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0062 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Destroy a file and delete from HDD.
   *
   * @access public
   * @param integer $iId file ID
   * @return boolean status of query
   *
   */
  public function destroyFile($iId) {
    try {
      $oQuery = $this->_oDb->prepare("SELECT
                                        file,
                                        album_id
                                      FROM
                                        " . SQL_PREFIX . "gallery_files
                                      WHERE
                                        id = :id");

      $oQuery->bindParam('id', $iId);

      $bReturn = $oQuery->execute();
      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0063 - ' . $p->getMessage());
      exit('SQL error.');
    }

    if ($bReturn === true) {
      try {
        $oQuery = $this->_oDb->prepare("DELETE FROM
                                          " . SQL_PREFIX . "gallery_files
                                        WHERE
                                          id = :id
                                        LIMIT
                                          1");

        $oQuery->bindParam('id', $iId);
        $bReturn = $oQuery->execute();

        if ($bReturn) {
          $aSizes = array ('32', 'popup', 'original', THUMB_DEFAULT_X);
          foreach ($aResult as $aRow) {
            $sPath = Helper::removeSlash(PATH_UPLOAD . '/' . $this->_aRequest['controller'] . '/' . $aRow['album_id']);
            foreach ($aSizes as $sSize)
              @unlink($sPath . '/' . $sSize . '/' . $aRow['file']);
          }
        }
        return $bReturn;
      }
      catch (\PDOException $p) {
        try {
          $this->_oDb->rollBack();
        }
        catch (\Exception $e) {
          AdvancedException::reportBoth('0064 - ' . $e->getMessage());
        }

        AdvancedException::reportBoth('0065 - ' . $p->getMessage());
        exit('SQL error.');
      }
    }
  }
}