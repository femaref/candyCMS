<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv='content-type' content='text/html;charset=utf-8' />
    <link href='%PATH_CSS%/essential.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <link href='%PATH_CSS%/style.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery{$_compress_files_suffix_}.js"></script>
    <script type="text/javascript">
      if (typeof jQuery == 'undefined')
        document.write(unescape("%3Cscript src='%PATH_PUBLIC%/js/core/jquery.1.6.2{$_compress_files_suffix_}.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <style stype="text/css">
      div.hidden{ display:none }
      h3:hover{ cursor:pointer }
    </style>
    <title>{$title}</title>
  </head>
  <body>
    <div id='container'>
      <div id='body' style="text-align:left;padding:10px">
        <p class="error">
          This installation does only work <strong>without</strong> a SQL-Prefix and is without any warranty. <strong>CandyCMS
            might override your existing tables if their names match</strong>.
        </p>
        <form action="index.php?step={$step}&action=install" method="post">
          %CONTENT%
          <div id="steps" style="margin-top:30px;text-align:right">
            {if $step > 2}
              <input type="button" id="prevstep" value="Back" onclick="stepBack({$step})"  />
            {/if}
            <input type="submit" id="nextstep" value="Next ({$step})" disabled />
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
<script type="text/javascript" language="javascript">
  function stepBack(step){
    var sPrevStep = step-2;
    location.href = "index.php?action=install&step=" + sPrevStep;
  }
</script>