{foreach $files as $file}
{strip}
  <div class='js-hover' style="margin-bottom:20px">
    <h2>
      <a href="#" onclick="$('#{$file@index}').parents().first().load('?file={$file.name}&action=migrate');return false;">
        {$file.name}
      </a>
    </h2>
    <div id="{$file@index}" style="display: none;">
      {$file.query}
    </div>
  </div>
{/strip}
{/foreach}
<script type='text/javascript'>
  $('.js-hover').on('mouseenter mouseleave', function(e) {
    if (e.type == 'mouseenter') {
      $(this).find('div').slideDown('fast');
    } else {
      $(this).find('div').slideUp('fast');
    }
  })
</script>