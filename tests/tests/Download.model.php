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
require_once('app/models/Download.model.php');

use \CandyCMS\Model\Download as Download;

class TestOfDownloadModel extends UnitTestCase {

  public $oDownload;
  public $iLastInsertId;

  function testConstructor() {

    $aRequest = array(
        'title' => 'Title',
        'content' => 'Content',
        'category' => 'Category',
        'downloads' => 0,
        'section' => 'download');

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

    $aFile = array();
    $aCookie = array();

    $this->oDownload = new Download($aRequest, $aSession, $aFile, $aCookie);
  }

  function testCreate() {
    $this->assertTrue($this->oDownload->create('test.test', 'test'));

    $this->iLastInsertId = (int) Download::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'Download #' . $this->iLastInsertId . ' created.');
  }

  function testGetData() {
    $this->assertIsA($this->oDownload->getData(0), 'array');
    $this->assertIsA($this->oDownload->getData(), 'array');
  }

  function testUpdate() {
    $this->assertTrue($this->oDownload->update($this->iLastInsertId), 'Download #' . $this->iLastInsertId . ' updated.');
  }

  function testUpdateDownloadCount() {
    $this->assertTrue($this->oDownload->updateDownloadCount($this->iLastInsertId), 'Download count #' . $this->iLastInsertId . ' updated.');
  }

  function testDestroy() {
    $this->assertTrue($this->oDownload->destroy($this->iLastInsertId), 'Download #' . $this->iLastInsertId . ' destroyed.');
  }
}