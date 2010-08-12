<h1>2. Enter your personell data</h1>
{if $status == true}
  <h3>2.1. Please enter information below. You can edit your personal information after your first login.</h3>
  <table>
    <tr class='row1'>
      <td class='tbl_left'>
        <label for='email'>E-Mail *</label>
      </td>
      <td class='tbl_right'>
        <input name='email' id='email' class="inputtext"
               value='' type='text' />
      </td>
    </tr>
    <tr class='row2'>
      <td class='tbl_left'>
        <label for='name'>Password *</label>
      </td>
      <td class='tbl_right'>
        <input name='password' id='password' class='inputtext'
               value='' type='password' />
      </td>
    </tr>
    <tr class='row1'>
      <td class='tbl_left'>
        <label for='email'>Repeat Password *</label>
      </td>
      <td class='tbl_right'>
        <input name='password2' id='password2' class='inputtext'
               value='' type='password' onkeyup="checkPassword()" />
      </td>
    </tr>
  </table>
{else}
  <h3>2.1. Make sure, you entered the tbl_right SQL information into your <em>config/Config.inc.php</em> or no database can be created!</h3>
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