<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# ------------------------------------------------------------------------------

# Set up your SQL preferences. If they are incorrect, the website won't work.
define('SQL_HOST', 'localhost');
define('SQL_USER', 'root');
define('SQL_PASSWORD', '');
define('SQL_DB', 'cms_new');

# ------------------------------------------------------------------------------

# Do you want to use SMTP to send your mails instead of mail()?
# DEFAULT: false
define('SMTP_ON', false);
define('SMTP_HOST', 'localhost');
define('SMTP_USER', '');
define('SMTP_PASSWORD', '');
define('SMTP_PORT', '1025');

# ------------------------------------------------------------------------------

# Define the ABSOLUTE path of your website.
# EXAMPLE: http://www.google.com
define('WEBSITE_URL', 'http://phpcms.localhost');

# ------------------------------------------------------------------------------

# Define the ABSOLUTE path of your cdn. If you don't use a cdn,
# enter your website url. This is relevant for your public folder.
# EXAMPLE: http://www.google.com
define('WEBSITE_CDN', 'http://phpcms.localhost');

# ------------------------------------------------------------------------------

# Use compressed or non-compressed files. Note that compressed files must be
# updated every time you work on a non-compressed file!
# DEFAULT: 'true'
define('WEBSITE_COMPRESS_FILES', false);
# ------------------------------------------------------------------------------

# Enter the full name of website. This is used for Emails and RSS
# NOTE: Also edit your website title and slogan in your language file of choice.
define('WEBSITE_NAME', 'dev.planetk4.de');

# ------------------------------------------------------------------------------

# Define an admin email for system responses
define('WEBSITE_MAIL', 'marco@empuxa.com');

# ------------------------------------------------------------------------------

# Define a noreply email for spam etc.
# EXAMPLE: no-reply@mydomain.tld
define('WEBSITE_MAIL_NOREPLY', 'no-reply@dev.planetk4.de');

# ------------------------------------------------------------------------------

# Is the website in development mode?
# DEFAULT: false
define('WEBSITE_DEV', true);

# ------------------------------------------------------------------------------

# Enter your Google tracking code here
# DEFAULT: ''
define('WEBSITE_TRACKING_CODE', 'UA-304730-2');

# ------------------------------------------------------------------------------

# Define the entry point of your website. If you want to use a static page, type
# "Static/nameofyourpage" and place it at
# "public/skins/_static/nameofyourpage.tpl"
# DEFAULT: Blog
define('WEBSITE_LANDING_PAGE', 'Blog');

# ------------------------------------------------------------------------------

# Number of seconds between cronjob execution (if enabled)
# DEFAULT: '86400'
define('CRONJOB_UPDATE_INTERVAL', '86400');

# ------------------------------------------------------------------------------

# Allow compressing of SQL backups
# DEFAULT: true
define('CRONJOB_GZIP_BACKUP', true);

# ------------------------------------------------------------------------------

# Do you want to receive a mail with the backup after it's created?
# DEFAULT: false
define('CRONJOB_SEND_PER_MAIL', false);

# ------------------------------------------------------------------------------

# If you want to override existing classes (placed in "app/addons/"), turn true
# DEFAULT: false
define('ALLOW_ADDONS', false);

# ------------------------------------------------------------------------------
# Tell the allowed plugins seperated by comma
# DEFAULT: 'Bbcode,FormatTimestamp,Cronjob,LazyLoad'
# OTHER OFFICIALLY SUPPORTED PLUGINS: Archive, Headlines, Adsense
define('ALLOW_PLUGINS', 'Bbcode,FormatTimestamp,Cronjob,Archive,Headlines,LazyLoad');

# ------------------------------------------------------------------------------
# Allow caching and compiling for better performance?
# DEFAULT: true
define('ALLOW_CACHE', true);

# ------------------------------------------------------------------------------

# Allow this software to connect the CandyCMS website to check for an update
# DEFAULT: true
define('ALLOW_VERSION_CHECK', true);

# ------------------------------------------------------------------------------

# Set the standard language (file must be placed in "languages")
# NOTE: lower cases required!
# DEFAULT: 'de_DE'
define('DEFAULT_LANGUAGE', 'de_DE');

# ------------------------------------------------------------------------------

# Set the standard date format (http://php.net/strftime)
# DEFAULT: '%d.%m.%Y'
define('DEFAULT_DATE_FORMAT', '%d.%m.%Y');

# ------------------------------------------------------------------------------

# Set the standard time format (with seperator - if wanted)
# (http://php.net/strftime)
# DEFAULT: ', %H:%M %p'
define('DEFAULT_TIME_FORMAT', ', %H:%M %p');

# ------------------------------------------------------------------------------

# Enter a random hash to higher the security of md5 hashs
# DEFAULT: None. Create one before you install this software
# NOTE: AVOID THE CHANGE OF THIS HASH AFTER USERS HAVE REGISTERED OR YOU WILL
# DESTROY THEIR LOGINS!
define('RANDOM_HASH', 'h7da87@#asd0io08');

# ------------------------------------------------------------------------------

# To avoid spam, we use reCaptcha (http://www.google.com/recaptcha). Get there,
# register yourself and get an account

# Enter given public key:
define('RECAPTCHA_PUBLIC', '6LeElLwSAAAAALqrk7EvegSRhXivkHf-CZSai104');

# Enter given private key:
define('RECAPTCHA_PRIVATE', '6LeElLwSAAAAAEm2k4HEN_LiRtG-1QXU_ApVOUC0');

# ------------------------------------------------------------------------------

# Set maximum image/video width (MEDIA_DEFAULT_X) and height (MEDIA_DEFAULT_Y) in px.
# Larger images and videos will be reseized or scaled down!
# DEFAULT: 660
define('MEDIA_DEFAULT_X', '660');

# DEFAULT: 371
define('MEDIA_DEFAULT_Y', '371');

# Set thumb width
# DEFAULT: 180
define('THUMB_DEFAULT_X', '180');

# Set maximum popup width
# DEFAULT: 1000 / 800
define('POPUP_DEFAULT_X', '1000');
define('POPUP_DEFAULT_Y', '800');

# ------------------------------------------------------------------------------

# If you want to use skins, enter name of skin-folder here
# They are placed at skins/<SkinName>/...
# DEFAULT: ''
define('PATH_CSS', 'marcoraddatz');

# DEFAULT: default
define('PATH_IMAGES', '');

# Define, where to search for additional templates
# DEFAULT: '', FOLDER: 'public/skins/SKINNAME'
define('PATH_TPL', 'marcoraddatz');

# Define, where to find static HTML-Templates
# DEFAULT: 'public/skins/default/view/_static'
define('PATH_TPL_STATIC', 'public/skins/_static');

# Define, where files are uploaded to
# DEFAULT: upload
define('PATH_UPLOAD', 'upload');

# Some SMARTY settings
define('CACHE_DIR', 'cache');
define('COMPILE_DIR', 'compile');

# ------------------------------------------------------------------------------

# Define limit for pictures per page (3 in a row)
# DEFAULT: 18
define('LIMIT_ALBUM_IMAGES', 18);

# Limit of thumbs at album preview
# DEFAULT: 32
define('LIMIT_ALBUM_THUMBS', 32);

# Limit of blog entries per page
# DEFAULT: 8
define('LIMIT_BLOG', 8);

# Limit of comments per page
# DEFAULT: 10
define('LIMIT_COMMENTS', 10);

# ------------------------------------------------------------------------------

?>