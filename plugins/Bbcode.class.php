<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/helpers/Image.helper.php';

class Bbcode {
  private final function _setFormatedText($sStr, $bUseParagraph) {
    # BB Code
    $sStr = str_replace('[hr]', '<hr />', $sStr);
    $sStr = preg_replace('/\[center\](.*)\[\/center]/isU', '<div style=\'text-align:center\'>\1</div>', $sStr);
    $sStr = preg_replace('/\[left\](.*)\[\/left]/isU', '<left>\1</left>', $sStr);
    $sStr = preg_replace('/\[right\](.*)\[\/right]/isU', '<right>\1</right>', $sStr);
    $sStr = preg_replace('/\[p\](.*)\[\/p]/isU', '<p>\1</p>', $sStr);
    $sStr = preg_replace('=\[hl\](.*)\[\/hl\]=Uis', '<h3>\1</h3>', $sStr);
    $sStr = preg_replace('=\[b\](.*)\[\/b\]=Uis', '<strong>\1</strong>', $sStr);
    $sStr = preg_replace('=\[i\](.*)\[\/i\]=Uis', '<em>\1</em>', $sStr);
    $sStr = preg_replace('=\[u\](.*)\[\/u\]=Uis', '<span style="text-decoration:underline">\1</span>', $sStr);
    $sStr = preg_replace('=\[del\](.*)\[\/del\]=Uis', '<span style="text-decoration:line-through">\1</span>', $sStr);
    $sStr = preg_replace('=\[box\](.*)\[\/box\]=Uis', '<div class="box">\1</div>', $sStr);
    $sStr = preg_replace('#\[abbr=(.*)\](.*)\[\/abbr\]#Uis', '<abbr title="\1">\2</abbr>', $sStr);
    $sStr = preg_replace('#\[acronym=(.*)\](.*)\[\/acronym\]#Uis', '<acronym title="\1">\2</acronym>', $sStr);
    $sStr = preg_replace('#\[color=(.*)\](.*)\[\/color\]#Uis', '<span style="color:\1">\2</span>', $sStr);
    $sStr = preg_replace('#\[size=(.*)\](.*)\[\/size\]#Uis', '<span style="font-size:\1%">\2</span>', $sStr);

		# Internal redirect
		$sStr = preg_replace('#\[site=(.*)\](.*)\[\/site\]#Uis', '<a href="\1">\2</a>', $sStr);

		# External redirect (not W3C strict!)
    $sStr = preg_replace('#\[url=(.*)\](.*)\[\/url\]#Uis',
            '<img src="%PATH_IMAGES%/spacer.png" class="icon-redirect" alt="" /> <a href="\1" target="_blank">\2</a>',
            $sStr);

		# Set anchor easily
    $sStr = preg_replace('#\[anchor:(.*)\]#Uis', '<a name="\1"></a>', $sStr);

		# Load specific icon
    $sStr = preg_replace('#\[icon:(.*)\]#Uis', '<img src="%PATH_IMAGES%/spacer.png" class="icon-\1" />', $sStr);

		# Insert uploaded image
    $sStr = preg_replace('#\[img:(.*)\]#Uis', '<img src="%PATH_IMAGES%/\1" alt="\1" style="vertical-align:baseline" />', $sStr);

    # Replace images with image tag (every location allowed)
    while(preg_match('=\[img\](.*)\[\/img\]=isU', $sStr, $sUrl)) {
      if(@getimagesize($sUrl[1]) == false)
        $sHTML = '';

      else {
        $aInfo = @getimagesize($sUrl[1]);

        if($aInfo[0] <= MEDIA_DEFAULT_X)
          $sHTML = '<img class=\'image\' src="'	.$sUrl[1].	'" width="'	.$aInfo[0].	'" height="'	.$aInfo[1].	'" alt="'	.$sUrl[1].	'" />';

        # Resize and save temp image
				else
        {
          if ($aInfo['mime'] == 'image/jpeg')
            $sFileType = 'jpg';

          elseif ($aInfo['mime'] == 'image/png')
            $sFileType = 'png';

          elseif ($aInfo['mime'] == 'image/gif')
            $sFileType = 'gif';

          else
            $sFileType = '';

          $sFileName = md5(MEDIA_DEFAULT_X.$sUrl[1]);
          $sFilePath = PATH_UPLOAD . '/temp/bbcode/' . $sFileName . '.' . $sFileType;

          if (!file_exists($sFilePath) && !empty($sFileType)) {
            $oImage = new Image($sFileName, 'temp', $sUrl[1], $sFileType);
            $oImage->resizeDefault(MEDIA_DEFAULT_X, '', 'bbcode');
          }

          # Override with full path
          $sFilePath = WEBSITE_URL . '/' . $sFilePath;

          $aNewInfo = @getimagesize($sFilePath);

          # Devide margin and padding from essential.css
          $iMarginTop = $aNewInfo[1] - 30;
          $iWidth = $aNewInfo[0] - 10;

          # Language
          $sText = str_replace('%w', $aInfo[0], LANG_GLOBAL_IMAGE_CLICK_TO_ENLARGE);
          $sText = str_replace('%h', $aInfo[1], $sText);

          $sHTML = '<div class="image" style="width:' . $aNewInfo[0] . 'px;height:' . $aNewInfo[1] . 'px">';
          $sHTML .= '<a href="' . $sUrl[1] . '" rel=\'lightbox\'>';
          $sHTML .= '<img src="' . $sFilePath . '" width="' . $aNewInfo[0] . '" height="' . $aNewInfo[1] . '" alt=\'\'';
          $sHTML .= 'onmouseover="fadeInDiv(\'' . $sFileName . '\')"';
          $sHTML .= 'onmouseout="fadeOutDiv(\'' . $sFileName . '\')" />';
          $sHTML .= '</a>';
          $sHTML .= '</div>';
          $sHTML .= '<div id="' . $sFileName . '" class="image_overlay" style="width:' . $iWidth . 'px">';
          $sHTML .= $sText;
          $sHTML .= '</div>';
        }
      }

      $sStr = preg_replace('=\[img\](.*)\[\/img\]=isU', $sHTML, $sStr, 1);
      unset($sHTML, $aInfo, $sUrl);
    }

    # Image with description
    $sStr = preg_replace(	"/\[img\=(.+)\](.*)\[\/img]/isU",
            "<div class='center' style='font-style:italic'><img class='image' src='\\2' alt='\\1' title='\\1' /><br />\\1</div>",
            $sStr);

    # [video]file[/video]
    if(preg_match('#\[video\](.*)\[\/video\]#Uis', $sStr)) {
      preg_match_all('#\[video\](.*)\[\/video\]#Uis', $sStr, $aOutput);

      $sFlash = '<object width="' . MEDIA_DEFAULT_X . '" height="' . MEDIA_DEFAULT_Y . '" type="application/x-shockwave-flash" data="%PATH_PUBLIC%/lib/nonverblaster/NonverBlaster.swf">';
      $sFlash .= '<param name="movie" value="%PATH_PUBLIC%/lib/nonverblaster/NonverBlaster.swf" />';
      $sFlash .= '<param name="FlashVars" value="mediaURL=\1.mp4&amp;controlColor=0xffffff&amp;showTimecode=true" />';
      $sFlash .= '<param name="allowFullScreen" value="true" />';
      $sFlash .= '</object>';

      # HTML 5 Video
      $sVideo = '<video width="' . MEDIA_DEFAULT_X . '" height="' . MEDIA_DEFAULT_Y . '" controls="controls">';
      $sVideo .= '<source src="\1.mp4"  type="video/mp4" />';
      $sVideo .= '<source src="\1.webm"  type="video/webm" />';
      $sVideo .= '<source src="\1.ogv"  type="video/ogg" />';
      $sVideo .= $sFlash;
      $sVideo .= '</video>';

      $sFile  = trim($aOutput[1][0]);
      $sVideo = $this->_getVideo($sFile) ? $sVideo : $sFlash;
      $sStr = preg_replace(	'#\[video\](.*)\[\/video\]#Uis',
              '<div class="video">' . $sVideo . '</div>',
              $sStr);
    }

    # [video width height]file[/video]
    if(preg_match('#\[video ([0-9]+) ([0-9]+)\](.*)\[\/video]#Uis', $sStr)) {
      preg_match_all('#\[video ([0-9]+) ([0-9]+)\](.*)\[\/video]#Uis', $sStr, $aOutput);

      $sFlash = '<object width="\1" height="\2" type="application/x-shockwave-flash" data="%PATH_PUBLIC%/lib/nonverblaster/NonverBlaster.swf">';
      $sFlash .= '<param name="movie" value="%PATH_PUBLIC%/lib/nonverblaster/NonverBlaster.swf" />';
      $sFlash .= '<param name="FlashVars" value="mediaURL=\3.mp4&amp;controlColor=0xffffff&amp;showTimecode=true" />';
      $sFlash .= '<param name="allowFullScreen" value="true" />';
      $sFlash .= '</object>';

      $sVideo = '<video width="\1" height="\2" controls="controls">';
      $sVideo .= '<source src="\3.mp4"  type="video/mp4" />';
      $sVideo .= '<source src="\3.webm"  type="video/webm" />';
      $sVideo .= '<source src="\3.ogv"  type="video/ogg" />';
      $sVideo .= '</video>';

      $sFile  = trim($aOutput[3][0]);
      $sVideo = $this->_getVideo($sFile) ? $sVideo : $sFlash;
      $sStr = preg_replace('#\[video ([0-9]+) ([0-9]+)\](.*)\[\/video]#Uis',
                      '<div class="video">' . $sVideo . '</div>',
                      $sStr);
    }

    # [video width height thumb]file[/video]
    if(preg_match('#\[video ([0-9]+) ([0-9]+) (.*)\](.*)\[\/video\]#Uis', $sStr)) {
      preg_match_all('#\[video ([0-9]+) ([0-9]+) (.*)\](.*)\[\/video\]#Uis', $sStr, $aOutput);

      $sFlash = '<object width="\1" height="\2" type="application/x-shockwave-flash" data="%PATH_PUBLIC%/lib/nonverblaster/NonverBlaster.swf">';
      $sFlash .= '<param name="movie" value="%PATH_PUBLIC%/lib/nonverblaster/NonverBlaster.swf" />';
      $sFlash .= '<param name="FlashVars" value="mediaURL=\4.mp4&amp;teaserURL=\3&amp;controlColor=0xffffff&amp;showTimecode=true" />';
      $sFlash .= '<param name="allowFullScreen" value="true" />';
      $sFlash .= '<img src="\3" width="\1" height="\2" alt="\3" />';
      $sFlash .= '</object>';

      $sVideo = '<video width="\1" height="\2" controls="controls">';
      $sVideo .= '<source src="\4.mp4"  type="video/mp4" />';
      $sVideo .= '<source src="\4.webm"  type="video/webm" />';
      $sVideo .= '<source src="\4.ogv"  type="video/ogg" />';
      $sVideo .= $sFlash;
      $sVideo .= '</video>';

      $sFile  = trim($aOutput[4][0]);
      $sVideo = $this->_getVideo($sFile) ? $sVideo : $sFlash;
      $sStr = preg_replace(	'#\[video ([0-9]+) ([0-9]+) (.*)\](.*)\[\/video\]#Uis',
              '<div class="video">' . $sVideo . '</div>',
              $sStr);
    }

    # Quote
    while(	preg_match("/\[quote\]/isU", $sStr) && preg_match("/\[\/quote]/isU",$sStr) ||
            preg_match("/\[quote\=/isU", $sStr) && preg_match("/\[\/quote]/isU", $sStr)) {
      $sStr = preg_replace("/\[quote\](.*)\[\/quote]/isU", "<div class='quote'>\\1</div>", $sStr);
      $sStr = preg_replace("/\[quote\=(.+)\](.*)\[\/quote]/isU", "<div class='quote'><h3>"	.LANG_GLOBAL_QUOTE_BY.	" \\1</h3>\\2</div>", $sStr);
    }

    while (preg_match("/\[toggle\=/isU", $sStr) && preg_match("/\[\/toggle]/isU", $sStr)) {
      $sStr = preg_replace("/\[toggle\=(.+)\](.*)\[\/toggle]/isU",
                      "<span class='js-toggle'><img src='%PATH_IMAGES%/spacer.png' class='icon-toggle_max' alt='' /> \\1</span><div class=\"js-element\">\\2</div>",
                      $sStr);
    }

    # Add a paragraph to create similar BB-Code for TinyMCE
    if( $bUseParagraph == true ) {
      if( substr($sStr, 0, 3) !== '<p>' )
        $sStr = '<p>'	.$sStr.	'</p>';
    }

    return $sStr;
  }

	public final function getFormatedText($sStr, $bUseParagraph) {
		return $this->_setFormatedText($sStr, $bUseParagraph);
  }

  private final function _getVideo($sFile) {
    # If external link, make local
    if(preg_match('/http:\/\/(.*)/', $sFile))
      $sFile = str_replace(WEBSITE_URL . '/', '', $sFile);

    # We have WebM (and mp4) and do use any browser exept FF
    if ( file_exists($sFile . '.webm') && !preg_match('/Firefox/', $_SERVER['HTTP_USER_AGENT']) )
      return true;

    # We have ogg (and mp4) - serves all
    elseif ( file_exists($sFile . '.ogv') )
      return true;

    # We do only have mp4, so use flash for Firefox and Opera
    elseif( preg_match('/(Firefox|Opera)/', $_SERVER['HTTP_USER_AGENT']) )
      return false;

    # We try HTML5 anyways or it's external
    else
      return true;
  }
}