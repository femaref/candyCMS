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

namespace CandyCMS\Core\Models;

use CandyCMS\Core\Helpers\AdvancedException;
use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\Pagination;
use CandyCMS\Core\Helpers\Upload;
use PDO;

require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/Pagination.helper.php';

class Galleries extends Main {

  /**
   *
   * @access private
   * @var array
   *
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
    $aInts = array('id', 'author_id', 'uid', 'files_sum');

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
                                      u.id AS user_id,
                                      u.name AS user_name,
                                      u.surname AS user_surname,
                                      u.email AS user_email,
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
      foreach ($aResult as $aRow) {
        $iId = $aRow['id'];

        # need to specify 'galleries' because this might be called for rss feed generation
        $this->_aData[$iId] = $this->_formatForOutput($aRow, $aInts, null, 'galleries');
        $this->_aData[$iId]['files'] = ($aRow['files_sum'] > 0) ? $this->getThumbs($aRow['id'], $bAdvancedImageInformation) : '';
        $this->_aData[$iId]['url_createfile'] = $this->_aData[$iId]['url_clean'] . '/createfile';
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
    $aInts = array('id', 'album_id', 'author_id');

    # Clear existing array (fix, when we got no images at a gallery
    if (!empty($this->_aThumbs))
      unset($this->_aThumbs);

    try {
      $oQuery = $this->_oDb->prepare("SELECT
                                        f.*,
                                        u.id AS user_id,
                                        u.name AS user_name,
                                        u.surname AS user_surname,
                                        u.email AS user_email
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

    $aSizes = array('32', 'popup', 'original', 'thumb');
    $iLoop = 0;
    foreach ($aResult as $aRow) {
      $iId           = $aRow['id'];

      $sUrlUpload    = Helper::addSlash(PATH_UPLOAD . '/galleries/' . $aRow['album_id']);

      $this->_aThumbs[$iId]                 = $this->_formatForOutput($aRow, $aInts);
      $this->_aThumbs[$iId]['url']          = '/galleries/' . $aRow['album_id'] . '/image/' . $iId;

      foreach ($aSizes as $sSize)
        $this->_aThumbs[$iId]['url_' . $sSize] = $sUrlUpload . '/' . $sSize . '/' . $aRow['file'];

      $this->_aThumbs[$iId]['url_upload']   = $sUrlUpload;
      $this->_aThumbs[$iId]['url_thumb']    = $sUrlUpload . '/thumbnail/' . $aRow['file'];
      # /{$_REQUEST.controller}/{$f.id}/updatefile
      $this->_aThumbs[$iId]['url_update']   = $this->_aThumbs[$iId]['url_update'] . 'file';
      # /{$_REQUEST.controller}/{$f.id}/destroyfile?album_id={$_REQUEST.id}
      $this->_aThumbs[$iId]['url_destroy']  = $this->_aThumbs[$iId]['url_destroy'] . 'file?album_id=' . $aRow['album_id'];
      $this->_aThumbs[$iId]['thumb_width']  = THUMB_DEFAULT_X;
      $this->_aThumbs[$iId]['loop']         = $iLoop;

      # We want to get the image dimension of the original image
      # This function is not set to default due its long processing time
      if ($bAdvancedImageInformation == true) {
        $aPopupSize = getimagesize(Helper::removeSlash($this->_aThumbs[$iId]['url_popup']));
        $aThumbSize = getimagesize(Helper::removeSlash($this->_aThumbs[$iId]['url_thumb']));
        $iImageSize = filesize(Helper::removeSlash($this->_aThumbs[$iId]['url_popup']));

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
   * Return album name and album content.
   *
   * @static
   * @access public
   * @param integer $iId album ID
   * @param array $aRequest current request
   * @return string album name
   *
   */
  public static function getAlbumNameAndContent($iId, $aRequest = '') {
    if (empty(parent::$_oDbStatic))
      parent::connectToDatabase();

    try {
      $oQuery = parent::$_oDbStatic->prepare("SELECT title, content FROM " . SQL_PREFIX . "gallery_albums WHERE id = :album_id");
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

    if ($bReturn === true) {
      foreach ($aResult as $sKey => $sValue)
        $aResult[$sKey] = Helper::formatOutput($sValue, $sHighlight);

      return $aResult;
    }
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

      $aData = $oQuery->fetch(PDO::FETCH_ASSOC);

      $aInts = array('id', 'album_id', 'author_id');
      foreach ($aInts as $sInt)
        $aData[$sInt] = (int) $aData[$sInt];

      return $aData;
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

        $aSizes = array ('32', 'popup', 'original', 'thumbnail');
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
          $aSizes = array ('32', 'popup', 'original', 'thumbnail');
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