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

class TestOfSessionController extends WebTestCase {

  function testConstructor() {
    $aRequest = array('section' => 'sessions');

    $this->oObject = new Sessions($aRequest, $aSession, $aFile, $aCookie);
  }

  function testShow() {
    $this->assertTrue($this->get(WEBSITE_URL . '/sessions'));
    $this->assertNoText(I18n::get('error.standard'));
    $this->assertResponse('200');
  }

  /**
   * @todo correct format?
   * @todo validation
   */
  function testCreate() {
    $this->setMaximumRedirects(0);
    $this->get(WEBSITE_URL . '/session/create');
    #$this->assertFieldById('input-email');
    $this->assertFieldById('input-password');

    $aParams = array('email' => 'admin@example.com', 'password' => 'test', 'formdata' => 'create_sessions');
    $this->assertTrue($this->post(WEBSITE_URL . '/sessions/create', $aParams));
    $this->assertResponse('200');
    $this->assertNoText(I18n::get('error.standard'));
    #$this->assertText(I18n::get('error.standard')); # Welcome ...
  }

  function testDestroy() {
    $this->setMaximumRedirects(0);
    $this->get(WEBSITE_URL . '/session/destroy');
    $this->assertResponse('302');
  }
}