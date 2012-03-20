<?php

require_once '../../config/Candy.inc.php';

require_once '../../app/helpers/Helper.helper.php';
require_once '../../app/helpers/Image.helper.php';

define('CREATE_IMAGES', false);

class AddonImage extends \CandyCMS\Helper\Image {

	public function resizeDefault($iWidth, $iMaxHeight = '', $sFolder = '') {
		if (empty($sFolder))
			$sFolder = $iWidth;

		if ($this->_aInfo[1] > $this->_aInfo[0] && !empty($iMaxHeight)) {
			$iFactor = $iMaxHeight / $this->_aInfo[1];
			$iNewY = $iMaxHeight;
			$iNewX = round($this->_aInfo[0] * $iFactor);
		}
		else {
			$iFactor = $iWidth / $this->_aInfo[0];
			$iNewX = $iWidth;
			$iNewY = round($this->_aInfo[1] * $iFactor);
		}

		$oNewImg = imagecreatetruecolor($iNewX, $iNewY);
		$oOldImg = ImageCreateFromJPEG($this->_sOriginalPath);
		imagecopyresampled($oNewImg, $oOldImg, 0, 0, 0, 0, $iNewX, $iNewY, $this->_aInfo[0], $this->_aInfo[1]);
		ImageJPEG($oNewImg, '../' . PATH_UPLOAD . '/' . $this->_sFolder . '/' . $sFolder . '/' . $this->_iId . '.jpg', 75);

		imagedestroy($oNewImg);
	}

	public function resizeAndCut($iWidth, $sFolder = '') {
		if (empty($sFolder))
			$sFolder = $iWidth;

#die(print_r($this));
		$iNewX = $iWidth;
		$iNewY = $iWidth;
		$iDstX = 0;
		$iDstY = 0;
		$iSrcX = 0;
		$iSrcY = 0;

		if ($this->_aInfo[1] > $this->_aInfo[0]) { // y bigger than x
			$iSrcY = ($this->_aInfo[1] - $this->_aInfo[0]) / 2;
			$iFactor = $iNewX / $this->_aInfo[0];
			$iNewY = round($this->_aInfo[1] * $iFactor);
		}
		else {
			$iSrcX = ($this->_aInfo[0] - $this->_aInfo[1]) / 2;
			$iFactor = $iNewY / $this->_aInfo[1];
			$iNewX = round($this->_aInfo[0] * $iFactor);
		}

		$oOldImg = ImageCreateFromJPEG($this->_sOriginalPath);
		$oNewImg = imagecreatetruecolor($iWidth, $iWidth);
		$oBg = ImageColorAllocate($oNewImg, 255, 255, 255);

#imagefill($oNewImg, 0, 0, $oBg);
		imagecopyresampled($oNewImg, $oOldImg, $iDstX, $iDstY, $iSrcX, $iSrcY, $iNewX, $iNewY, $this->_aInfo[0], $this->_aInfo[1]);

		ImageJPEG($oNewImg, '../' . PATH_UPLOAD . '/' . $this->_sFolder . '/' . $sFolder . '/' . $this->_iId . '.' . $this->_sImgType, 75);

		imagedestroy($oNewImg);
	}
}

class Convert {

	public $oDb;

	function __construct() {
		$this->oDb = new PDO('mysql:host=' . SQL_HOST . ';port=' . SQL_PORT . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
								PDO::ATTR_PERSISTENT => true));
		$this->oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	function getUsers() {
		$oQuery = $this->oDb->query("TRUNCATE website_users");
		$oQuery = $this->oDb->query("SELECT * FROM users WHERE id > 1");
		$aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);

		$iId = 1;
		foreach ($aResult as $aRow) {

			$oExtra = $this->oDb->query("SELECT * FROM users_extra WHERE uid = " . $aRow['id']);
			$aExtra = $oExtra->fetch(PDO::FETCH_ASSOC);

			$oQuery = $this->oDb->prepare("
  INSERT INTO
  website_users(id, name, surname, email, user_right, date, last_login, password)
  VALUES
  ( :id, :name, :surname, :email, :user_right, :date, :last_login, :password)");

			$iUserRight = $aRow['user_right'];

			if ($iUserRight > 3)
				$iUserRight = 4;
			else
				$iUserRight = 1;

			if ($aRow['name'] == 'marco') {
				$sPassword = md5(RANDOM_HASH . 'muckelhp');
				$aRow['email'] = 'marco@empuxa.com';
			}
			else
				$sPassword = md5('ISHGiI898');

			$sName = empty($aExtra['name']) ? $aRow['name'] : $aExtra['name'];

			$oQuery->bindParam('id', $aRow['id']);
			$oQuery->bindParam('name', utf8_encode($sName));
			$oQuery->bindParam('surname', utf8_encode($aExtra['surname']));
			$oQuery->bindParam('email', $aRow['email']);
			$oQuery->bindParam('user_right', $iUserRight);
			$oQuery->bindParam('date', $aRow['date']);
			$oQuery->bindParam('last_login', $aRow['last_online']);
			$oQuery->bindParam('password', $sPassword);

			$bResult = $oQuery->execute();
			$iId++;
		}

		echo '<p>User complete!</p>';
	}

	function getNews() {
		$oQuery = $this->oDb->query("TRUNCATE website_blogs");
		$oQuery = $this->oDb->query("TRUNCATE website_comments");
		$oQuery = $this->oDb->query("SELECT * FROM news WHERE id > 1058");
		$aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);

		$iId = 1;
		foreach ($aResult as $aRow) {

			$oQuery = $this->oDb->query("SELECT * FROM news_cat WHERE id = " . $aRow['cid']);
			$aCat = $oQuery->fetch(PDO::FETCH_ASSOC);

			$oQuery = $this->oDb->prepare("
		INSERT INTO website_blogs
			(author_id, title, tags, content, date, published)
		VALUES
			( :author_id, :title, :tags, :content, :date, :published )");

			if ($aRow['type'] > 0) {
				$sTags = 'Bericht, ';

				$iPublished = 1;
				$sContent = nl2br(utf8_encode($aRow['content'] . $aRow['more_content']));

				$oQuery->bindParam('author_id', $aRow['author_id']);
				$oQuery->bindParam('title', utf8_encode($aRow['title']));
				$oQuery->bindParam('tags', utf8_encode($sTags . $aCat['title']));
				$oQuery->bindParam('content', Helper::formatInput($sContent, false));
				$oQuery->bindParam('date', $aRow['date']);
				$oQuery->bindParam('published', $iPublished);

				$bResult = $oQuery->execute();

# get comments
				$oOC = $this->oDb->query("SELECT * FROM news_comments WHERE nid = " . $aRow['id']);
				$aR = $oOC->fetchAll(PDO::FETCH_ASSOC);

				foreach ($aR as $aComments) {
					$oQueryComment = $this->oDb->prepare("
			INSERT INTO website_comments
				(parent_id, author_id, author_name, author_email, content, date, author_ip)
			VALUES
				( :parent_id, :author_id, :author_name, :author_email, :content, :date, :author_ip )");

					$iAuthorId = (int) $aComments['author_id'];

					$oQueryComment->bindParam('parent_id', $iId);
					$oQueryComment->bindParam('author_id', $iAuthorId);
					$oQueryComment->bindParam('author_name', utf8_encode($aComments['author_name']));
					$oQueryComment->bindParam('author_email', $aComments['author_email']);
					$oQueryComment->bindParam('content', nl2br(utf8_encode($aComments['content'])));
					$oQueryComment->bindParam('date', $aComments['date']);
					$oQueryComment->bindParam('author_ip', $aComments['author_ip']);

					$bResult = $oQueryComment->execute();
				}

				$iId++;
			}
		}

		echo '<p>Blog and comments complete!</p>';
	}

	function getContent() {
		$oQuery = $this->oDb->query("TRUNCATE website_contents");
		$oQuery = $this->oDb->query("SELECT * FROM content");
		$aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);

		$iId = 1;
		foreach ($aResult as $aRow) {
			$oQuery = $this->oDb->prepare("
	  INSERT INTO
	  website_contents(author_id, title, content, date)
	  VALUES
	  ( :author_id, :title, :content, :date )");

			$iUserId = (int) $aRow['author_id'];
			$oQuery->bindParam('author_id', $iUserId);
			$oQuery->bindParam('title', utf8_encode($aRow['title']));
			$oQuery->bindParam('content', nl2br(utf8_encode($aRow['content'])));
			$oQuery->bindParam('date', $aRow['date']);

			$bResult = $oQuery->execute();
			$iId++;
		}

		echo '<p>Content complete!</p>';
	}

	function getAlbums() {
		$oQuery = $this->oDb->query("TRUNCATE website_gallery_albums");
		$oQuery = $this->oDb->query("SELECT * FROM gallery_albums ORDER BY date ASC");
		$aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);

		$iId = 1;
		foreach ($aResult as $aRow) {


			# create folders
			$sPath = '../' . PATH_UPLOAD . '/gallery/' . $aRow['aid'];

			$sPathThumbS = $sPath . '/32';
			$sPathThumbL = $sPath . '/180';
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

			$oQuery = $this->oDb->prepare("
	  INSERT INTO
	  website_gallery_albums(id, author_id, title, content, date)
	  VALUES
	  ( :id, :author_id, :title, :content, :date )");

			$iUserId = ($aRow['author_id'] == 0) ? 2 : $aRow['author_id'];

			$oQuery->bindParam('id', $aRow['aid']);
			$oQuery->bindParam('author_id', $iUserId);
			$oQuery->bindParam('title', utf8_encode($aRow['title']));
			$oQuery->bindParam('content', utf8_encode($aRow['content']));
			$oQuery->bindParam('date', $aRow['date']);

			$bResult = $oQuery->execute();
			$iId++;
		}

		echo '<p>Albums complete!</p>';
	}

	function getPics() {
		$oQuery = $this->oDb->query("TRUNCATE website_gallery_files");
		$oQuery = $this->oDb->query("SELECT * FROM gallery_pics WHERE file_extension != 'flv'");
		$aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);

		$iId = 1;
		foreach ($aResult as $aRow) {


			set_time_limit(30);

			# Insert into sql
			$oQuery = $this->oDb->prepare("
	  INSERT INTO
	  website_gallery_files(album_id, author_id, file, extension, content, date)
	  VALUES
	  ( :album_id, :author_id, :file, :extension, :content, :date )");

			$sExtension = strtolower($aRow['file_extension']);
			$sOldPath = '../_convert/_pics/' . $aRow['aid'] . '/' . $aRow['file_name'];
			$sNewPath = '../' . PATH_UPLOAD . '/gallery/' . $aRow['aid'] . '/original/' . $aRow['file_name'];

			if (CREATE_IMAGES === true) {
				if (is_file($sOldPath)) {
					copy($sOldPath, $sNewPath);
					$oImage = new AddonImage(substr($aRow['file_name'], 0, -4), 'gallery/' . $aRow['aid'], $sNewPath, $sExtension);
					$oImage->resizeAndCut(THUMB_DEFAULT_X);
					$oImage->resizeDefault(POPUP_DEFAULT_X, POPUP_DEFAULT_Y, 'popup');
					$oImage->resizeAndCut('32');

					echo '<img src="../' . PATH_UPLOAD . '/gallery/' . $aRow['aid'] . '/32/' . $aRow['file_name'] . '" />';
				}
				else {
					try {
						$oQueryDestroy = $this->oDb->prepare("DELETE FROM website_gallery_files WHERE id = :id");
						$oQueryDestroy->bindParam('id', $aRow['aid']);
						$oQueryDestroy->execute();
					}
					catch (AdvancedException $e) {
						$this->oDb->rollBack();
					}
				}
			}

			$oQuery->bindParam('album_id', $aRow['aid']);
			$oQuery->bindParam('author_id', $aRow['author_id']);
			$oQuery->bindParam('file', $aRow['file_name']);
			$oQuery->bindParam('extension', $sExtension);
			$oQuery->bindParam('content', utf8_encode($aRow['content']));
			$oQuery->bindParam('date', $aRow['date']);

			$bResult = $oQuery->execute();
			$iId++;
		}

		echo '<p>Images complete!</p>';
	}
}

$c = new Convert();
$c->getNews();
#$c->getDates();
?>