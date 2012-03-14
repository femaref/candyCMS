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
require_once PATH_STANDARD . '/app/controllers/Comments.controller.php';

use \CandyCMS\Controller\Comments as Comments;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfCommentController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'blogs';
		$this->oObject = new Comments($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1'));
		$this->assertText('7c883dc7d2');
    $this->assertResponse(200);
	}

	function testCreateSuccess() {
		$this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1', array(
				'name' => 'Name',
				'content' => 'Test',
				'create_comment' => 'formdata',
				'parent_id' => '1'
		)));

    $this->assertResponse(200);
		$this->assertText(I18n::get('success.create'));
	}

	/**
	 * @todo Check parent_id
	 */
	function testCreateFail() {
		$this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1', array(
				'create_comment' => 'formdata'
		)));

		$this->assertResponse(200);
		$this->assertText(I18n::get('error.form.missing.name'));
		$this->assertText(I18n::get('error.form.missing.content'));
	}

	function testDestroy() {
		$this->assertTrue($this->get(WEBSITE_URL . '/comments/1/destroy/1'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}
}