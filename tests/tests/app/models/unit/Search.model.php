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

require_once PATH_STANDARD . '/app/models/Search.model.php';

use \CandyCMS\Model\Search as Search;

class TestOfSearchModel extends CandyUnitTest {

	function setUp() {
		$this->aRequest['section'] = 'search';
		$this->oObject = new Search($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	/**
	 * Tests if query run through. We use only the blogs table.
	 */
  function testGetData() {
		$aData = $this->oObject->getData(md5(RANDOM_HASH), array('blogs'));
    $this->assertIsA($aData['blogs'], 'array');
  }
}