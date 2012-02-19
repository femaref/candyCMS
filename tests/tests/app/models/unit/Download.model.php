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

require_once PATH_STANDARD . '/app/models/Download.model.php';

use \CandyCMS\Model\Download as Download;

class UnitTestOfDownloadModel extends UnitTestCase {

	public $oObject;
	protected $_aRequest;
	protected $_aSession;
	protected $_aFile;
	protected $_aCookie;

	# Last entry
  public $iLastInsertId;

  function setUp() {

    $this->_aRequest = array(
				'title' => 'Title',
				'content' => 'Content',
				'category' => 'Category',
				'downloads' => 0,
				'section' => 'download');

		$this->_aSession['userdata'] = array(
				'email' => '',
				'facebook_id' => '',
				'id' => 0,
				'name' => '',
				'surname' => '',
				'password' => '',
				'role' => 0,
				'full_name' => ''
		);

    $this->oObject = new Download($aRequest, $aSession);
  }

	function tearDown() {
		parent::tearDown();
		unset($this->_aRequest, $this->_aSession['userdata']);
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