<h1>1. Select migration(s)</h1>
{foreach from=$files item=f}
  <div style="margin-bottom:20px">
    <a href="#" onclick="sendPost('{$f.name}')">
      {$f.name}
    </a>
    <div class="description" id="{$f.name}">
      {$f.query}
    </div>
  </div>
{/foreach}
<script type="text/javascript" language="javascript">
  var sAction = '{$action}';
  {literal}
    function sendPost(sFile) {
      $(sFile).set('html', "<img src='../../public/images/loading.gif' alt='' />");
      $(sFile).load(sAction + '?file=' + sFile + '&action=migrate');
    }
  {/literal}
</script>
