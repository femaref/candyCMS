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
require_once('app/models/User.model.php');

use \CandyCMS\Model\User as User;

class TestOfUserModel extends UnitTestCase {

  public $oUser;
  public $iLastInsertId;

  function testConstructor() {

    $aRequest = array('email' => 'email@example.com',
                      'password' => 'Password',
                      'name' => 'Name',
                      'surname' => 'Surname');
    $aSession = array();
    $aCookie  = array();

    $this->oUser = new User($aRequest, $aUser, $aCookie, '');
  }

  function testCreate() {
    $this->assertTrue($this->oUser->create('000000000000'));

    $this->iLastInsertId = User::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'string', 'User #' . $this->iLastInsertId . ' created.');
  }
/*
  function testGetData() {
    $this->assertIsA($this->oUser->getData(), 'array');
  }

  function testUpdate() {
    $this->assertTrue($this->oUser->update(0), 'User updated.');
  }

  function testDestroy() {
    $this->assertTrue($this->oUser->destroy($this->iLastInsertId), 'User #' .$this->iLastInsertId. ' destroyed.');
  }*/
}