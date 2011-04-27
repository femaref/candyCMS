<h1>1. Installation</h1>
<h3>2.1. Go to google.com/recaptcha and register your domain (if not already done).</h3>
<div class="hidden">
  <p>
    A CAPTCHA is a program that can tell whether its user is a human or a computer. You've probably seen them — colorful
    images with distorted text at the bottom of Web registration forms. CAPTCHAs are used by many websites to prevent
    abuse from "bots," or automated programs usually written to generate spam. No computer program can read distorted
    text as well as humans can, so bots cannot navigate sites protected by CAPTCHAs.
  </p>
  <p>
    <a href="http://www.google.com/recaptcha">http://www.google.com/recaptcha</a>
  </p>
</div>
<h3>2.2. Set all variables in <em>config/Config.inc.php</em> correctly.</h3>
<div class="hidden">
  <p>
    Read each description and modify the software for your needs.
  </p>
</div>
<h3>2.3. Edit your website title and slogan in <em>languages/yl/yourlanguage.language.php</em>:</h3>
<div class="hidden">
  <p>This helps you to provide individual information about your website for each language.</p>
  <ul>
    <li>LANG_WEBSITE_TITLE</li>
    <li>LANG_WEBSITE_SLOGAN</li>
    <li>LANG_WEBSITE_KEYWORDS</li>
  </ul>
</div>
<h3>2.4. Set following dirs to <em>CHMOD 777 (recursive)</em> via the FTP software of your choice:</h3>
<div class="hidden">
  (If this is an nightly build, install the <a href="http://github.com/downloads/marcoraddatz/candyCMS/missing_folders.zip" target="_blank">additional folder package</a> first!)
  {$permissions}
</div>
<h3>2.5. If you want to use the Facebook plugin, register your Domain.</h3>
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
<script type="text/javascript">
  $('h3').click(function(){
    $(this).next().toggle();
  });

  $(document).ready(function(){
    $("input[type='submit']").attr('disabled',false);
  });
</script>