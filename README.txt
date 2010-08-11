Sorry, not in english yet!
---------------------------------------------------------------------------------


Generelle Features
---------------------------------------------------------------------------------
- Mulitlingual: Schnelles Umschalten zwischen verschiedenen Sprachen möglich
- einfaches Hinzufügen und Bearbeiten von Einträgen dank WYSIWYG-Editor (TinyMCE)
- gängiger BBCode als alternative Formatierung
- Smarty-Templates
- Verwendung von Captchas (recaptcha.org)
- Flash Videos können anhand von BB-Code in sekundenschnelle über eine interne Anzeige wiedergegeben werden
- komplett objektorientiert (mit Ausnahme der externen Komponenten) in PHP5 und MySQL5 geschrieben
- einfache Einbindung von Addons
- eigene Skins erstellbar
- entspricht allen W3C-Standards (XHTML und CSS)


Blog, sowie Content-Seiten
---------------------------------------------------------------------------------
- einfaches Hinzufügen, Editieren und Löschen von Einträgen dank intuitivem Aufbau des Scripts
- optionale Angabe von Tags erlaubt eine einfache Kategoriesierung von Einträgen
- konfigurierbare Seiten- / Kommentarlimits
- optionale Anzeige der letzten Aktualisierung von Einträgen
- Unterstützung der gängisten Social-Networks (Facebook, Digg etc.)
- RSS-Feed (nur Blog)


Galerie
---------------------------------------------------------------------------------
- Mulitupload möglich
- Bilder lassen sich proportional verkleinern oder beschneiden


Usermanagement
---------------------------------------------------------------------------------
- simple Registration
- Unterteilung in verschiedene Rechtegruppen, wie u.a. Gäste, Mitglieder, besondere Mitglieder, Moderatoren und Administratoren


Allgemeine Funktionen
---------------------------------------------------------------------------------
- Newsletterfunktion
- eingebauter Dateimanager
- gerade einmal 2,2MB groß
- Auslagerung der statischen Dateien auf einem CDN ohne großen Aufwand möglich
- Übersichtliche URLs dank der Verwendung von mod_rewrite
- mehr als 90% der Punkte bei YSLOW standardmäßig möglich, mit der Einrichtung eines CDNs sind sogar 99% erreichbar!
- verfügbare Cronjob-Schnittstelle für Backups und Cleanups


Voraussetzungen
---------------------------------------------------------------------------------
- PHP 5 & MySQL 4
- mod_rewrite aktiviert
- eine Registration bei http://recaptcha.org zur Verwendung von Captchas
- min. 3 MB Webspace


Installation
---------------------------------------------------------------------------------

1. Ggf. einen Account auf reCaptcha.org einrichten.
2. Die Ordner "/backup", "/cache", "/compile" und "/upload" erstellen und mit den Rechten "0755" versehen.
3. Die Config ("/config/Config.php.inc") anpassen.
4. Installationsscript via "/install" aufrufen und die Schritte durchführen.