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
require_once PATH_STANDARD . '/app/controllers/Logs.controller.php';
require_once PATH_STANDARD . '/app/models/Logs.model.php';

use \CandyCMS\Controller\Logs as Logs;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfLogController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'logs';
		$this->oObject = new Logs($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testDestroy() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

  function testCreate() {
    # there is no create
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
    $this->assert404();
  }

  function testUpdate() {
    # there is no update
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
    $this->assert404();
  }

}

class UnitTestOfLogController extends CandyUnitTest {

  function setUp() {
    $this->aRequest = array('controller' => 'logs');

    $this->oObject = new Logs($this->aRequest, $this->aSession);
  }

  function testInsert() {
    $iTime = time() - 100;
    $this->assertTrue(Logs::insert('test', 'create', 1, 0, $iTime, $iTime));

    $this->iLastInsertId = (int) \CandyCMS\Model\Logs::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testUpdateEndTime() {
    $iTime = time() + 100;
    $this->assertTrue($this->oObject->updateEndTime($iTime));

  }

}