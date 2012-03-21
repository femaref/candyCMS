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

  function testCreate() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2/create'));
		$this->assertText(I18n::get('global.contact'));
		$this->assertText('c2f9619961');
		$this->assertResponse('200');

    $this->assertField('email');
    $this->assertField('subject');
    $this->assertField('content');

    #submit empty form
    $this->click(I18n::get('global.submit'));
		$this->assertResponse(200);
 		$this->assertText(I18n::get('error.form.missing.email'));
		$this->assertText(I18n::get('error.form.missing.content'));

    $this->assertTrue($this->setField('email', 'wrongly..formated@email.com'));
    $this->assertTrue($this->setField('content', 'some content'));
    #submit form with wrongly formated email
    $this->click(I18n::get('global.submit'));
		$this->assertResponse(200);
		$this->assertText(I18n::get('error.mail.format'));

    $this->assertTrue($this->setField('email', WEBSITE_MAIL_NOREPLY));
    $this->assertTrue($this->setField('content', 'some content'));
    #submit form with wrongly formated email
    $this->click(I18n::get('global.submit'));
		$this->assertResponse(200);
		$this->assertText(I18n::get('mails.success_page.title'));
  }

  function testShow() {
    # there is no show
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/show'));
    $this->assert404();
  }

  function testUpdate() {
    # there is no update
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
    $this->assert404();
  }

  function testDestroy() {
    # there is no destroy
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
    $this->assert404();
  }
}