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
require_once('app/models/Blog.model.php');

use \CandyCMS\Model\Blog as Blog;

class TestOfBlogModel extends UnitTestCase {

  public $oBlog;
  public $iLastInsertId;

  function testConstructor() {

    $aRequest = array(
        'title' => 'Title',
        'tags' => 'Tags',
        'teaser' => 'Teaser',
        'content' => 'Blog',
        'keywords' => 'Keywords',
        'published' => 0,
        'author_id' => 0,
        'section' => 'blog');

    $aSession['userdata'] = array(
        'email' => '',
        'facebook_id' => '',
        'id' => 0,
        'name' => '',
        'surname' => '',
        'password' => '',
        'role' => 0,
        'full_name' => ''
    );

    $aFile = array();
    $aCookie = array();

    $this->oBlog = new Blog($aRequest, $aSession, $aFile, $aCookie);
  }

  function testCreate() {
    $this->assertTrue($this->oBlog->create());

    $this->iLastInsertId = (int) Blog::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'Blog #' . $this->iLastInsertId . ' created.');
  }

  function testGetData() {
    $this->assertIsA($this->oBlog->getData(0), 'array');
    $this->assertIsA($this->oBlog->getData(), 'array');
  }

  function testUpdate() {
    $this->assertTrue($this->oBlog->update($this->iLastInsertId), 'Blog #' . $this->iLastInsertId . ' updated.');
  }

  function testDestroy() {
    $this->assertTrue($this->oBlog->destroy($this->iLastInsertId), 'Blog #' . $this->iLastInsertId . ' destroyed.');
  }
}