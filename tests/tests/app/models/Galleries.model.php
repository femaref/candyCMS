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

require_once PATH_STANDARD . '/app/models/Galleries.model.php';

use \CandyCMS\Model\Galleries as Galleries;

class UnitTestOfGalleryModel extends CandyUnitTest {

  function setUp() {

    $this->aRequest = array(
        'title'     => 'Title',
        'content'   => 'Content',
        'id'        => 0,
        'controller'=> 'galleries');

    $this->oObject = new Galleries($this->aRequest, $this->aSession);
  }

  function testCreate() {
    $this->assertTrue($this->oObject->create());

    $this->iLastInsertId = (int) Galleries::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testGetData() {
    $this->assertIsA($this->oObject->getData(1), 'array');
    $this->assertIsA($this->oObject->getData(), 'array');
  }

  function testGetAlbumName() {
    $this->assertIsA($this->oObject->getAlbumName($this->iLastInsertId), 'string');
  }

  function testGetAlbumContent() {
    $this->assertIsA($this->oObject->getAlbumContent($this->iLastInsertId), 'string');
  }

  function testUpdate() {
    $this->assertTrue($this->oObject->update($this->iLastInsertId));
  }

  function testDestroy() {
    $this->assertTrue($this->oObject->destroy($this->iLastInsertId));
  }

  function testCreateFile() {
    $this->assertTrue($this->oObject->createFile('test.test', 'test'));

    $this->iLastInsertId = (int) Galleries::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testGetThumbs() {
    $this->assertIsA($this->oObject->getThumbs(1), 'array');
  }

  function testGetFileDetails() {
    $this->assertIsA($this->oObject->getFileDetails($this->iLastInsertId), 'array');
  }

  function testGetFileData() {
    $this->assertIsA($this->oObject->getFileData($this->iLastInsertId), 'array');
  }

  function testUpdateFile() {
    $this->assertTrue($this->oObject->updateFile($this->iLastInsertId));
  }

  function testDestroyFile() {
    $this->assertTrue($this->oObject->destroyFile($this->iLastInsertId));
  }
}