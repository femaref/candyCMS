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
require_once PATH_STANDARD . '/app/controllers/Mail.controller.php';

use \CandyCMS\Controller\Mail as Mail;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfMailController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'mail';
		$this->oObject = new Mail($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

  function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2'));
		$this->assertText(I18n::get('global.contact'));
		$this->assertText('c2f9619961');
		$this->assertResponse('200');
	}

	/**
	 * @todo tests when not all inputs are filled out
	 * @todo test if mail sends
	 */
	function testCreate() {
		$this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2', array(
				'email' => WEBSITE_MAIL_NOREPLY,
				'content' => 'Test'
		));

		#$this->assertText(I18n::get('mail.info.title'));
		$this->assertResponse(200);
	}
}