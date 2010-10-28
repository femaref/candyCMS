<script language='javascript'
        src='%PATH_PUBLIC%/js/plugins/lazyload{$_compress_files_suffix_}.js'
        type='text/javascript'>
</script>
<script type="text/javascript">
  window.addEvent('domready',function() {
    var lazyloader = new LazyLoad({
      range: {$_thumb_default_x_},
      image: '%PATH_IMAGES%/spacer.gif',
      elements: '.image'
    });
  });
</script>
<style type="text/css">
  .gallery_files .image, .element .image {
    width:{$_thumb_default_x_}px;
    height:{$_thumb_default_x_}px;
  }
</style>