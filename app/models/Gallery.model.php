<?php

/*
 * This software is copyright protected. Use only allowed on licensed
 * websites. Contact author for further information or to receive a license.
 *
 * @link http://marcoraddatz.com
 * @copyright 2007 - 2008 Marco Raddatz
 * @author Marco Raddatz <mr at marcoraddatz dot com>
 * @package CMS
 * @version 1.0
 */

class Model_Gallery extends Model_Main {
	private $_aThumbs;

	private final function _setData($bEdit = false) {
		if( !empty($this->_iID) )
			$sWhere = "WHERE a.id = '"	.$this->_iID.	"'";
		else
			$sWhere = '';

		$oGetData = new Query("	SELECT
															a.*,
															u.id AS uid,
															u.name,
															u.surname,
															COUNT(f.id) AS filesSum
														FROM
															gallery_album a
														LEFT JOIN
															user u
														ON
															a.authorID=u.id
														LEFT JOIN
															gallery_file f
														ON
															f.aid=a.id
														"	.$sWhere.	"
														GROUP BY
															a.id
														ORDER BY
															a.id DESC");

		if($bEdit == true) {
			$aRow = $oGetData->fetch();
			$this->_aData = array(	'title' => Helper::removeSlahes($aRow['title']),
					'description' => Helper::removeSlahes($aRow['description'], true));
		}
		else {
			while($aRow = $oGetData->fetch()) {
				$iID = $aRow['id'];
				$this->_aData[$iID] = array('id' => $aRow['id'],
						'authorID' => $aRow['authorID'],
						'title' => Helper::formatBBCode($aRow['title']),
						'description' => Helper::formatBBCode($aRow['description'], true),
						'date' => Helper::formatTimestamp($aRow['date']),
						'files_sum' => $aRow['filesSum']
				);

				if($aRow['filesSum'] > 0)
					$this->_aData[$iID]['files'] = $this->getThumbs($iID, LIMIT_ALBUM_THUMBS);
				else
					$this->_aData[$iID]['files'] = '';
			}
		}
	}

	public final function getData($iID = '', $bEdit = false) {
		if( !empty($iID) )
			$this->_iID = (int)$iID;

		$this->_setData($bEdit);
		return $this->_aData;
	}

	public final function getId() {
		return $this->_iID;
	}

	private final function _setThumbs($iAID, $iLimit) {
		# Clear existing array
		if(!empty($this->_aThumbs))
			unset($this->_aThumbs);

		$oCountEntries = new Query("SELECT
																	COUNT(*)
																FROM
																	gallery_file
																WHERE
																	aid='"	.$iAID.	"'");

		$this->_iEntries = $oCountEntries->count();
		$this->_oPages = new Pages($this->m_aRequest, $this->_iEntries, $iLimit);

		$oGetData = new Query("	SELECT
															f.*
														FROM
															gallery_file f
														WHERE
															f.aid="	.$iAID.	"
														ORDER BY
															f.date ASC
														LIMIT
															"	.$this->_oPages->getOffset().	",
															"	.$this->_oPages->getLimit() );

		$iLoop = 0;
		while($aRow = $oGetData->fetch()) {
			$iID = $aRow['id'];
			$this->_aThumbs[$iID] = array(	'id' => $aRow['id'],
					'file' => $aRow['file'],
					'full_path' => WEBSITE_URL. '/' .PATH_UPLOAD.	'/gallery/'	.$aRow['aid'],
					'description' => Helper::formatBBCode($aRow['description']),
					'date' => Helper::formatTimestamp($aRow['date']),
					'extension' => $aRow['extension'],
					'dim' => THUMB_DEFAULT_X,
					'loop' => $iLoop
			);
			$iLoop++;
		}
	}

	public final function getThumbs($iAID, $iLimit) {
		$this->_setThumbs($iAID, $iLimit);
		return $this->_aThumbs;
	}

	private final function _setAlbumName($iAID) {
		$oGetName = new Query("	SELECT
															title
														FROM
															gallery_album
														WHERE
															id='"	.(int)$iAID.	"'");

		$this->_aAlbumName = $oGetName->fetch();
		return $this->_aAlbumName['title'];
	}

	public final function getAlbumName($iAID) {
		return $this->_setAlbumName($iAID);
	}

	private final function _setAlbumDescription($iAID) {
		$oGetDescription = new Query("SELECT
																		description
																	FROM
																		gallery_album
																	WHERE
																		id='"	.(int)$iAID.	"'");

		$this->_aAlbumDescription = $oGetDescription->fetch();
		return $this->_aAlbumDescription['description'];
	}

	public final function getAlbumDescription($iAID) {
		return $this->_setAlbumDescription($iAID);
	}

	public function create() {
		$oQuery = new Query("	INSERT INTO
														gallery_album(authorID, title, description, date)
													VALUES(
														'"	.USERID.	"',
														'"	.Helper::formatHTMLCode($this->m_aRequest['title']).	"',
														'"	.Helper::formatHTMLCode($this->m_aRequest['description']).	"',
														'"	.time().	"')
														");

		$this->_iID = mysql_insert_id();
		$sPath = PATH_UPLOAD.	'/gallery/'	.$this->_iID;

		$sPathThumbS = $sPath.	'/32';
		$sPathThumbL = $sPath.	'/'	.THUMB_DEFAULT_X;
		$sPathThumbP = $sPath.	'/' .POPUP_DEFAULT_X;
		$sPathThumbO = $sPath.	'/original';

		if(!is_dir($sPath))
			mkdir($sPath, 0755);

		if(!is_dir($sPathThumbS))
			mkdir($sPathThumbS, 0755);

		if(!is_dir($sPathThumbL))
			mkdir($sPathThumbL, 0755);

		if(!is_dir($sPathThumbP))
			mkdir($sPathThumbP, 0755);

		if(!is_dir($sPathThumbO))
			mkdir($sPathThumbO, 0755);

		return $oQuery;
	}

	public function update($iID) {
		return new Query("UPDATE
												`gallery_album`
											SET
												title = '"	.Helper::formatHTMLCode($this->m_aRequest['title'], false).	"',
												description = '"	.Helper::formatHTMLCode($this->m_aRequest['description'], false).	"'
											WHERE
												`id` = '"	.(int)$iID.	"'");
	}

	public final function destroy($iID) {
		$sPath = PATH_UPLOAD.	'/gallery/'	.(int)$iID;

		# Delete Files
		$oGetImages = new Query("	SELECT
																file
															FROM
																gallery_file
															WHERE
																aid = '"	.(int)$iID.	"'");

		while($aRow = $oGetImages->fetch()) {
			@unlink($sPath.	'/32/'	.$aRow['file']);
			@unlink($sPath.	'/'	.THUMB_DEFAULT_X.	'/'	.$aRow['file']);
			@unlink($sPath.	'/' .POPUP_DEFAULT_X. '/'	.$aRow['file']);
			@unlink($sPath.	'/original/'	.$aRow['file']);
		}

		# Clear Database
		new Query("	DELETE FROM
									`gallery_file`
								WHERE
									`aid` = '"	.(int)$this->m_aRequest['id'].	"'");

		new Query("	DELETE FROM
									`gallery_album`
								WHERE
									`id` = '"	.(int)$this->m_aRequest['id'].	"'");

		# Delete Folders
		@rmdir($sPath.	'/32/');
		@rmdir($sPath.	'/'	.THUMB_DEFAULT_X);
		@rmdir($sPath.	'/' .POPUP_DEFAULT_X);
		@rmdir($sPath.	'/original');
		@rmdir($sPath);

		return $oGetImages;
	}

	public final function createFile() {
		$oUploadFile = new Upload($this->m_aRequest, $this->m_aFile);
		$sFilePath = $oUploadFile->uploadGalleryFile();
    $this->m_aRequest['description']  = (isset($this->m_aRequest['description']) && !empty($this->m_aRequest['description']))
                                      ? $this->m_aRequest['description']
                                      : '';

		new Query("	INSERT INTO
									gallery_file(aid, file, extension, description, date)
								VALUES(
									'"	.(int)$this->m_aRequest['id'].	"',
									'"	.$oUploadFile->getId().	"',
									'"	.$oUploadFile->getExtension().	"',
									'"	.Helper::formatHTMLCode($this->m_aRequest['description']).	"',
									'"	.time().	"')");

		return $sFilePath;
	}

	public final function updateFile($iID) {
		$oQuery = new Query(" UPDATE
                            `gallery_file`
                          SET
                            description = '"	.Helper::formatHTMLCode($this->m_aRequest['description'], false).	"'
                          WHERE
                            `id` = '"	.$iID.	"'");
    return $oQuery;
	}

	public final function destroyFile($iID) {
		$oGetFileData = new Query("	SELECT
																	file, aid
																FROM
																	gallery_file
																WHERE
																	id = '"	.(int)$iID.	"'");

		$aRow = $oGetFileData->fetch();
		$sPath = PATH_UPLOAD.	'/gallery/'	.$aRow['aid'];

		@unlink($sPath.	'/32/'	.$aRow['file']);
		@unlink($sPath.	'/'	.THUMB_DEFAULT_X.	'/'	.$aRow['file']);
		@unlink($sPath.	'/' .POPUP_DEFAULT_X. '/'	.$aRow['file']);
		@unlink($sPath.	'/original/'	.$aRow['file']);

		return new Query("DELETE FROM
                        `gallery_file`
                      WHERE
                        `id` = '"	.(int)$this->m_aRequest['id'].	"'");
	}
}