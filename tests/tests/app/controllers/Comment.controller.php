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
require_once PATH_STANDARD . '/app/controllers/Comment.controller.php';

use \CandyCMS\Controller\Comment as Comment;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfCommentController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'blog';
		$this->oObject = new Comment($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1'));
		$this->assertText('7c883dc7d2');
    $this->assertResponse(200);
	}

	function testDestroy() {
		$this->assertTrue($this->get(WEBSITE_URL . '/commetn/1/destroy/1'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}
}