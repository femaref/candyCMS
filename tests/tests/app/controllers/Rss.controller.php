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

require_once PATH_STANDARD . '/app/controllers/Main.controller.php';
require_once PATH_STANDARD . '/app/controllers/Rss.controller.php';

use \CandyCMS\Controller\Rss as Rss;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfRssController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'rss';
		$this->oObject = new Rss($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertResponse(200);
		$this->assertText('c11be3b344');
		$this->assertNoText('e12b3a84b2');
	}

	function testMedia() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/galleries/1'));
		$this->assertResponse(200);
		$this->assertText('982e960e18');
		$this->assertText('782c660e17');
	}
}