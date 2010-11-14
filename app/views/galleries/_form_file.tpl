<link href='%PATH_PUBLIC%/css/fancyupload-min.css' rel='stylesheet' type='text/css' media='screen, projection' />
  <script type='text/javascript' src="%PATH_PUBLIC%/lib/fancyupload/source/Swiff.Uploader.js"></script>
  <script type='text/javascript' src="%PATH_PUBLIC%/lib/fancyupload/source/Fx.ProgressBar.js"></script>
  <script type='text/javascript' src="%PATH_PUBLIC%/lib/fancyupload/source/FancyUpload2.js"></script>
{if $smarty.get.action == 'createfile'}
  <script type='text/javascript'>
    window.addEvent('domready', function()
    {
      var up = new FancyUpload2($('fancy-status'), $('fancy-list'),
      {
        verbose: false,
        url: $('upload').action,
        data: $('upload').toQueryString(),
        path: '%PATH_PUBLIC%/lib/fancyupload/source/Swiff.Uploader.swf',

        typeFilter: {
          'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
        },

        target: 'fancy-browse',

        onLoad: function() {
          $('fancy-status').removeClass('hide');
          $('fancy-fallback').destroy();

          this.target.addEvents({
            click: function() {
              return false;
            },
            mouseenter: function() {
              this.addClass('hover');
            },
            mouseleave: function() {
              this.removeClass('hover');
              this.blur();
            },
            mousedown: function() {
              this.focus();
            }
          });

          $('fancy-upload').addEvent('click', function() {
            up.start();
            return false;
          });
        },

        onBeforeStart: function() {
          up.setOptions({
            data: $('upload').toQueryString()
          });
        },

        onSelectFail: function(files) {
          files.each(function(file) {
            new Element('li', {
              'class': 'validation-error',
              html: file.validationErrorMessage || file.validationError,
              title: MooTools.lang.get('FancyUpload', 'removeTitle'),
              events: {
                click: function() {
                  this.destroy();
                }
              }
            }).inject(this.list, 'top');
          }, this);
        },

        onFileSuccess: function(file, response) {
          var json = new Hash(JSON.decode(response, true) || {});

          if (json.get('status') == '1') {
            file.element.addClass('file-success');
            file.info.set('html', '<strong>Image was uploaded:</strong> ' + json.get('width') + ' x ' + json.get('height') + 'px, <em>' + json.get('mime') + '</em>');
          } else {
            file.element.addClass('file-failed');
            file.info.set('html', '<strong>An error occured:</strong> ' + (json.get('error') ? (json.get('error') + ' #' + json.get('code')) : response));
          }
        },

        onComplete: function() {
          window.location.href = '/Gallery/{$_request_id_}';
        },

        onFail: function(error) {
          switch (error) {
            case 'hidden': /* works after enabling the movie and clicking refresh */
              alert('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).');
              break;
            case 'blocked': /* This no *full* fail, it works after the user clicks the button */
              alert('To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).');
              break;
            case 'empty': /* Oh oh, wrong path */
              alert('A required file was not found, please be patient and we fix this.');
              break;
            case 'flash': /* no flash 9+ :( */
              alert('To enable the embedded uploader, install the latest Adobe Flash plugin.')
          }
        },
      });
    });
  </script>
{/if}
<form action='{$_action_url_}' method='post' enctype='multipart/form-data' id='upload'>
  <table>
    <tr>
      <th colspan='2'>{$lang_headline}</th>
    </tr>
    {if $smarty.get.action == 'createfile'}
      <tr class='row1'>
        <td class='td_left'>
          <label for='file'>{$lang_file_choose}</label>
        </td>
        <td class='td_right'>
          <div id='fancy-fallback'>
            <input type='file' name='file' id='file' />
          </div>
          <div id="fancy-status" class="hide">
            <p>
              <a href="#" id="fancy-browse">{$lang_file_choose}</a>
            </p>
            <div>
              <strong class="overall-title"></strong>
              <br />
              <img src="%PATH_PUBLIC%/lib/fancyupload/assets/progress-bar/bar.gif"
                   class="progress overall-progress" alt="" />
            </div>
            <div>
              <strong class="current-title"></strong>
              <br />
              <img src="%PATH_PUBLIC%/lib/fancyupload/assets/progress-bar/bar.gif"
                   class="progress current-progress" alt="" />
            </div>
            <div class="current-text"></div>
          </div>
          {if $smarty.get.action == 'createfile'}
            <div class='description'>{$lang_same_filetype}</div>
          {/if}
          <ul id="fancy-list"></ul>
        </td>
      </tr>
      <tr class='row2'>
        <td class='td_left'>
          <label for='cut'>{$lang_cut}</label>
        </td>
        <td class='td_right'>
          <div class="dropdown">
            <select name='cut' id='cut'>
              <option value='c' {if $default == 'c'}default='default'{/if}>{$lang_create_file_cut}</option>
              <option value='r' {if $default == 'r'}default='default'{/if}>{$lang_create_file_resize}</option>
            </select>
          </div>
        </td>
      </tr>
    {/if}
      <tr class='row1'>
        <td class='td_left'>
          <label for='description'>{$lang_description}</label>
        </td>
        <td class='td_right'>
          <div class="input">
            <input type='text' name='description' value='{$description}' />
          </div>
        </td>
      </tr>
  </table>
  {if $smarty.get.action == 'updatefile'}
    <div class="submit">
      <input type='submit' id='submit' value='{$lang_headline}' />
    </div>
    <div class="button">
      <input type='reset' value='{$lang_reset}' />
    </div>
    <div class="cancel">
      <input type='button' value='{$lang_destroy}'
             onclick="confirmDelete('/Gallery/{$_request_id_}/destroyfile')" />
    </div>
  {else}
    <div class="submit">
      <input type='button' id='fancy-upload' value='{$lang_headline}' />
    </div>
  {/if}
  <input type='hidden' value='formdata' name='{$_formdata_}' />
</form>