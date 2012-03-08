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

require_once PATH_STANDARD . '/app/controllers/Error.controller.php';
require_once PATH_STANDARD . '/app/helpers/Helper.helper.php';

use \CandyCMS\Controller\Error as Error;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfErrorController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'error';
		$this->oObject = new Error($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow404() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/404'));
		$this->assertResponse(200);
		$this->assertText(I18n::get('error.404.title'));
	}
}