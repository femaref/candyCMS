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

require_once PATH_STANDARD . '/vendor/candyCMS/models/Comments.model.php';
require_once PATH_STANDARD . '/vendor/candyCMS/helpers/Pagination.helper.php';

use \CandyCMS\Core\Model\Comments as Comments;

class UnitTestOfCommentModel extends CandyUnitTest {

  function setUp() {

    $this->aRequest = array(
				'section'		=> 'blog',
        'name'			=> 'Name',
        'email'			=> 'email@example.com',
        'content'		=> 'Content',
        'parent_id' => '666',
        'controller' => 'blogs');

    $this->oObject = new Comments($this->aRequest, $this->aSession);
  }

  function testCreate() {
    $this->assertTrue($this->oObject->create());

    $this->iLastInsertId = (int) Comments::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testGetData() {
    $this->assertIsA($this->oObject->getData(0, 1, 1), 'array');
  }

  function testGetParentId() {
    $this->assertEqual($this->oObject->getParentId($this->iLastInsertId), '666');
  }

  function testDestroy() {
    $this->assertTrue($this->oObject->destroy($this->iLastInsertId));
  }
}