<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

final class Helper {
  public static final function successMessage($sMSG) {
    $_SESSION['flash_message']['type']      = 'success';
    $_SESSION['flash_message']['message']   = $sMSG;
    $_SESSION['flash_message']['headline']  = '';
  }

  public static final function errorMessage($sMSG = '', $sHL = '') {
    if(empty($sHL))
      $sHL = LANG_ERROR_GLOBAL;

    if(empty($sMSG))
      $sMSG = LANG_ERROR_GLOBAL;

    $_SESSION['flash_message']['type']      = 'error';
    $_SESSION['flash_message']['message']   = $sMSG;
    $_SESSION['flash_message']['headline']  = $sHL;
  }

  public static final function debugMessage($sMSG) {
    $_SESSION['flash_message']['type']      = 'debug';
    $_SESSION['flash_message']['message']   = $sMSG;
    $_SESSION['flash_message']['headline']  = '';
  }

  public static final function redirectTo($sURL) {
    header('Location:'	.WEBSITE_URL.$sURL);
    die();
  }

  # TODO: Use config for variables
  public static final function formatTimestamp($iTime, $sStyle = ' - ') {
    if(date('d.m.Y', $iTime) == date('d.m.Y', time())) {
      $sDay = LANG_GLOBAL_TODAY;
      $sStyle = ',&nbsp;';
      $sTime = date('H:i', $iTime);
    }
    elseif(date('d.m.Y', $iTime) == date('d.m.Y', (time()-60*60*24))) {
      $sDay = LANG_GLOBAL_YESTERDAY;
      $sStyle = ',&nbsp;';
      $sTime = date('H:i', $iTime);
    }
    else {
      $sDay = date('d.m.Y', $iTime);
      $sTime = date('H:i', $iTime);
    }

    return $sDay.$sStyle.$sTime;
  }

  public final static function getFileSize($sPath) {
    $iSize = filesize($sPath);

    if($iSize > 1024 && $iSize < 1048576)
      $sReturn = round(($iSize / 1024), 2). ' KB';

    elseif($iSize >= 1048576 && $iSize < 1073741824)
      $sReturn = round(($iSize / 1048576), 2). ' MB';

    elseif($iSize >= 1073741824)
      $sReturn = round(($iSize / 1073741824), 2). ' GB';

    else
      $sReturn = round($iSize, 2). ' Byte';

    return $sReturn;
  }

  public final static function getAvatar($sPath, $iUID, $aGravatar = '') {
    if(!empty($aGravatar)) {
      $sMail  = $aGravatar['email'];
      $iSize  = $aGravatar['size'];
      return '';
    }
    else {
      $sFile = PATH_UPLOAD.	'/'	.$sPath.$iUID.	'.jpg';
      if(is_file($sFile))
        return WEBSITE_URL.  '/' .$sFile;
      else
        return WEBSITE_CDN.  '/' .PATH_IMAGES.  '/missing_avatar.jpg';
    }
  }

  public final static function createRandomChar($iLength) {
    $sChars='ABCDEFGHJKLMNOPQRSTUVWXYZabcedfghijklmnopqrstuvwxyz123456789';
    srand(microtime()*1000000);

    $sString = '';
    for($iI = 1; $iI <= $iLength ; $iI++) {
      $iTemp = rand(1, strlen($sChars));
      $iTemp--;
      $sString .= $sChars[$iTemp];
    }

    return $sString;
  }

  public final static function formatHTMLCode($sStr, $bBB = true) {
    $sStr = addslashes($sStr);

    if( $bBB == true )
      $sStr = htmlspecialchars($sStr);

    return $sStr;
  }

  public final static function removeSlahes($sStr) {
    $sStr = str_replace('\&quot;', '"', $sStr);
    $sStr = str_replace('\"', '"', $sStr);
    $sStr = str_replace("\'", "'", $sStr);
    return $sStr;
  }

  public final static function formatBBCode($sStr, $bUseParagraph = false) {
    $sStr = trim($sStr);
    $sStr = preg_replace('/\S{500}/', '\0 ', $sStr);

    # Remove Slashes
    $sStr = str_replace('\&quot;', '"', $sStr);
    $sStr = str_replace('\"', '"', $sStr);
    $sStr = str_replace("\'", "'", $sStr);

    # Format SpecialChars
    $sStr = str_replace('&quot;', '"', $sStr);

    # BB Code
    $sStr = str_replace('[hr]', '<hr />', $sStr);
    $sStr = preg_replace('/\[center\](.*)\[\/center]/isU', '<div style=\'text-align:center\'>\1</div>', $sStr);
    $sStr = preg_replace('/\[left\](.*)\[\/left]/isU', '<left>\1</left>', $sStr);
    $sStr = preg_replace('/\[right\](.*)\[\/right]/isU', '<right>\1</right>', $sStr);
    $sStr = preg_replace('/\[p\](.*)\[\/p]/isU', '<p>\1</p>', $sStr);
    $sStr = preg_replace('=\[hl\](.*)\[/hl\]=Uis', '<h3>\1</h3>', $sStr);
    $sStr = preg_replace('=\[b\](.*)\[/b\]=Uis', '<strong>\1</strong>', $sStr);
    $sStr = preg_replace('=\[i\](.*)\[/i\]=Uis', '<em>\1</em>', $sStr);
    $sStr = preg_replace('=\[u\](.*)\[/u\]=Uis', '<span style="text-decoration:underline">\1</span>', $sStr);
    $sStr = preg_replace('=\[del\](.*)\[/del\]=Uis', '<span style="text-decoration:line-through">\1</span>', $sStr);
    $sStr = preg_replace('=\[box\](.*)\[/box\]=Uis', '<div class="box">\1</div>', $sStr);
    $sStr = preg_replace('#\[abbr=(.*)\](.*)\[/abbr\]#Uis', '<abbr title="\1">\2</abbr>', $sStr);
    $sStr = preg_replace('#\[acronym=(.*)\](.*)\[/acronym\]#Uis', '<acronym title="\1">\2</acronym>', $sStr);
    $sStr = preg_replace('#\[color=(.*)\](.*)\[/color\]#Uis', '<span style="color:\1">\2</span>', $sStr);
    $sStr = preg_replace('#\[size=(.*)\](.*)\[/size\]#Uis', '<span style="font-size:\1%">\2</span>', $sStr);
    $sStr = preg_replace('#\[site=(.*)\](.*)\[/site\]#Uis', '<a href="\1">\2</a>', $sStr);
    $sStr = preg_replace('#\[url=(.*)\](.*)\[/url\]#Uis',
            '<img src="%PATH_IMAGES%/spacer.gif" class="icon-redirect" alt="" /> <a href="\1" target="_blank">\2</a>',
            $sStr);
    $sStr = preg_replace('#\[anchor:(.*)\]#Uis', '<a name="\1"></a>', $sStr);
    $sStr = preg_replace('#\[icon:(.*)\]#Uis', '<img src="%PATH_IMAGES%/spacer.gif" class="icon-\1" alt="\1" />', $sStr);

    # replace the image tag
    while(preg_match('=\[img\](.*)\[/img\]=isU', $sStr, $sUrl)) {
      if(@getimagesize($sUrl[1]) == false)
        $sHTML = '';
      else {
        $aInfo = @getimagesize($sUrl[1]);
        if($aInfo[0] <= MEDIA_DEFAULT_X)
          $sHTML = '<img class=\'image\' src="'	.$sUrl[1].	'" width="'	.$aInfo[0].	'" height="'	.$aInfo[1].	'" alt="'	.$sUrl[1].	'" />';
        else // Resize
        {
          $iFactor = 575 / $aInfo[0];
          $aInfo[0] = $aInfo[0] * $iFactor;
          $aInfo[1] = $aInfo[1] * $iFactor;
          $sHTML = '<a href="'	.$sUrl[1].	'" rel=\'lightbox\'><img class=\'image\' src="'	.$sUrl[1].	'" width="'	.$aInfo[0].	'" height="'	.$aInfo[1].	'" alt=\'\' /></a>';
        }
      }

      $sStr = preg_replace('=\[img\](.*)\[/img\]=isU', $sHTML, $sStr, 1);
      unset($sHTML, $aInfo, $sUrl);
    }

    # Image with description
    $sStr = preg_replace(	"/\[img\=(.+)\](.*)\[\/img]/isU",
            "<div style='text-align:center;font-style:italic'><img class='image' src='\\2' alt='\\1' title='\\1' />\n\\1</div>",
            $sStr);

    # Include Flash Player
    $iRand = rand(10000, 99999);
    $sHTML = '<div class=\'media_player center\' id=\'media_player'	.$iRand.	'\'>';
    $sHTML .= '<a href=\'http://www.macromedia.com/go/getflashplayer\'>'	.LANG_ERROR_HELPER_NO_FLASH_INSTALLED.	'</a>';
    $sHTML .= '</div>';

    $sStr = preg_replace(	'#\[media ([0-9]+) ([0-9]+)\](.*)\[/media\]#Uis',
            $sHTML.	'<script type="text/javascript">new Swiff("%PATH_PUBLIC%/flv/mediaplayer.swf",{id: "'  .$iRand.  '",width: \1,height: \2,container:"media_player'  .$iRand.  '",params:{allowfullscreen: "true"},vars:{file:"\3",config:"%PATH_PUBLIC%/flv/config.xml"}});</script>',
            $sStr);

    $sStr = preg_replace(	'#\[media ([0-9]+) ([0-9]+) (.*)\](.*)\[/media\]#Uis',
            $sHTML.	'<script type="text/javascript">new Swiff("%PATH_PUBLIC%/flv/mediaplayer.swf", {id: "'  .$iRand.  '",width: \1,height: \2,container:"media_player'  .$iRand.  '",params:{allowfullscreen: "true"},vars:{file:"\4",config:"%PATH_PUBLIC%/flv/config.xml",image:"\3"}});</script>',
            $sStr);

    $sStr = preg_replace(	'#\[media\](.*)\[/media\]#Uis',
            $sHTML.	'<script type="text/javascript">new Swiff("%PATH_PUBLIC%/flv/mediaplayer.swf", {id: "'  .$iRand.  '",width: ' .MEDIA_DEFAULT_X.  ',height: '  .MEDIA_DEFAULT_Y.  ',container:"media_player'  .$iRand.  '",params:{allowfullscreen: "true"},vars:{file:"\1",config:"%PATH_PUBLIC%/flv/config.xml"}});</script>',
            $sStr);

    /* Quote */
    while(	preg_match("/\[quote\]/isU", $sStr) && preg_match("/\[\/quote]/isU",$sStr) ||
            preg_match("/\[quote\=/isU", $sStr) && preg_match("/\[\/quote]/isU", $sStr)) {
      $sStr = preg_replace("/\[quote\](.*)\[\/quote]/isU", "<div class='quote'>\\1</div>", $sStr);
      $sStr = preg_replace("/\[quote\=(.+)\](.*)\[\/quote]/isU", "<div class='quote'><h3>"	.LANG_GLOBAL_QUOTE_BY.	" \\1</h3>\\2</div>", $sStr);
    }

    while(  preg_match("/\[toggle\=/isU", $sStr) && preg_match("/\[\/toggle]/isU", $sStr)) {
      $iRand = rand(10000, 99999);
      $sStr = preg_replace("/\[toggle\=(.+)\](.*)\[\/toggle]/isU", "<a href='#' onclick=\"showDiv('toggle" .$iRand.  "')\"><img src='%PATH_IMAGES%/spacer.gif' class='icon-toggle_max' alt='' /> \\1</a><div id=\"toggle" .$iRand.  "\" style='display:none'>\\2</div>", $sStr);
    }

    /* Add a paragraph to create similar BB-Code for TinyMCE */
    if( $bUseParagraph == true ) {
      if( substr($sStr, 0, 3) !== '<p>' )
        $sStr = '<p>'	.$sStr.	'</p>';
    }

    #$sStr = nl2br($sStr);
    return $sStr;
  }

  public final function templateDir($sTemplate) {
    if(!file_exists('app/view/'	.$sTemplate.	'.tpl'))
      throw new AdvancedException(LANG_ERROR_GLOBAL_NO_TEMPLATE);

    try {
      if( @file_exists(PATH_TPL_ADDON.	'/'	.$sTemplate.	'.tpl') )
        return PATH_TPL_ADDON;
      else
        return 'app/view/';
    }
    catch (Exception $e ) {
      die($e->getMessage());
      # TODO: MAIL TO ADMIN
    }
  }

  public static final function checkEmailAddress($sMail) {
    if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $sMail))
      return true;
    else
      return false;
  }
}