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
require_once PATH_STANDARD . '/app/controllers/Newsletters.controller.php';

use \CandyCMS\Controller\Newsletters as Newsletters;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfNewsletterController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'newsletters';
		$this->oObject = new Newsletters($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertResponse(200);
		$this->assertText(I18n::get('newsletters.title.subscribe'));
	}

  /**
   * @todo validate forms
   */
	function testSubscribe() {
		$this->post(WEBSITE_URL . '/' . $this->aRequest['controller'], array(
				'name' => md5($this->aSession['user']['name'] . time()),
				'surname' => md5($this->aSession['user']['surname'] . time()),
				'email' => time() . '_' . WEBSITE_MAIL_NOREPLY,
        'subscribe_newsletter' => 'formdata'
		));

		$this->assertText(I18n::get('success.newsletter.create'));
		$this->assertResponse(200);
	}

	function testSubscribeWithWrongEmailAddress() {
		$this->post(WEBSITE_URL . '/' . $this->aRequest['controller'], array(
				'name' => md5($this->aSession['user']['name'] . time()),
				'surname' => md5($this->aSession['user']['surname'] . time()),
				'email' => str_replace('@', '', WEBSITE_MAIL_NOREPLY),
        'subscribe_newsletter' => 'formdata'
		));

		$this->assertText(I18n::get('error.standard'));
		$this->assertResponse(200);
	}
}