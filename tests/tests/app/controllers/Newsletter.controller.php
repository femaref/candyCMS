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
require_once PATH_STANDARD . '/app/controllers/Newsletter.controller.php';

use \CandyCMS\Controller\Newsletter as Newsletter;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfNewsletterController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'newsletter';
		$this->oObject = new Newsletter($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertResponse(200);
		$this->assertText(I18n::get('newsletter.title.subscribe'));
	}

	function testSubscribe() {
		$this->post(WEBSITE_URL . '/' . $this->aRequest['controller'], array(
				'name' => md5($this->aSession['userdata']['name'] . time()),
				'surname' => md5($this->aSession['userdata']['surname'] . time()),
				'email' => time() . '_' . WEBSITE_MAIL_NOREPLY
		));

		$this->assertText(I18n::get('success.newsletter.create'));
		$this->assertResponse(200);
	}

	function testSubscribeWithWrongEmailAddress() {
		$this->post(WEBSITE_URL . '/' . $this->aRequest['controller'], array(
				'name' => md5($this->aSession['userdata']['name'] . time()),
				'surname' => md5($this->aSession['userdata']['surname'] . time()),
				'email' => str_replace('@', '', WEBSITE_MAIL_NOREPLY)
		));

		$this->assertText(I18n::get('error.standard'));
		$this->assertResponse(200);
	}
}