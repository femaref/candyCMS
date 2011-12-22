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
require_once('app/models/Comment.model.php');

use \CandyCMS\Model\Comment as Comment;

class TestOfCommentModel extends UnitTestCase {

  public $oComment;
  public $iLastInsertId;

  function testConstructor() {

    $aRequest = array(
        'name' => 'Name',
        'email' => 'email@example.com',
        'content' => 'Content',
        'parent_id' => 0);

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

    $this->oComment = new Comment($aRequest, $aSession, $aFile, $aCookie);
  }

  function testCreate() {
    $this->assertTrue($this->oComment->create());

    $this->iLastInsertId = (int) Comment::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'Comment #' . $this->iLastInsertId . ' created.');
  }

  function testGetData() {
    $this->assertIsA($this->oComment->getData(0, 1, 1), 'array');
  }

  function testDestroy() {
    $this->assertTrue($this->oComment->destroy($this->iLastInsertId), 'Comment #' . $this->iLastInsertId . ' destroyed.');
  }
}