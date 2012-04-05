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

require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Rss.controller.php';

use \CandyCMS\Core\Controllers\Rss;
use \CandyCMS\Core\Helpers\I18n;

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
		$this->assertText('hs24br55e2');
		$this->assertNoText('e12b3a84b2');
	}

	function testMedia() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/galleries/1'));
		$this->assertResponse(200);
		$this->assertText('982e960e18');
		$this->assertText('782c660e17');
	}

  function testCreate() {
    # there is no create, but we redirect to show
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
    $this->assertResponse(200);
    $this->assertText('c11be3b344');
    $this->assertNoText('e12b3a84b2');
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