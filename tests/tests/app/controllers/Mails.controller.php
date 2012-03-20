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
require_once PATH_STANDARD . '/app/controllers/Mails.controller.php';

use \CandyCMS\Controller\Mails as Mails;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfMailController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'mails';
		$this->oObject = new Mails($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

  function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2/create'));
		$this->assertText(I18n::get('global.contact'));
		$this->assertText('c2f9619961');
		$this->assertResponse('200');
	}

	function testCreateSuccess() {
    # Success
		$this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2/create', array(
				'email' => WEBSITE_MAIL_NOREPLY,
				'content' => 'Test',
				'create_mails' => 'formdata'
		)));

		$this->assertResponse(200);
		$this->assertText(I18n::get('mails.success_page.title'));

    # Fail because fields are missing
		$this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/create', array(
				'create_mails' => 'formdata'
		)));

		$this->assertResponse(200);
		$this->assertText(I18n::get('error.form.missing.email'));
		$this->assertText(I18n::get('error.form.missing.content'));

        # Fail because fields are missing
		$this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/create', array(
				'create_mails' => 'formdata',
        'email' => 'wrongly..formated@email.com'
		)));

		$this->assertResponse(200);
		$this->assertText(I18n::get('error.mail.format'));
		$this->assertText(I18n::get('error.form.missing.content'));
  }
}