<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

require_once PATH_STANDARD . '/app/helpers/Image.helper.php';

use \CandyCMS\Helper\Helper as Helper;
use \CandyCMS\Helper\Image as Image;

class UnitTestOfImageHelper extends CandyUnitTest {

  function setUp() {
    $this->sImagePath = PATH_STANDARD . '/' . Helper::removeSlash(PATH_UPLOAD) . '/temp/medias/test.png';

    $this->oObject = new Image('test',
            'temp',
            PATH_STANDARD . '/' . Helper::removeSlash(WEBSITE_CDN) . '/images/candy.global/spacer.png',
            'png');
  }

  function tearDown() {
    parent::tearDown();
  }

  function testResizeDefault() {
    $this->assertIsA($this->oObject->resizeDefault(THUMB_DEFAULT_X, THUMB_DEFAULT_Y, 'medias'), 'string');
    $aInfo = @getimagesize($this->sImagePath);

    $this->assertIsA($aInfo, 'array');
    $this->assertEqual($aInfo[0], THUMB_DEFAULT_X);
    $this->assertEqual($aInfo[1], THUMB_DEFAULT_Y);
    $this->assertTrue(unlink($this->sImagePath));
  }

  function testResizeAndCut() {
    $this->assertIsA($this->oObject->resizeAndCut(THUMB_DEFAULT_X, 'medias'), 'string');
    $aInfo = @getimagesize($this->sImagePath);

    $this->assertIsA($aInfo, 'array');
    $this->assertEqual($aInfo[0], THUMB_DEFAULT_X);
    $this->assertEqual($aInfo[1], THUMB_DEFAULT_Y);
    $this->assertTrue(unlink($this->sImagePath));
  }
}