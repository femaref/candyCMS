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
require_once('app/models/Gallery.model.php');

use \CandyCMS\Model\Gallery as Gallery;

class TestOfGalleryModel extends UnitTestCase {

  public $oGallery;
  public $iLastInsertId;

  function testConstructor() {

    $aRequest = array(
        'title' => 'Title',
        'content' => 'Content',
        'id' => 0,
        'section' => 'gallery');

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

    $this->oGallery = new Gallery($aRequest, $aSession, $aFile, $aCookie);
  }

  function testCreate() {
    $this->assertTrue($this->oGallery->create());

    $this->iLastInsertId = (int) Gallery::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'Gallery album #' . $this->iLastInsertId . ' created.');
  }

  function testGetData() {
    $this->assertIsA($this->oGallery->getData(0), 'array');
    $this->assertIsA($this->oGallery->getData(), 'array');
  }

  function testGetAlbumName() {
    $this->assertIsA($this->oGallery->getAlbumName($this->iLastInsertId), 'string');
  }

  function testGetAlbumContent() {
    $this->assertIsA($this->oGallery->getAlbumContent($this->iLastInsertId), 'string');
  }

  function testGetId() {
    $this->assertIsA($this->oGallery->getId(), 'integer');
  }

  function testUpdate() {
    $this->assertTrue($this->oGallery->update($this->iLastInsertId), 'Gallery album #' . $this->iLastInsertId . ' updated.');
  }

  function testDestroy() {
    $this->assertTrue($this->oGallery->destroy($this->iLastInsertId), 'Gallery album #' . $this->iLastInsertId . ' destroyed.');
  }

  function testCreateFile() {
    $this->assertTrue($this->oGallery->createFile('test.test', 'test'));

    $this->iLastInsertId = (int) Gallery::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'Gallery album #' . $this->iLastInsertId . ' created.');
  }

  function testGetThumbs() {
    $this->assertIsA($this->oGallery->getThumbs(0), 'array');
  }

  function testGetFileContent() {
    $this->assertIsA($this->oGallery->getFileContent($this->iLastInsertId), 'string');
  }

  function testGetFileData() {
    $this->assertIsA($this->oGallery->getFileData($this->iLastInsertId), 'array');
  }

  function testUpdateFile() {
    $this->assertTrue($this->oGallery->updateFile($this->iLastInsertId), 'Gallery file #' . $this->iLastInsertId . ' updated.');
  }

  function testDestroyFile() {
    $this->assertTrue($this->oGallery->destroyFile($this->iLastInsertId), 'Gallery file #' . $this->iLastInsertId . ' destroyed.');
  }
}