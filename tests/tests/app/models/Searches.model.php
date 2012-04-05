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

require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Searches.model.php';

use \CandyCMS\Core\Models\Searches;

class TestOfSearchModel extends CandyUnitTest {

	function setUp() {
		$this->aRequest['controller'] = 'searches';
		$this->oObject = new Searches($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

  function testGetData() {
		$aData = $this->oObject->getData(md5(RANDOM_HASH), array('blogs'));
    $this->assertIsA($aData['blogs'], 'array');
  }
}