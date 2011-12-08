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
require_once('app/controllers/Index.controller.php');

use \CandyCMS\Controller\Index as Index;

class TestOfIndexController extends UnitTestCase {

  public $oIndex;

  function testConstructor() {

    $aRequest = array();
    $aSession = array();
    $aCookie = array();

    $this->oIndex = new Index($aRequest, $aSession, $aCookie, '');
  }

  function testGetConfigFiles () {
    $this->assertTrue(file_exists('config/Candy.inc.php'), 'Candy.inc.php exists.');
    $this->assertTrue(file_exists('config/Plugins.inc.php'), 'Plugins.inc.php exists.');
    $this->assertTrue($this->oIndex->getConfigFiles(array('Candy', 'Plugins')));
  }

  function testGetPlugins() {
    $this->assertTrue($this->oIndex->getPlugins(ALLOW_PLUGINS));
  }

  function testGetLanguage() {
    $this->oIndex->getLanguage();
  }

  function testSetuser() {
    $this->assertFalse($this->oIndex->setUser());
  }

  function testSetTemplate() {
    $this->assertIsA($this->oIndex->setTemplate(), 'string');
  }

  function testShow() {
    #$this->expectException($this->oIndex->show(), 'string');
  }
}