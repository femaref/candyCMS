<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

class Model_Newsletter extends Model_Main {
  private function _setData() {
    if( isset($this->m_aRequest['email']) && !empty($this->m_aRequest['email']) ) {
      $oGetData = new Query("	SELECT
																email
															FROM
																newsletter
															WHERE
																email ='"	.Helper::formatInput($this->m_aRequest['email']).	"'
															LIMIT
																1");

      if($oGetData->numRows() == true) {
        $oQuery = new Query("	DELETE FROM
                                newsletter
                              WHERE
                                email = '"	.Helper::formatInput($this->m_aRequest['email']).	"'
                              LIMIT 1");
        return 'DESTROY';
      }
      elseif($oGetData->numRows() == false) {
        $oQuery = new Query("	INSERT INTO
                                newsletter(email)
                              VALUES(
                                '"	.Helper::formatInput($this->m_aRequest['email']).	"')");

        return 'INSERT';
      }
      else
        return false;
    }
  }

  public function handleNewsletter() {
    return $this->_setData();
  }
}