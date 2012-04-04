<?php /* Smarty version Smarty-3.1.8, created on 2012-04-04 14:29:02
         compiled from "/Users/marcoraddatz/Sites/phpcms/vendor/candyCMS/views/searches/_form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15293765964f7c3e8e5dd9a9-27654272%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1a5a0ade2e5a70e70872750d14fa954fb39ef067' => 
    array (
      0 => '/Users/marcoraddatz/Sites/phpcms/vendor/candyCMS/views/searches/_form.tpl',
      1 => 1333444536,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15293765964f7c3e8e5dd9a9-27654272',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MOBILE' => 0,
    'lang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_4f7c3e8e6a50f7_96865897',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4f7c3e8e6a50f7_96865897')) {function content_4f7c3e8e6a50f7_96865897($_smarty_tpl) {?><form method='post' class='form-horizontal'><?php if (!$_smarty_tpl->tpl_vars['MOBILE']->value){?><div class='page-header'><h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['global']['search'];?>
</h1></div><?php }?><div class='control-group'><label for='input-search' class='control-label'><?php echo $_smarty_tpl->tpl_vars['lang']->value['searches']['label']['terms'];?>
 <span title='<?php echo $_smarty_tpl->tpl_vars['lang']->value['global']['required'];?>
'>*</span></label><div class='controls'><input type='search' class='span4 focused' name='search'id='input-search' autofocus required /><input type='submit' name='submit' class='btn btn-primary'value='<?php echo $_smarty_tpl->tpl_vars['lang']->value['global']['search'];?>
' data-theme='b' /></div></div></form><?php }} ?>