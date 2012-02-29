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

require_once PATH_STANDARD . '/app/controllers/Blog.controller.php';

use \CandyCMS\Controller\Blog as Blog;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfBlogController extends CandyWebTest {

	function setUp() {
		$this->aRequest['section'] = 'blog';
		$this->oObject = new Blog($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShowBlog() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section']));
		$this->assertResponse(200);
		$this->assertText('b3cf6b2dd0');
	}

	function testShowBlogEntryWithShortId() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/1'));
		$this->assertResponse(200);
	}

	function testShowBlogEntryWithLongId() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/1/b3cf6b2dd0'));
		$this->assertResponse(200);
	}

	function testShowBlogEntryUnpublished() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/2'));
		$this->assertResponse(200);
    $this->assertText(I18n::get('error.404.title'));
	}

  function testShowPageTwo() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/page/2'));
		$this->assertResponse(200);
    $this->assertText('c11be3b344');
  }

  function testShowBlogEntryWithDifferentLanguage() {
    # Entry is not listed...
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/page/3'));
		$this->assertResponse(200);
    $this->assertText(I18n::get('error.404.title'));

    # ...but we can access it directly.
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/4'));
		$this->assertResponse(200);
    $this->assertText('1d2275e170');
  }

  function testShowTags() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/tag1'));
		$this->assertResponse(200);
    $this->assertText('tag1');
  }

	function testCreateBlog() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/create'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testUpdateBlog() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/1/update'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testDestroyBlog() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/1/destroy'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}
}