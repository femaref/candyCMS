<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

class Bbcode {
  private final function _setFormatedText($sStr, $bUseParagraph) {

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

    return $sStr;
  }

  public final function getFormatedText($sStr, $bUseParagraph) {
    return $this->_setFormatedText($sStr, $bUseParagraph);
  }
}