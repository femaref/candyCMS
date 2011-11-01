<?php

/**
 * Resize images.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Helper;

class Image {

  /**
   * @var array
   * @access protected
   */
  protected $_aInfo;

  /**
   * @var string
   * @access protected
   */
  protected $_sFolder;

  /**
   * @var string
   * @access protected
   */
  protected $_sId;

  /**
   * @var string
   * @access protected
   */
  protected $_sImgType;

  /**
   * @var string
   * @access protected
   */
  protected $_sOriginalPath;

	/**
	 * Set up the new image.
	 *
	 * @access public
	 * @param string $sId name of the file.
   * @param string $sFolder section to upload image into
   * @param string $sOriginalPath path of the image to clone from
   * @param string $sImgType type of image
	 *
	 */
  public function __construct($sId, $sFolder, $sOriginalPath, $sImgType = 'jpg') {
    $this->_sId           = & $sId;
    $this->_sOriginalPath = & $sOriginalPath;
    $this->_sFolder       = & $sFolder;
    $this->_sImgType      = & $sImgType;
    $this->_aInfo         = getimagesize($this->_sOriginalPath);

    if (!$this->_aInfo) {
      $this->_aInfo[0] = 1;
      $this->_aInfo[1] = 1;
    }
  }

	/**
   * Create the new image with given params.
   *
   * @access private
   * @param integer $iX width of the new image
   * @param integer $iY height of the new image
   * @param string $sFolder section to upload image into
   * @return string $sPath path of the new image
   *
   */
  private function _createImage($iX, $iY, $sFolder) {
    $sPath = PATH_UPLOAD . '/' . $this->_sFolder . '/' . $sFolder . '/' . $this->_sId . '.' . $this->_sImgType;

    if ($this->_sImgType == 'jpg' || $this->_sImgType == 'jpeg')
      $oOldImg = ImageCreateFromJPEG($this->_sOriginalPath);

    elseif ($this->_sImgType == 'png')
      $oOldImg = ImageCreateFromPNG($this->_sOriginalPath);

    elseif ($this->_sImgType == 'gif')
      $oOldImg = ImageCreateFromGIF($this->_sOriginalPath);

    $oNewImg = imagecreatetruecolor($iX, $iY);
    ImageColorAllocate($oNewImg, 255, 255, 255);
    imagecopyresampled($oNewImg, $oOldImg, 0, 0, 0, 0, $iX, $iY, $this->_aInfo[0], $this->_aInfo[1]);

    if ($this->_sImgType == 'jpg' || $this->_sImgType == 'jpeg')
      ImageJPEG($oNewImg, $sPath, 75);

    elseif ($this->_sImgType == 'png') {
      imagealphablending($oNewImg, false);
      imagesavealpha($oNewImg, true);
      ImagePNG($oNewImg, $sPath, 9);
    }
    elseif ($this->_sImgType == 'gif')
      ImageGIF($oNewImg, $sPath);

    imagedestroy($oNewImg);

    return $sPath;
  }

	/**
   * Proportional resizing.
   *
   * @access public
   * @param integer $iWidth width of the new image
   * @param integer $iMaxHeight maximum height of the new image
   * @param string $sFolder folder of the new image
   * @return string $sPath path of the new image
   *
   */
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

    return $this->_createImage($iNewX, $iNewY, $sFolder);
  }

	/**
   * Cut resizing.
   *
   * @access public
   * @param integer $iWidth width and height of the new image
   * @param string $sFolder folder of the new image
   * @return string $sPath path of the new image
   *
   */
  public function resizeAndCut($iWidth, $sFolder = '') {
    if (empty($sFolder))
      $sFolder = $iWidth;

    $iNewX = & $iWidth;
    $iNewY = & $iWidth;

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

    return $this->_createImage($iNewX, $iNewY, $sFolder);
  }
}