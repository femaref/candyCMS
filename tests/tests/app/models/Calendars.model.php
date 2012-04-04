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

require_once PATH_STANDARD . '/vendor/candyCMS/models/Calendars.model.php';

use \CandyCMS\Core\Model\Calendars as Calendars;

class UnitTestOfCalendarModel extends CandyUnitTest {

  function setUp() {

    $this->aRequest = array(
        'title' => 'Title',
        'content' => 'Content',
        'start_date' => '0000-00-00',
        'end_date' => '0000-00-00',
        'section' => 'calendar',
        'controller'  => 'calendars',
        'language'    => 'en');

    $this->oObject = new Calendars($this->aRequest, $this->aSession);
  }

  function testCreate() {
    $this->assertTrue($this->oObject->create());

    $this->iLastInsertId = (int) Calendars::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'Calendar #' . $this->iLastInsertId . ' created.');
  }

  function testGetData() {
    $this->assertIsA($this->oObject->getData(0), 'array');
    $this->assertIsA($this->oObject->getData(), 'array');
  }

  function testUpdate() {
    $this->assertTrue($this->oObject->update($this->iLastInsertId), 'Calendar #' . $this->iLastInsertId . ' updated.');
  }

  function testDestroy() {
    $this->assertTrue($this->oObject->destroy($this->iLastInsertId), 'Calendar #' . $this->iLastInsertId . ' destroyed.');
  }
}