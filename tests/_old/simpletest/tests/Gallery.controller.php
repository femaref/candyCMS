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
    $aFile    = array();
    $aCookie  = array();
    $aSession['userdata'] = array(
      'email' => '',
      'facebook_id' => '',
      'id' => 0,
      'name' => '',
      'surname' => '',
      'password' => '',
      'role' => 0,
      'full_name' => ''
    );

    $this->oGallery = new Gallery($aRequest, $aSession, $aFile, $aCookie);
  }

  function testDirIsWritable() {
    $oFile = fopen('upload/gallery/test.log', 'a');
    fwrite($oFile, 'Is writeable.' . "\n");
    fclose($oFile);

    $this->assertTrue(file_exists('upload/gallery/test.log'), 'File was created.');
    @unlink('upload/gallery/test.log');
  }
}