CandyCMS Version 2.0
========================================================================================================================

What is CandyCMS?
------------------------------------------------------------------------------------------------------------------------

CandyCMS is a modern PHP CMS with main focus on usability, speed and security.

Its main functions are:

- a blog that supports tags, comments, RSS and full social media integration
- content pages
- a gallery with multiple file upload (based on HTML5) and Media RSS
- a calendar
- a download section
- easy user management
- file management
- newsletter management (uses [Mailchimp API](http://mailchimp.com))
- a full log system


Additional reasons, why CandyCMS might be interesting for you
------------------------------------------------------------------------------------------------------------------------
- easy internationalization and localization via YAML
- WYSIWYG-Editor ([TinyMCE](http://tinymce.moxiecode.com/)) and support of [BB-Code](https://github.com/marcoraddatz/candyCMS/wiki/BBCode)
- uses the [Smarty template engine](http://smarty.org) and lots of HTML5
- supports [reCAPTCHA](http://recaptcha.org)
- completely object oriented and use of MVC
- easy to use addons or modify
- supports templates
- clean URLs due to mod_rewrite
- full Facebook integration
- supports CDNs
- easy to update or migrate
- SEO optimized (sitemap.xml and basic stuff)
- 2click social share privacy


Requirements
------------------------------------------------------------------------------------------------------------------------
- at least PHP 5.1 & PDO supported database
- Imagemagick, GD2 and mod_rewrite
- an account at http://recaptcha.org to use captchas
- an account at http://mailchimp.com to use the newsletter management
- about 5MB webspace


Setup
------------------------------------------------------------------------------------------------------------------------
Configure your website settings at "config/Candy.inc.php, upload all files and execute the "/install/index.php" file.
Follow the instructions and make sure, you delete the install dir after installation.

To upgrade CandyCMS, upload the install folder, run "/install/index.php" and click on "migrate". Make sure you override
the existing "app", "lib" and "plugin" folders before. Please also take a look at the release notes.


Credits
------------------------------------------------------------------------------------------------------------------------
Icons were created by [icondock.com](http://icondock.com) and [famfamfam.com](http://famfamfam.com).


License
------------------------------------------------------------------------------------------------------------------------
CandyCMS is licensed under MIT license.