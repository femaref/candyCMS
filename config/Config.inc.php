<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# ------------------------------------------------------------------------------

# SQL Properties
define('SQL_HOST', 'localhost');
define('SQL_USER', 'root');
define('SQL_PASSWORD', '');
define('SQL_DB', 'cms_new');

# ------------------------------------------------------------------------------

# Do you want to use SMTP to send your mails instead of mail()?
# DEFAULT: '0'
define('SMTP_ON', '0');
define('SMTP_HOST', 'localhost');
define('SMTP_USER', '');
define('SMTP_PASSWORD', '');
define('SMTP_PORT', '1025');

# ------------------------------------------------------------------------------

# Define ABSOLUTE path of website
# EXAMPLE: http://www.google.com
define('WEBSITE_URL', 'http://phpcms.localhost');

# ------------------------------------------------------------------------------

# Define ABSOLUTE path of your cdn. If you don't use a cdn,
# enter your website url.
# EXAMPLE: http://www.google.com
define('WEBSITE_CDN', 'http://phpcms.localhost');

# ------------------------------------------------------------------------------

# Use compressed or non-compressed files. Note that compressed files must be
# updated every time you work on a non-compressed file!
# DEFAULT: 'true'
define('WEBSITE_COMPRESS_FILES', false);
# ------------------------------------------------------------------------------

# Enter full name of Website. This is, where the E-Mails are sent from.
# NOTE: Also edit your website title and slogan in your language file of choice
# in config/language/YOURLANG.php
define('WEBSITE_NAME', 'dev.planetk4.de');

# ------------------------------------------------------------------------------

# Define an email, where user responses for mails and newsletters are going to
# be sent to!
define('WEBSITE_MAIL', 'marco@empuxa.com');

# ------------------------------------------------------------------------------

# Define a noreply email for spam etc.
define('WEBSITE_MAIL_NOREPLY', 'no-reply@dev.planetk4.de');

# ------------------------------------------------------------------------------

# Is the website in development mode?
# DEFAULT: '0'
define('WEBSITE_DEV', '1');

# ------------------------------------------------------------------------------

# If you use plugins (placed in "app/addons/"), turn true
# DEFAULT: '0'
define('ALLOW_ADDONS', '0');

# ------------------------------------------------------------------------------
# Tell the allowed plugins seperated by comma
# DEFAULT: 'Bbcode,FormatTimestamp'
define('ALLOW_PLUGINS', 'Bbcode,FormatTimestamp,Archive');

# ------------------------------------------------------------------------------
# Allow caching and compiling for better performance?
# DEFAULT: '1'
define('ALLOW_CACHE', '1');

# ------------------------------------------------------------------------------

# Allow compressing of SQL Backups
# DEFAULT: '1'
define('ALLOW_GZIP_BACKUP', '1');

# ------------------------------------------------------------------------------

# Allow this software to connect the CandyCMS website to check for an update
# DEFAULT: '1'
define('ALLOW_VERSION_CHECK', '1');

# ------------------------------------------------------------------------------

# Set the standard language (file must be placed in "config/language")
# NOTE: lower cases required!
# DEFAULT: 'ger'
define('DEFAULT_LANGUAGE', 'ger');

# ------------------------------------------------------------------------------

# Set the standard date format
# DEFAULT: 'd.m.Y'
define('DEFAULT_DATE_FORMAT', 'd.m.Y');

# ------------------------------------------------------------------------------

# Set the standard time format (with seperator - if wanted)
# DEFAULT: ', H:i a'
define('DEFAULT_TIME_FORMAT', ', H:i a');

# ------------------------------------------------------------------------------

# Enter a random hash to higher the security of md5 hashs
# NOTE: AVOID THE CHANGE OF THIS HASH AFTER USERS HAVE REGISTERED OR YOU WILL DESTROY
# THEIR LOGINS!
define('RANDOM_HASH', 'h7da87@#asd0io08');

# ------------------------------------------------------------------------------

# To avoid spam, we use ReCaptcha (http://recaptcha.org). Get there, register
# yourself and get an account
# Enter given private key here:
define('RECAPTCHA_PRIVATE', '6LeElLwSAAAAAEm2k4HEN_LiRtG-1QXU_ApVOUC0');

# Enter given public key here:
define('RECAPTCHA_PUBLIC', '6LeElLwSAAAAALqrk7EvegSRhXivkHf-CZSai104');

# ------------------------------------------------------------------------------

# Set maximum image/video width (MEDIA_DEFAULT_X) and height (MEDIA_DEFAULT_Y) in px.
# Larger images and videos will be reseized or scaled down!
# DEFAULT: 720
define('MEDIA_DEFAULT_X', '720');

# DEFAULT: 405
define('MEDIA_DEFAULT_Y', '405');

# Set thumb width
# DEFAULT: 200
define('THUMB_DEFAULT_X', '200');

# Set popup width
# DEFAULT: 800
define('POPUP_DEFAULT_X', '800');
define('POPUP_DEFAULT_Y', '800');

# ------------------------------------------------------------------------------

# If you want to use skins, enter name of skin-folder here
# They are placed at skins/<SkinName>/...
# DEFAULT: ''
define('PATH_CSS', '');

# DEFAULT: default
define('PATH_IMAGES', '');

# Define, where to search for additional templates
# DEFAULT: '', FOLDER: 'public/skins/SKINNAME'
define('PATH_TPL', '');

# Define, where to find static HTML-Templates
# DEFAULT: 'public/skins/default/view/_static'
define('PATH_TPL_STATIC', 'public/skins/_static');

# Define, where files are uploaded to
# DEFAULT: upload
define('PATH_UPLOAD', 'upload');

# ------------------------------------------------------------------------------

# Define limit for pictures per page (3 in a row)
# DEFAULT: 9
define('LIMIT_ALBUM_IMAGES', 9);
define('LIMIT_ALBUM_THUMBS', 9999);

# Limit of blog entries per page
# DEFAULT: 5
define('LIMIT_BLOG', 2);

# Limit of comments per page
# DEFAULT: 25
define('LIMIT_COMMENTS', 5);

# ------------------------------------------------------------------------------

?>