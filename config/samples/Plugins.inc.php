<?php

/**
 * Configure your plugins.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

# Define how many headlines will be displayed.
# DEFAULT: 10
define('PLUGIN_HEADLINES_LIMIT', 10);

# How many archive entries should be displayed? Avoid to display only a few when also
# using the headlines plugins due to categorization into month.
# DEFAULT: 1000
define('PLUGIN_ARCHIVE_LIMIT', 1000);

# Enter your Google tracking code here to track visits.
# DEFAULT: ''
define('PLUGIN_ANALYTICS_TRACKING_CODE', '');

# ------------------------------------------------------------------------------

# Admins user id(s). Must be comma-separated. (more info at http://developers.facebook.com/docs/opengraph/)
# DEFAULT: ''
define('PLUGIN_FACEBOOK_ADMIN_ID', '');

# Your application settings (http://www.facebook.com/developers/apps.php)
# DEFAULT: '' (both)
define('PLUGIN_FACEBOOK_APP_ID', '');
define('PLUGIN_FACEBOOK_SECRET', '');

# ------------------------------------------------------------------------------

# Tracking information for Piwik as an alternative to Google Analytics.
# DEFAULT: '' (both)
define('PLUGIN_PIWIK_URL', '');
define('PLUGIN_PIWIK_ID', '');

# ------------------------------------------------------------------------------

# To avoid spam, we use reCaptcha (http://www.google.com/recaptcha). Get there,
# register yourself and get an account

## Recaptcha public and private key:
define('PLUGIN_RECAPTCHA_PUBLIC_KEY', '');
define('PLUGIN_RECAPTCHA_PRIVATE_KEY', '');

# ------------------------------------------------------------------------------

# Number of seconds between cronjob execution (if enabled at ALLOWED_PLUGINS)
# DEFAULT: 86400 ( = 24 hours)
define('PLUGIN_CRONJOB_UPDATE_INTERVAL', 86400);

# Allow compressing of SQL backups
# DEFAULT: true
define('PLUGIN_CRONJOB_GZIP_BACKUP', true);

# Do you want to receive a mail with the backup after it's created?
# DEFAULT: false
define('PLUGIN_CRONJOB_SEND_PER_MAIL', false);

?>