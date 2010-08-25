<h1>2. Enter your personal data</h1>
{if $status == true}
  <fieldset>
    <legend>2.1. Please enter information below. You can edit your personal information after your first login.</legend>
    <table>
      <tr class='row1'>
        <td class='td_left'>
          <label for='email'>E-Mail *</label>
        </td>
        <td class='td_right'>
          <div class="input">
            <input name='email' id='email'
                   value='' type='text' />
          </div>
        </td>
      </tr>
      <tr class='row2'>
        <td class='td_left'>
          <label for='name'>Name *</label>
        </td>
        <td class='td_right'>
          <div class="input">
            <input name='name' id='name'
                   value='' type='text' />
          </div>
        </td>
      </tr>
      <tr class='row1'>
        <td class='td_left'>
          <label for='name'>Surname *</label>
        </td>
        <td class='td_right'>
          <div class="input">
            <input name='surname' id='surname'
                   value='' type='text' />
          </div>
        </td>
      </tr>
      <tr class='row2'>
        <td class='td_left'>
          <label for='name'>Password *</label>
        </td>
        <td class='td_right'>
          <div class="input">
            <input name='password' id='password'
                   value='' type='password' />
          </div>
        </td>
      </tr>
      <tr class='row1'>
        <td class='td_left'>
          <label for='email'>Repeat Password *</label>
        </td>
        <td class='td_right'>
          <div class="input">
            <input name='password2' id='password2'
                   value='' type='password' onkeyup="checkPassword()" />
          </div>
        </td>
      </tr>
    </table>
  </fieldset>
  <fieldset style="margin-top:25px">
    <legend>2.2. Example data.</legend>
    <table>
      <tr class='row1'>
        <td class='td_left'>
          <label for='email'>Create sample content?</label>
        </td>
        <td class='td_right'>
          <div class="checkbox">
            <input name='create_content' id='create_content'
                   value='' type='checkbox' />
          </div>
        </td>
      </tr>
    </table>
  </fieldset>
{else}
  <h3>2.1. Make sure, you entered the td_right SQL information into your <em>config/Config.inc.php</em> or no database can be created!</h3>
{/if}
{literal}
  <script type="text/javascript" language="javascript">
    window.addEvent('domready', function() {
      $('nextstep').disabled = 'disabled';
    });

    function checkPassword() {
        if( $('password').value == $('password2').value ) {
          if( $('email').value !== '' ) {
            $('nextstep').disabled = '';
          }
        }
      }
  </script>
{/literal}