<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'app/models/Blog.model.php';

class BlogExtended {
  public function __construct($aRequest, $aSession, $aFile = '') {
    $this->_aRequest	=& $aRequest;
    $this->_aSession	=& $aSession;
    $this->_aFile			=& $aFile;
  }

	public function __init() {
		$this->_oModel = new Model_Blog($this->_aRequest, $this->_aSession);
	}

  public function show() {
  }
}

?>
