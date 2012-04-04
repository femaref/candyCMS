<?php /* Smarty version Smarty-3.1.8, created on 2012-04-04 14:29:02
         compiled from "/Users/marcoraddatz/Sites/phpcms/vendor/candyCMS/views/layouts/application.mob" */ ?>
<?php /*%%SmartyHeaderCode:14582754524f7c3e8e6ba069-17232050%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f4d65feaeeb2cb6858c044e3309dae4320ab7854' => 
    array (
      0 => '/Users/marcoraddatz/Sites/phpcms/vendor/candyCMS/views/layouts/application.mob',
      1 => 1333533493,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14582754524f7c3e8e6ba069-17232050',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    '_PATH' => 0,
    '_SYSTEM' => 0,
    '_title_' => 0,
    '_SESSION' => 0,
    'lang' => 0,
    '_REQUEST' => 0,
    '_flash_type_' => 0,
    '_flash_headline_' => 0,
    '_flash_message_' => 0,
    '_content_' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4f7c3e8e7f3dc4_60717801',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4f7c3e8e7f3dc4_60717801')) {function content_4f7c3e8e7f3dc4_60717801($_smarty_tpl) {?><!DOCTYPE html><html><head><meta name='viewport' content='width=device-width, initial-scale=1' /><meta http-equiv='content-type' content='text/html;charset=utf-8'/><link href='<?php echo $_smarty_tpl->tpl_vars['_PATH']->value['css'];?>
/mobile/jquery.mobile<?php echo $_smarty_tpl->tpl_vars['_SYSTEM']->value['compress_files_suffix'];?>
.css' rel='stylesheet' type='text/css' media='screen, projection'/><link href='<?php echo $_smarty_tpl->tpl_vars['_PATH']->value['css'];?>
/mobile/essential<?php echo $_smarty_tpl->tpl_vars['_SYSTEM']->value['compress_files_suffix'];?>
.css' rel='stylesheet' type='text/css' media='screen, projection'/><link href='<?php echo $_smarty_tpl->tpl_vars['_PATH']->value['css'];?>
/mobile/application<?php echo $_smarty_tpl->tpl_vars['_SYSTEM']->value['compress_files_suffix'];?>
.css' rel='stylesheet' type='text/css' media='screen, projection'/><script type='text/javascript' src='http://code.jquery.com/jquery-1.7.1<?php echo $_smarty_tpl->tpl_vars['_SYSTEM']->value['compress_files_suffix'];?>
.js'></script><script type="text/javascript">if (typeof jQuery == 'undefined')document.write(unescape("%3Cscript src='<?php echo $_smarty_tpl->tpl_vars['_PATH']->value['js'];?>
/core/jquery.1.7.1<?php echo $_smarty_tpl->tpl_vars['_SYSTEM']->value['compress_files_suffix'];?>
.js' type='text/javascript'%3E%3C/script%3E"));</script><script type="text/javascript" src="http://code.jquery.com/mobile/1.1.0-rc.1/jquery.mobile-1.1.0-rc.1<?php echo $_smarty_tpl->tpl_vars['_SYSTEM']->value['compress_files_suffix'];?>
.js"></script><script type="text/javascript">if (typeof jQuery == 'undefined')document.write(unescape("%3Cscript src='<?php echo $_smarty_tpl->tpl_vars['_PATH']->value['js'];?>
/mobile/jquery.mobile.1.1.0-rc1<?php echo $_smarty_tpl->tpl_vars['_SYSTEM']->value['compress_files_suffix'];?>
.js' type='text/javascript'%3E%3C/script%3E"));</script><title><?php echo $_smarty_tpl->tpl_vars['_title_']->value;?>
</title></head><body><div data-role='page' data-theme='d' data-add-back-btn='true'><div data-role='header'><h1><?php echo $_smarty_tpl->tpl_vars['_title_']->value;?>
</h1><?php if ($_smarty_tpl->tpl_vars['_SESSION']->value['user']['role']==0){?><a href='/sessions/create' class='ui-btn-right'><?php echo $_smarty_tpl->tpl_vars['lang']->value['global']['login'];?>
</a><?php }else{ ?><a href='/sessions/destroy' class='ui-btn-right'><?php echo $_smarty_tpl->tpl_vars['lang']->value['global']['logout'];?>
</a><?php }?></div><!-- /header --><div data-role='navbar'><ul><li><a href='/blogs' <?php if ($_smarty_tpl->tpl_vars['_REQUEST']->value['controller']=='blogs'){?>class='ui-btn-active'<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['global']['blog'];?>
</a></li><li><a href='/galleries' <?php if ($_smarty_tpl->tpl_vars['_REQUEST']->value['controller']=='galleries'){?>class='ui-btn-active'<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['global']['gallery'];?>
</a></li><li><a href='/calendars' <?php if ($_smarty_tpl->tpl_vars['_REQUEST']->value['controller']=='calendars'){?>class='ui-btn-active'<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['global']['calendar'];?>
</a></li><li><a href='/downloads' <?php if ($_smarty_tpl->tpl_vars['_REQUEST']->value['controller']=='downloads'){?>class='ui-btn-active'<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['global']['download'];?>
</a></li><li><a href='/searches' <?php if ($_smarty_tpl->tpl_vars['_REQUEST']->value['controller']=='searches'){?>class='ui-btn-active' data-ajax='false'<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['global']['search'];?>
</a></li></ul></div><!-- /navbar --><div data-role='content'><section id='<?php echo $_smarty_tpl->tpl_vars['_REQUEST']->value['controller'];?>
'><?php if ($_smarty_tpl->tpl_vars['_flash_type_']->value){?><div id='js-flash_message'><div class='<?php echo $_smarty_tpl->tpl_vars['_flash_type_']->value;?>
' id='js-flash_<?php echo $_smarty_tpl->tpl_vars['_flash_type_']->value;?>
'><h4><?php echo $_smarty_tpl->tpl_vars['_flash_headline_']->value;?>
</h4><p><?php echo $_smarty_tpl->tpl_vars['_flash_message_']->value;?>
</p></div></div><?php }?><?php echo $_smarty_tpl->tpl_vars['_content_']->value;?>
</section></div><!-- /content --><div data-role="footer" data-position='fixed' class="ui-bar"><div data-role="controlgroup" data-type="horizontal"><a id='top' data-role="button" class="ui-btn-right" data-icon="arrow-u">Top</a><a href='?mobile=0' data-ajax="false"><?php echo $_smarty_tpl->tpl_vars['lang']->value['global']['view']['web'];?>
</a></div></div><!-- /footer --></div><!-- /page --><script type='text/javascript'>$('#top').live('click', function(e){$('body').clearQueue();$.mobile.silentScroll(0);});$('img').each(function() {var iNewWidth = screen.width - 30;var iNewHeight =  iNewWidth / $(this).width() * $(this).height();if($(this).width() > iNewWidth) {$(this).attr('width', iNewWidth);$(this).attr('height', iNewHeight);}});</script><script type='text/javascript' src='<?php echo $_smarty_tpl->tpl_vars['_PATH']->value['js'];?>
/core/scripts<?php echo $_smarty_tpl->tpl_vars['_SYSTEM']->value['compress_files_suffix'];?>
.js'></script><!-- plugin:analytics --><!-- plugin:piwik --></body></html><?php }} ?>