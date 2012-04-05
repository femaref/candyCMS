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

require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Blogs.model.php';

use \CandyCMS\Core\Models\Blogs;

class UnitTestOfBlogModel extends CandyUnitTest {

  function setUp() {
    $this->aRequest = array(
        'title'       => 'Title',
        'tags'        => 'Tags',
        'teaser'      => 'Teaser',
        'content'     => 'Blog',
        'date'        => '0',
        'keywords'    => 'Keywords',
        'published'   => 0,
        'author_id'   => 0,
        'controller'  => 'blogs',
        'language'    => 'en');

    $this->oObject = new Blogs($this->aRequest, $this->aSession);
  }

	function tearDown() {
		parent::tearDown();
	}

  function testCreate() {
    $this->assertTrue($this->oObject->create());

    $this->iLastInsertId = (int) Blogs::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testGetData() {
    $this->assertIsA($this->oObject->getData(1), 'array');
    $this->assertIsA($this->oObject->getData(), 'array');
  }

  function testUpdate() {
    $this->assertTrue($this->oObject->update($this->iLastInsertId));
  }

  function testDestroy() {
    $this->assertTrue($this->oObject->destroy($this->iLastInsertId));
  }
}