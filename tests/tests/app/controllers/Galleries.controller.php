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
require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Galleries.controller.php';

use \CandyCMS\Core\Controllers\Galleries;
use \CandyCMS\Core\Helpers\I18n;

class WebTestOfGalleryController extends CandyWebTest {

  function setUp() {
    $this->aRequest['controller'] = 'galleries';
  }

  function tearDown() {
    parent::tearDown();
  }

  function testShow() {
    # Show overview
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
    $this->assertResponse(200);
    $this->assertText('6dffc4c552');

    # Show album
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1'));
    $this->assertResponse(200);
    $this->assertText('982e960e18');

    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/6dffc4c552'));
    $this->assertResponse(200);
    $this->assertText('982e960e18');

    # Show image
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/image/1'));
    $this->assertResponse(200);
    $this->assertText('782c660e17');
  }

  function testDirIsWritable() {
    $sFile = PATH_STANDARD . '/upload/' . $this->aRequest['controller'] . '/test.log';
    $oFile = fopen($sFile, 'a');
    fwrite($oFile, 'Is writeable.' . "\n");
    fclose($oFile);

    $this->assertTrue(file_exists($sFile));
    $this->assertTrue(unlink($sFile));
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

  function testCreateFile() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/createfile'));
    $this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
  }

  function testUpdateFile() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/updatefile'));
    $this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
  }

  function testDestroyFile() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroyfile'));
    $this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
  }
}