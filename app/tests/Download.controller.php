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
require_once('app/controllers/Download.controller.php');

use \CandyCMS\Controller\Download as Download;

class TestOfDownloadController extends UnitTestCase {

  public $oDownload;

  function testConstructor() {

    $aRequest = array('section' => 'download');
    $aSession = array();
    $aCookie  = array();

    $this->oDownload = new Download($aRequest, $aSession, $aCookie, '');
  }

  function testDirIsWritable() {
    $oFile = fopen('upload/download/test.log', 'a');
    fwrite($oFile, 'Is writeable.' . "\n");
    fclose($oFile);

    $this->assertTrue(file_exists('upload/dowload/test.log'), 'File was created.');
    @unlink('upload/download/test.log');
  }
}