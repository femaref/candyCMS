<h2>1. Go to google.com/recaptcha and register your domain (if not already done).</h2>
<p>
  A CAPTCHA is a program that can tell whether its user is a human or a computer. You've probably seen them — colorful
  images with distorted text at the bottom of Web registration forms. CAPTCHAs are used by many websites to prevent
  abuse from "bots," or automated programs usually written to generate spam. No computer program can read distorted
  text as well as humans can, so bots cannot navigate sites protected by CAPTCHAs.
</p>
<p>
  <a href="http://www.google.com/recaptcha">http://www.google.com/recaptcha</a>
</p>
<hr />

<h2>2. Set all variables in <em>config/Candy.inc.php</em> correctly.</h2>
<p>
  Read each description and modify the software for your needs. @todo
</p>
<hr />

<h2>3. Edit your website title and slogan in <em>languages/yourlanguage.language.yml</em>.</h2>
<p>This helps you to provide individual information about your website for each language.</p>
<hr />

<h2>2.4. Set following dirs to <em>CHMOD 777 (recursive)</em> via the FTP software of your choice:</h2>
<div class="hidden">
  (If this is an nightly build, install the <a href="http://github.com/downloads/marcoraddatz/candyCMS/missing_folders.zip" target="_blank">additional folder package</a> first!)
  {$permissions}
</div>
<h2>2.5. If you want to use the Facebook plugin, register your Domain.</h2>
<div class="hidden">
  <p>
    With Facebook you can easily track any "likes" and user activities on your website. It also gives users the possibility
    to comment posts with their Facebook ID via Facebook Connect.
  </p>
  <p>
    <a href="http://www.facebook.com/developers/createapp.php">Facebook</a>
  </p>
  <div class="notice">
    Don't forget to enter your App ID and Secret into <em>config/Facebook.inc.php</em>!
  </div>
</div>
<h2>2.6. Register at MailChimp to send newsletters.</h2>
<div class="hidden">
  <p>
    MailChimp helps you design email newsletters, share them on social networks, integrate with services you already use, and track your results. It's like your own personal publishing platform.
  </p>
  <p>
    <a href="http://mailchimp.com/">MailChimp</a>
  </p>
  <div class="notice">
    Don't forget to to set information at <em>config/Mailchimp.inc.php</em>!
  </div>
</div>
<ul class='pager clearfix'>
  <li class='next'>
    <a href='/?action=install&step=2' rel='next'>Step2 &rarr;</a>
  </li>
</ul>