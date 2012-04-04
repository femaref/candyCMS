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

require_once PATH_STANDARD . '/vendor/candyCMS/controllers/Errors.controller.php';
require_once PATH_STANDARD . '/vendor/candyCMS/helpers/Helper.helper.php';

use \CandyCMS\Core\Controller\Errors as Errors;
use \CandyCMS\Core\Helper\I18n as I18n;

class WebTestOfErrorController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'errors';
		$this->oObject = new Errors($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
    $this->assert404();
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/404'));
		$this->assert404();
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

  function testDestroy() {
    # there is no destroy
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
    $this->assert404();
  }
}