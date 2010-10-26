<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Model_Gallery extends Model_Main {
	private $_aThumbs;

	private final function _setData($bEdit = false) {
    $sWhere = '';

		if (!empty($this->_iId))
      $sWhere = "WHERE a.id = '" . $this->_iId . "'";

    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->query("	SELECT
                                a.*,
                                u.id AS uid,
                                u.name,
                                u.surname,
                                COUNT(f.id) AS filesSum
                              FROM
                                gallery_albums a
                              LEFT JOIN
                                users u
                              ON
                                a.author_id=u.id
                              LEFT JOIN
                                gallery_files f
                              ON
                                f.album_id=a.id
                              "	.$sWhere.	"
                              GROUP BY
                                a.id
                              ORDER BY
                                a.id DESC");

      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
      $oDb = null;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

		if($bEdit == true) {
      $aRow = & $aResult;

      # Fix fetchAll with array 0
			$this->_aData = array(
          'title'       => Helper::removeSlahes($aRow[0]['title']),
					'description' => Helper::removeSlahes($aRow[0]['description'], true));
		}
		else {
			foreach ($aResult as $aRow) {
				$iId = $aRow['id'];
				$this->_aData[$iId] = array(
            'id'          => $aRow['id'],
            'author_id'   => $aRow['author_id'],
            'title'       => Helper::formatOutput($aRow['title']),
            'description' => Helper::formatOutput($aRow['description'], true),
            'date'        => Helper::formatTimestamp($aRow['date'], true),
            'datetime'    => Helper::formatTimestamp($aRow['date']),
            'files_sum'   => $aRow['filesSum']
				);

				if ($aRow['filesSum'] > 0)
          $this->_aData[$iId]['files'] = $this->getThumbs($iId, LIMIT_ALBUM_THUMBS);
        else
          $this->_aData[$iId]['files'] = '';
			}
		}
	}

	public final function getData($iId = '', $bEdit = false) {
    if (!empty($iId))
      $this->_iId = (int) $iId;

    $this->_setData($bEdit);
    return $this->_aData;
  }

	public final function getId() {
    return $this->_iId;
  }

	private final function _setThumbs($iId, $iLimit) {
		# Clear existing array
		if (!empty($this->_aThumbs))
      unset($this->_aThumbs);

    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
                  PDO::ATTR_PERSISTENT => true
              ));
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT * FROM gallery_files WHERE album_id = :album_id");
      $oQuery->bindParam('album_id', $iId);
      $bReturn = $oQuery->execute();

      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    $this->_iEntries = count($aResult);
    $this->oPages = new Pages($this->_aRequest, $this->_iEntries, $iLimit);

    if($this->_iEntries > 0) {
      try {
        $oQuery = $oDb->prepare("	SELECT
                                    *
                                  FROM
                                    gallery_files
                                  WHERE
                                    album_id= :album_id
                                  ORDER BY
                                    date ASC
                                  LIMIT
                                    :offset,
                                    :limit");

        $oQuery->bindParam('album_id', $iId);
        $oQuery->bindParam('limit', $this->oPages->getLimit(), PDO::PARAM_INT);
        $oQuery->bindParam('offset', $this->oPages->getOffset(), PDO::PARAM_INT);
        $oQuery->execute();

        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
        $oDb = null;
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
      }

      $iLoop = 0;
      foreach ($aResult as $aRow) {
        $iId = $aRow['id'];
        $this->_aThumbs[$iId] = array(
            'id'          => $aRow['id'],
            'album_id'    => $aRow['album_id'],
            'file'        => $aRow['file'],
            'full_path'   => WEBSITE_URL. '/' .PATH_UPLOAD.	'/gallery/'	.$aRow['album_id'],
            'description' => Helper::formatOutput($aRow['description']),
            'date'        => Helper::formatTimestamp($aRow['date']),
            'extension'   => $aRow['extension'],
            'dim'         => THUMB_DEFAULT_X,
            'loop'        => $iLoop
        );

        $iLoop++;
      }
    }
    else {
      $oDb = null;
      return false;
    }
	}

	public final function getThumbs($iId, $iLimit) {
    $this->_setThumbs($iId, $iLimit);
    return $this->_aThumbs;
  }

  public final static function getAlbumName($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT title FROM gallery_albums WHERE id = :album_id");

      $oQuery->bindParam('album_id', $iId);
      $bReturn = $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    if($bReturn == true)
      return $aResult['title'];
  }

  public final static function getAlbumDescription($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT description FROM gallery_albums WHERE id = :album_id");

      $oQuery->bindParam('album_id', $iId);
      $bReturn = $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    if($bReturn == true)
      return $aResult['description'];
  }

  public final function getFileDescription($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT description FROM gallery_files WHERE id = :id");

      $oQuery->bindParam('id', $iId);
      $bReturn = $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    if($bReturn == true)
      return $aResult['description'];
  }

	public function create() {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare(" INSERT INTO
                                  gallery_albums (author_id, title, description, date)
                                VALUES
                                  ( :author_id, :title, :description, :date )");

      $iUserId = USER_ID;
      $oQuery->bindParam('author_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title']));
      $oQuery->bindParam('description', Helper::formatInput($this->_aRequest['description']));
      $oQuery->bindParam('date', time());
      $bResult = $oQuery->execute();

      $this->_iId = $oDb->lastInsertId();
      $oDb = null;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    if ($bResult == true) {
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
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	UPDATE
                                  gallery_albums
                                SET
                                  title = :title,
                                  description = :description
                                WHERE
                                  id = :where");

      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title']));
      $oQuery->bindParam('description', Helper::formatInput($this->_aRequest['description']));
      $oQuery->bindParam('where', $iId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

	public final function destroy($iId) {
    $sPath = PATH_UPLOAD . '/gallery/' . (int) $iId;

    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
                  PDO::ATTR_PERSISTENT => true
              ));
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT file FROM gallery_files WHERE album_id = :album_id");

      $oQuery->bindParam('album_id', $iId);
      $bReturn = $oQuery->execute();
      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    if ($bReturn == true) {
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
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->prepare("	DELETE FROM
                                    gallery_files
                                  WHERE
                                    album_id = :album_id");

        $oQuery->bindParam('album_id', $iId);
        $bResult = $oQuery->execute();
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
      }

      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->prepare("	DELETE FROM
                                    gallery_albums
                                  WHERE
                                    id = :album_id
                                  LIMIT
                                    1");

        $oQuery->bindParam('album_id', $iId);
        $bResult = $oQuery->execute();
        $oDb = null;
        return $bResult;
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
      }
    }
    else {
      $oDb = null;
      return false;
    }
  }

	public final function createFile($iUserId = '') {
    $oUploadFile = new Upload($this->_aRequest, $this->_aFile);

    if($oUploadFile->uploadGalleryFile() == true) {
      $this->_aRequest['description'] = (isset($this->_aRequest['description']) && !empty($this->_aRequest['description'])) ?
              $this->_aRequest['description'] :
              '';

      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->prepare(" INSERT INTO
                                    gallery_files (album_id, author_id, file, extension, description, date)
                                  VALUES
                                    ( :album_id, :author_id, :file, :extension, :description, :date )");

        $oQuery->bindParam('album_id', $this->_aRequest['id']);
        $oQuery->bindParam('author_id', $iUserId);
        $oQuery->bindParam('file', $oUploadFile->getId());
        $oQuery->bindParam('extension', $oUploadFile->getExtension());
        $oQuery->bindParam('description', Helper::formatInput($this->_aRequest['description']));
        $oQuery->bindParam('date', time());

        $bResult = $oQuery->execute();
        $oDb = null;
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
      }

      # Log uploaded image. Request ID = album id
      if($bResult === true)
        Helper::log($this->_aRequest['section'], 'createfile', (int) $this->_aRequest['id']);
      print_r($this->_aRequest);

			# TODO: Return true or false?
      return $oUploadFile->sFilePath;
    }
    else
			return false;
      #return Helper::errorMessage (LANG_ERROR_UPLOAD_CREATE);
  }

	public final function updateFile($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	UPDATE
                                  gallery_files
                                SET
                                  description = :description
                                WHERE
                                  id = :where");

      $oQuery->bindParam('description', Helper::formatInput($this->_aRequest['description']));
      $oQuery->bindParam('where', $iId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

	public final function destroyFile($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
                  PDO::ATTR_PERSISTENT => true
              ));
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT file, album_id FROM gallery_files WHERE id = :id");

      $oQuery->bindParam('id', $iId);
      $bReturn = $oQuery->execute();
      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    if ($bReturn == true) {
      foreach ($aResult as $aRow) {
        $sPath = PATH_UPLOAD . '/gallery/' . $aRow['album_id'];
        @unlink($sPath . '/32/' . $aRow['file']);
        @unlink($sPath . '/' . THUMB_DEFAULT_X . '/' . $aRow['file']);
        @unlink($sPath . '/popup/' . $aRow['file']);
        @unlink($sPath . '/original/' . $aRow['file']);
      }

      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->prepare("	DELETE FROM
                                    gallery_files
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
    else {
      $oDb = null;
      return false;
    }
	}
}