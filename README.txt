Sorry, not in english yet!
---------------------------------------------------------------------------------


Generelle Infos
---------------------------------------------------------------------------------
- Mulitlingual; Unterstützt die schnelle Umschaltung zwischen mehreren Sprachen
- WYSIWYG-Editor (TinyMCE - http://tinymce.moxiecode.com/)
- gängiger BB-Code ebenfalls möglich
- Smarty-Templates (http://smarty.org)
- Verwendung von Captchas (http://recaptcha.org)
- Flash-Videos können anhand von BB-Code in sekundenschnelle eingebunden werden
- komplett objektorientiert (mit Ausnahme der externen Komponenten) in PHP5 und MySQL4 geschrieben
- einfache Einbindung von Addons
- eigene Skins erstellbar
- entspricht allen W3C-Standards (XHTML und CSS)
- Newsletterfunktion
- eingebauter Dateimanager
- gerade einmal 2,5 MB groß
- Auslagerung der statischen Dateien auf einem CDN ohne großen Aufwand möglich
- Übersichtliche URLs dank der Verwendung von mod_rewrite
- über 90% der Punkte bei YSLOW standardmäßig möglich, mit der Einrichtung eines CDNs sind sogar 99% erreichbar!
- verfügbare Cronjob-Schnittstelle für Backups und Cleanups


Blog, sowie Content-Seiten
---------------------------------------------------------------------------------
- einfache Usability
- optionale Angabe von Tags erlaubt eine einfache Kategoriesierung von Einträgen
- konfigurierbare Seiten- / Kommentarlimits
- optionale Anzeige der letzten Aktualisierung von Einträgen
- Unterstützung der gängisten Social-Networks (Facebook, Digg etc.)
- RSS-Feed (nur Blog)


Galerie
---------------------------------------------------------------------------------
- Mulitupload möglich
- Bilder lassen sich proportional verkleinern oder quadratisch beschneiden


Usermanagement
---------------------------------------------------------------------------------
- simple Registration
- Unterteilung in verschiedene Rechtegruppen, wie u.a. Gäste, Mitglieder, besondere Mitglieder, Moderatoren und Administratoren


Voraussetzungen
---------------------------------------------------------------------------------
- PHP 5.1 & PDO unterstütze Datenbank (http://www.phpro.org/tutorials/Introduction-to-PHP-PDO.html#2)
- Imagemagick, GD2 Support und mod_rewrite aktiviert
- eine Registration bei http://recaptcha.org zur Verwendung von Captchas
- min. 3 MB Webspace


Installation
---------------------------------------------------------------------------------

1. Ggf. einen Account auf http://recaptcha.org einrichten.
2. Die Config ("/config/Config.php.inc") anpassen.
3. Die Ordner "/backup", "/cache", "/compile" und "/upload" erstellen und mit den Rechten "777" versehen.
3.1. Ebenfalls die Ordner "/upload/gallery", "/upload/media", "/upload/temp", "/upload/gallery", "/upload/user/32", "/upload/user/64", "/upload/user/100", "/upload/user/200", "/upload/user/POPUP_DEFAULT_X" (Wert aus der Config; Standard: 800), "/upload/user/original" erstellen und mit den CHMOD-Rechten 777 ausstatten.
4. Installationsscript via "/install" aufrufen und die einzelnen Schritte durchführen.