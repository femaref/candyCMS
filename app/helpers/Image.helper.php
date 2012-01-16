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

use CandyCMS\Helper\Helper as Helper;

class Image {

  /**
   * @var array
   * @access protected
   */
  protected $_aInfo;

  /**
   * @var integer
   * @access protected
   */
  protected $_iImageWidth;

  /**
   * @var integer
   * @access protected
   */
  protected $_iImageHeight;

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
   * @var string
   * @access protected
   */
  protected $_sUploadDir;

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
  public function __construct($sId, $sUploadDir, $sOriginalPath, $sImgType = 'jpg') {
    $this->_sId           = & $sId;
    $this->_sOriginalPath = & $sOriginalPath;
    $this->_sUploadDir    = & $sUploadDir;
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
   * @param array $aParams width and height params
   * @param string $sFolder section to upload image into
   * @return string $sPath path of the new image
   *
   */
  private function _createImage($iX, $iY, $iSrcX, $iSrcY) {
    $sPath = PATH_UPLOAD . '/' . $this->_sUploadDir . '/' . $this->_sFolder . '/' . $this->_sId . '.' . $this->_sImgType;

    if ($this->_sImgType == 'jpg' || $this->_sImgType == 'jpeg')
      $oOldImg = ImageCreateFromJPEG($this->_sOriginalPath);

    elseif ($this->_sImgType == 'png')
      $oOldImg = ImageCreateFromPNG($this->_sOriginalPath);

    elseif ($this->_sImgType == 'gif')
      $oOldImg = ImageCreateFromGIF($this->_sOriginalPath);

    $oNewImg = imagecreatetruecolor($this->_iImageWidth, $this->_iImageHeight);
    $oBg = ImageColorAllocate($oNewImg, 255, 255, 255);

    imagefill($oNewImg, 0, 0, $oBg);
    imagecopyresampled($oNewImg, $oOldImg, 0, 0, $iSrcX, $iSrcY, $iX, $iY, $this->_aInfo[0], $this->_aInfo[1]);

    if ($this->_sImgType == 'jpg' || $this->_sImgType == 'jpeg')
      ImageJPEG($oNewImg, Helper::removeSlash($sPath), 75);

    elseif ($this->_sImgType == 'png') {
      imagealphablending($oNewImg, false);
      imagesavealpha($oNewImg, true);
      ImagePNG($oNewImg, Helper::removeSlash($sPath), 9);
    }
    elseif ($this->_sImgType == 'gif')
      ImageGIF($oNewImg, Helper::removeSlash($sPath));

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
    # Y bigger than X and max height
    if ($this->_aInfo[1] > $this->_aInfo[0] && !empty($iMaxHeight)) {
      $iFactor = $iMaxHeight / $this->_aInfo[1];
      $iX = round($this->_aInfo[0] * $iFactor);
      $iY = $iMaxHeight;
    }
    else {
      $iFactor = $iWidth / $this->_aInfo[0];
      $iX = $iWidth;
      $iY = round($this->_aInfo[1] * $iFactor);
    }

    $this->_sFolder = empty($sFolder) ? $iWidth : $sFolder;
    $this->_iImageWidth   = $iX;
    $this->_iImageHeight  = $iY;

    return $this->_createImage($iX, $iY, 0, 0);
  }

	/**
   * Cut resizing.
   *
   * @access public
   * @param integer $iDim width and height of the new image
   * @param string $sFolder folder of the new image
   * @return string $sPath path of the new image
   *
   */
  public function resizeAndCut($iDim, $sFolder = '') {
    $iX = $iDim;
    $iY = $iDim;
    $iSrcX = 0;
    $iSrcY = 0;

    # Y bigger than X
    if ($this->_aInfo[1] > $this->_aInfo[0]) {
      $iSrcY = ($this->_aInfo[1] - $this->_aInfo[0]) / 2;
      $iFactor = $iDim / $this->_aInfo[0];
      $iY = round($this->_aInfo[1] * $iFactor);
    }

    # X bigger than Y
    else {
      $iSrcX = ($this->_aInfo[0] - $this->_aInfo[1]) / 2;
      $iFactor = $iDim / $this->_aInfo[1];
      $iX = round($this->_aInfo[0] * $iFactor);
    }

    $this->_sFolder = empty($sFolder) ? $iDim : $sFolder;
    $this->_iImageWidth   = $iDim;
    $this->_iImageHeight  = $iDim;

    return $this->_createImage($iX, $iY, $iSrcX, $iSrcY);
  }
}