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
require_once('app/models/Calendar.model.php');

use \CandyCMS\Model\Calendar as Calendar;

class TestOfCalendarModel extends UnitTestCase {

  public $oCalendar;
  public $iLastInsertId;

  function testConstructor() {

    $aRequest = array('title' => 'Title',
                      'content' => 'Content',
                      'start_date' => '0000-00-00',
                      'end_date' => '0000-00-00');
    $aSession = array();
    $aCookie  = array();

    $this->oCalendar = new Calendar($aRequest, $aSession, $aCookie, '');
  }

  function testCreate() {
    $this->assertTrue($this->oCalendar->create());

    $this->iLastInsertId = Calendar::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'string', 'Calendar #' . $this->iLastInsertId . ' created.');
  }

  function testGetData() {
    $this->assertIsA($this->oCalendar->getData(0), 'array');
    $this->assertIsA($this->oCalendar->getData(), 'array');
  }

  function testUpdate() {
    $this->assertTrue($this->oCalendar->update($this->iLastInsertId), 'Calendar #' . $this->iLastInsertId . ' updated.');
  }

  function testDestroy() {
    $this->assertTrue($this->oCalendar->destroy($this->iLastInsertId), 'Calendar #' .$this->iLastInsertId. ' destroyed.');
  }
}