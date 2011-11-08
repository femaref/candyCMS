<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv='content-type' content='text/html;charset=utf-8' />
    <link href='%PATH_CSS%/core/essential.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <link href='%PATH_CSS%/core/application.css' rel='stylesheet' type='text/css' media='screen, projection' />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery{$_compress_files_suffix_}.js"></script>
    <script type="text/javascript">
      if (typeof jQuery == 'undefined')
        document.write(unescape("%3Cscript src='%PATH_PUBLIC%/js/core/jquery.1.7{$_compress_files_suffix_}.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <title>{$title}</title>
  </head>
  <body>
    <div id='container'>
      <div id='body' style="text-align:left;padding:10px">
        <p class="error">
          This update does only work <strong>without</strong> a SQL-Prefix and is without any warranty.
          <br />
          If you used a prefix, modify the queries at "install/migrate/sql" manually.
        </p>
        %CONTENT%
      </div>
    </div>
  </body>
</html>