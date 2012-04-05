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

require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Sessions.controller.php';

use CandyCMS\Core\Controllers\Sessions;
use CandyCMS\Core\Helpers\I18n;

class WebTestOfSessionController extends CandyWebTest {

  function setUp() {
    $this->aRequest['controller'] = 'sessions';
    $this->oObject = new Sessions($this->aRequest, $this->aSession);
  }

  function tearDown() {
    parent::tearDown();
  }

  function testShow() {
    # for now, this should be a redirect
    $this->setMaximumRedirects(0);
    $this->get(WEBSITE_URL . '/' . $this->aRequest['controller']);
    $this->assertResponse(302);
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
    $this->assertTrue($this->setField('password', 'test'));

    # login with wrong input
    $this->clickSubmit(I18n::get('global.login'));

    $this->assertText(I18n::get('error.session.create'));

    $this->assertTrue($this->setField('email', 'admin@example.com'));
    $this->assertTrue($this->setField('password', 'test'));

    # login with correct input
    $this->click(I18n::get('global.login'));

    $this->assertResponse(200);
    $this->assertText(I18n::get('success.session.create'));

    # logout
    $this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/destroy');
  }

  function testDestroy() {
    $this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/destroy');
    $this->assertResponse('200');
  }

  function testResendPassword() {
    $this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create');
    $this->assertText(I18n::get('sessions.password.title'));
    $this->click(I18n::get('sessions.password.title'));
    $this->assertField('email', '');

    # try to resend without email
    $this->click(I18n::get('global.submit'));
    $this->assertText(I18n::get('error.form.missing.email'));

    $this->assertTrue($this->setField('email', 'this aint an email adress'));
    # try to resend with wrong email
    $this->click(I18n::get('global.submit'));
    $this->assertText(I18n::get('error.mail.format'));

    $this->assertTrue($this->setField('email', WEBSITE_MAIL));
    # try to resend with proper email that is not in the system (hopefully)
    $this->click(I18n::get('global.submit'));
    $this->assertText(I18n::get('error.session.account'));
  }

  function testResendVerification() {
    $this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create');
    $this->assertText(I18n::get('sessions.verification.title'));
    $this->click(I18n::get('sessions.verification.title'));
    $this->assertField('email', '');

    # try to resend without email
    $this->click(I18n::get('global.submit'));
    $this->assertText(I18n::get('error.form.missing.email'));

    $this->assertTrue($this->setField('email', 'this aint an email adress'));
    # try to resend with wrong email
    $this->click(I18n::get('global.submit'));
    $this->assertText(I18n::get('error.mail.format'));

    $this->assertTrue($this->setField('email', WEBSITE_MAIL));
    # try to resend with proper email that is not in the system (hopefully)
    $this->click(I18n::get('global.submit'));
    $this->assertText(I18n::get('error.session.account'));
  }

  function testUpdate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
    $this->assert404();
  }
}