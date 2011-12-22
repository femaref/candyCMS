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
require_once('app/models/Search.model.php');

use \CandyCMS\Model\Search as Search;

class TestOfSearchModel extends UnitTestCase {

  public $oSearch;
  public $iLastInsertId;

  function testConstructor() {

    $aRequest = array('section' => 'search');

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

    $this->oSearch = new Search($aRequest, $aSession, $aFile, $aCookie);
  }

  function testGetData() {
    $this->assertIsA($this->oSearch->getData('test'), 'array');
  }
}