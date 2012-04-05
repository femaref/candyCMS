<form action='?action=install&step=2' method='post'>
  <h2>
    1. Create your Configuration.
  </h2>
  <p>
    Copy <em>Candy.inc.php</em> and <em>Plugins.inc.php</em>
    from <em>config/samples/</em> to <em>config/</em>,
    read each description carefully and modify the software to your needs.
  </p>
  <div class='alert alert-danger'>
    <h4 class='alert-heading'>Important information</h4>
    <ul>
      {if !$_configs_exist_.main}
        <li>
          Copy <em>config/samples/Candy.inc.php</em> to <em>config/Candy.inc.php</em>,
          read each description carefully and modify the software for your needs.
        </li>
      {/if}
      {if !$_configs_exist_.plugins}
        <li>
          Copy <em>config/samples/Plugins.inc.php</em> to <em>config/Plugins.inc.php</em>,
          and modify the defines for all your Plugins (ALLOWED_PLUGINS in <em>config/Candy.inc.php</em>).
        </li>
      {/if}
      {if !$_hash_changed_}
        <li>
          Make sure, you set a random hash at RANDOM_HASH and NEVER change it again.
        </li>
      {/if}
      {if $WEBSITE_MODE != 'production'}
        <li>
          The WEBSITE_MODE is very important. For security reasons never run a
          productive system with a state different than 'production'!
        </li>
      {/if}
      <li>
        Your system slows down with every additional plugin enabled and if extensions
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
  <p class='alert alert-notice'>
    CAPTCHAs can be enabled / disabled via adding Recaptcha as plugin at ALLOWED_PLUGINS
    in <em>config/Candy.inc.php</em>.
    Also your public and private key have to be added to <em>config/Plugins.inc.php</em>
    (PLUGIN_RECAPTCHA_PUBLIC_KEY & PLUGIN_RECAPTCHA_PRIVATE_KEY).
  </p>
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
    Don't forget to enter your App ID and Secret into <em>config/Plugins.inc.php</em>
    and add Facebook as plugin at ALLOWED_PLUGINS in the <em>config/Candy.inc.php</em>!
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
  {if !$_configs_exist_.mailchimp}
    <div class="alert alert-danger">
      Copy <em>config/samples/Mailchimp.inc.php</em> to <em>config/Mailchimp.inc.php</em>
      and set your information. Even if you don't want to use MailChimp, you still have
      to create the Config-File.
    </div>
  {else}
    <div class="alert alert-notice">
      Don't forget to to set information at <em>config/Mailchimp.inc.php</em>!
    </div>
  {/if}
  {if !$_has_errors_}
    <div class='form-actions right'>

      <input type='submit' class='btn' value='Create Folder Structure' />
    </div>
  {/if}
</form>
