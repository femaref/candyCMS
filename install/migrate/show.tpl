{foreach from=$files item=f}
  <p id="{$f.name}">
    <a href="#" onclick="sendPost('{$f.name}')">
      {$f.name}
    </a>
    <br />
    <span class="description">
      {$f.query}
    </span>
  </p>
{/foreach}
<script type="text/javascript" language="javascript">
  var sAction = '{$action}';
  {literal}
    function sendPost(sFile) {
      $(sFile).setStyle('display', 'none');
      new Request({url: sAction}).send('file=' + sFile );
    }
  {/literal}
</script>
