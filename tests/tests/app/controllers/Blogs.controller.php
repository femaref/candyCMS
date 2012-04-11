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

require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Blogs.controller.php';

use CandyCMS\Core\Controllers\Blogs;
use CandyCMS\Core\Helpers\I18n;

class WebTestOfBlogController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'blogs';
		$this->oObject = new Blogs($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
    # Overview
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertResponse(200);
		$this->assertText('hs24br55e2');
		$this->assertNoText('1d2275e170'); #not visible since different language

    # Short ID
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1'));
		$this->assertResponse(200);

    # Long ID
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/b3cf6b2dd0'));
		$this->assertResponse(200);
	}

  function testShowWithAPIToken() {
    # Overview with correct token
    # @todo this must be JSON
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '?api_token=c2f9619961'));
    $this->assertResponse(200);
    $this->assertText(I18n::get('global.create.entry'));

    # Overview with wrong token
    # @todo this must be JSON
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '?api_token=notatoken'));
    $this->assertResponse(200);
    $this->assertNoText(I18n::get('global.create.entry'));
  }

	function testShowEntryUnpublished() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2'));
		$this->assertResponse(200);
    $this->assertText(I18n::get('error.404.title'));
	}

  function testShowEntryUnpublishedWithAPIToken() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/2?api_token=c2f9619961'));
    $this->assertResponse(200);
    $this->assertText(I18n::get('global.not_published'));
  }

  function testShowPageTwo() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/page/2'));
		$this->assertResponse(200);
    $this->assertText('b3cf6b2dd0');
    $this->assertNoText('e12b3a84b2');
  }

  function testShowEntryWithDifferentLanguage() {
    # Entry is not listed...
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/page/3'));
		$this->assertResponse(200);
    $this->assertText(I18n::get('error.404.title'));

    # ...but we can access it directly.
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/4'));
		$this->assertResponse(200);
    $this->assertText('1d2275e170');
  }

  function testShowTags() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/tag1'));
		$this->assertResponse(200);
    $this->assertText('tag1');
  }

	function testCreate() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

  function testCreateWithAPIToken() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create?api_token=notatoken'));
    $this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);

    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create?api_token=c2f9619961'));
    $this->assertResponse(200);
    $this->assertField('title', '');
    $this->assertField('teaser', '');
    $this->assertField('tags', '');
    // ...
    # still have to manually send api token, so normal submit has to fail
    $this->click(I18n::get('global.create.create'));
    $this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);

    //actually creating and deleting is done in testCreateAndDestroyWithAPIToken
  }

	function testUpdate() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

  function testUpdateWithAPIToken() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update?api_token=notatoken'));
    $this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);

    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update?api_token=c2f9619961'));
    $this->assertResponse(200);
    $this->assertField('title', 'b3cf6b2dd0');
    $this->assertField('teaser', '');
    $this->assertField('tags', 'tag1');
    // ...
    # still have to manually send api token, so normal submit should fail
    $this->click(I18n::get('global.update.update'));
    $this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);

    # really update, by sending api_token
    $sContent = 'Content change at : ' . time();
    $this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update?api_token=c2f9619961',
                                  array('author_id' => '2',
                                        'content' => $sContent,
                                        'date' => '1',
                                        'id' => '1',
                                        'keywords' => 'APITesting',
                                        'language' => 'en',
                                        'published' => '1',
                                        'tags' => 'tag1',
                                        'title' => 'b3cf6b2dd0',
                                        'update_blogs' => 'formdata')));
    $this->assertResponse(200);
    $this->assertText(I18n::get('success.update'));
    $this->assertText($sContent);
  }

	function testDestroy() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

  function testCreateAndDestroyWithAPIToken() {
    $sTimestamp = '' . time();
    $sContent = 'Content created at : ' . $sTimestamp;
    #try to create without valid api_token
    $this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create?api_token=notatoken',
                                  array('content' => $sContent,
                                      'keywords' => '',
                                      'language' => 'en',
                                      'tags' => 'api',
                                      'title' => $sTimestamp,
                                      'teaser' => 'API stuff',
                                      'create_blogs' => 'formdata')));
		$this->assertText(I18n::get('error.missing.permission'));

    # create with valid api token
    $this->assertTrue($this->post(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create?api_token=c2f9619961',
                                  array('content' => $sContent,
                                      'keywords' => '',
                                      'language' => 'en',
                                      'tags' => 'api',
                                      'title' => $sTimestamp,
                                      'teaser' => 'API stuff',
                                      'published' => '1',
                                      'create_blogs' => 'formdata')));
    $this->assertResponse(200);
    $this->assertText(I18n::get('success.create'));
    $this->assertText($sTimestamp);

    # open the newly created blog entry
    $this->assertTrue($this->click($sTimestamp));
    $this->assertResponse(200);
    $sUrl = $this->getUrl();
    $sUrl = substr($sUrl, 0, strrpos($sUrl, '/'));

    $this->assertTrue($this->get($sUrl . '/destroy?api_token=c2f9619961'));
    $this->assertResponse(200);
    $this->assertText(I18n::get('success.destroy'));
  }
}