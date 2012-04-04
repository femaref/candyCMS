<?php

/**
 * Handle BB code.
 *
 * This plugin is the most powerful plugin, if you don't want to write every
 * text in HTML. It also enables users that are not allowed to post HTML to
 * format their text.
 *
 * A detailed documentation of how to use the tags can be found at
 * http://github.com/marcoraddatz/candyCMS/wiki/BBCode
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 * @see https://github.com/marcoraddatz/candyCMS/wiki/BBCode
 *
 */

namespace CandyCMS\Plugins;

use CandyCMS\Core\Helpers\I18n;
use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\Image;

final class Bbcode {

  /**
   * Search and replace BB code.
   *
   * @static
   * @access public
   * @param string $sStr HTML to replace
   * @return string $sStr HTML with formated code
   *
   */
  private final static function _setFormatedText($sStr) {

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
    $sStr = preg_replace('=\[code](.*)\[\/code]=Uis', '<pre>\1</pre>', $sStr);
    $sStr = preg_replace('#\[abbr=(.*)\](.*)\[\/abbr\]#Uis', '<abbr title="\1">\2</abbr>', $sStr);
    $sStr = preg_replace('#\[acronym=(.*)\](.*)\[\/acronym\]#Uis', '<acronym title="\1">\2</acronym>', $sStr);
    $sStr = preg_replace('#\[color=(.*)\](.*)\[\/color\]#Uis', '<span style="color:\1">\2</span>', $sStr);
    $sStr = preg_replace('#\[size=(.*)\](.*)\[\/size\]#Uis', '<span style="font-size:\1%">\2</span>', $sStr);
    $sStr = preg_replace('#\[anchor:(.*)\]#Uis', '<a name="\1"></a>', $sStr);

    # Load specific icon
    $sStr = preg_replace('#\[icon:(.*)\]#Uis', '<img src="{$_PATH.images}/candy.global/spacer.png" class="icon-\1" />', $sStr);

    # Insert uploaded image
    $sStr = preg_replace('#\[img:(.*)\]#Uis', '<img src="{$_PATH.images}/\1" alt="\1" style="vertical-align:baseline" />', $sStr);

    # Replace images with image tag (every location allowed, but external is verrry slow)
    while (preg_match('=\[img\](.*)\[\/img\]=isU', $sStr, $sUrl)) {
			$sUrl[1] = Helper::removeSlash($sUrl[1]);
      $sImageExtension = strtolower(substr(strrchr($sUrl[1], '.'), 1));
      $sTempFileName = md5(MEDIA_DEFAULT_X . $sUrl[1]);
      $sTempFilePath = Helper::removeSlash(PATH_UPLOAD . '/temp/bbcode/' . $sTempFileName . '.' . $sImageExtension);

      $aInfo = @getimagesize($sUrl[1]);

      # Image is small and on our website, so we don't need a preview
      if ($aInfo[0] <= MEDIA_DEFAULT_X) {
        $sUrl[1] = substr($sUrl[1], 0, 7) !== 'http://' ? '/' . $sUrl[1] : $sUrl[1];
        $sHTML  = '<div class=\'image\' rel="images">';
        $sHTML .= '<img src="' . $sUrl[1] . '" width="' . $aInfo[0] . '" height="' . $aInfo[1] . '" alt="' . $sUrl[1] . '" />';
        $sHTML .= '</div>';
      }

      # We do not have a preview
      else {
        require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/Image.helper.php';

        if (!file_exists($sTempFilePath)) {
          $oImage = new Image($sTempFileName, 'temp', $sUrl[1], $sImageExtension);
          $oImage->resizeDefault(MEDIA_DEFAULT_X, '', 'bbcode');
        }

        $aNewInfo = @getimagesize($sTempFilePath);

        # Language
        $sText = I18n::get('global.image.click_to_enlarge', $aInfo[0], $aInfo[1]);

        # we have to make sure, that this absolute URL won't begin with a slash
        $sUrl[1] = substr($sUrl[1], 0, 7) !== 'http://' ? '/' . $sUrl[1] : $sUrl[1];
        $sTempFilePath = Helper::addSlash($sTempFilePath);

        $sHTML = '<figure class="image">';
        $sHTML .= '<a class="js-fancybox" rel="images" href="' . $sUrl[1] . '">';
        $sHTML .= '<img class="js-image" alt="' . $sText . '" src="' . $sTempFilePath . '" width="' . $aNewInfo[0] . '" height="' . $aNewInfo[1] . '" />';
        $sHTML .= '</a>';
        $sHTML .= '</figure>';
      }

      $sStr = preg_replace('=\[img\](.*)\[\/img\]=isU', $sHTML, $sStr, 1);
    }

    # using [audio]file.ext[/audio]
    while (preg_match('#\[audio\](.*)\[\/audio\]#Uis', $sStr, $aMatch)) {
      $sUrl = 'http://url2vid.com/?url=' . $aMatch[1] . '&w=' . MEDIA_DEFAULT_X . '&h=30&callback=?';
      $sStr = preg_replace('#\[audio\](.*)\[\/audio\]#Uis',
              '<div class="js-media" title="' . $sUrl . '"><a href="' . $sUrl . '">' . $aMatch[1] . '</a></div>',
              $sStr);
    }

    # [video]file[/video]
    while (preg_match('#\[video\](.*)\[\/video\]#Uis', $sStr, $aMatch)) {
      $sUrl   = 'http://url2vid.com/?url=' . $aMatch[1] . '&w=' . MEDIA_DEFAULT_X . '&h=' . MEDIA_DEFAULT_Y . '&callback=?';
      $sStr = preg_replace('#\[video\](.*)\[\/video\]#Uis',
              '<a href="' . $aMatch[1] . '" class="js-media" title="' . $sUrl . '">' . $aMatch[1] . '</a>',
              $sStr,
              1);
    }

    # [video thumbnail]file[/video]
    while (preg_match('#\[video (.*)\](.*)\[\/video]#Uis', $sStr, $aMatch)) {
      $sUrl = 'http://url2vid.com/?url=' . $aMatch[2] . '&w=' . MEDIA_DEFAULT_X . '&h=' . MEDIA_DEFAULT_Y . '&p=' . $aMatch[1] . '&callback=?';
      $sStr = preg_replace('#\[video (.*)\](.*)\[\/video]#Uis',
              '<div class="js-media" title="' . $sUrl . '"><a href="' . $aMatch[2] . '">' . $aMatch[2] . '</a></div>',
              $sStr,
              1);
    }

    # [video width height thumbnail]file[/video]
    while (preg_match('#\[video ([0-9]+) ([0-9]+) (.*)\](.*)\[\/video\]#Uis', $sStr, $aMatch)) {
      $sUrl = 'http://url2vid.com/?url=' . $aMatch[4] . '&w=' . $aMatch[1] . '&h=' . $aMatch[2] . '&p=' . $aMatch[3] . '&callback=?';
      $sStr = preg_replace('#\[video ([0-9]+) ([0-9]+) (.*)\](.*)\[\/video\]#Uis',
              '<div class="js-media" title="' . $sUrl . '"><a href="' . $aMatch[4] . '" class="js-media">' . $aMatch[4] . '</a></div>',
              $sStr,
              1);
    }

    # Quote
    while (preg_match("/\[quote\]/isU", $sStr) && preg_match("/\[\/quote]/isU", $sStr) ||
      preg_match("/\[quote\=/isU", $sStr) && preg_match("/\[\/quote]/isU", $sStr)) {
      $sStr = preg_replace("/\[quote\](.*)\[\/quote]/isU", "<blockquote>\\1</blockquote>", $sStr);
      $sStr = preg_replace("/\[quote\=(.+)\](.*)\[\/quote]/isU", "<blockquote><h4>" . I18n::get('global.quote.by') . " \\1</h4>\\2</blockquote>", $sStr);
    }

    while (preg_match("/\[toggle\=/isU", $sStr) && preg_match("/\[\/toggle]/isU", $sStr)) {
      $sStr = preg_replace("/\[toggle\=(.+)\](.*)\[\/toggle]/isU", "<span class='js-toggle-headline'><img src='%PATH_IMAGES%/candy.global/spacer.png' class='icon-toggle_max' alt='' /> \\1</span><div class=\"js-toggle-element\">\\2</div>", $sStr);
    }

    # Fix quote and allow these tags
    $sStr = str_replace("&lt;blockquote&gt;", "<blockquote>", $sStr);
    $sStr = str_replace("&lt;/blockquote&gt;", "</blockquote>", $sStr);
    $sStr = str_replace("&lt;h4&gt;", "<h4>", $sStr);
    $sStr = str_replace("&lt;/h4&gt;", "</h4>", $sStr);

    return $sStr;
  }

  /**
   * Return the formatted code.
   *
   * @static
   * @access public
   * @param string $sStr
   * @return string HTML with formated code
   *
   */
  public final function getFormatedText($sStr) {
    return self::_setFormatedText($sStr);
  }
}