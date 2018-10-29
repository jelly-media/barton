/**
 * WonderPlugin Carousel Skin Options
 * Copyright 2017 Magic Hills Pty Ltd - http://www.wonderplugin.com
 */

var WONDERPLUGIN_3DCAROUSEL_SKIN_TEMPLATE = {
	
	carouselsliderwithdarkoverlay : {
		skintemplate: '<div class="wonderplugin3dcarousel-content">\n\t<div class="wonderplugin3dcarousel-image">__IMAGE__</div>\n</div>',
		skincss: '@import url(https://fonts.googleapis.com/css?family=Open+Sans);\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content {\n\tposition: relative;\n\tdisplay: block;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay {\n\tposition: absolute;\n\tdisplay: block;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tmargin: 0;\n\tpadding: 8px;\n\tbox-sizing: border-box;\n\tbackground-color: #f8f8f8;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-text {\n\tdisplay: block;\n\tposition: absolute;\n\twidth:100%;\n\ttop: 50%;\n\tleft:0;\n\ttransform: translateY(-50%);\n\t-webkit-transform: translateY(-50%);\n\tbackface-visibility: hidden;\n\t-webkit-backface-visibility: hidden;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay {\n\tdisplay: block;\n\tposition: absolute;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tbackground-color: #000;\n\topacity: 0.5;\n\ttransition: opacity 0.3s;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay:hover,\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-item-current .wonderplugin3dcarousel-img-overlay {\n\topacity: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-nav {\n\tdisplay: block;\n\tposition: relative;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}'
	},
	
	carouselsliderwithhoverover : {
		skintemplate: '<div class="wonderplugin3dcarousel-content">\n\t<div class="wonderplugin3dcarousel-image">__IMAGE__</div>\n</div>\n\n<div class="wonderplugin3dcarousel-hoveroverlay">\n\t<div class="wonderplugin3dcarousel-hoveroverlay-text">\n\t\t<div class="wonderplugin3dcarousel-hoveroverlay-title">__TITLE__</div>\n\t\t<div class="wonderplugin3dcarousel-hoveroverlay-description">__DESCRIPTION__</div>\n\t</div>\n</div>',
		skincss: '@import url(https://fonts.googleapis.com/css?family=Open+Sans);\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content {\n\tposition: relative;\n\tdisplay: block;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay {\n\tposition: absolute;\n\tdisplay: block;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tmargin: 0;\n\tpadding: 8px;\n\tbox-sizing: border-box;\n\tbackground-color: rgba(48,48,48,0.7);\n\tcolor: #fff;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-text {\n\tdisplay: block;\n\tposition: absolute;\n\twidth:100%;\n\ttop: 50%;\n\tleft:0;\n\ttransform: translateY(-50%);\n\t-webkit-transform: translateY(-50%);\n\tbackface-visibility: hidden;\n\t-webkit-backface-visibility: hidden;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay {\n\tdisplay: block;\n\tposition: absolute;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tbackground-color: #000;\n\topacity: 0.5;\n\ttransition: opacity 0.3s;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay:hover,\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-item-current .wonderplugin3dcarousel-img-overlay {\n\topacity: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-nav {\n\tdisplay: block;\n\tposition: relative;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}'
	},
		
	threedslider : {
		skintemplate: '<div class="wonderplugin3dcarousel-content">\n\t<div class="wonderplugin3dcarousel-image">__IMAGE__</div>\n</div>',
		skincss: '@import url(https://fonts.googleapis.com/css?family=Open+Sans);\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content {\n\tposition: relative;\n\tdisplay: block;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay {\n\tposition: absolute;\n\tdisplay: block;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tmargin: 0;\n\tpadding: 8px;\n\tbox-sizing: border-box;\n\tbackground-color: #f8f8f8;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-text {\n\tdisplay: block;\n\tposition: absolute;\n\twidth:100%;\n\ttop: 50%;\n\tleft:0;\n\ttransform: translateY(-50%);\n\t-webkit-transform: translateY(-50%);\n\tbackface-visibility: hidden;\n\t-webkit-backface-visibility: hidden;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay {\n\tdisplay: block;\n\tposition: absolute;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tbackground-color: #000;\n\topacity: 0.5;\n\ttransition: opacity 0.3s;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay:hover,\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-item-current .wonderplugin3dcarousel-img-overlay {\n\topacity: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-nav {\n\tdisplay: block;\n\tposition: relative;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}'
	},
	
	threedsliderwithtitle : {
		skintemplate: '<div class="wonderplugin3dcarousel-content">\n\t<div class="wonderplugin3dcarousel-image">__IMAGE__</div>\n\t<div class="wonderplugin3dcarousel-content-title">__TITLE__</div>\n</div>',
		skincss: '@import url(https://fonts.googleapis.com/css?family=Open+Sans);\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content {\n\tposition: relative;\n\tdisplay: block;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay {\n\tposition: absolute;\n\tdisplay: block;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tmargin: 0;\n\tpadding: 8px;\n\tbox-sizing: border-box;\n\tbackground-color: #f8f8f8;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-text {\n\tdisplay: block;\n\tposition: absolute;\n\twidth:100%;\n\ttop: 50%;\n\tleft:0;\n\ttransform: translateY(-50%);\n\t-webkit-transform: translateY(-50%);\n\tbackface-visibility: hidden;\n\t-webkit-backface-visibility: hidden;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay {\n\tdisplay: block;\n\tposition: absolute;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tbackground-color: #000;\n\topacity: 0.5;\n\ttransition: opacity 0.3s;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay:hover,\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-item-current .wonderplugin3dcarousel-img-overlay {\n\topacity: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-nav {\n\tdisplay: block;\n\tposition: relative;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}'
	},
	
	threedsliderwithtitleandflip : {
		skintemplate: '<div class="wonderplugin3dcarousel-content">\n\t<div class="wonderplugin3dcarousel-image">__IMAGE__</div>\n\t<div class="wonderplugin3dcarousel-content-title">__TITLE__</div>\n</div>\n\n<div class="wonderplugin3dcarousel-hoveroverlay">\n\t<div class="wonderplugin3dcarousel-hoveroverlay-text">\n\t\t<div class="wonderplugin3dcarousel-hoveroverlay-title">__TITLE__</div>\n\t\t<div class="wonderplugin3dcarousel-hoveroverlay-description">__DESCRIPTION__</div>\n\t</div>\n</div>',
		skincss: '@import url(https://fonts.googleapis.com/css?family=Open+Sans);\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content {\n\tposition: relative;\n\tdisplay: block;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay {\n\tposition: absolute;\n\tdisplay: block;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tmargin: 0;\n\tpadding: 8px;\n\tbox-sizing: border-box;\n\tbackground-color: #f8f8f8;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-text {\n\tdisplay: block;\n\tposition: absolute;\n\twidth:100%;\n\ttop: 50%;\n\tleft:0;\n\ttransform: translateY(-50%);\n\t-webkit-transform: translateY(-50%);\n\tbackface-visibility: hidden;\n\t-webkit-backface-visibility: hidden;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay {\n\tdisplay: block;\n\tposition: absolute;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tbackground-color: #000;\n\topacity: 0.5;\n\ttransition: opacity 0.3s;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay:hover,\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-item-current .wonderplugin3dcarousel-img-overlay {\n\topacity: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-nav {\n\tdisplay: block;\n\tposition: relative;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}'
	},
	
	threedsliderwithdarkoverlay : {
		skintemplate: '<div class="wonderplugin3dcarousel-content">\n\t<div class="wonderplugin3dcarousel-image">__IMAGE__</div>\n</div>',
		skincss: '@import url(https://fonts.googleapis.com/css?family=Open+Sans);\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content {\n\tposition: relative;\n\tdisplay: block;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay {\n\tposition: absolute;\n\tdisplay: block;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tmargin: 0;\n\tpadding: 8px;\n\tbox-sizing: border-box;\n\tbackground-color: #f8f8f8;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-text {\n\tdisplay: block;\n\tposition: absolute;\n\twidth:100%;\n\ttop: 50%;\n\tleft:0;\n\ttransform: translateY(-50%);\n\t-webkit-transform: translateY(-50%);\n\tbackface-visibility: hidden;\n\t-webkit-backface-visibility: hidden;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay {\n\tdisplay: block;\n\tposition: absolute;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tbackground-color: #000;\n\topacity: 0.5;\n\ttransition: opacity 0.3s;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay:hover,\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-item-current .wonderplugin3dcarousel-img-overlay {\n\topacity: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-nav {\n\tdisplay: block;\n\tposition: relative;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}'
	},
	
	threedsliderwithflip : {
		skintemplate: '<div class="wonderplugin3dcarousel-content">\n\t<div class="wonderplugin3dcarousel-image">__IMAGE__</div>\n</div>\n\n<div class="wonderplugin3dcarousel-hoveroverlay">\n\t<div class="wonderplugin3dcarousel-hoveroverlay-text">\n\t\t<div class="wonderplugin3dcarousel-hoveroverlay-title">__TITLE__</div>\n\t\t<div class="wonderplugin3dcarousel-hoveroverlay-description">__DESCRIPTION__</div>\n\t</div>\n</div>',
		skincss: '@import url(https://fonts.googleapis.com/css?family=Open+Sans);\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content {\n\tposition: relative;\n\tdisplay: block;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay {\n\tposition: absolute;\n\tdisplay: block;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tmargin: 0;\n\tpadding: 8px;\n\tbox-sizing: border-box;\n\tbackground-color: #f8f8f8;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-text {\n\tdisplay: block;\n\tposition: absolute;\n\twidth:100%;\n\ttop: 50%;\n\tleft:0;\n\ttransform: translateY(-50%);\n\t-webkit-transform: translateY(-50%);\n\tbackface-visibility: hidden;\n\t-webkit-backface-visibility: hidden;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay {\n\tdisplay: block;\n\tposition: absolute;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tbackground-color: #000;\n\topacity: 0.5;\n\ttransition: opacity 0.3s;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay:hover,\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-item-current .wonderplugin3dcarousel-img-overlay {\n\topacity: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-nav {\n\tdisplay: block;\n\tposition: relative;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}'
	},
	
	threedsliderwithfade : {
		skintemplate: '<div class="wonderplugin3dcarousel-content">\n\t<div class="wonderplugin3dcarousel-image">__IMAGE__</div>\n</div>\n\n<div class="wonderplugin3dcarousel-hoveroverlay">\n\t<div class="wonderplugin3dcarousel-hoveroverlay-text">\n\t\t<div class="wonderplugin3dcarousel-hoveroverlay-title">__TITLE__</div>\n\t\t<div class="wonderplugin3dcarousel-hoveroverlay-description">__DESCRIPTION__</div>\n\t</div>\n</div>',
		skincss: '@import url(https://fonts.googleapis.com/css?family=Open+Sans);\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content {\n\tposition: relative;\n\tdisplay: block;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay {\n\tposition: absolute;\n\tdisplay: block;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tmargin: 0;\n\tpadding: 8px;\n\tbox-sizing: border-box;\n\tbackground-color: #f8f8f8;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-text {\n\tdisplay: block;\n\tposition: absolute;\n\twidth:100%;\n\ttop: 50%;\n\tleft:0;\n\ttransform: translateY(-50%);\n\t-webkit-transform: translateY(-50%);\n\tbackface-visibility: hidden;\n\t-webkit-backface-visibility: hidden;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay {\n\tdisplay: block;\n\tposition: absolute;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tbackground-color: #000;\n\topacity: 0.5;\n\ttransition: opacity 0.3s;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay:hover,\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-item-current .wonderplugin3dcarousel-img-overlay {\n\topacity: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-nav {\n\tdisplay: block;\n\tposition: relative;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}'
	},
	
	threedcarousel : {
		skintemplate: '<div class="wonderplugin3dcarousel-content">\n\t<div class="wonderplugin3dcarousel-image">__IMAGE__</div>\n</div>',
		skincss: '@import url(https://fonts.googleapis.com/css?family=Open+Sans);\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content {\n\tposition: relative;\n\tdisplay: block;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay {\n\tposition: absolute;\n\tdisplay: block;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tmargin: 0;\n\tpadding: 8px;\n\tbox-sizing: border-box;\n\tbackground-color: #f8f8f8;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-text {\n\tdisplay: block;\n\tposition: absolute;\n\twidth:100%;\n\ttop: 50%;\n\tleft:0;\n\ttransform: translateY(-50%);\n\t-webkit-transform: translateY(-50%);\n\tbackface-visibility: hidden;\n\t-webkit-backface-visibility: hidden;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay {\n\tdisplay: block;\n\tposition: absolute;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tbackground-color: #000;\n\topacity: 0.5;\n\ttransition: opacity 0.3s;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay:hover,\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-item-current .wonderplugin3dcarousel-img-overlay {\n\topacity: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-nav {\n\tdisplay: block;\n\tposition: relative;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}'
	},
	
	threedcarouselwithdarkoverlay: {
		skintemplate: '<div class="wonderplugin3dcarousel-content">\n\t<div class="wonderplugin3dcarousel-image">__IMAGE__</div>\n</div>',
		skincss: '@import url(https://fonts.googleapis.com/css?family=Open+Sans);\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content {\n\tposition: relative;\n\tdisplay: block;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay {\n\tposition: absolute;\n\tdisplay: block;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tmargin: 0;\n\tpadding: 8px;\n\tbox-sizing: border-box;\n\tbackground-color: #f8f8f8;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-text {\n\tdisplay: block;\n\tposition: absolute;\n\twidth:100%;\n\ttop: 50%;\n\tleft:0;\n\ttransform: translateY(-50%);\n\t-webkit-transform: translateY(-50%);\n\tbackface-visibility: hidden;\n\t-webkit-backface-visibility: hidden;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay {\n\tdisplay: block;\n\tposition: absolute;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tbackground-color: #000;\n\topacity: 0.5;\n\ttransition: opacity 0.3s;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay:hover,\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-item-current .wonderplugin3dcarousel-img-overlay {\n\topacity: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-nav {\n\tdisplay: block;\n\tposition: relative;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}'
	},
	
	threedcarouselwithfrontface : {
		skintemplate: '<div class="wonderplugin3dcarousel-content">\n\t<div class="wonderplugin3dcarousel-image">__IMAGE__</div>\n</div>\n\n<div class="wonderplugin3dcarousel-hoveroverlay">\n\t<div class="wonderplugin3dcarousel-hoveroverlay-text">\n\t\t<div class="wonderplugin3dcarousel-hoveroverlay-title">__TITLE__</div>\n\t\t<div class="wonderplugin3dcarousel-hoveroverlay-description">__DESCRIPTION__</div>\n\t</div>\n</div>',
		skincss: '@import url(https://fonts.googleapis.com/css?family=Open+Sans);\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 8px auto;\n\tpadding: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-title {\n\ttext-align: center; \n\tfont-size: 16px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-description {\n\ttext-align: center;\n\tfont-size: 12px;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-content {\n\tposition: relative;\n\tdisplay: block;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay {\n\tposition: absolute;\n\tdisplay: block;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tmargin: 0;\n\tpadding: 8px;\n\tbox-sizing: border-box;\n\tbackground-color: #f8f8f8;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-hoveroverlay-text {\n\tdisplay: block;\n\tposition: absolute;\n\twidth:100%;\n\ttop: 50%;\n\tleft:0;\n\ttransform: translateY(-50%);\n\t-webkit-transform: translateY(-50%);\n\tbackface-visibility: hidden;\n\t-webkit-backface-visibility: hidden;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay {\n\tdisplay: block;\n\tposition: absolute;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 100%;\n\tbackground-color: #000;\n\topacity: 0.5;\n\ttransition: opacity 0.3s;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-img-overlay:hover,\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-item-current .wonderplugin3dcarousel-img-overlay {\n\topacity: 0;\n}\n\n#wonderplugin3dcarousel-3DCAROUSELID .wonderplugin3dcarousel-nav {\n\tdisplay: block;\n\tposition: relative;\n\tmargin: 0 auto;\n\tpadding: 8px;\n}'
	}
};

var WONDERPLUGIN_3DCAROUSEL_SKIN_OPTIONS = {
	
	threedslider: {
		
		template: 					'3dslider',
		
		// image
		imghovereffect:				'none',
		carouselmargin:				'48px 0',
		medium_carouselmargin:		'48px 0',
		small_carouselmargin:		'32px 0',
		
		// arrows
		arrowsinsidelist:			true,
		arrowstyle:					'mouseover',
		arrowpos:					'side',
		arrowimage:					'arrows-48-48-1.png',
		arrowwidth:					48,
		arrowheight:				48,
		arrowanimation:				'slide',					
		
		// bullets
		navstyle:					'bullets',					
		navwidth:					16,
		navheight:					16,
		navspacing:					8,			
		navimage:					'bullet-16-16-0.png',
						
		// SIZE
		width: 						400,
		height: 					300,
		visibleitems: 				5,
		perspective: 				2000,
		xdis: 						300,
		zdis: 						300,
		yrotate: 					45,
		transition: 				'all 0.5s ease-in-out',
		
		medium_screenwidth:			768,
		medium_width: 				400,
		medium_height:				300,
		medium_visibleitems: 		3,
		medium_perspective: 		2000,
		medium_xdis: 				300,
		medium_zdis: 				300,
		medium_yrotate: 			45,
		medium_transition: 			'all 0.5s ease-in-out',
		
		small_screenwidth:			414,
		small_width: 				300,
		small_height:				200,
		small_visibleitems: 		1,
		small_perspective: 			2000,
		small_xdis: 				300,
		small_zdis: 				300,
		small_yrotate: 				45,
		small_transition: 			'all 0.5s ease-in-out'
	},
	
	threedsliderwithtitle: {
		
		template: 					'3dslider',
		
		// image
		imghovereffect:				'flipy',
		carouselmargin:				'48px 0',
		medium_carouselmargin:		'48px 0',
		small_carouselmargin:		'32px 0',
		itembgcolor:				'#fff',
		itemborder:					6,
		
		// text
		showtitle:					false,
		
		// arrows
		arrowsinsidelist:			true,
		arrowstyle:					'mouseover',
		arrowpos:					'side',
		arrowimage:					'arrows-48-48-4.png',
		arrowwidth:					48,
		arrowheight:				48,
		arrowanimation:				'slide',					
		
		// bullets
		navstyle:					'bullets',					
		navwidth:					16,
		navheight:					16,
		navspacing:					8,			
		navimage:					'bullet-16-16-0.png',
						
		// SIZE
		width: 						400,
		height: 					300,
		visibleitems: 				5,
		perspective: 				2000,
		xdis: 						300,
		zdis: 						300,
		yrotate: 					45,
		transition: 				'all 0.5s ease-in-out',
		
		medium_screenwidth:			768,
		medium_width: 				400,
		medium_height:				300,
		medium_visibleitems: 		3,
		medium_perspective: 		2000,
		medium_xdis: 				300,
		medium_zdis: 				300,
		medium_yrotate: 			45,
		medium_transition: 			'all 0.5s ease-in-out',
		
		small_screenwidth:			414,
		small_width: 				300,
		small_height:				200,
		small_visibleitems: 		1,
		small_perspective: 			2000,
		small_xdis: 				300,
		small_zdis: 				300,
		small_yrotate: 				45,
		small_transition: 			'all 0.5s ease-in-out'
	},
	
	threedsliderwithtitleandflip: {
		
		template: 					'3dslider',
		
		// image
		imghovereffect:				'flipy',
		carouselmargin:				'48px 0',
		medium_carouselmargin:		'48px 0',
		small_carouselmargin:		'32px 0',
		itembgcolor:				'#fff',
		itemborder:					6,
		
		// text
		showtitle:					false,
		
		// arrows
		arrowsinsidelist:			true,
		arrowstyle:					'mouseover',
		arrowpos:					'side',
		arrowimage:					'arrows-32-32-2.png',
		arrowwidth:					32,
		arrowheight:				32,
		arrowanimation:				'slide',					
		
		// bullets
		navstyle:					'bullets',					
		navwidth:					16,
		navheight:					16,
		navspacing:					8,			
		navimage:					'bullet-16-16-0.png',
						
		// SIZE
		width: 						400,
		height: 					300,
		visibleitems: 				5,
		perspective: 				2000,
		xdis: 						300,
		zdis: 						300,
		yrotate: 					45,
		transition: 				'all 0.5s ease-in-out',
		
		medium_screenwidth:			768,
		medium_width: 				400,
		medium_height:				300,
		medium_visibleitems: 		3,
		medium_perspective: 		2000,
		medium_xdis: 				300,
		medium_zdis: 				300,
		medium_yrotate: 			45,
		medium_transition: 			'all 0.5s ease-in-out',
		
		small_screenwidth:			414,
		small_width: 				300,
		small_height:				200,
		small_visibleitems: 		1,
		small_perspective: 			2000,
		small_xdis: 				300,
		small_zdis: 				300,
		small_yrotate: 				45,
		small_transition: 			'all 0.5s ease-in-out'
	},
	
	carouselsliderwithdarkoverlay: {
		
		template: 					'3dslider',
		
		// image
		addimgoverlay:				true,
		imghovereffect:				'none',
		carouselmargin:				'48px 0',
		medium_carouselmargin:		'48px 0',
		small_carouselmargin:		'32px 0',
		
		// arrows
		arrowsinsidelist:			true,
		arrowstyle:					'mouseover',
		arrowpos:					'side',
		arrowimage:					'arrows-48-48-1.png',
		arrowwidth:					48,
		arrowheight:				48,
		arrowanimation:				'slide',					
		
		// bullets
		navstyle:					'bullets',					
		navwidth:					16,
		navheight:					16,
		navspacing:					8,			
		navimage:					'bullet-16-16-0.png',
						
		// SIZE
		width: 						400,
		height: 					300,
		visibleitems: 				5,
		perspective: 				2000,
		xdis: 						400,
		zdis: 						0,
		yrotate: 					0,
		transition: 				'all 0.5s ease-in-out',
		
		medium_screenwidth:			768,
		medium_width: 				400,
		medium_height:				300,
		medium_visibleitems: 		3,
		medium_perspective: 		2000,
		medium_xdis: 				300,
		medium_zdis: 				0,
		medium_yrotate: 			0,
		medium_transition: 			'all 0.5s ease-in-out',
		
		small_screenwidth:			414,
		small_width: 				300,
		small_height:				200,
		small_visibleitems: 		1,
		small_perspective: 			2000,
		small_xdis: 				300,
		small_zdis: 				0,
		small_yrotate: 				0,
		small_transition: 			'all 0.5s ease-in-out'
	},
	
	carouselsliderwithhoverover: {
		
		template: 					'3dslider',
		
		showtitle:					false,
		showdescription:			false,
		
		// image
		addimgoverlay:				false,
		imghovereffect:				'fade',
		carouselmargin:				'48px 0',
		medium_carouselmargin:		'48px 0',
		small_carouselmargin:		'32px 0',
		
		// arrows
		arrowsinsidelist:			true,
		arrowstyle:					'mouseover',
		arrowpos:					'side',
		arrowimage:					'arrows-48-48-1.png',
		arrowwidth:					48,
		arrowheight:				48,
		arrowanimation:				'slide',					
		
		// bullets
		navstyle:					'bullets',					
		navwidth:					16,
		navheight:					16,
		navspacing:					8,			
		navimage:					'bullet-16-16-0.png',
						
		// SIZE
		width: 						400,
		height: 					300,
		visibleitems: 				5,
		perspective: 				2000,
		xdis: 						400,
		zdis: 						0,
		yrotate: 					0,
		transition: 				'all 0.5s ease-in-out',
		
		medium_screenwidth:			768,
		medium_width: 				400,
		medium_height:				300,
		medium_visibleitems: 		3,
		medium_perspective: 		2000,
		medium_xdis: 				300,
		medium_zdis: 				0,
		medium_yrotate: 			0,
		medium_transition: 			'all 0.5s ease-in-out',
		
		small_screenwidth:			414,
		small_width: 				300,
		small_height:				200,
		small_visibleitems: 		1,
		small_perspective: 			2000,
		small_xdis: 				300,
		small_zdis: 				0,
		small_yrotate: 				0,
		small_transition: 			'all 0.5s ease-in-out'
	},
	
	threedsliderwithdarkoverlay: {
		
		template: 					'3dslider',
		
		// image
		addimgoverlay:				true,
		imghovereffect:				'none',
		carouselmargin:				'48px 0',
		medium_carouselmargin:		'48px 0',
		small_carouselmargin:		'32px 0',
		
		// arrows
		arrowsinsidelist:			true,
		arrowstyle:					'mouseover',
		arrowpos:					'side',
		arrowimage:					'arrows-48-48-1.png',
		arrowwidth:					48,
		arrowheight:				48,
		arrowanimation:				'slide',					
		
		// bullets
		navstyle:					'bullets',					
		navwidth:					16,
		navheight:					16,
		navspacing:					8,			
		navimage:					'bullet-16-16-0.png',
						
		// SIZE
		width: 						400,
		height: 					300,
		visibleitems: 				5,
		perspective: 				2000,
		xdis: 						300,
		zdis: 						300,
		yrotate: 					45,
		transition: 				'all 0.5s ease-in-out',
		
		medium_screenwidth:			768,
		medium_width: 				400,
		medium_height:				300,
		medium_visibleitems: 		3,
		medium_perspective: 		2000,
		medium_xdis: 				300,
		medium_zdis: 				300,
		medium_yrotate: 			45,
		medium_transition: 			'all 0.5s ease-in-out',
		
		small_screenwidth:			414,
		small_width: 				300,
		small_height:				200,
		small_visibleitems: 		1,
		small_perspective: 			2000,
		small_xdis: 				300,
		small_zdis: 				300,
		small_yrotate: 				45,
		small_transition: 			'all 0.5s ease-in-out'
	},
	
	threedsliderwithflip: {
		
		template: 					'3dslider',
		
		// image
		imghovereffect:				'flipy',
		carouselmargin:				'48px 0',
		medium_carouselmargin:		'48px 0',
		small_carouselmargin:		'32px 0',
		
		// texts
		textstyle:					'none',
		
		// arrows
		arrowsinsidelist:			true,
		arrowstyle:					'mouseover',
		arrowpos:					'side',
		arrowimage:					'arrows-48-48-1.png',
		arrowwidth:					48,
		arrowheight:				48,
		arrowanimation:				'slide',					
		
		// bullets
		navstyle:					'bullets',					
		navwidth:					16,
		navheight:					16,
		navspacing:					8,			
		navimage:					'bullet-16-16-0.png',
						
		// SIZE
		width: 						400,
		height: 					300,
		visibleitems: 				5,
		perspective: 				2000,
		xdis: 						300,
		zdis: 						300,
		yrotate: 					45,
		transition: 				'all 0.5s ease-in-out',
		
		medium_screenwidth:			768,
		medium_width: 				400,
		medium_height:				300,
		medium_visibleitems: 		3,
		medium_perspective: 		2000,
		medium_xdis: 				300,
		medium_zdis: 				300,
		medium_yrotate: 			45,
		medium_transition: 			'all 0.5s ease-in-out',
		
		small_screenwidth:			414,
		small_width: 				300,
		small_height:				200,
		small_visibleitems: 		1,
		small_perspective: 			2000,
		small_xdis: 				300,
		small_zdis: 				300,
		small_yrotate: 				45,
		small_transition: 			'all 0.5s ease-in-out'
	},
	
	threedsliderwithfade: {
		
		template: 					'3dslider',
		
		// image
		imghovereffect:				'fade',
		carouselmargin:				'48px 0',
		medium_carouselmargin:		'48px 0',
		small_carouselmargin:		'32px 0',
		
		// texts
		textstyle:					'none',
		
		// arrows
		arrowsinsidelist:			true,
		arrowstyle:					'mouseover',
		arrowpos:					'side',
		arrowimage:					'arrows-48-48-1.png',
		arrowwidth:					48,
		arrowheight:				48,
		arrowanimation:				'slide',					
		
		// bullets
		navstyle:					'bullets',					
		navwidth:					16,
		navheight:					16,
		navspacing:					8,			
		navimage:					'bullet-16-16-0.png',
						
		// SIZE
		width: 						400,
		height: 					300,
		visibleitems: 				5,
		perspective: 				2000,
		xdis: 						300,
		zdis: 						300,
		yrotate: 					45,
		transition: 				'all 0.5s ease-in-out',
		
		medium_screenwidth:			768,
		medium_width: 				400,
		medium_height:				300,
		medium_visibleitems: 		3,
		medium_perspective: 		2000,
		medium_xdis: 				300,
		medium_zdis: 				300,
		medium_yrotate: 			45,
		medium_transition: 			'all 0.5s ease-in-out',
		
		small_screenwidth:			414,
		small_width: 				300,
		small_height:				200,
		small_visibleitems: 		1,
		small_perspective: 			2000,
		small_xdis: 				300,
		small_zdis: 				300,
		small_yrotate: 				45,
		small_transition: 			'all 0.5s ease-in-out'
	},
	
	threedcarousel: {
		
		template: 					'3dcarousel',
		
		// image
		imghovereffect:				'none',
		carouselmargin:				'96px 0 32px',
		medium_carouselmargin:		'48px 0 16px',
		small_carouselmargin:		'0',
		
		// arrows
		arrowsinsidelist:			false,
		arrowstyle:					'always',	
		arrowpos:					'bottom',
		arrowimage:					'arrows-48-48-1.png',
		arrowwidth:					48,
		arrowheight:				48,
		arrowanimation:				'slide',					
		
		// bullets
		navstyle:					'none',
						
		// SIZE
		width: 						300,
		height: 					200,
		
		medium_screenwidth:			768,
		small_screenwidth:			414,
		medium_width: 				300,
		medium_height:				200,
		small_width: 				300,
		small_height:				200,
		
		facemode:					'circle', 
		itemspace:					64,
		rotatex:					-8,
		scaleratio:					1.2
	},
	
	threedcarouselwithdarkoverlay: {
		
		template: 					'3dcarousel',
		
		// image
		addimgoverlay:				true,
		imghovereffect:				'none',
		carouselmargin:				'96px 0 32px',
		medium_carouselmargin:		'48px 0 16px',
		small_carouselmargin:		'0',
		
		// arrows
		arrowsinsidelist:			false,
		arrowstyle:					'always',	
		arrowpos:					'bottom',
		arrowimage:					'arrows-32-32-2.png',
		arrowwidth:					32,
		arrowheight:				32,
		arrowanimation:				'slide',					
		
		// bullets
		navstyle:					'none',
						
		// SIZE
		width: 						300,
		height: 					200,
		
		medium_screenwidth:			768,
		small_screenwidth:			414,
		medium_width: 				300,
		medium_height:				200,
		small_width: 				300,
		small_height:				200,
		
		facemode:					'circle', 
		itemspace:					64,
		rotatex:					-8,
		scaleratio:					1.2
	},
	
	threedcarouselwithfrontface: {
		
		template: 					'3dcarousel',
		
		// image
		imghovereffect:				'flipy',
		carouselmargin:				'128px 0 32px',
		medium_carouselmargin:		'48px 0 16px',
		small_carouselmargin:		'0',
		
		// arrows
		arrowsinsidelist:			false,
		arrowstyle:					'always',	
		arrowpos:					'bottom',
		arrowimage:					'arrows-48-48-1.png',
		arrowwidth:					48,
		arrowheight:				48,
		arrowanimation:				'slide',					
		
		// bullets
		navstyle:					'none',
						
		// SIZE
		width: 						300,
		height: 					200,
		
		medium_screenwidth:			768,
		small_screenwidth:			414,
		medium_width: 				300,
		medium_height:				200,
		small_width: 				300,
		small_height:				200,
		
		facemode:					'front', 
		itemspace:					96,
		rotatex:					-10,
		scaleratio:					1.2
	}
};