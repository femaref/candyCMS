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
require_once PATH_STANDARD . '/app/controllers/Sitemaps.controller.php';

use \CandyCMS\Controller\Sitemaps as Sitemaps;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfSitemapController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'sitemaps';
		$this->oObject = new Sitemaps($this->aRequest, $this->aSession);
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

  function testCreate() {
    # there is no create
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
    $this->assert404();
  }

  function testUpdate() {
    # there is no update
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
    $this->assert404();
  }

  function testDestroy() {
    # there is no destroy
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
    $this->assert404();
  }
}