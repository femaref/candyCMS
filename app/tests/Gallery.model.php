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

    $aRequest = array('title' => 'Title',
                      'content' => 'Content',
                      'id' => 0);
    $aSession = array();
    $aCookie  = array();

    $this->oGallery = new Gallery($aRequest, $aSession, $aCookie, '');
  }

  function testCreateAlbum() {
    $this->assertTrue($this->oGallery->create());

    $this->iLastInsertId = Gallery::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'string', 'Gallery album #' . $this->iLastInsertId . ' created.');
  }

  function testGetDataAlbum() {
    $this->assertIsA($this->oGallery->getData(0), 'array');
    $this->assertIsA($this->oGallery->getData(), 'array');
  }

  function testUpdateAlbum() {
    $this->assertTrue($this->oGallery->update($this->iLastInsertId), 'Gallery album #' . $this->iLastInsertId . ' updated.');
  }

  function testDestroyAlbum() {
    $this->assertTrue($this->oGallery->destroy($this->iLastInsertId), 'Gallery album #' .$this->iLastInsertId. ' destroyed.');
  }

  function testCreateFile() {
    $this->assertTrue($this->oGallery->createFile('test.test', 'test'));

    $this->iLastInsertId = Gallery::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'string', 'Gallery album #' . $this->iLastInsertId . ' created.');
  }

  function testGetDataFile() {
    $this->assertIsA($this->oGallery->getThumbs(0), 'array');
  }

  function testUpdateFile() {
    $this->assertTrue($this->oGallery->update($this->iLastInsertId), 'Gallery file #' . $this->iLastInsertId . ' updated.');
  }

  function testDestroyFile() {
    $this->assertTrue($this->oGallery->destroy($this->iLastInsertId), 'Gallery file #' .$this->iLastInsertId. ' destroyed.');
  }
}