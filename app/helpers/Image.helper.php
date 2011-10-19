<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @todo documentation and refactoring
 */

# TODO: MERGE METHODS


namespace CandyCMS\Helper;

class Image {
  protected $_sOriginalPath;
  protected $_sFolder;
  protected $_aInfo;
  protected $_iId;
  protected $_sImgType;

  public function __construct($iId, $sFolder, $sOriginalPath, $sImgType = 'jpg') {
    $this->_iId = & $iId;
    $this->_sOriginalPath = & $sOriginalPath;
    $this->_sFolder = & $sFolder;
    $this->_sImgType = & $sImgType;
    $this->_aInfo = getimagesize($this->_sOriginalPath);

    if (!$this->_aInfo) {
      $this->_aInfo[0] = 1;
      $this->_aInfo[1] = 1;
    }
  }

  public function resizeDefault($iWidth, $iMaxHeight = '', $sFolder = '') {
    if(empty($sFolder))
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

    if ($this->_sImgType == 'jpg' || $this->_sImgType == 'jpeg') {
      $oOldImg = ImageCreateFromJPEG($this->_sOriginalPath);
      imagecopyresampled($oNewImg, $oOldImg, 0, 0, 0, 0, $iNewX, $iNewY, $this->_aInfo[0], $this->_aInfo[1]);
      ImageJPEG($oNewImg, PATH_UPLOAD . '/' . $this->_sFolder . '/' . $sFolder . '/' . $this->_iId . '.jpg', 75);
    }
    elseif ($this->_sImgType == 'png') {
      $oOldImg = ImageCreateFromPNG($this->_sOriginalPath);
      imagealphablending($oNewImg, false);
      imagesavealpha($oNewImg, true);
      imagecopyresampled($oNewImg, $oOldImg, 0, 0, 0, 0, $iNewX, $iNewY, $this->_aInfo[0], $this->_aInfo[1]);
      ImagePNG($oNewImg, PATH_UPLOAD . '/' . $this->_sFolder . '/' . $sFolder . '/' . $this->_iId . '.png', 5);
    }
    elseif ($this->_sImgType == 'gif') {
      $oOldImg = ImageCreateFromGIF($this->_sOriginalPath);
      imagecopyresampled($oNewImg, $oOldImg, 0, 0, 0, 0, $iNewX, $iNewY, $this->_aInfo[0], $this->_aInfo[1]);
      ImageGIF($oNewImg, PATH_UPLOAD . '/' . $this->_sFolder . '/' . $sFolder . '/' . $this->_iId . '.gif');
    }

    imagedestroy($oNewImg);
  }

  public function resizeAndCut($iWidth, $sFolder = '') {
    if(empty($sFolder))
      $sFolder = $iWidth;

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

    if ($this->_sImgType == 'jpg' || $this->_sImgType == 'jpeg')
      $oOldImg = ImageCreateFromJPEG($this->_sOriginalPath);

    elseif ($this->_sImgType == 'png')
      $oOldImg = ImageCreateFromPNG($this->_sOriginalPath);

    elseif ($this->_sImgType == 'gif')
      $oOldImg = ImageCreateFromGIF($this->_sOriginalPath);

    $oNewImg = imagecreatetruecolor($iWidth, $iWidth);
    $oBg = ImageColorAllocate($oNewImg, 255, 255, 255);

    imagefill($oNewImg, 0, 0, $oBg);
    imagecopyresampled($oNewImg, $oOldImg, $iDstX, $iDstY, $iSrcX, $iSrcY, $iNewX, $iNewY, $this->_aInfo[0], $this->_aInfo[1]);

    if ($this->_sImgType == 'jpg' || $this->_sImgType == 'jpeg')
      ImageJPEG($oNewImg, PATH_UPLOAD . '/' . $this->_sFolder . '/' . $sFolder . '/' . $this->_iId . '.' . $this->_sImgType, 75);

    elseif ($this->_sImgType == 'png') {
      imagealphablending($oNewImg, false);
      imagesavealpha($oNewImg, true);
      ImagePNG($oNewImg, PATH_UPLOAD . '/' . $this->_sFolder . '/' . $sFolder . '/' . $this->_iId . '.png', 9);
    }
    elseif ($this->_sImgType == 'gif')
      ImageGIF($oNewImg, PATH_UPLOAD . '/' . $this->_sFolder . '/' . $sFolder . '/' . $this->_iId . '.gif');

    imagedestroy($oNewImg);
  }
}