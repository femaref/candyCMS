<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

require_once('app/models/Calendar.model.php');

use \CandyCMS\Model\Calendar as Calendar;

class TestOfCalendarModel extends UnitTestCase {

  public $oCalendar;
  public $iLastInsertId;

  function testConstructor() {

    $aRequest = array(
        'title' => 'Title',
        'content' => 'Content',
        'start_date' => '0000-00-00',
        'end_date' => '0000-00-00',
        'section' => 'calendar');

    $aSession['userdata'] = array(
        'email' => '',
        'facebook_id' => '',
        'id' => 0,
        'name' => '',
        'surname' => '',
        'password' => '',
        'right' => 0,
        'full_name' => ''
    );

    $aFile = array();
    $aCookie = array();

    $this->oCalendar = new Calendar($aRequest, $aSession, $aFile, $aCookie);
  }

  function testCreate() {
    $this->assertTrue($this->oCalendar->create());

    $this->iLastInsertId = (int) Calendar::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'Calendar #' . $this->iLastInsertId . ' created.');
  }

  function testGetData() {
    $this->assertIsA($this->oCalendar->getData(0), 'array');
    $this->assertIsA($this->oCalendar->getData(), 'array');
  }

  function testUpdate() {
    $this->assertTrue($this->oCalendar->update($this->iLastInsertId), 'Calendar #' . $this->iLastInsertId . ' updated.');
  }

  function testDestroy() {
    $this->assertTrue($this->oCalendar->destroy($this->iLastInsertId), 'Calendar #' . $this->iLastInsertId . ' destroyed.');
  }
}