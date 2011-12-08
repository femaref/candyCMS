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
require_once('app/models/Content.model.php');

use \CandyCMS\Model\Content as Content;

class TestOfContentModel extends UnitTestCase {

  public $oContent;
  public $iLastInsertId;

  function testConstructor() {

    $aRequest = array('title' => 'Title',
                      'teaser' => 'Teaser',
                      'content' => 'Content',
                      'keywords' => 'Keywords');
    $aSession = array();
    $aCookie  = array();

    $this->oContent = new Content($aRequest, $aSession, $aCookie, '');
  }

  function testCreate() {
    $this->assertTrue($this->oContent->create());

    $this->iLastInsertId = (int) Content::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'Content #' . $this->iLastInsertId . ' created.');
  }

  function testGetData() {
    $this->assertIsA($this->oContent->getData(0), 'array');
    $this->assertIsA($this->oContent->getData(), 'array');
  }

  function testUpdate() {
    $this->assertTrue($this->oContent->update($this->iLastInsertId), 'Content #' . $this->iLastInsertId . ' updated.');
  }

  function testDestroy() {
    $this->assertTrue($this->oContent->destroy($this->iLastInsertId), 'Content #' .$this->iLastInsertId. ' destroyed.');
  }
}