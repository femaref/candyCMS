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

require_once PATH_STANDARD . '/app/models/Comment.model.php';

use \CandyCMS\Model\Comment as Comment;

class UnitTestOfCommentModel extends CandyUnitTest {

  function setUp() {

    $this->aRequest = array(
				'section'		=> 'blog',
        'name'			=> 'Name',
        'email'			=> 'email@example.com',
        'content'		=> 'Content',
        'parent_id' => 0);

    $this->oObject = new Comment($this->aRequest, $this->aSession);
  }

  function testCreate() {
    $this->assertTrue($this->oObject->create());

    $this->iLastInsertId = (int) Comment::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testGetData() {
    $this->assertIsA($this->oObject->getData(0, 1, 1), 'array');
  }

  function testDestroy() {
    $this->assertTrue($this->oObject->destroy($this->iLastInsertId));
  }
}