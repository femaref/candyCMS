<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class LazyLoad {

	public function show() {
		$oSmarty = new Smarty();

		# System variables
		$oSmarty->assign('_compress_files_suffix_', WEBSITE_COMPRESS_FILES == true ? '-min' : '');
		$oSmarty->assign('_thumb_default_x_', THUMB_DEFAULT_X);

		$oSmarty->cache_dir = CACHE_DIR;
		$oSmarty->compile_dir = COMPILE_DIR;
		$oSmarty->template_dir = 'public/skins/_plugins';
		return $oSmarty->fetch('lazyload.tpl');
	}
}