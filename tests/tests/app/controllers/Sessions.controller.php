<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

require_once('lib/simpletest/web_tester.php');
require_once('lib/simpletest/reporter.php');
require_once('app/controllers/Sessions.controller.php');

use CandyCMS\Controller\Sessions as Sessions;
use CandyCMS\Helper\I18n as I18n;

class TestOfSessionController extends WebTestCase {

  public $osession;

  function testConstructor() {
    $aRequest = array('section' => 'session');
    $aFile    = array();
    $aCookie  = array();
    $aSession['user'] = array(
      'email' => '',
      'facebook_id' => '',
      'id' => 0,
      'name' => '',
      'surname' => '',
      'password' => '',
      'role' => 0,
      'full_name' => ''
    );

    $this->oSessions = new Sessions($aRequest, $aSession, $aFile, $aCookie);
  }

  function testShow() {
    $this->assertTrue($this->get(WEBSITE_URL . '/session'));
    $this->assertNoText(I18n::get('error.standard'));
    $this->assertResponse('200');
  }

  function testCreate() {
    $this->setMaximumRedirects(0);
    $this->get(WEBSITE_URL . '/session/create');
    #$this->assertFieldById('input-email');
    $this->assertFieldById('input-password');

    $aParams = array('email' => 'email@example.com', 'password' => 'Password', 'formdata' => 'create_session');
    $this->assertTrue($this->post(WEBSITE_URL . '/session/create', $aParams));
    $this->assertResponse('200');
    $this->assertNoText(I18n::get('error.standard'));
  }

  function testDestroy() {
    $this->setMaximumRedirects(0);
    $this->get(WEBSITE_URL . '/session/destroy');
    $this->assertResponse('302');
  }
}