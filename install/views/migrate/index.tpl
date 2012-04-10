<ul>
  {foreach $files as $file}
    <li>
      <a class='js-tooltip' href='#' title='{$file.query}'
          onclick="$(this).load('?file={$file.name}&action=migrate').parent().hide();return false;">
        {$file.name}
      </a>
    </li>
  {/foreach}
</ul>
<script type='text/javascript' src='../public/js/core/jquery.bootstrap.tooltip.js'></script>
<script type='text/javascript' src='../public/js/core/scripts.js'></script>