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
require_once PATH_STANDARD . '/app/controllers/Blogs.controller.php';

use \CandyCMS\Controller\Blogs as Blogs;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfBlogController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'blogs';
		$this->oObject = new Blogs($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
    # Overview
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertResponse(200);
		$this->assertText('hs24br55e2');
		$this->assertNoText('1d2275e170');

    # Short ID
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1'));
		$this->assertResponse(200);

    # Long ID
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/b3cf6b2dd0'));
		$this->assertResponse(200);
	}

	function testShowEntryUnpublished() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2'));
		$this->assertResponse(200);
    $this->assertText(I18n::get('error.404.title'));
	}

  function testShowPageTwo() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/page/2'));
		$this->assertResponse(200);
    $this->assertText('b3cf6b2dd0');
    $this->assertNoText('e12b3a84b2');
  }

  function testShowEntryWithDifferentLanguage() {
    # Entry is not listed...
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/page/3'));
		$this->assertResponse(200);
    $this->assertText(I18n::get('error.404.title'));

    # ...but we can access it directly.
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/4'));
		$this->assertResponse(200);
    $this->assertText('1d2275e170');
  }

  function testShowTags() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/tag1'));
		$this->assertResponse(200);
    $this->assertText('tag1');
  }

	function testCreate() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testUpdate() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testDestroy() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}
}