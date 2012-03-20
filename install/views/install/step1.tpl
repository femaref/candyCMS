<form action='?action=install&step=2' method='post'>
  <h2>
    1. Set all variables in <em>config/Candy.inc.php</em> correctly.
  </h2>
  <p>
    Read each description and modify the software for your needs.
  </p>
  <div class='alert alert-danger'>
    <h4 class='alert-heading'>Important information</h4>
    <ul>
      <li>
        Make sure, you set a random hash at RANDOM_HASH and NEVER change it again.
      </li>
      <li>
        The WEBSITE_MODE is very important. For security reasons never run a
        productive system with a state different than 'production'!
      </li>
      <li>
        Your system slows down with every additional plugin enabled and if addons
        are allowed.
      </li>
      <li>
        Try to avoid changes to THUMB_DEFAULT_X and THUMB_DEFAULT_Y after the first
        images have been uploaded. That might cause problems.
      </li>
    </ul>
  </div>
  <hr />

  <h2>
    2. Edit your website title and slogan.
  </h2>
  <p>
    Go to <em>languages/yourlanguage.language.yml</em> and edit WEBSITE_TITLE and
    WEBSITE_SLOGAN. This helps you to provide individual information about your
    website for each language.
  </p>
  <hr />

  <h2>
    3. Set following dirs to <em>CHMOD 777 (recursive)</em>.
  </h2>
  <p>
    You might have to use a FTP programm (like <a href='http://cyberduck.ch/'>Cyberduck</a>)
    for that. If the folders were not created by the system, you have to create
    them manually.
  </p>
  <ul>
    <li style='color:{$_color_backup_}'>/backup</li>
    <li style='color:{$_color_cache_}'>/cache</li>
    <li style='color:{$_color_compile_}'>/compile</li>
    <li style='color:{$_color_logs_}'>/logs</li>
    <li style='color:{$_color_upload_}'>/upload</li>
  </ul>
  <hr />

  <h2>
    4. Go to <a href="http://www.google.com/recaptcha">google.com/recaptcha</a> and
    register your domain.
  </h2>
  <p>
    <img src='http://www.google.com/recaptcha/static/images/smallCaptchaSpaceWithRoughAlpha.png'
         style='float:right;margin-left:10px' />
    A CAPTCHA is a program that can tell whether its user is a human or a computer.
    You've probably seen them — colorful images with distorted text at the bottom
    of Web registration forms. CAPTCHAs are used by many websites to prevent abuse
    from "bots," or automated programs usually written to generate spam. No computer
    program can read distorted text as well as humans can, so bots cannot navigate
    sites protected by CAPTCHAs.
  </p>
  <p class='alert alert-notice'>
    CAPTCHAs can be enabled / disabled via <em>config/Candy.inc.php</em> (RECAPTCHA_ENABLED).
    Also your public and private key have to be added there (RECAPTCHA_PUBLIC & RECAPTCHA_PRIVATE).
  </p>
  <hr class="clearfix" />

  <h2>
    5. If you want to use the Facebook plugin, register your domain.
  </h2>
  <p>
    With <a href="http://www.facebook.com/developers/createapp.php">Facebook</a> you
    can easily track any "likes" and user activities on your website.
    It also gives users the possibility to comment posts with their Facebook ID via
    Facebook Connect.
  </p>
  <div class="alert alert-notice">
    Don't forget to enter your App ID and Secret into <em>config/Facebook.inc.php</em>
    and add Facebook as a plugin at ALLOWED_PLUGINS in the <em>config/Candy.inc.php</em>!
  </div>
  <hr />

  <h2>
    6. Register at MailChimp to send newsletters.
  </h2>
  <p>
    <a href="http://mailchimp.com/">MailChimp</a> helps you design email newsletters,
    share them on social networks, integrate with services you already use, and
    track your results. It's like your own personal publishing platform.
  </p>
  <div class="alert alert-notice">
    Don't forget to to set information at <em>config/Mailchimp.inc.php</em>!
  </div>
  <div class='form-actions right'>

    <input type='submit' class='btn' value='Create database' />
  </div>
</form>
