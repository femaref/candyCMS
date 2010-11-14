<script language='javascript'
        src='%PATH_PUBLIC%/js/plugins/lazyload{$_compress_files_suffix_}.js'
        type='text/javascript'>
</script>
<script type="text/javascript">
  window.addEvent('domready',function() {
    var lazyloader = new LazyLoad({
      range: 0,
      image: '%PATH_IMAGES%/spacer.png',
      elements: '.image img',
      onLoad: function(img) {
        img.setStyle('opacity',0).fade(1);
      }
    });
  });
</script>
<style type="text/css">
  .gallery_files .image, .element .image {
    width:{$THUMB_DEFAULT_X}px;
    height:{$THUMB_DEFAULT_X}px;
    line-height:{$THUMB_DEFAULT_X}px;
  }

  .gallery_files .image img, .element .image img {
    vertical-align: middle;
  }
</style>