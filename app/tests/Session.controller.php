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

    $this->oSession = new Session($aRequest, $aSession, $aCookie, '');
  }

  function testShow() {
    $this->assertTrue($this->get(WEBSITE_URL . '/session'));
    $this->assertResponse('200');
  }

  function testCreate() {
    $this->setMaximumRedirects(0);
    $aParams = array('email' => 'email@example.com', 'password' => 'Password', 'formdata' => 'create_create');
    $this->assertTrue($this->post(WEBSITE_URL . '/session/create', $aParams));
  }

 /* function testUpdate() {
    $this->setMaximumRedirects(0);
    $this->get(WEBSITE_URL . '/session/update');
    $this->assertResponse('200');
  }*/

  function testDestroy() {
    $this->setMaximumRedirects(0);
    $this->get(WEBSITE_URL . '/session/destroy');
    $this->assertResponse('302');
  }
}