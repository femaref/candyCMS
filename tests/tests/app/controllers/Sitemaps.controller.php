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

require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Sitemaps.controller.php';

use \CandyCMS\Core\Controllers\Sitemaps;
use \CandyCMS\Core\Helpers\I18n;

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
		$this->assertText('hs24br55e2'); # Blogs
		$this->assertText('18855f87f2'); # Contents
		$this->assertText('6dffc4c552'); # Galleries
	}

	function testShowXML() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '.xml'));
		$this->assertResponse(200);
		$this->assertText('hs24br55e2');
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