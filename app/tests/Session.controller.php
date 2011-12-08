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
require_once('app/controllers/Session.controller.php');

use \CandyCMS\Controller\Session as Session;

class TestOfSessionController extends WebTestCase {

  public $osession;

  function testConstructor() {
    $aRequest = array('section' => 'session');
    $aSession = array();
    $aCookie  = array();

    $this->osession = new Session($aRequest, $aSession, $aCookie, '');
  }

  function testShow() {
    $this->assertTrue($this->get(WEBSITE_URL . '/session'));
    $this->assertResponse('200');
  }

  function testShowPost() {
    $aRequest = array('email' => 'email@example.com', 'password' => 'Password', 'formdata' => 'create_session');
    $this->post(WEBSITE_URL . '/session', $aRequest);
  }

  function testCreate() {
    $this->get(WEBSITE_URL . '/session/create');
    $this->assertResponse('200');
  }

  function testUpdate() {
    $this->get(WEBSITE_URL . '/session/update');
    $this->assertResponse('200');
  }

  function testDestroy() {
    $this->get(WEBSITE_URL . '/session/destroy');
    $this->assertResponse('200');
  }
}