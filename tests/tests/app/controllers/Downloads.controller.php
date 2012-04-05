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
require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Downloads.controller.php';

use \CandyCMS\Core\Controllers\Downloads;
use \CandyCMS\Core\Helpers\I18n;

class WebTestOfDownloadController extends CandyWebTest {

  function setUp() {
    $this->aRequest['controller'] = 'downloads';
    $this->oObject = new Downloads($this->aRequest, $this->aSession);
  }

  function tearDown() {
    parent::tearDown();
  }

  function testShow() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
    $this->assertResponse(200);
    $this->assertText('098dec456d');
  }

  function testShowWithId() {
    //get an entry
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2'));
    $this->assertResponse(200);
    //get an entry with long id
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2/098dec456d'));
    $this->assertResponse(200);

    //get a missing entry
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/42'));
    $this->assertResponse(200);
    $this->assertText(I18n::get('error.missing.id'));
  }

  function testCreate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
    $this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
  }

  function testUpdate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
    $this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
  }

  function testDestroy() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
    $this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
  }

  function testDirIsWritable() {
    $this->assertTrue(parent::createFile('upload/' . $this->aRequest['controller']));
    $this->assertTrue(parent::removeFile('upload/' . $this->aRequest['controller']));
  }
}