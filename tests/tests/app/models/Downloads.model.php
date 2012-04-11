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

require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Downloads.model.php';

use \CandyCMS\Core\Models\Downloads;

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
		$this->aSession['user'] = array(
				'email' => 'test@example.com',
				'facebook_id' => '',
				'id' => 5,
				'name' => 'Test',
				'surname' => 'User',
				'password' => '',
				'role' => 1,
				'full_name' => '');

    $this->assertTrue($this->oObject->create('filename', 'ext'));

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