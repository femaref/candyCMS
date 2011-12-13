<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

require_once('lib/simpletest/web_tester.php');
require_once('lib/simpletest/reporter.php');
require_once('app/controllers/Download.controller.php');

use \CandyCMS\Controller\Download as Download;

class TestOfDownloadController extends WebTestCase {

  public $oDownload;

  function testConstructor() {
    $aRequest = array('section' => 'download');
    $aFile    = array();
    $aCookie  = array();
    $aSession['userdata'] = array(
      'email' => '',
      'facebook_id' => '',
      'id' => 0,
      'name' => '',
      'surname' => '',
      'password' => '',
      'user_right' => 0,
      'full_name' => ''
    );

    $this->oDownload = new Download($aRequest, $aSession, $aFile, $aCookie);
  }

  function testShow() {
    $this->assertTrue($this->get(WEBSITE_URL . '/download'));
    $this->assertResponse('200');
  }

  function testCreate() {
    $this->get(WEBSITE_URL . '/download/create');
    $this->assertResponse('200');
  }

  function testUpdate() {
    $this->get(WEBSITE_URL . '/download/update');
    $this->assertResponse('200');
  }

  function testDestroy() {
    $this->get(WEBSITE_URL . '/download/destroy');
    $this->assertResponse('200');
  }

  /*function testDirIsWritable() {
    $oFile = fopen(PATH_UPLOAD . '/download/test.log', 'a');
    fwrite($oFile, 'Is writeable.' . "\n");
    fclose($oFile);

    $this->assertTrue(file_exists(PATH_UPLOAD . '/dowload/test.log'), 'File was created.');
    @unlink(PATH_UPLOAD . '/download/test.log');
  }*/
}