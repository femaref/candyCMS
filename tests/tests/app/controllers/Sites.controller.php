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

require_once PATH_STANDARD . '/vendor/candyCMS/controllers/Main.controller.php';
require_once PATH_STANDARD . '/vendor/candyCMS/controllers/Sites.controller.php';

use \CandyCMS\Core\Controller\Sites as Sites;
use \CandyCMS\Core\Helper\I18n as I18n;

class WebTestOfSitesController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'sites';
		$this->oObject = new Sites($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/welcome'));
		$this->assertResponse(200);
		$this->assertText('Welcome');
  }
}