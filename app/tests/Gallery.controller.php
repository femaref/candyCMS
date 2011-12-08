<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

require_once('lib/simpletest/autorun.php');
require_once('app/controllers/Gallery.controller.php');

use \CandyCMS\Controller\Gallery as Gallery;

class TestOfGalleryController extends UnitTestCase {

  public $oGallery;

  function testConstructor() {

    $aRequest = array('section' => 'gallery');
    $aSession = array();
    $aCookie  = array();

    $this->oGallery = new Gallery($aRequest, $aSession, $aCookie, '');
  }

  function testDirIsWritable() {
    $oFile = fopen('upload/gallery/test.log', 'a');
    fwrite($oFile, 'Is writeable.' . "\n");
    fclose($oFile);

    $this->assertTrue(file_exists('upload/gallery/test.log'), 'File was created.');
    @unlink('upload/gallery/test.log');
  }
}