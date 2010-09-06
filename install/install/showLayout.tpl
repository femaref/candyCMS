<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv='content-type' content='text/html;charset=utf-8' />
    <link href='%PATH_CSS%/essential.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <link href='%PATH_CSS%/style.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <script language='javascript' src='%PATH_PUBLIC%/js/core/mootools.js' type='text/javascript'></script>
    <title>{$title}</title>
  </head>
  <body>
    <div id='container'>
      <div id='body' style="text-align:left;padding:10px">
        <form action="index.php?step={$step}&action=install" method="post">
          %CONTENT%
          <div style="margin-top:30px;text-align:right">
            {if $step > 2}
              <input type="button" id="prevstep" value="Step back" onclick="stepBack({$step})"  />
            {/if}
            <input type="submit" id="nextstep" value="Next step ({$step})" />
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
{literal}
  <script type="text/javascript" language="javascript">
    function stepBack(step){
      var sPrevStep = step-2;
      location.href = "index.php?action=install&step=" + sPrevStep;
    }
  </script>
{/literal}