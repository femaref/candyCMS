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

    $aRequest = array(
        'title' => 'Title',
        'teaser' => 'Teaser',
        'content' => 'Content',
        'keywords' => 'Keywords',
        'section' => 'content');

    $aSession['userdata'] = array(
        'email' => '',
        'facebook_id' => '',
        'id' => 0,
        'name' => '',
        'surname' => '',
        'password' => '',
        'user_right' => 0,
        'full_name' => ''
    );

    $aFile = array();
    $aCookie = array();

    $this->oContent = new Content($aRequest, $aSession, $aFile, $aCookie);
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
    $this->assertTrue($this->oContent->destroy($this->iLastInsertId), 'Content #' . $this->iLastInsertId . ' destroyed.');
  }
}