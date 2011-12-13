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

use \CandyCMS\Controller\User as User;
use \CandyCMS\Helper\I18n as I18n;

class TestOfUserController extends WebTestCase {

  public $oUser;

  function testConstructor() {

    $aRequest = array('section' => 'user');
    $aFile    = array();
    $aCookie  = array();
    $aSession['userdata'] = array(
      'email' => '',
      'facebook_id' => '',
      'id' => 0,
      'name' => '',
      'surname' => '',
      'password' => '',
      'user_right' => 0,
      'full_name' => ''
    );

    $this->oUser = new User($aRequest, $aSession, $aFile, $aCookie);
  }

  function testShow() {
    $this->assertTrue($this->get(WEBSITE_URL . '/user'));
    $this->assertText(I18n::get('lang.error.missing.permission')); # user has not enough permission
    $this->assertResponse('200');
  }

  function testCreate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/user/create'));
    $this->assertNoText(I18n::get('lang.error.missing.permission')); # user has not enough permission
    $this->assertResponse('200');
  }

  function testUpdate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/user/0/update'));
    $this->assertText(I18n::get('lang.error.session.create_first')); # user has not enough permission
    $this->assertResponse('200');
  }

  function testDestroy() {
    $this->assertTrue($this->get(WEBSITE_URL . '/user/0/destroy'));
    $this->assertText(I18n::get('lang.error.missing.permission')); # user has not enough permission
    $this->assertResponse('200');
  }

  function testDirIsWritable() {
    $oFile = fopen('upload/user/test.log', 'a');
    fwrite($oFile, 'Is writeable.' . "\n");
    fclose($oFile);

    $this->assertTrue(file_exists('upload/user/test.log'), 'File was created.');
    @unlink('upload/user/test.log');
  }
}