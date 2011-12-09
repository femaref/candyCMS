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
require_once('app/controllers/Media.controller.php');

use \CandyCMS\Controller\Media as Media;

class TestOfMediaController extends WebTestCase {

  public $oMedia;

  function testConstructor() {

    $aRequest = array('section' => 'media');
    $aSession = array();
    $aCookie  = array();

    $this->oMedia = new Media($aRequest, $aSession, $aCookie, '');
  }

  function testDirIsWritable() {
    $oFile = fopen('upload/media/test.log', 'a');
    fwrite($oFile, 'Is writeable.' . "\n");
    fclose($oFile);

    $this->assertTrue(file_exists('upload/media/test.log'), 'File was created.');
    @unlink('upload/media/test.log');
  }
}