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

require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Galleries.model.php';

use \CandyCMS\Core\Models\Galleries;

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
    $this->aSession['user'] = array(
				'email' => 'test@example.com',
				'facebook_id' => '',
				'id' => 5,
				'name' => 'Test',
				'surname' => 'User',
				'password' => '',
				'role' => 1,
				'full_name' => '');

    $this->assertTrue($this->oObject->create());

    $this->iLastInsertId = (int) Galleries::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testGetData() {
    $this->assertIsA($this->oObject->getData(1), 'array');
    $this->assertIsA($this->oObject->getData(), 'array');
  }

  function testGetAlbumNameAndContent() {
    $aData = $this->oObject->getAlbumNameAndContent($this->iLastInsertId);
    $this->assertIsA($aData, 'array');
    $this->assertIsA($aData['title'], 'string');
    $this->assertIsA($aData['content'], 'string');
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

  function testGetFileData() {
    $aData = $this->oObject->getFileData($this->iLastInsertId);
    $this->assertIsA($aData, 'array');
    $this->assertIsA($aData['album_id'], 'int');
    $this->assertIsA($aData['content'], 'string');
  }

  function testUpdateFile() {
    $this->assertTrue($this->oObject->updateFile($this->iLastInsertId));
  }

  function testDestroyFile() {
    $this->assertTrue($this->oObject->destroyFile($this->iLastInsertId));
  }
}