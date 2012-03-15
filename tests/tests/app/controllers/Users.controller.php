<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */
require_once PATH_STANDARD . '/app/controllers/Main.controller.php';
require_once PATH_STANDARD . '/app/controllers/Users.controller.php';

use \CandyCMS\Controller\Users as Users;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfUserController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'users';
		$this->oObject = new Users($this->aRequest, $this->aSession);
	}

  function testShowOverview() {
    # Show user overview
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
    $this->assertText(I18n::get('error.missing.permission')); # user has no permission
    $this->assertResponse('200');
	}

  function testShowUsers() {
    # Show user with id
    #@todo user not found?
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1'));
    $this->assertText(I18n::get('users.label.registered_since')); # "registered at"
    $this->assertResponse('200');

    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2'));
    $this->assertText(I18n::get('users.label.registered_since')); # "registered at"
    $this->assertText('Administrator c2f9619961');
    $this->assertResponse('200');
  }

  function testCreate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
    $this->assertNoText(I18n::get('error.missing.permission')); # user has no permission
    $this->assertResponse('200');
  }

  function testUpdate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/0/update'));
    $this->assertText(I18n::get('error.session.create_first')); # user has to login first
    $this->assertResponse('200');
  }

  function testDestroy() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/0/destroy'));
    $this->assertText(I18n::get('error.missing.permission')); # user has no permission
    $this->assertResponse('200');
  }

  function testDirIsWritable() {
		$this->assertTrue(parent::createFile('upload/' . $this->aRequest['controller']));
		$this->assertTrue(parent::removeFile('upload/' . $this->aRequest['controller']));
  }

  function testVerifyEmail() {
    $this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/ThisIsNotAValidVerificationCode/verification'));
    $this->assertResponse(200);
		$this->assertText(I18n::get('error.user.verification'));
 }

  function testGetToken() {
    $this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2/token', array(
				'email' => 'admin@example.com',
				'password' => 'test'
		)));
    $this->assertResponse(200);
		$this->assertText('c2f9619961');
		$this->assertText('"token"');
		$this->assertText('"success":true');

    //test with a wrong password
    $this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2/token', array(
				'email' => 'admin@example.com',
				'password' => 'abc'
		)));
    $this->assertResponse(200);
		$this->assertNoText('c2f9619961');
		$this->assertText('"success":false');

    //test with a not existing
    $this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/token', array(
				'email' => 'admin@example.com',
				'password' => 'abc'
		)));
    $this->assertResponse(200);
		$this->assertNoText('c2f9619961');
		$this->assertText('"success":false');

    //test without data
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/token'));
    $this->assertResponse(200);
		$this->assertText('"success":false');
  }
}