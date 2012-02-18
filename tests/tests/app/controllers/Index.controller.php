<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */


require_once PATH_STANDARD . '/tests/simpletest/web_tester.php';
require_once PATH_STANDARD . '/tests/simpletest/reporter.php';
require_once PATH_STANDARD . '/app/controllers/Index.controller.php';

use \CandyCMS\Controller\Index as Index;

class TestOfIndexController extends WebTestCase {

  public $oIndex;

  function testConstructor() {
    $aRequest = array();
    $aFile    = array();
    $aCookie  = array();
    $aSession['userdata'] = array(
      'email' => '',
      'facebook_id' => '',
      'id' => 0,
      'name' => '',
      'surname' => '',
      'password' => '',
      'role' => 0,
      'full_name' => ''
    );

    $this->oIndex = new Index($aRequest, $aSession, $aFile, $aCookie);

    $this->get(WEBSITE_URL);
    $this->assertResponse('200');
  }

  function testGetConfigFiles () {
    $this->assertTrue(file_exists(PATH_STANDARD . '/config/Candy.inc.php'), 'Candy.inc.php exists.');
    $this->assertTrue(file_exists(PATH_STANDARD . '/config/Plugins.inc.php'), 'Plugins.inc.php exists.');
    $this->assertTrue(file_exists(PATH_STANDARD . '/config/Mailchimp.inc.php'), 'Plugins.inc.php exists.');
    $this->assertTrue($this->oIndex->getConfigFiles(array('Candy', 'Plugins', 'Mailchimp')));
  }

  function testGetPlugins() {
    $this->assertTrue($this->oIndex->getPlugins(ALLOW_PLUGINS));
  }

  function testGetLanguage() {
    $this->assertTrue($this->oIndex->getLanguage());
  }

  #function testSetUser() {
  #  $this->oIndex->setUser();
    #$this->assert
    #$this->assert($this->oIndex->setUser(), 'array');
  #}

  #function testSetTemplate() {
    #$this->assertIsA($this->oIndex->setTemplate(), 'string');
  #}

  function testDirIsWritable() {
		$sFile = PATH_STANDARD . '/upload/temp/test.log';
    $oFile = fopen($sFile, 'a');
    fwrite($oFile, 'Is writeable.' . "\n");
    fclose($oFile);

    $this->assertTrue(file_exists($sFile), 'Upload folder is basically writeable.');
    @unlink($sFile);
  }

  #function testShow() {
    #$this->expectException($this->oIndex->show(), 'string');
  #}
}