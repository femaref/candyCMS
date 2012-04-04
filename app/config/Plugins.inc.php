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

# DEFAULT: 10
define('PLUGIN_HEADLINES_LIMIT', 10);

# DEFAULT: 1000
define('PLUGIN_ARCHIVE_LIMIT', 1000);

# Enter your Google tracking code here
# DEFAULT: ''
define('PLUGIN_ANALYTICS_TRACKING_CODE', '');

# Admins user id(s). Must be comma-separated. (more info at http://developers.facebook.com/docs/opengraph/)
define('PLUGIN_FACEBOOK_ADMIN_ID', '1130922727');

# Your application settings (http://www.facebook.com/developers/apps.php)
define('PLUGIN_FACEBOOK_APP_ID', '202021149901124');
define('PLUGIN_FACEBOOK_SECRET', 'eba3342f5cf108c048f2be1995b767ab');

#Piwik Tracking Information for Piwik Plugin
define('PLUGIN_PIWIK_URL', '');
define('PLUGIN_PIWIK_ID', '');

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