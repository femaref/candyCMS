<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/models/Search.model.php';

class Search extends Main {
  protected $_aRequest;
  protected $_aSession;

  public function __init() {
    $this->_oModel = new Model_Search($this->_aRequest, $this->_aSession);
  }

  # We can show search directly or per post
  public function show() {
    if (!isset($this->_aRequest['id']) || empty($this->_aRequest['id']))
      $this->_aError['id'] = LANG_ERROR_FORM_MISSING_CONTENT;

    if (isset($this->_aError))
      return $this->showFormTemplate();

    else {
      $aTables = array('blogs', 'contents');
      $sSearch = Helper::formatInput($this->_aRequest['id']);
      $sHeadline = str_replace('%s', $sSearch, LANG_SEARCH_SHOW_TITLE);

      # Fetch data
      $this->_aData = $this->_oModel->getData($sSearch, $aTables);

      # Build real table names
      foreach($aTables as $sTable) {
        $iTableLen = strlen($sTable) - 1;
        $this->_aData[$sTable]['title'] = substr(ucfirst($sTable), 0, $iTableLen);
      }

      $this->_oSmarty->assign('search', $sSearch);
      $this->_oSmarty->assign('tables', $this->_aData);

      # Create page title and description
      $this->_setDescription($sHeadline);
      $this->_setTitle($sHeadline);

      # Language
      $this->_oSmarty->assign('lang_headline', $sHeadline);

      $this->_oSmarty->template_dir = Helper::getTemplateDir('searches/_form');
      return $this->_oSmarty->fetch('searches/show.tpl');
    }
  }

  public function showFormTemplate() {
    $this->_setDescription(LANG_GLOBAL_SEARCH);
    $this->_setTitle(LANG_GLOBAL_SEARCH);

    $this->_oSmarty->template_dir = Helper::getTemplateDir('searches/_form');
    return $this->_oSmarty->fetch('searches/_form.tpl');
  }
}