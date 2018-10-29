jQuery(function($) {
  // Only run this routine once
  var flag = 'nextgen_pro_film';
  if (typeof($(window).data(flag)) == 'undefined')
      $(window).data(flag, true);
  else return;
  
	var adaptFilmBoxes = function () {
    $('.nextgen_pro_film').each(function() {
        var $this = $(this);
        var images = $this.find('.image-wrapper a img');
        var tallest = 0;
        
        images.each(function (idx) {
        	var jimg = $(this);
        	
        	if (jimg.height() > tallest)
        		tallest = jimg.height();
        });
        
        if (tallest > 0) {
        	$this.find('.image-wrapper a').height(tallest);
        }
    });
	};
	
	$(window).on('orientationchange resize onfullscreenchange', function (e) {
		adaptFilmBoxes();
	});
	
	adaptFilmBoxes();
});
