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

require_once PATH_STANDARD . '/vendor/symfony_yaml/sfYaml.php';
require_once PATH_STANDARD . '/app/helpers/I18n.helper.php';

use \CandyCMS\Helper\I18n as I18n;

class UnitTestOfI18nHelper extends CandyUnitTest {

  function setUp() {
    $this->oObject = new I18n('en');
  }

  function tearDown() {
    parent::tearDown();
  }

  function testGetArray() {
    $this->assertIsA(I18n::getArray(), 'array');
    $this->assertEqual(count(I18n::getArray('website')), 2);
  }

  function testGetJson() {
    $this->assertIsA(json_decode(I18n::getJson(), true), 'array');
  }

  function testGet() {
    $this->assertIsA(I18n::get('website.description'), 'string');
    $this->assertFalse(I18n::get('test'));
  }

  function testUnsetLanguage() {
    I18n::unsetLanguage();
    $this->assertFalse(isset($_SESSION['lang']));
  }
}