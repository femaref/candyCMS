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
require_once PATH_STANDARD . '/app/controllers/Search.controller.php';

use \CandyCMS\Controller\Search as Search;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfSearchController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'search';
		$this->oObject = new Search($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertResponse(200);
		$this->assertText(I18n::get('global.search'));
	}

	function testSearch() {
		$this->post(WEBSITE_URL . '/' . $this->aRequest['controller'], array(
				'search' => md5(RANDOM_HASH)
		));

		$this->assertText(md5(RANDOM_HASH));
		$this->assertResponse(200);
	}

  function testShowWithDirectLink() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/098dec456d'));
		$this->assertResponse(200);
		$this->assertText('(1)');
  }
}