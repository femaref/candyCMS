<form action='?action=install&step=2' method='post'>
  <h2>
    1. Create your Configuration.
  </h2>
  <p>
    Copy <code>Candy.inc.php</code> and <code>Plugins.inc.php</code>
    from <code>app/config/samples/</code> to <code>app/config/</code>,
    read each description carefully and modify the software to your needs.
  </p>
  <div class='alert alert-danger'>
    <h3 class='alert-heading'>
      Important information
    </h3>
    <ul>
      {if !$_configs_exist_.main}
        <li>
          Copy <code>app/config/samples/Candy.inc.php</code> to <code>app/config/Candy.inc.php</code>,
          read each description carefully and modify the software for your needs.
        </li>
      {/if}
      {if !$_configs_exist_.plugins}
        <li>
          Copy <code>app/config/samples/Plugins.inc.php</code> to <code>app/config/Plugins.inc.php</code>,
          and modify the defines for all your Plugins (ALLOWED_PLUGINS in <code>config/Candy.inc.php</code>).
        </li>
      {/if}
      {if !$_hash_changed_}
        <li>
          Make sure, you set a random hash at <code>RANDOM_HASH</code> and NEVER change it again.
        </li>
      {/if}
      {if $WEBSITE_MODE != 'production'}
        <li>
          The <code>WEBSITE_MODE</code> is very important. For security reasons never run a
          productive system with a state different than 'production'!
        </li>
      {/if}
      <li>
        Your system slows down with every additional plugin enabled and if extensions
        are allowed.
      </li>
      <li>
        Try to avoid changes to <code>THUMB_DEFAULT_X</code> and
        <code>THUMB_DEFAULT_Y</code> after the first images have been uploaded.
        That might cause problems.
      </li>
    </ul>
  </div>
  <hr />

  <h2>
    2. Edit your website title and slogan.
  </h2>
  <p>
    Go to <code>app/languages/yourlanguage.language.yml</code> and edit <code>WEBSITE_TITLE</code> and
    <code>WEBSITE_SLOGAN</code>. This helps you to provide individual information about your
    website for each language and might be good for SEO.
  </p>
  <hr />

  <h2>
    3. Go to <a href="http://www.google.com/recaptcha">google.com/recaptcha</a> and
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
  <div class='alert alert-notice'>
    <p>
      CAPTCHAs can be enabled / disabled via adding Recaptcha as plugin at ALLOWED_PLUGINS
      in <code>app/config/Candy.inc.php</code>.
    </p>
    <p>
      Also your public and private key have to be added to <code>app/config/Plugins.inc.php</code>
      (<code>PLUGIN_RECAPTCHA_PUBLIC_KEY</code> & <code>PLUGIN_RECAPTCHA_PRIVATE_KEY</code>).
    </p>
  </div>
  <hr class="clearfix" />

  <h2>
    4. If you want to use the Facebook plugin, register your domain.
  </h2>
  <p>
    With <a href="http://www.facebook.com/developers/createapp.php">Facebook</a> you
    can easily track any "likes" and user activities on your website.
    It also gives users the possibility to comment posts with their Facebook ID via
    Facebook Connect.
  </p>
  <div class="alert alert-notice">
    Don't forget to enter your App ID and Secret into <code>config/Plugins.inc.php</code>
    and add Facebook as plugin at <code>ALLOWED_PLUGINS</code> in the <code>config/Candy.inc.php</code>!
  </div>
  <hr />

  <h2>
    5. Register at MailChimp to send newsletters.
  </h2>
  <p>
    <a href="http://mailchimp.com/">MailChimp</a> helps you design email newsletters,
    share them on social networks, integrate with services you already use, and
    track your results. It's like your own personal publishing platform.
  </p>
  <div class="alert alert-notice">
    You must set your Mailchimp information in the <code>config/Candy.inc.php</code>!
  </div>
  <div class='form-actions right'>
    <input type='submit' class='btn' value='Step 2: Create folder structure &rarr;' />
  </div>
</form>