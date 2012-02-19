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

require_once PATH_STANDARD . '/app/controllers/Index.controller.php';

use \CandyCMS\Controller\Index as Index;

class WebTestOfIndexController extends CandyWebTest {

	function setUp() {
		$this->oObject = new Index($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShowIndexAsGuest() {
		$this->assertTrue($this->get(WEBSITE_URL));
		$this->assertText('Login'); # This should be on every page.
	}
}