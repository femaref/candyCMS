<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

try {
  /* Load Parent */
  if( !file_exists('app/models/Main.model.php') ||
          !file_exists('app/controllers/Main.controller.php') ||
          !file_exists('app/controllers/Index.controller.php') ||
          !file_exists('app/controllers/Gallery.controller.php') ||
          !file_exists('app/helpers/AdvancedException.helper.php') ||
          !file_exists('app/helpers/Section.helper.php') ||
          !file_exists('app/helpers/Helper.helper.php') ||
          !file_exists('app/helpers/SqlConnect.helper.php') ||
          !file_exists('app/helpers/SqlQuery.helper.php')
  )
    throw new Exception('Could not load required classes.');
  else {
    require_once 'app/models/Main.model.php';
    require_once 'app/controllers/Main.controller.php';
    require_once 'app/controllers/Index.controller.php';
    require_once 'app/controllers/Gallery.controller.php';

    # All helpers
    require_once 'app/helpers/AdvancedException.helper.php';
    require_once 'app/helpers/Section.helper.php';
    require_once 'app/helpers/Helper.helper.php';
    require_once 'app/helpers/SqlConnect.helper.php';
    require_once 'app/helpers/SqlQuery.helper.php';
  }
}
catch (Exception $e) {
  die($e->getMessage());
}

final class Multiple_Upload extends Index {
  private $_aReturn = '';
  private $_sFilePath;

  public final function validateAndUpload() {
    $sError = '';
    if (!isset($this->_aFile['Filedata']) || !is_uploaded_file($this->_aFile['Filedata']['tmp_name'])) {
      $sError = 'Invalid Upload';
    }

    if( empty($sError) )
      $this->createFiles();

    return $this->returnStatus($sError);
  }

  public final function createFiles() {
    if( isset($this->_aRequest['section']) && 'gallery' == $this->_aRequest['section']) {
      $this->_oModel = new Model_Gallery($this->_aRequest, $this->_aSession, $this->_aFile);
      $this->_sFilePath = $this->_oModel->createFile(USER_ID);
      $this->_aReturn['link'] = $this->_sFilePath;
    }
  }

  public final function returnStatus($sError) {
    if(!empty($sError)) {
      $this->_aReturn = array('status' => '0',
              'error' => $sError);
    }
    else {
      $this->_aReturn = array('status' => '1',
              'name' => $this->_aFile['Filedata']['name']);

      if(file_exists($this->_aFile['Filedata']['tmp_name']))
        $this->_aReturn['hash'] = md5_file($this->_aFile['Filedata']['tmp_name']);

      $aInfo = @getimagesize($this->_sFilePath);

      if( $aInfo ) {
        $this->_aReturn['width']	= $aInfo[0];
        $this->_aReturn['height']	= $aInfo[1];
        $this->_aReturn['mime']		= $aInfo['mime'];
      }
    }

    if(	isset($this->_aRequest['response']) && $this->_aRequest['response'] == 'xml') {
      $sOutput = '<response>';

      foreach($this->_aReturn as $sKey => $sValue) {
        $sOutput .= '<'	.$sKey.	'><![CDATA['	.$sValue.	']]></'	.$sKey.	'>';
      }

      $sOutput .= '</response>';
      return $sOutput;
    }
    else
      return json_encode($this->_aReturn);
  }
}

if(isset ($_FILES) && !empty($_FILES) ) {
  $aFile =& $_FILES;
  session_start();

  $oUpload = new Multiple_Upload($_REQUEST, $_SESSION, $aFile, $_COOKIE);
  $oUpload->loadConfig();
  $oUpload->connectDB();

  $aUser =& $oUpload->setUser($_GET['session_id']);

  define( 'USER_ID',	(int)$aUser['id'] );
  define( 'USER_RIGHT',(int)$aUser['userright'] );

  if(USER_RIGHT > 3)
    echo $oUpload->validateAndUpload();
  else
    die('No Permission!'  .print_r($aUser));

} else
  die('NO FILES TO UPLOAD!');

?>