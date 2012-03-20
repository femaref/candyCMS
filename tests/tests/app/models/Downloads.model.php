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

require_once PATH_STANDARD . '/app/models/Downloads.model.php';

use \CandyCMS\Model\Downloads as Downloads;

class UnitTestOfDownloadModel extends CandyUnitTest {

  function setUp() {
    $this->aRequest = array(
				'title'     => 'Title',
				'content'   => 'Content',
				'category'  => 'Category',
				'downloads' => 0,
				'controller'=> 'downloads');

		$this->oObject = new Downloads($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

  function testCreate() {
    $this->assertTrue($this->oObject->create('filename', 'extension'));

    $this->iLastInsertId = (int) Downloads::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testGetData() {
    $this->assertIsA($this->oObject->getData(1), 'array');
    $this->assertIsA($this->oObject->getData(), 'array');
  }

  function testUpdate() {
    $this->assertTrue($this->oObject->update($this->iLastInsertId));
  }

  function testUpdateDownloadCount() {
    $this->assertTrue($this->oObject->updateDownloadCount($this->iLastInsertId));
  }

  function testDestroy() {
    $this->assertTrue($this->oObject->destroy($this->iLastInsertId));
  }
}