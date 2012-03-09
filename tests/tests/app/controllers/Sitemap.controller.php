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
require_once PATH_STANDARD . '/app/controllers/Sitemap.controller.php';

use \CandyCMS\Controller\Sitemap as Sitemap;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfSitemapController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'sitemap';
		$this->oObject = new Sitemap($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertResponse(200);
		$this->assertText('c11be3b344');
		$this->assertText('18855f87f2');
		$this->assertText('6dffc4c552');
	}

	function testShowXML() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '.xml'));
		$this->assertResponse(200);
		$this->assertText('c11be3b344');
		$this->assertText('18855f87f2');
		$this->assertText('6dffc4c552');
		$this->assertText('hourly');
	}
}