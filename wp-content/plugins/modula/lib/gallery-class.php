<?php

define("MODULA_DB_MODE", 1);
define("MODULA_WP_MODE", 2);

if (!class_exists("ModulaFE")) {
	class ModulaFE {

		private $defaultValues;

		public function __construct($db, $id, $defaultValues)
		{
			$this->gallery = new stdClass();
			$this->db = $db;
			$this->wp_images = array();
			$this->images = array();
			$this->id = $id;
			$this->defaultValues = $defaultValues;
			$this->init();
		}

		public function init()
		{
			$this->gallery = $this->db->getGalleryById($this->id);
			foreach ( $this->defaultValues as $k => $v ) {
				if(! isset($this->gallery->$k))
					$this->gallery->$k = $v;
			}

			$this->gallery->mode = MODULA_DB_MODE;

			if(! empty($_GET["debug"]))
			{
				print "<!--\n";
				print "Gallery id: $this->id\n";
				print "settings:\n";
				print_r($this->gallery);
				print "\n-->\n";
			}

			if(! $this->gallery->hasResizedImages) {

				$images = $this->db->getImagesByGalleryId( $this->id );
				$images = ModulaTools::check_and_resize( $images, $this->gallery->img_size );
				foreach($images as $img) {
					$this->db->editImage( $img->Id, (array)$img );
				}
				$this->gallery->hasResizedImages = true;
				$this->db->editGallery($this->id, (array)$this->gallery);
			}

			$this->images = $this->loadModulaImages();


			if($this->gallery->shuffle == 'T')
			{
				shuffle( $this->images );
				$temp = array();
				foreach ($this->images as $image)
					$temp[$image->imageId] = $image;
				$this->images = $temp;
			}

			if($this->gallery->maxImagesCount > 0)
			{
				$index = 0;
				$reduced = array();
				foreach ($this->images as $k => $v)
				{
					if($index++ == $this->gallery->maxImagesCount)
						break;
					$reduced[$k] = $v;
				}

				$this->images = $reduced;
			}


			$ids = array();
			foreach($this->images as $img)
				$ids[] = $img->imageId;

			if(count($this->images) > 0)
				$this->loadWPImages($ids);
		}
        
        public function loadWPImages($ids)
        {
            $args = array(
				'post_type' => 'attachment',
				'posts_per_page' => -1,
				'include' => $ids
			);

	        $this->wp_images = get_posts( $args );
	        if ( $this->gallery->lightbox == "attachment-page" ) {
		        foreach ( $this->wp_images as $att ) {
			        $att->url = get_attachment_link( $att->ID );

			        if ( $this->gallery->mode == MODULA_DB_MODE ) {
				        //$this->images[$att->ID]->imagePath = $att->guid;
				        $this->images[ $att->ID ]->url = $att->url;
				        $this->images[ $att->ID ]->alt = get_post_meta( $att->ID, '_wp_attachment_image_alt', true );
			        }
		        }
	        }
        }
		
		public function initByImageIds($ids)
		{
			$this->imageIds = $ids;
			$this->gallery->mode = MODULA_WP_MODE;
			$this->loadWPImages($ids);
			
            foreach($this->wp_images as $att)
            {            
            	$image = new stdClass();
            	$image->imageId = $att->ID;
            	$image->url = $att->url;
            	$image->Id = $att->ID;
            	$image->imagePath = $att->guid;
            	$image->link = get_post_meta($att->ID, "_modula_link", true);

            	switch($this->gallery->wp_field_caption)
            	{
            		case 'title':
		            	$image->description = $att->post_title;
		            	break;
		            case 'caption':
		            	$image->description = $att->post_excerpt;
		            	break;
		            case 'description':
		            	$image->description = $att->post_content;
		            	break;
            	}
	            $this->images[$image->imageId] = $image;
	        }
	        
		}				
						
		public function getGallery()
		{
			return $this->gallery;
		}
		
		private function getLightboxClass($image)
		{
			if(! empty($image->link))
				return '';
				
			if(empty($this->gallery->lightbox))
				return '';
				
			return 'modula-lightbox';
		}
		
		private function getHoverEffect($code)
		{
			global $ob_Modula;
			foreach($ob_Modula->hoverEffects as $effect)
				if($effect->code == $code)
					return $effect;
		}
		
		private function getLink($image)
		{
			if(! empty($image->link))
				return "href='" . $image->link . "'";
						
			if(empty($this->gallery->lightbox))
				return '';
							
			if($this->gallery->lightbox == 'attachment-page')
				return "href='" . $image->url . "'";

			return "href='" . wp_get_attachment_url( $image->imageId ) . "'";
		}
		
		private function getTarget($image)
		{
			if(! empty($image->target))
				return "target='" . $image->target . "'";
						
			// if($this->gallery->blank == 'T')
			// 	return "target='_blank'";
							
			return '';
		}
		
		private function getdef($value, $default)
		{
			if($value == NULL || empty($value))
				return $default;
				
			return $value;
		}
        
        private function toRGB($Hex){
            
            if (substr($Hex,0,1) == "#")
                $Hex = substr($Hex,1);
            
            $R = substr($Hex,0,2);
            $G = substr($Hex,2,2);
            $B = substr($Hex,4,2);

            $R = hexdec($R);
            $G = hexdec($G);
            $B = hexdec($B);

            $RGB['R'] = $R;
            $RGB['G'] = $G;
            $RGB['B'] = $B;
            
            $RGB[0] = $R;
            $RGB[1] = $G;
            $RGB[2] = $B;

            return $RGB;

        }
		
		static public function slugify($text)
		{ 
		  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
		  $text = trim($text, '-');
		  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		  $text = strtolower($text);
		  $text = preg_replace('~[^-\w]+~', '', $text);

		  if (empty($text))
		  {
		    return 'n-a';
		  }

		  return $text;
		}

		static public function getFilters($filters)
		{
			if(empty($filters))
				return "";

			$css = array();
			foreach (explode("|", $filters) as $f) {
				$css[] = "jtg-filter-" . ModulaFE::slugify($f);
			}

			return implode(" ", $css);
		} 

		private function v($name)
		{
			switch($this->mode)
			{
				default:
				case MODULA_DB_MODE:
					return $this->gallery->$name;
					break;
				case MODULA_WP_MODE:
					return $this->settings[$name];
			}
		}

		public function render() 
		{	
			$rid = rand(1, 1000);			 


         
	    	$gallery = $this->gallery;         	

            $html = "";            

			$html .= "<style>\n";

			if($this->gallery->borderSize)
				$html .= "#jtg-$this->id$rid .item { border: " . $this->gallery->borderSize . "px solid " . $this->gallery->borderColor . "; }\n";

			$html .= "#jtg-$this->id$rid .item { background-color:".$this->gallery->hoverColor ."; }\n";
			
			if($this->gallery->hoverOpacity <= 100)
			{
				$html .= "#jtg-$this->id$rid .item:hover img { opacity: ".(1 - $this->gallery->hoverOpacity/100)."; }\n";
			}

			if($this->gallery->borderRadius)
				$html .= "#jtg-$this->id$rid .item { border-radius: " . $this->gallery->borderRadius . "px; }\n";

			if($this->gallery->shadowSize)
				$html .= "#jtg-$this->id$rid .item { box-shadow: " . $this->gallery->shadowColor . " " . $this->gallery->shadowSize . "px " . $this->gallery->shadowSize ."px " . $this->gallery->shadowSize . "px; }\n";   
            
			if($this->gallery->socialIconColor)
				$html .= "#jtg-$this->id$rid .item .jtg-social a { color: " . $this->gallery->socialIconColor . " }\n";

            $html .= "#jtg-$this->id$rid .item .caption { background-color: ".$this->gallery->captionColor.";  }\n";
            
            $html .= "#jtg-$this->id$rid .item .figc { color: ".$this->gallery->captionColor."; font-size: ".$this->gallery->captionFontSize."px; }\n";
            
            $html .= "#jtg-$this->id$rid .item .figc h2.jtg-title {  font-size: ".$this->gallery->titleFontSize."px; color: ".$this->gallery->captionColor."; }\n";
           
            $html .= "#jtg-$this->id$rid .item { transform: scale(" .$gallery->loadedScale/100 .") translate(" . $gallery->loadedHSlide . 'px,' . $gallery->loadedVSlide . "px) rotate(" . $gallery->loadedRotate .  "deg); }\n";

            
            $html .= "#jtg-$this->id$rid .items { width:".$this->gallery->width. "; height:".$this->gallery->height. "px; }\n";
            
            $html .= "#jtg-$this->id$rid .items .figc p.description { color:".$this->gallery->captionColor."; }\n";


			if(strlen($this->gallery->style))
				$html .= $this->gallery->style;



			$html .= "</style>\n";   
			$filter_click = $this->gallery->filterClick;
			$html .= "<input type='hidden' id='filterClick' value='$filter_click'>" ;             	           
			$current_filter = isset($_GET['jtg-filter']) ? $_GET['jtg-filter'] : null;

			$id = $this->id;
			$html .="<a name='$id'> </a>";
            $html .= "<div class='modula' id='jtg-$this->id$rid'>\n";
            if(strlen($this->gallery->filters))
            {
            	$filters = explode("|", $this->gallery->filters);
            	$filter_url = $this->gallery->filterClick == "F" ? '#' : '?jtg-filter=all';
            	$html .= "<div class='filters'>\n";
            	$html .= "\t<a data-filter='all' href='$filter_url' class='selected'>".$gallery->allFilterLabel."</a>\n";
            	foreach($filters as $filter)
            	{
            		$filter_url = $this->gallery->filterClick == "F" ? "#jtg-filter-". ModulaFE::slugify($filter) : '?jtg-filter=' . ModulaFE::slugify($filter) .'#' . $this->gallery->Id;
            		$html .= "\t<a data-filter='" . ModulaFE::slugify($filter) ."' href='$filter_url'>$filter</a>\n";
            	}
            	$html .= "</div>\n";
            }
            $html .= "<div class='items'>\n";
			foreach($this->images as $image)
			{	
				$img_filters = array_map('ModulaFE::slugify', explode('|', $image->filters));

				if($current_filter != null && $current_filter != "all" && !in_array($current_filter, $img_filters))
				{		
					continue;			
				}
				//$imgUrl = WP_PLUGIN_URL . '/modula/img.php?id=' . $image->imageId;
				
				$title = in_array($this->gallery->lightbox, array('prettyphoto', 'fancybox', 'swipebox', 'lightbox2')) ? "title" : "data-title";
				$rel = $this->gallery->lightbox == "prettyphoto" ? "prettyPhoto[jtg-$this->id$rid]" : "jtg-$this->id$rid";
            	$hoverEffect = $this->getHoverEffect($this->gallery->hoverEffect);
				$image->alt = isset( $image->alt ) ? $image->alt : '';

               	$hasTitle = empty($image->title) ? 'notitle' : '';

                $html .= "\t<div class=\"item " . $hasTitle . " " . ModulaFE::getFilters($image->filters) . " effect-". $hoverEffect->code ."\">\n";
                $html .= "<a data-sub-html='".htmlspecialchars($image->title . "<br>".$image->description, ENT_QUOTES)."' $title='$image->description' ". ($this->gallery->lightbox == "lightbox2" ? "data-lightbox='gallery'" : "") ." rel='$rel' " . $this->getTarget($image) . " class='tile-inner " . ($this->getLightboxClass($image)) . "' " . $this->getLink($image) . ">\n";
				$html .= "\t\t<img data-valign='$image->valign' alt='$image->alt' data-halign='$image->halign' class='pic' data-class='pic' src='$image->imagePath'  />\n";
				$html .= "\t\t<div class=\"figc\">\n";
				$html .= "\t\t\t<div class=\"figc-inner\">\n";
				if($this->gallery->wp_field_title != 'none')
					if(($hoverEffect->allowTitle && ! empty($image->title) || empty($this->gallery->hoverEffect)))
						$html .= "\t\t\t\t<h2 class='jtg-title'>".$image->title."</h2>\n";

				if($this->gallery->wp_field_caption != 'none')
					if(($hoverEffect->allowSubtitle && ! empty($image->description)) ||
						$hoverEffect->maxSocial > 0 || (empty($this->gallery->hoverEffect))) 
					{
						$html .= "\t\t\t\t\t<p class=\"description\">";											
						if($hoverEffect->allowSubtitle || empty($this->gallery->hoverEffect) )
							$html .= $image->description;
						$html .= "</p>\n";		
					}					
				$html .= "\t\t\t</div>\n";	
				$html .= "\t\t</div>\n";
				$html .= "\t</a>\n";
				$html .= "\t</div>\n";
			}

            $html .= "\t</div>\n";
            $html .= "</div>\n";
            
            $hoverEffect = $this->getHoverEffect($this->gallery->hoverEffect);
            
            $html .= "<script type='text/javascript'>\n";
            $html .= "\tjQuery('#jtg-$this->id$rid').modulaGallery({\n";
            
            if(strlen($this->gallery->script))
            {
            	$html .= "\t\tonComplete: function () { " . stripslashes($this->gallery->script) . "},\n";
            }
            
            $html .= "\t\tmargin: ". $this->gallery->margin .",\n";   
            // $html .= "\t\tkeepArea: " . ($this->gallery->keepArea == "T" ? "true" : "false") . ",\n";
			$html .= "\t\tenableTwitter: " . ($hoverEffect->maxSocial && $this->gallery->enableTwitter == "T" ? "true" : "false") . ",\n";
			$html .= "\t\tenableFacebook: " . ($hoverEffect->maxSocial && $this->gallery->enableFacebook == "T" ? "true" : "false") . ",\n";
			$html .= "\t\tenablePinterest: " . ($hoverEffect->maxSocial && $this->gallery->enablePinterest == "T" ? "true" : "false") . ",\n";
			$html .= "\t\tenableGplus: " . ($hoverEffect->maxSocial && $this->gallery->enableGplus == "T" ? "true" : "false") . ",\n";
			$html .= "\t\trandomFactor: " . ($this->gallery->randomFactor / 100) . ",\n";
			// $html .= "\t\tscrollEffect: '"  . ($this->gallery->scrollEffect) . "'\n";
            $html .= "\t});\n";			
            
			$html .= "\tjQuery(function () {\n";
			switch ($this->gallery->lightbox) {
				default:
					break;
				case 'magnific':
					$html .= "\t\tjQuery('#jtg-$this->id$rid').magnificPopup({type:'image', zoom: { enabled: true, duration: 300, easing: 'ease-in-out' }, image: { titleSrc: 'data-title' }, gallery: { enabled: true }, delegate: '.tile:not(.jtg-hidden) .modula-lightbox ' });\n";
					break;
				case 'prettyphoto':
					$html .= "\t\tjQuery('#jtg-$this->id$rid .tile a.modula-lightbox').prettyPhoto({social_tools:''});\n";
					break;
				case 'colorbox':
					$html .= "\t\tjQuery('#jtg-$this->id$rid .tile a.modula-lightbox').colorbox({rel: 'gallery', title: function () { return jQuery(this).data('title'); }});\n";
					break;
				case 'fancybox':
					$html .= "\t\tjQuery('#jtg-$this->id$rid .tile a.modula-lightbox').fancybox({});\n";
					break;
				case 'swipebox':
					$html .= "\t\tjQuery('#jtg-$this->id$rid .tile a.modula-lightbox').swipebox({});\n";
					break;
				case 'lightgallery':
					$html .= "\t\tjQuery('#jtg-$this->id$rid').lightGallery({ selector: 'a.modula-lightbox' });\n";
					break;	
			}

			$html .= "\t});\n";			 
			$html .= "</script>";


			if(! empty($_GET["debug"]))
				return $html;

			return str_replace(array("\n", "\t"), "", $html);
		}

		public function useCaptions()
		{
			if($this->gallery->wp_field_caption == "none" && $this->gallery->wp_field_title == "none")
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		
		public function loadModulaImages() 
		{
			$images = array();
			foreach($this->db->getImagesByGalleryId($this->id) as $img)
				$images[$img->imageId] = $img;
			return $images;
		}			
	}
}
?>