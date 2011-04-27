<h1>2. Enter your personal data</h1>
<p>
  <label for='email'>E-Mail *</label>
  <input name='email' value='' type='email' required />
</p>
<p>
  <label for='name'>Name *</label>
  <input name='name' value='' type='name' required />
</p>
<p>
  <label for='name'>Surname *</label>
  <input name='surname' value='' type='text' required />
</p>
<p>
  <label for='name'>Password *</label>
  <input name='password' value='' type='password' required />
</p>
<p>
  <label for='email'>Repeat Password *</label>
  <input name='password2' value='' type='password' required />
</p>
<hr />
<p>
  <label for='email'>Create sample content?</label>
  <input name='create_content' value='' type='checkbox' />
</p>
<div id="loading" style="text-align:center"></div>
<script type="text/javascript" language="javascript">
  $("input[type='submit']").click(function() {
    $(this).val(LANG_LOADING).attr('disabled',true);
  });

  $("input[name='password2']").keyup(function(){
    if ($("input[name='password']").val() === $("input[name='password2']").val()){
      $("input[type='submit']").attr('disabled',false);
    } else {
      $("input[type='submit']").attr('disabled');
    }
  });
</script>