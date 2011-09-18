<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

define( 'LANG_NEWSLETTER_CREATE_INFO', '%u wird zum Usernamen!<br />Bedenke bitte ebenfalls, dir die gesamte Nachricht noch einmal vor dem Abschicken durchzulesen, um Rechtschreibfehler oder falsch gesetzte Links in der E-Mail zu vermeiden.' );
define( 'LANG_NEWSLETTER_CREATE_TITLE', 'Newsletter verfassen' );
define( 'LANG_NEWSLETTER_CREATE_LABEL_SUBMIT', 'Newsletter verschicken' );
define( 'LANG_NEWSLETTER_HANDLE_INFO', 'Trage hier deine E-Mail Adresse ein um den Newsletter zu abonieren, bzw. abzubestellen. Du benötigst den Newsletter nicht mehr, wenn du dich auf dieser Seite registriert hast!' );
define( 'LANG_NEWSLETTER_HANDLE_TITLE', 'Newsletter abonieren / abbestellen' );
define( 'LANG_NEWSLETTER_SHOW_DEFAULT_NAME', 'Abonnenten' ); # Name of anonymous receivers
define( 'LANG_PAGES_NEXT_ENTRIES', 'Ältere Beiträge' );
define( 'LANG_PAGES_PREVIOUS_ENTRIES', 'Neuere Beiträge' );
define( 'LANG_SEARCH_SHOW_TITLE', 'Suchergebnisse für "%s"' );
define( 'LANG_SEARCH_SHOW_LABEL_TERMS', 'Suchbegriffe' );
define( 'LANG_SESSION_CREATE_SUCCESSFUL', 'Login erfolgreich!' );
define( 'LANG_SESSION_DESTROY_SUCCESSFUL', 'Logout erfolgreich!' );
define( 'LANG_SESSION_PASSWORD_CREATE_SUCCESSFUL', 'Dein neues Passwort wurde dir erfolgreich per E-Mail zugeschickt!' );
define( 'LANG_SESSION_PASSWORD_TITLE', 'Password vergessen?' );
define( 'LANG_SESSION_PASSWORD_INFO', 'Trage hier deine registrierte E-Mail Adresse ein und wir schicken dir ein neues Passwort innerhalb der nächsten Minuten zu.' );
define( 'LANG_SESSION_PASSWORD_LABEL_SUBMIT', 'Password zuschicken' );
define( 'LANG_SESSION_VERIFICATION_TITLE', 'E-Mail-Bestätigung erneut zusenden' );
define( 'LANG_SESSION_VERIFICATION_INFO', 'Lasse dir jetzt erneut eine E-Mail mit Bestätigungslink an deine Adresse zuschicken.' );
define( 'LANG_SESSION_VERIFICATION_LABEL_SUBMIT', 'E-Mail zuschicken' );
define( 'LANG_USER_CREATE_SUCCESSFUL', 'Du wurdest erfolgreich registriert und solltest eine E-Mail erhalten haben, über die du die Echtheit deiner E-Mail-Adresse bestätigten kannst!' );
define( 'LANG_USER_CREATE_TITLE', 'Benutzer erstellen' );
define( 'LANG_USER_SHOW_OVERVIEW_TITLE', 'Benutzerübersicht' );
define( 'LANG_USER_SHOW_USER_ACTION_CONTACT_VIA_EMAIL', 'Klicke hier, um %u eine E-Mail zu schicken!' );
define( 'LANG_USER_SHOW_USER_LABEL_DESCRIPTION', 'Über %u' );
define( 'LANG_USER_SHOW_USER_LABEL_LAST_LOGIN', 'Letzter Login' );
define( 'LANG_USER_SHOW_USER_REGISTERED_SINCE', 'Registriert seit' );
define( 'LANG_USER_UPDATE_ACCOUNT_INFO', 'Bitte gib zur eigenen Sicherheit dein aktuelles Passwort ein. Sobald du auf "Account löschen" klickst, wird dein Zugang gelöscht und ist nicht wiederherstellbar!' );
define( 'LANG_USER_UPDATE_ACCOUNT_TITLE', 'Account löschen' );
define( 'LANG_USER_UPDATE_IMAGE_LABEL_CHANGE', 'Userbild ändern?' );
define( 'LANG_USER_UPDATE_IMAGE_LABEL_CHOOSE', 'Bild auswählen' );
define( 'LANG_USER_UPDATE_IMAGE_LABEL_TERMS', 'Ich darf das Bild hochladen und verletze keine Rechte Dritter.' );
define( 'LANG_USER_UPDATE_IMAGE_TITLE', 'Bild hochladen' );
define( 'LANG_USER_UPDATE_IMAGE_INFO', 'Bitte nur Bilder mit einer Größe von bis zu 400KB hochladen!' );
define( 'LANG_USER_UPDATE_PASSWORD_LABEL_NEW', 'Neues Passwort' );
define( 'LANG_USER_UPDATE_PASSWORD_LABEL_OLD', 'Altes Passwort' );
define( 'LANG_USER_UPDATE_PASSWORD_TITLE', 'Passwort ändern' );
define( 'LANG_USER_UPDATE_TITLE', 'Einstellungen bearbeiten' );
define( 'LANG_USER_UPDATE_USER_GRAVATAR_INFO', 'Aktivierst du dieses Kästchen, wird automatisch nach einem von dir auf <a href="http://gravatar.com" target="_blank">Gravatar.com</a> hinterlegten Profilbild gesucht. Alternativ kannst du aber auch direkt ein Bild hochladen.' );
define( 'LANG_USER_UPDATE_USER_LABEL_DESCRIPTION', 'Über dich' );
define( 'LANG_USER_UPDATE_USER_LABEL_GRAVATAR', 'Gravatar verwenden?' );
define( 'LANG_USER_UPDATE_USER_LABEL_NEWSLETTER', 'Newsletter abonieren?' );
define( 'LANG_USER_UPDATE_USER_LABEL_SUBMIT', 'Profil aktualisieren' );
define( 'LANG_USER_UPDATE_USER_TITLE', 'Persönliche Daten' );
define( 'LANG_USER_VERIFICATION_SUCCESSFUL', 'Vielen Dank für die Bestätigung deiner E-Mail-Adresse.' );


# MAIL CONTENT GLOBAL
define( 'LANG_MAIL_GLOBAL_NO_REPLY', '<p>(Dies ist eine autogenerierte E-Mail - bitte nicht beantworten!)</p>' );
define( 'LANG_MAIL_GLOBAL_SENT_TITLE', 'Nachricht wurde erfolgreich verschickt.' );
define( 'LANG_MAIL_GLOBAL_SENT_INFO', 'Du wirst in 15 Sekunden auf die Startseite weitergeleitet.' );
define( 'LANG_MAIL_GLOBAL_SIGNATURE', '<p>Viel Spaß wünscht das Team von<br /><a href="%WEBSITE_URL">%WEBSITE_NAME</a></p>' );
define( 'LANG_MAIL_GLOBAL_SUBJECT_BY', 'Neue Nachricht von %u' );


# MAIL CONTENT BY SECTION
define( 'LANG_MAIL_CRONJOB_CREATE_BODY', "Ihre Sicherungskopie wurde erfolreich erstellt und befindet sich im Anhang.\r\n" );
define( 'LANG_MAIL_CRONJOB_CREATE_SUBJECT', 'Sicherungskopie vom %d' );
define( 'LANG_MAIL_NEWSLETTER_CREATE_SUBJECT', 'Newsletter von %WEBSITE_NAME' );
define( 'LANG_MAIL_NEWSLETTER_CREATE_BODY', "Hallo,\r\nvielen Dank für Ihr Interesse an unserem Newsletter. Falls Sie diese E-Mail fälschlicherweise erreicht hat, klicken Sie bitte <a href='%WEBSITE_URL/Newsletter'>hier</a>.%SIGNATURE");
define( 'LANG_MAIL_SESSION_PASSWORD_BODY', "Hallo %u!\r\n\r\nDu kannst dich ab sofort mit folgendem Passwort einloggen: <em>%p</em> %SIGNATURE %NOREPLY");
define( 'LANG_MAIL_SESSION_PASSWORD_SUBJECT', 'Dein neues Passwort' );
define( 'LANG_MAIL_SESSION_VERIFICATION_BODY', "Hallo %u!\r\n\r\nBitte klicke auf diesen Link, um deine E-Mail-Adresse zu bestätigen: %v %SIGNATURE %NOREPLY");
define( 'LANG_MAIL_SESSION_VERIFICATION_SUBJECT', 'Deine E-Mail-Bestätigung für %WEBSITE_NAME' );
define( 'LANG_MAIL_USER_CREATE_BODY', "Hallo %u!\r\n\r\nDanke für deine Registrierung auf %WEBSITE_NAME. Um dich einloggen zu können, musst du noch die Echtheit deiner E-Mail-Adresse über diesen Link bestätigen: %v %SIGNATURE %NOREPLY");
define( 'LANG_MAIL_USER_CREATE_SUBJECT', 'Deine Registrierung auf %WEBSITE_NAME.' );


# ERRORS IN FORM ELEMENTS
define( 'LANG_ERROR_FORM_TITLE', 'Bitte fülle die markierten Felder aus:' );
define( 'LANG_ERROR_FORM_MISSING_CATEGORY', 'Bitte wähle eine Kategorie!' );
define( 'LANG_ERROR_FORM_MISSING_CONTENT', 'Bitte fülle den Inhalt aus!' );
define( 'LANG_ERROR_FORM_MISSING_EMAIL', 'Bitte gib deine E-Mail-Adresse an!' );
define( 'LANG_ERROR_FORM_MISSING_FILE', 'Bitte gib eine Datei zum Upload an!' );
define( 'LANG_ERROR_FORM_MISSING_NAME', 'Bitte gib deinen Namen an!' );
define( 'LANG_ERROR_FORM_MISSING_TITLE', 'Bitte gib einen Titel an!' );
define( 'LANG_ERROR_FORM_MISSING_PASSWORD', 'Bitte gib ein Passwort an!' );
define( 'LANG_ERROR_FORM_MISSING_SUBJECT', 'Bitte gib einen Betreff ein!' );


# ERROR MESSAGES
define( 'LANG_ERROR_GALLERY_NO_FILES_UPLOADED', 'Es wurden noch keine Bilder hochgeladen!' );
define( 'LANG_ERROR_GLOBAL', 'Es trat ein Fehler auf!' );
define( 'LANG_ERROR_GLOBAL_404_INFO', 'Die angeforderte Datei wurde nicht gefunden. Entweder haben Sie auf einen fehlerhaften Link geklickt, die Seite wurde gelöscht oder die URL ist falsch eingegeben worden.' );
define( 'LANG_ERROR_GLOBAL_404_TITLE', 'Error 404' );
define( 'LANG_ERROR_GLOBAL_CREATE_SESSION_FIRST', 'Bitte logge dich zuerst ein!' );
define( 'LANG_ERROR_GLOBAL_FILE_COULD_NOT_BE_DESTROYED', 'Die Datei konnte nicht gelöscht werden!' );
define( 'LANG_ERROR_GLOBAL_MISSING_ENTRY', 'Eintrag nicht vorhanden!' );
define( 'LANG_ERROR_GLOBAL_NO_ENTRIES', 'Es existieren noch keine Einträge!' );
define( 'LANG_ERROR_GLOBAL_NO_LANGUAGE', 'Die Sprachdatei konnte nicht geladen werden!' );
define( 'LANG_ERROR_GLOBAL_NO_PERMISSION', 'Du hast keine Berechtigung diese Aktion auszuführen!' );
define( 'LANG_ERROR_GLOBAL_NO_TEMPLATE', 'Es wurde keine passende Template gefunden!' );
define( 'LANG_ERROR_GLOBAL_NOT_PUBLISHED', 'Nicht veröffentlicht:' );
define( 'LANG_ERROR_GLOBAL_PASSWORDS_DO_NOT_MATCH', 'Bitte zwei identische Passwörter angeben!' );
define( 'LANG_ERROR_GLOBAL_READ_DISCLAIMER', 'Bitte den Nutzungsbedingungen zustimmen!' );
define( 'LANG_ERROR_GLOBAL_WRONG_EMAIL_FORMAT', 'Dies ist keine gültige E-Mail-Adresse!' );
define( 'LANG_ERROR_GLOBAL_WRONG_ID', 'Falsche oder keine ID angegeben.' );
define( 'LANG_ERROR_HELPER_NO_FLASH_INSTALLED', 'Downloade den Flashplayer, um dieses Video anzugucken!' );
define( 'LANG_ERROR_MAIL_CAPTCHA_NOT_CORRECT', 'Der Code war leider nicht korrekt!' );
define( 'LANG_ERROR_MAIL_CAPTCHA_NOT_LOADED', 'Captcha konnte nicht geladen werden, bitte aktiviere Javascript!' );
define( 'LANG_ERROR_MAIL_ERROR', 'Die E-Mail konnte nicht verschickt werden. Bitte überprüfe ggf. deine E-Mail-Adresse und falls diese korrekt sein sollte, kontaktiere den Admin.' );
define( 'LANG_ERROR_MEDIA_FILE_EMPTY_FOLDER', 'Es wurden bisher keine Dateien hochgeladen.' );
define( 'LANG_ERROR_MEDIA_FILE_NOT_AVAIABLE', 'Datei nicht verfügbar!' );
define( 'LANG_ERROR_MEDIA_MAX_FILESIZE_REACHED', 'Maximale Dateigröße von 400KB erreicht!' );
define( 'LANG_ERROR_MEDIA_WRONG_FILETYPE', 'Es wurde ein falscher Dateityp hochgeladen, es sind nur *.jpg Dateien erlaubt!' );
define( 'LANG_ERROR_PLUGIN_BBCODE_AUDIO', 'Ihr Browser unterstützt leider das Abspielen dieser Audiodatei nicht. Klicken Sie auf <a href="%u">diesen Link</a> um die Datei herunterzuladen.' );
define( 'LANG_ERROR_REQUEST_MISSING_ACTION', 'Die Aktion konnte aufgrund von fehlenden Parametern nicht durchgeführt werden!' );
define( 'LANG_ERROR_SESSION_CREATE', 'E-Mail-Adresse und Passwort stimmen nicht überein. Falls du deine E-Mail-Adresse noch nicht bestätigt hast, mache das bitte vor dem Login.' );
define( 'LANG_ERROR_SESSION_CREATE_TITLE', 'Kann dich nicht einloggen!' );
define( 'LANG_ERROR_SQL_QUERY', 'Es gab einen Fehler beim Verarbeiten des SQL-Statements.' );
define( 'LANG_ERROR_UPLOAD_CREATE', 'Der Upload schlug fehl.' );
define( 'LANG_ERROR_USER_CREATE_EMAIL_ALREADY_EXISTS', 'Es ist bereits ein Nutzer mit dieser E-Mail-Adresse vorhanden!' );
define( 'LANG_ERROR_USER_UPDATE_AGREE_UPLOAD', 'Du musst das Bild hochladen dürfen!' );
define( 'LANG_ERROR_USER_UPDATE_PASSWORD_NEW_EMPTY', 'Bitte trage dein neues Passwort ein!' );
define( 'LANG_ERROR_USER_UPDATE_PASSWORD_NEW_DO_NOT_MATCH', 'Du scheinst dich beim neuen Passwort vertippt zu haben. Achte darauf, dass du zweimal das gleiche Passwort eintippst!' );
define( 'LANG_ERROR_USER_UPDATE_PASSWORD_OLD_EMPTY', 'Bitte trage dein altes Passwort ein!' );
define( 'LANG_ERROR_USER_UPDATE_PASSWORD_OLD_WRONG', 'Dein altes Passwort stimmt leider nicht!' );
define( 'LANG_ERROR_USER_VERIFICATION', 'Du konntest nicht freigeschaltet werden, da kein Account mit einem solchen Code vorhanden ist!' );

?>