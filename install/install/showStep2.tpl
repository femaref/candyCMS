<h1>2. Enter your personal data</h1>
<p>
  <label for='input-email'>E-Mail *</label>
  <input name='email' value='' id="input-email" type='email' required />
</p>
<p>
  <label for='input-name'>Name *</label>
  <input name='name' value='' id="input-name" type='name' required />
</p>
<p>
  <label for='input-surname'>Surname *</label>
  <input name='surname' value=''id="input-surname" type='text' required />
</p>
<p>
  <label for='input-password'>Password *</label>
  <input name='password' value=''id="input-password" type='password' required />
</p>
<p>
  <label for='input-password2'>Repeat Password *</label>
  <input name='password2' value=''id="input-password2" type='password' required />
</p>
<hr />
<p>
  <label for='input-create_content'>Create sample content?</label>
  <input name='create_content'id="input-create_content" value='' type='checkbox' />
</p>
<div id="loading" style="text-align:center"></div>
<script type="text/javascript" language="javascript">
  $("input[type='submit']").click(function() {
    $(this).val('Loading...').attr('disabled',true);
  });

  $("input[name='password2']").keyup(function(){
    if ($("input[name='password']").val() === $("input[name='password2']").val()){
      $("input[type='submit']").attr('disabled',false);
    } else {
      $("input[type='submit']").attr('disabled');
    }
  });
</script>