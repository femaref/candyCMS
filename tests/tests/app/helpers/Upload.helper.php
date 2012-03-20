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
require_once PATH_STANDARD . '/app/helpers/Upload.helper.php';

use \CandyCMS\Helper\Upload as Upload;
use \CandyCMS\Helper\Image as Image;

class UnitTestOfUploadHelper extends CandyUnitTest {

  function setUp() {
    if (!file_exists(TESTFILE))
      touch(TESTFILE);

    $file = array(
        'image' => array (
            'name' => 'test.png',
            'tmp_name' => TESTFILE,
            'type' => 'image/png',
            'error' => 0,
            'size' => 0));
 
    $this->oObject = new Upload($this->_aRequest, $this->_aSession, $file);
  }

  function tearDown() {
    parent::tearDown();
  }

  function testUploadFiles() {
    //TODO
   // $bRet = $this->oObject->uploadFiles('temp');
   // $this->assertTrue($bRet[0]);
    //TODO i have to figure this out some other day ...
  }

  function testUploadGalleryFiles() {
    //TODO
  }

  function testUploadAvatarFile() {
    //TODO

  }

  function testDestroyAvatarFiles() {
    //TODO
  }

  function testGetIds() {
    $this->assertSame(count($this->oObject->getIds(false)), 0);
    $this->assertSame(count($this->oObject->getIds(true)), 0);
  }

  function testGetExtensions() {
    $this->assertSame(count($this->oObject->getExtensions()), 0);
  }
}