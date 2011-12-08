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
require_once('app/controllers/User.controller.php');

use \CandyCMS\Controller\User as User;

class TestOfUserController extends UnitTestCase {

  public $oUser;

  function testConstructor() {

    $aRequest = array('section' => 'user');
    $aSession = array();
    $aCookie  = array();

    $this->oUser = new User($aRequest, $aSession, $aCookie, '');
  }

  function testDirIsWritable() {
    $oFile = fopen('upload/user/test.log', 'a');
    fwrite($oFile, 'Is writeable.' . "\n");
    fclose($oFile);

    $this->assertTrue(file_exists('upload/user/test.log'), 'File was created.');
    @unlink('upload/user/test.log');
  }
}