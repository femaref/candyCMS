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

require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Searches.controller.php';

use \CandyCMS\Core\Controllers\Searches;
use \CandyCMS\Core\Helpers\I18n;

class WebTestOfSearchController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'searches';
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
    # test the form
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
    $this->assertField('search', '');

    # search for md5(Random_Hash)
    $this->assertTrue($this->setField('search', md5(RANDOM_HASH)));
    $this->click(I18n::get('global.search'));
		$this->assertText(md5(RANDOM_HASH));
		$this->assertResponse(200);
	}

  function testShowWithDirectLink() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/098dec456d'));
		$this->assertResponse(200);
		$this->assertText('(1)');
  }

  function testDestroy() {
    #destroy action should search instead
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
    $this->assertResponse(200);
    $this->assertText(I18n::get('global.search'));
  }

  function testUpdate() {
    #update action should search instead
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
    $this->assertResponse(200);
    $this->assertText(I18n::get('global.search'));
  }

}