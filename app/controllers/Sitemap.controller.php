<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Blog.model.php';
require_once 'app/models/Content.model.php';
require_once 'app/models/Gallery.model.php';

class Sitemap extends Main {

  public function __init() {
    Header('Content-Type: text/xml');
  }

  public function show() {
    # start
    $sWebsiteLandingPage = WEBSITE_URL . '/' . WEBSITE_LANDING_PAGE;

    # blog
    $oBlog = new Model_Blog();
    $aBlog = $oBlog->getData('', false, 1000);

    # content
    $oContent = new Model_Content();
    $aContent = $oContent->getData();

    # gallery
    $oGallery = new Model_Gallery();
    $aGallery = $oGallery->getData();

    # user
    $oUser = new Model_User();
    $aUser = $oUser->getData();

    $this->_oSmarty->assign('_website_landing_page_', $sWebsiteLandingPage);
    $this->_oSmarty->assign('_website_url_', WEBSITE_URL);

    $this->_oSmarty->assign('blog', $aBlog);
    $this->_oSmarty->assign('content', $aContent);
    $this->_oSmarty->assign('gallery', $aGallery);
    $this->_oSmarty->assign('user', $aUser);

    $this->_oSmarty->template_dir = Helper::getTemplateDir('sitemaps/xml');
    return $this->_oSmarty->fetch('sitemaps/xml.tpl');
  }
}