<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# This plugin is the most powerful plugin, if you don't want to write every
# text in HTML. It also enables users that are not allowed to post HTML to
# format their text.
# A detailed documentation of how to use the tags can be found at
# http://github.com/marcoraddatz/candyCMS/wiki/BBCode

require_once 'app/helpers/Image.helper.php';

final class Bbcode {

  private final function _setFormatedText($sStr) {

    # BBCode
    $sStr = str_replace('[hr]', '<hr />', $sStr);
    $sStr = preg_replace('/\[center\](.*)\[\/center]/isU', '<div style=\'text-align:center\'>\1</div>', $sStr);
    $sStr = preg_replace('/\[left\](.*)\[\/left]/isU', '<left>\1</left>', $sStr);
    $sStr = preg_replace('/\[right\](.*)\[\/right]/isU', '<right>\1</right>', $sStr);
    $sStr = preg_replace('/\[p\](.*)\[\/p]/isU', '<p>\1</p>', $sStr);
    $sStr = preg_replace('=\[b\](.*)\[\/b\]=Uis', '<strong>\1</strong>', $sStr);
    $sStr = preg_replace('=\[i\](.*)\[\/i\]=Uis', '<em>\1</em>', $sStr);
    $sStr = preg_replace('=\[u\](.*)\[\/u\]=Uis', '<span style="text-decoration:underline">\1</span>', $sStr);
    $sStr = preg_replace('=\[del\](.*)\[\/del\]=Uis', '<span style="text-decoration:line-through">\1</span>', $sStr);
    $sStr = preg_replace('=\[code](.*)\[\/code]=Uis', '<code>\1</code>', $sStr);
    $sStr = preg_replace('#\[abbr=(.*)\](.*)\[\/abbr\]#Uis', '<abbr title="\1">\2</abbr>', $sStr);
    $sStr = preg_replace('#\[acronym=(.*)\](.*)\[\/acronym\]#Uis', '<acronym title="\1">\2</acronym>', $sStr);
    $sStr = preg_replace('#\[color=(.*)\](.*)\[\/color\]#Uis', '<span style="color:\1">\2</span>', $sStr);
    $sStr = preg_replace('#\[size=(.*)\](.*)\[\/size\]#Uis', '<span style="font-size:\1%">\2</span>', $sStr);
    $sStr = preg_replace('#\[anchor:(.*)\]#Uis', '<a name="\1"></a>', $sStr);

    # Load specific icon
    $sStr = preg_replace('#\[icon:(.*)\]#Uis',
            '<img src="%PATH_IMAGES%/spacer.png" class="icon-\1" />',
            $sStr);

    # Insert uploaded image
    $sStr = preg_replace('#\[img:(.*)\]#Uis',
            '<img src="%PATH_IMAGES%/\1" alt="\1" style="vertical-align:baseline" />',
            $sStr);

    # Replace images with image tag (every location allowed, but external is very slow)
    while (preg_match('=\[img\](.*)\[\/img\]=isU', $sStr, $sUrl)) {
      $sImageExtension = strtolower(substr(strrchr($sUrl[1], '.'), 1));
      $sTempFileName = md5(MEDIA_DEFAULT_X . $sUrl[1]);
      $sTempFilePath = PATH_UPLOAD . '/temp/bbcode/' . $sTempFileName . '.' . $sImageExtension;

      $aInfo = @getimagesize($sUrl[1]);

      # Image is small and on our website, so we don't need a preview
      if ($aInfo[0] <= MEDIA_DEFAULT_X)
        $sHTML = '<div class=\'image\'><img src="' . $sUrl[1] . '" width="' . $aInfo[0] . '" height="' . $aInfo[1] . '" alt="' . $sUrl[1] . '" /></div>';

      # We do not have a preview, the image is local an biiig
      else {

        if (!file_exists($sTempFilePath)) {
          $oImage = new Image($sTempFileName, 'temp', $sUrl[1], $sImageExtension);
          $oImage->resizeDefault(MEDIA_DEFAULT_X, '', 'bbcode');
        }

        $sTempFilePath = WEBSITE_URL . '/' . $sTempFilePath;
        $aNewInfo = getimagesize($sTempFilePath);

        # Language
        $sText = str_replace('%w', $aInfo[0], LANG_GLOBAL_IMAGE_CLICK_TO_ENLARGE);
        $sText = str_replace('%h', $aInfo[1], $sText);

        $sHTML = '<div class="image">';
        $sHTML .= '<a class="js-fancybox" rel="images" href="' . $sUrl[1] . '">';
        $sHTML .= '<img class="js-image" alt="' . $sText . '" src="' . $sTempFilePath . '" width="' . $aNewInfo[0] . '" height="' . $aNewInfo[1] . '" />';
        $sHTML .= '</a>';
        $sHTML .= '</div>';
      }

      $sStr = preg_replace('=\[img\](.*)\[\/img\]=isU', $sHTML, $sStr, 1);
      unset($sHTML, $aInfo, $sUrl);
    }

    # Image with description
    $sStr = preg_replace("/\[img\=(.+)\](.*)\[\/img]/isU",
            "<div class='image' style='font-style:italic'><img src='\\2' alt='\\1' title='\\1' /><br />\\1</div>",
            $sStr);

		# [audio]file[/audio]
		if (preg_match('#\[audio\](.*)\[\/audio\]#Uis', $sStr)) {
      preg_match_all('#\[audio\](.*)\[\/audio\]#Uis', $sStr, $aOutput);

      # Get file name without extension
      # TODO: Put into method
      $sFile = trim($aOutput[1][0]);
      $iExtensionLength = strlen($sFile) - strlen(substr(strrchr($sFile, '.'), 0));

      $sFile = substr($sFile, 0, $iExtensionLength);

      # HTML 5 Audio
      $sAudio = '<audio controls="controls">';
      $sAudio .= '<source src="' . $sFile . '.ogg" type="audio/ogg" />';
      $sAudio .= '<source src="' . $sFile . '.mp3" type="audio/mp3" />';
      $sAudio .= '<div class="error">'  .  str_replace('%u', trim($aOutput[1][0]), LANG_ERROR_PLUGIN_BBCODE_AUDIO).  '</div>';
      $sAudio .= '</audio>';

      $sStr = preg_replace('#\[audio\](.*)\[\/audio\]#Uis', '<div class="audio">' . $sAudio . '</div>', $sStr);
    }
    
    # [video]file[/video]
    if (preg_match('#\[video\](.*)\[\/video\]#Uis', $sStr)) {
      preg_match_all('#\[video\](.*)\[\/video\]#Uis', $sStr, $aOutput);

      # Get file name without extension
      $sFile = trim($aOutput[1][0]);
      $iExtensionLength = strlen($sFile) - strlen(substr(strrchr($sFile, '.'), 0));
      $sFile = substr($sFile, 0, $iExtensionLength);

      # HTML 5 Video
      $sVideo = '<video width="' . MEDIA_DEFAULT_X . '" height="' . MEDIA_DEFAULT_Y . '" controls="controls" preload="none">';
			$sVideo .= '<source src="' . $sFile . '.mp4" type="video/mp4" />';
			$sVideo .= '<source src="' . $sFile . '.webm" type="video/webm" />';
			$sVideo .= '<source src="' . $sFile . '.ogv" type="video/ogg" />';
			$sVideo .= '<object width="' . MEDIA_DEFAULT_X . '" height="' . MEDIA_DEFAULT_Y . '" type="application/x-shockwave-flash" data="%PATH_PUBLIC%/lib/mediaelement/flashmediaelement.swf">';
			$sVideo .= '<param name="movie" value="%PATH_PUBLIC%/lib/mediaelement/flashmediaelement.swf" />';
			$sVideo .= '<param name="flashvars" value="file=' . $sFile . '.mp4&amp;controls=true" />';
			$sVideo .= '</object>';
			$sVideo .= '</video>';

			$sStr = preg_replace('#\[video\](.*)\[\/video\]#Uis', '<div class="video video-js-box">' . $sVideo . '</div>', $sStr);
		}

    # [video thumbnail]file[/video]
    if (preg_match('#\[video (.*)\](.*)\[\/video]#Uis', $sStr)) {
      preg_match_all('#\[video (.*)\](.*)\[\/video]#Uis', $sStr, $aOutput);

      # Get file name without extension
      $sFile = trim($aOutput[2][0]);
			$iExtensionLength = strlen($sFile) - strlen(substr(strrchr($sFile, '.'), 0));
			$sFile = substr($sFile, 0, $iExtensionLength);

			$sVideo = '<video width="' . MEDIA_DEFAULT_X . '" height="' . MEDIA_DEFAULT_Y . '" controls="controls" poster="\1" preload="none">';
			$sVideo .= '<source src="' . $sFile . '.mp4" type="video/mp4" />';
			$sVideo .= '<source src="' . $sFile . '.webm" type="video/webm" />';
			$sVideo .= '<source src="' . $sFile . '.ogv" type="video/ogg" />';
			$sVideo .= '<object width="' . MEDIA_DEFAULT_X . '" height="' . MEDIA_DEFAULT_Y . '" type="application/x-shockwave-flash" data="%PATH_PUBLIC%/lib/mediaelement/flashmediaelement.swf">';
			$sVideo .= '<param name="movie" value="%PATH_PUBLIC%/lib/mediaelement/flashmediaelement.swf" />';
			$sVideo .= '<param name="flashvars" value="file=' . $sFile . '.mp4&amp;poster=\1&amp;controls=true" />';
			$sVideo .= '<img src="\1" width="' . MEDIA_DEFAULT_X . '" height="' . MEDIA_DEFAULT_Y . '" alt="" title="" />';
			$sVideo .= '</object>';
			$sVideo .= '</video>';

			$sStr = preg_replace('#\[video (.*)\](.*)\[\/video]#Uis', '<div class="video video-js-box">' . $sVideo . '</div>', $sStr);
    }

    # [video width height thumbnail]file[/video]
    if (preg_match('#\[video ([0-9]+) ([0-9]+) (.*)\](.*)\[\/video\]#Uis', $sStr)) {
      preg_match_all('#\[video ([0-9]+) ([0-9]+) (.*)\](.*)\[\/video\]#Uis', $sStr, $aOutput);

      # Get file name without extension
      $sFile = trim($aOutput[4][0]);
      $iExtensionLength = strlen($sFile) - strlen(substr(strrchr($sFile, '.'), 0));
      $sFile = substr($sFile, 0, $iExtensionLength);

      $sVideo = '<video width="\1" height="\2" controls="controls" poster="\3" preload="none">';
			$sVideo .= '<source src="' . $sFile . '.mp4" type="video/mp4" />';
			$sVideo .= '<source src="' . $sFile . '.webm" type="video/webm" />';
			$sVideo .= '<source src="' . $sFile . '.ogv" type="video/ogg" />';
			$sVideo .= '<object width="\1" height="\2" type="application/x-shockwave-flash" data="%PATH_PUBLIC%/lib/mediaelement/flashmediaelement.swf">';
			$sVideo .= '<param name="movie" value="%PATH_PUBLIC%/lib/mediaelement/flashmediaelement.swf" />';
			$sVideo .= '<param name="flashvars" value="file=' . $sFile . '.mp4&amp;poster=\3" />';
			$sVideo .= '<img src="\3" width="\1" height="\2" alt="" title="" />';
			$sVideo .= '</object>';
			$sVideo .= '</video>';

			$sStr = preg_replace('#\[video ([0-9]+) ([0-9]+) (.*)\](.*)\[\/video\]#Uis', '<div class="video video-js-box">' . $sVideo . '</div>', $sStr);
    }

    # Quote
    while (preg_match("/\[quote\]/isU", $sStr) && preg_match("/\[\/quote]/isU", $sStr) ||
    preg_match("/\[quote\=/isU", $sStr) && preg_match("/\[\/quote]/isU", $sStr)) {
      $sStr = preg_replace("/\[quote\](.*)\[\/quote]/isU", "<blockquote>\\1</blockquote>", $sStr);
      $sStr = preg_replace("/\[quote\=(.+)\](.*)\[\/quote]/isU", 
              "<blockquote><h3>" . LANG_GLOBAL_QUOTE_BY . " \\1</h3>\\2</blockquote>", 
              $sStr);
    }

    while (preg_match("/\[toggle\=/isU", $sStr) && preg_match("/\[\/toggle]/isU", $sStr)) {
      $sStr = preg_replace("/\[toggle\=(.+)\](.*)\[\/toggle]/isU",
              "<span class='js-toggle-headline'><img src='%PATH_IMAGES%/spacer.png' class='icon-toggle_max' alt='' /> \\1</span><div class=\"js-toggle-element\">\\2</div>", 
              $sStr);
    }


    # Fix quote bug and allow these tags only
    $sStr = str_replace("&lt;blockquote&gt;", "<blockquote>", $sStr);
    $sStr = str_replace("&lt;/blockquote&gt;", "</blockquote>", $sStr);
    $sStr = str_replace("&lt;h3&gt;", "<h3>", $sStr);
    $sStr = str_replace("&lt;/h3&gt;", "</h3>", $sStr);

    return $sStr;
  }

  public final function getFormatedText($sStr) {
    return $this->_setFormatedText($sStr);
  }
}