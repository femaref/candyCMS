<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */
require_once('lib/simpletest/autorun.php');
require_once('app/controllers/User.controller.php');

use CandyCMS\Controller\User as User;
use CandyCMS\Helper\I18n as I18n;

class TestOfUserController extends WebTestCase {

  function testShow() {
    # Show user overview
    $this->assertTrue($this->get(WEBSITE_URL . '/user'));
    $this->assertText(I18n::get('error.missing.permission')); # user has no permission
    $this->assertResponse('200');

    # Show user with id
    $this->assertTrue($this->get(WEBSITE_URL . '/user/1'));
    $this->assertText(I18n::get('user.label.registered_since')); # "registered at"
    $this->assertResponse('200');
  }

  function testCreate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/user/create'));
    $this->assertNoText(I18n::get('error.missing.permission')); # user has no permission
    $this->assertResponse('200');
  }

  function testUpdate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/user/0/update'));
    $this->assertText(I18n::get('error.session.create_first')); # user has to login first
    $this->assertResponse('200');
  }

  function testDestroy() {
    $this->assertTrue($this->get(WEBSITE_URL . '/user/0/destroy'));
    $this->assertText(I18n::get('error.missing.permission')); # user has no permission
    $this->assertResponse('200');
  }

  function testDirIsWritable() {
		$this->assertTrue(parent::createFile('upload/' . $this->aRequest['controller']));
		$this->assertTrue(parent::removeFile('upload/' . $this->aRequest['controller']));
  }
}