<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Blog.model.php';
require_once 'app/models/Content.model.php';
require_once 'app/models/Gallery.model.php';
require_once 'app/models/User.model.php';

class Sitemap extends Main {

  public function __init() {}

  public function showXML() {
    Header('Content-Type: text/xml');

    $this->_oSmarty->assign('_website_landing_page_', WEBSITE_URL . '/' . WEBSITE_LANDING_PAGE);
    $this->_oSmarty->assign('_website_url_', WEBSITE_URL);

    $this->_getSitemap();

    $this->_oSmarty->template_dir = Helper::getTemplateDir('sitemaps/xml');
    return $this->_oSmarty->fetch('sitemaps/xml.tpl');
  }

  public function show() {
    $this->_getSitemap();

    $this->_oSmarty->template_dir = Helper::getTemplateDir('sitemaps/show');
    return $this->_oSmarty->fetch('sitemaps/show.tpl');
  }

  private function _getSitemap() {
    $oBlog		= new Model_Blog();
    $aBlog		= $oBlog->getData('', false, 1000);
    $oContent = new Model_Content();
    $aContent = $oContent->getData();
    $oGallery = new Model_Gallery();
    $aGallery = $oGallery->getData();

    $this->_oSmarty->assign('blog', $aBlog);
    $this->_oSmarty->assign('content', $aContent);
    $this->_oSmarty->assign('gallery', $aGallery);
  }
}