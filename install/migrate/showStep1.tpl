<h1>1. Migrate database</h1>
{foreach from=$files item=f}
  <div id="{$f@index}" style="margin-bottom:20px">
    <h2 style="margin-bottom:0px">
      <a href="#" onclick="$('#{$f@index}').load('{$action}?file={$f.name}&action=migrate');return false;">
        {$f.name}
      </a>
    </h2>
    <textarea cols="85" rows="6">
      {$f.query}
    </textarea>
  </div>
{/foreach}
<a href="/install">Back to start</a>