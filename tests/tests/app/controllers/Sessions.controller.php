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
require_once('app/controllers/Sessions.controller.php');

use CandyCMS\Controller\Sessions as Sessions;
use CandyCMS\Helper\I18n as I18n;

class WebTestOfSessionController extends CandyWebTest {

  function setUp() {
    $this->aRequest['controller'] = 'sessions';
    $this->oObject = new Sessions($this->aRequest, $this->aSession);
  }

  function tearDown() {
    parent::tearDown();
  }

  function testShow() {
    $this->assertTrue($this->get(WEBSITE_URL . '/sessions'));
    $this->assertNoText(I18n::get('error.standard'));
    $this->assertResponse('200');
  }

  function testCreate() {
    $this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create');
    $this->assertField("email", '');
    $this->assertField('password', '');

    # login with no input
    $this->click(I18n::get('global.login'));

    $this->assertText(I18n::get('error.form.missing.email'));
    $this->assertText(I18n::get('error.form.missing.password'));

    $this->assertTrue($this->setField('email', 'admin@wrongexample.com'));
    $this->setField('password', 'test');

    # login with wrong input
    $this->clickSubmit(I18n::get('global.login'));

    $this->assertText(I18n::get('error.session.create'));

    $this->setField('email', 'admin@example.com');
    $this->setField('password', 'test');

    # login with correct input
    $this->click(I18n::get('global.login'));

    $this->assertResponse(200);
    $this->assertText(I18n::get('success.session.create'));

    # logout
    $this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/destroy');
  }

  function testDestroy() {
    $this->setMaximumRedirects(0);
    $this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/destroy');
    $this->assertResponse('302');
  }

  function testResendPassword() {
    # @todo
  }

  function testVerification() {
    # @todo
  }
}