<?php

class WonderPlugin_3DCarousel_Creator {

	private $parent_view, $list_table;
	
	function __construct($parent) {
		
		$this->parent_view = $parent;
	}
	
	function render( $id, $config, $thumbnailsize ) {
		
		?>
		
		<h3><?php _e( 'General Options', 'wonderplugin_3dcarousel' ); ?></h3>
		
		<div id="wonderplugin-3dcarousel-id" style="display:none;"><?php echo $id; ?></div>
		
		<?php 
		$config = str_replace('\\\"', '"', $config);
		$config = str_replace("\\\'", "'", $config);
		$config = str_replace("<", "&lt;", $config);
		$config = str_replace(">", "&gt;", $config);
		$config = str_replace("\\\\", "\\", $config);
		$config = str_replace("&quot;", "\&quot;", $config);
		?>
		
		<div id="wonderplugin-3dcarousel-id-config" style="display:none;"><?php echo $config; ?></div>
		<div id="wonderplugin-3dcarousel-pluginfolder" style="display:none;"><?php echo WONDERPLUGIN_3DCAROUSEL_URL; ?></div>
		<div id="wonderplugin-3dcarousel-jsfolder" style="display:none;"><?php echo WONDERPLUGIN_3DCAROUSEL_URL . 'engine/'; ?></div>
		<div id="wonderplugin-3dcarousel-viewadminurl" style="display:none;"><?php echo admin_url('admin.php?page=wonderplugin_3dcarousel_show_item'); ?></div>
		<div id="wonderplugin-3dcarousel-wp-history-media-uploader" style="display:none;"><?php echo ( function_exists("wp_enqueue_media") ? "0" : "1"); ?></div>
		<div id="wonderplugin-3dcarousel-thumbnailsize" style="display:none;"><?php echo $thumbnailsize; ?></div>
		<div id="wonderplugin-3dcarousel-ajaxnonce" style="display:none;"><?php echo wp_create_nonce( 'wonderplugin-3dcarousel-ajaxnonce' ); ?></div>
		<div id="wonderplugin-3dcarousel-saveformnonce" style="display:none;"><?php wp_nonce_field('wonderplugin-3dcarousel', 'wonderplugin-3dcarousel-saveform'); ?></div>
		<?php 
			$cats = get_categories();
			$catlist = array();
			foreach ( $cats as $cat )
			{
				$catlist[] = array(
						'ID' => $cat->cat_ID,
						'cat_name' => $cat ->cat_name
				);
			}
		?>
		<div id="wonderplugin-3dcarousel-catlist" style="display:none;"><?php echo json_encode($catlist); ?></div>

		<?php 
		$custom_post_types = get_post_types( array('_builtin' => false), 'objects' );
	
		$custom_post_list = array();
		foreach($custom_post_types as $custom_post)
		{
			$custom_post_list[] = array(
					'name' => $custom_post->name,
					'taxonomies' => array()
				);
		}

		foreach($custom_post_list as &$custom_post)
		{
			$taxonomies = get_object_taxonomies($custom_post['name'], 'objects');			
			if (!empty($taxonomies))
			{
				
				$taxonomies_list = array();
				foreach($taxonomies as $taxonomy)
				{
					$terms = get_terms($taxonomy->name);
					
					$terms_list = array();
					foreach($terms as $term)
					{
						$terms_list[] = array(
								'name' => $term->name,
								'slug' => $term->slug
							);
					}

					$taxonomies_list[] = array(
							'name' => $taxonomy->name,
							'terms' => $terms_list
						);
				}
				
				$custom_post['taxonomies'] = $taxonomies_list;
			}
		}
		?>
		<div id="wonderplugin-3dcarousel-custompostlist" style="display:none;"><?php echo json_encode($custom_post_list); ?></div>
		
		<div style="margin:0 12px;">
		<table class="wonderplugin-form-table">
			<tr>
				<th><?php _e( 'Name', 'wonderplugin_3dcarousel' ); ?></th>
				<td><input name="wonderplugin-3dcarousel-name" type="text" id="wonderplugin-3dcarousel-name" value="My Carousel" class="regular-text" /></td>
			</tr>
		</table>
		</div>
		
		<h3><?php _e( 'Designing', 'wonderplugin_3dcarousel' ); ?></h3>
		
		<div style="margin:0 12px;">
		<ul class="wonderplugin-tab-buttons" id="wonderplugin-3dcarousel-toolbar">
			<li class="wonderplugin-tab-button step1 wonderplugin-tab-buttons-selected"><?php _e( 'Images & Videos', 'wonderplugin_3dcarousel' ); ?></li>
			<li class="wonderplugin-tab-button step2"><?php _e( 'Skins', 'wonderplugin_3dcarousel' ); ?></li>
			<li class="wonderplugin-tab-button step3"><?php _e( 'Options', 'wonderplugin_3dcarousel' ); ?></li>
			<li class="wonderplugin-tab-button step4"><?php _e( 'Preview', 'wonderplugin_3dcarousel' ); ?></li>
			<li class="laststep"><input class="button button-primary" type="button" value="<?php _e( 'Save & Publish', 'wonderplugin_3dcarousel' ); ?>"></input></li>
		</ul>
				
		<ul class="wonderplugin-tabs" id="wonderplugin-3dcarousel-tabs">
			<li class="wonderplugin-tab wonderplugin-tab-selected">	
			
				<div class="wonderplugin-toolbar">	
					<input type="button" class="button" id="wonderplugin-add-image" value="<?php _e( 'Add Image', 'wonderplugin_3dcarousel' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-video" value="<?php _e( 'Add Video', 'wonderplugin_3dcarousel' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-youtube" value="<?php _e( 'Add YouTube', 'wonderplugin_3dcarousel' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-youtube-playlist" value="<?php _e( 'Add YouTube Playlist', 'wonderplugin_3dcarousel' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-vimeo" value="<?php _e( 'Add Vimeo', 'wonderplugin_3dcarousel' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-posts" value="<?php _e( 'Add WordPress Posts', 'wonderplugin_3dcarousel' ); ?>" />
					<input type="button" class="button" id="wonderplugin-add-custompost" value="<?php _e( 'Add WooCommerce / Custom Post Type', 'wonderplugin_3dcarousel' ); ?>" />
					<label style="float:right;"><input type="button" class="button" id="wonderplugin-deleteall" value="<?php _e( 'Delete All', 'wonderplugin_3dcarousel' ); ?>" /></label>
					<label style="float:right;margin-right:4px;"><input type="button" class="button" id="wonderplugin-reverselist" value="<?php _e( 'Reverse List', 'wonderplugin_3dcarousel' ); ?>" /></label>
					<label style="float:right;padding-top:4px;margin-right:8px;"><input type='checkbox' id='wonderplugin-newestfirst' value='' /> Add new item to the beginning</label>
				</div>
        		
        		<ul class="wonderplugin-table" id="wonderplugin-3dcarousel-media-table">
			    </ul>
			    <div style="clear:both;"></div>
      
			</li>
			<li class="wonderplugin-tab">
				<form>
					<fieldset>
						
						<?php 
						$skins = array(
								"threedslider" => "3D Slider",
								"threedsliderwithtitle" => "3D Slider with Title",
								"threedsliderwithtitleandflip" => "3D Slider with Title and Flip",
								"threedsliderwithdarkoverlay" => "3D Slider with Dark Overlay",
								"threedsliderwithflip" => "3D Slider with Flip",
								"threedsliderwithfade" => "3D Slider with Fade",
								"threedcarousel" => "3D Carousel",
								"threedcarouselwithdarkoverlay" => "3D Carousel with Dark Overlay",
								"threedcarouselwithfrontface" => "3D Carousel with Front Face and Flip",
								"carouselsliderwithdarkoverlay" => "Carousel Slider with Dark Overlay",
								"carouselsliderwithhoverover" => "Carousel Slider with Hoverover Text"
								);
						
						$skin_index = 0;
						foreach ($skins as $key => $value) {
							if ($skin_index > 0 && $skin_index % 3 == 0)
								echo '<div style="clear:both;"></div>';
							$skin_index++;
						?>
							<div class="wonderplugin-tab-skin">
							<label><input type="radio" name="wonderplugin-3dcarousel-skin" value="<?php echo $key; ?>" selected> <?php echo $value; ?> <br /><img class="selected" src="<?php echo WONDERPLUGIN_3DCAROUSEL_URL; ?>images/<?php echo $key; ?>.jpg" /></label>
							</div>
						<?php
						}
						?>
						
					</fieldset>
				</form>
			</li>
			<li class="wonderplugin-tab">
			
				<div class="wonderplugin-3dcarousel-options">
					<div class="wonderplugin-3dcarousel-options-menu" id="wonderplugin-3dcarousel-options-menu">
						<div class="wonderplugin-3dcarousel-options-menu-item wonderplugin-3dcarousel-options-menu-item-selected"><?php _e( 'Skins', 'wonderplugin_3dcarousel' ); ?></div>
						<div class="wonderplugin-3dcarousel-options-menu-item"><?php _e( 'Slideshow', 'wonderplugin_3dcarousel' ); ?></div>
						<div class="wonderplugin-3dcarousel-options-menu-item"><?php _e( 'Size and Style', 'wonderplugin_3dcarousel' ); ?></div>
						<div class="wonderplugin-3dcarousel-options-menu-item"><?php _e( 'Content template', 'wonderplugin_3dcarousel' ); ?></div>
						<div class="wonderplugin-3dcarousel-options-menu-item"><?php _e( 'Skin CSS', 'wonderplugin_3dcarousel' ); ?></div>
						<div class="wonderplugin-3dcarousel-options-menu-item"><?php _e( 'Lightbox options', 'wonderplugin_3dcarousel' ); ?></div>
						<div class="wonderplugin-3dcarousel-options-menu-item"><?php _e( 'Advanced options', 'wonderplugin_3dcarousel' ); ?></div>
					</div>
					
					<div class="wonderplugin-3dcarousel-options-tabs" id="wonderplugin-3dcarousel-options-tabs">
					
						<div class="wonderplugin-3dcarousel-options-tab wonderplugin-3dcarousel-options-tab-selected">
							<p class="wonderplugin-3dcarousel-options-tab-title"><?php _e( 'Options will be restored to the default value if you switch to a new skin in the Skins tab.', 'wonderplugin_3dcarousel' ); ?></p>
							<table class="wonderplugin-form-table-noborder">
							
								<tr>
									<th>Image</th>
									<td><label><select name='wonderplugin-3dcarousel-scalemode' id='wonderplugin-3dcarousel-scalemode'>
											<option value="fill">Resize to fill, crop the image if necessary</option>
											<option value="fit">Resize to fit</option>
										</select></label>
										<p><label><input name='wonderplugin-3dcarousel-donotzoomin' type='checkbox' id='wonderplugin-3dcarousel-donotzoomin'  /> Do not enlarge small images</label></p>
										<p><label>Item border size: <input name="wonderplugin-3dcarousel-itemborder" type="number" id="wonderplugin-3dcarousel-itemborder" value="0" min="0" class="small-text" /></label>
										<label style="margin-left:12px;">Item background color: <input name='wonderplugin-3dcarousel-itembgcolor' type='text' class="regular-text" id='wonderplugin-3dcarousel-itembgcolor' value='transparent' /></label></p>
									</td>
								</tr>
								
								<tr>
									<th></th>
									<td><hr></td>
								</tr>
								
								<tr>
									<th>Image Effect</th>
									<td>
										<label>Image hover effect: <select name='wonderplugin-3dcarousel-imghovereffect' id='wonderplugin-3dcarousel-imghovereffect'>
											<option value="none">None</option>
											<option value="fade">Fade</option>
											<option value="flipy">Flip Y</option>
										</select></label>
										<p>* To enable image hover effect, the class <i>wonderplugin3dcarousel-content</i> and class <i>wonderplugin3dcarousel-hoveroverlay</i> must be defined in the Content template.</p>
										<p><label><input name='wonderplugin-3dcarousel-applylinktohoveroverlay' type='checkbox' id='wonderplugin-3dcarousel-applylinktohoveroverlay'  /> Enable the click event for the image hover overlay</label></p>
										<p><label><input name='wonderplugin-3dcarousel-addimgoverlay' type='checkbox' id='wonderplugin-3dcarousel-addimgoverlay'  /> Add a dark overlay to the images that are not in the center</label></p>
									</td>
								</tr>
								
								<tr>
									<th></th>
									<td><hr></td>
								</tr>
								
								<tr>
									<th></th>
									<td><hr></td>
								</tr>
								
								<tr>
									<th>Carousel Text</th>
									<td><label><select name='wonderplugin-3dcarousel-textstyle' id='wonderplugin-3dcarousel-textstyle'>
											<option value="always">Always show</option>
											<option value="none">Do not show</option>
										</select></label>
										<p><label><input name='wonderplugin-3dcarousel-showtitle' type='checkbox' id='wonderplugin-3dcarousel-showtitle'  /> Show title</label></p>
										<p><label><input name='wonderplugin-3dcarousel-showdescription' type='checkbox' id='wonderplugin-3dcarousel-showdescription'  /> Show description</label></p>
										<label>Animation effect: <select name='wonderplugin-3dcarousel-texteffect' id='wonderplugin-3dcarousel-texteffect'>
											<option value="fade">Fade</option>
											<option value="none">No effect</option>
										</select></label>
									</td>
								</tr>
								
								<tr>
									<th></th>
									<td><hr></td>
								</tr>
								
								<tr>
									<th>Arrows</th>
									<td><label>
										<select name='wonderplugin-3dcarousel-arrowstyle' id='wonderplugin-3dcarousel-arrowstyle'>
										  <option value="always">Always show</option>
										  <option value="mouseover">Show on mouseover</option>
										  <option value="none">Hide</option>
										</select>
									</label>
									</td>
								</tr>
								<tr>
									<th></th>
									<td>
										<div>
											<div style="float:left;margin-right:12px;">
											<label>
											<img id="wonderplugin-3dcarousel-displayarrowimage" />
											</label>
											</div>
											<div style="float:left;">
											<label>
												<input type="radio" name="wonderplugin-3dcarousel-arrowimagemode" value="custom">
												<span style="display:inline-block;min-width:250px;">Use own image (absolute URL required):</span>
												<input name='wonderplugin-3dcarousel-customarrowimage' type='text' class="regular-text" id='wonderplugin-3dcarousel-customarrowimage' value='' />
											</label>
											<br />
											<label>
												<input type="radio" name="wonderplugin-3dcarousel-arrowimagemode" value="defined">
												<span style="display:inline-block;min-width:250px;">Select from pre-defined images:</span>
												<select name='wonderplugin-3dcarousel-arrowimage' id='wonderplugin-3dcarousel-arrowimage'>
												<?php 
													$arrowimage_list = array("arrows-28-28-0.png", 
															"arrows-32-32-0.png", "arrows-32-32-1.png", "arrows-32-32-2.png", "arrows-32-32-3.png", "arrows-32-32-4.png", 
															"arrows-36-36-0.png", "arrows-36-36-1.png",
															"arrows-36-80-0.png",
															"arrows-42-60-0.png",
															"arrows-48-48-0.png", "arrows-48-48-1.png", "arrows-48-48-2.png", "arrows-48-48-3.png", "arrows-48-48-4.png",
															"arrows-72-72-0.png");
													foreach ($arrowimage_list as $arrowimage)
														echo '<option value="' . $arrowimage . '">' . $arrowimage . '</option>';
												?>
												</select>
											</label>
											</div>
											<div style="clear:both;"></div>
										</div>
										<script language="JavaScript">
										jQuery(document).ready(function(){
											jQuery("input:radio[name=wonderplugin-3dcarousel-arrowimagemode]").click(function(){
												if (jQuery(this).val() == 'custom')
													jQuery("#wonderplugin-3dcarousel-displayarrowimage").attr("src", jQuery('#wonderplugin-3dcarousel-customarrowimage').val());
												else
													jQuery("#wonderplugin-3dcarousel-displayarrowimage").attr("src", "<?php echo WONDERPLUGIN_3DCAROUSEL_URL . 'engine/'; ?>" + jQuery('#wonderplugin-3dcarousel-arrowimage').val());
											});

											jQuery("#wonderplugin-3dcarousel-arrowimage").change(function(){
												if (jQuery("input:radio[name=wonderplugin-3dcarousel-arrowimagemode]:checked").val() == 'defined')
													jQuery("#wonderplugin-3dcarousel-displayarrowimage").attr("src", "<?php echo WONDERPLUGIN_3DCAROUSEL_URL . 'engine/'; ?>" + jQuery(this).val());
												var arrowsize = jQuery(this).val().split("-");
												if (arrowsize.length > 2)
												{
													if (!isNaN(arrowsize[1]))
														jQuery("#wonderplugin-3dcarousel-arrowwidth").val(arrowsize[1]);
													if (!isNaN(arrowsize[2]))
														jQuery("#wonderplugin-3dcarousel-arrowheight").val(arrowsize[2]);
												}
													
											});
										});
										</script>
										<label>Width (px): <input name='wonderplugin-3dcarousel-arrowwidth' type='number' class="small-text" id='wonderplugin-3dcarousel-arrowwidth' value='32' /></label>
										<label><span style="margin-left:8px;">Height (px):</span> <input name='wonderplugin-3dcarousel-arrowheight' type='number' class="small-text" id='wonderplugin-3dcarousel-arrowheight' value='32' /></label>
										<label><span style="margin-left:8px;">Animation effect:</span> <select name='wonderplugin-3dcarousel-arrowanimation' id='wonderplugin-3dcarousel-arrowanimation'>
											<option value="fade">Fade</option>
											<option value="slide">Slide</option>
										</select></label>	
										<p><label>
									Position: <select name='wonderplugin-3dcarousel-arrowpos' id='wonderplugin-3dcarousel-arrowpos'>
										  <option value="side">Side</option>
										  <option value="bottom">Bottom</option>
										</select>
									</label><label style="margin-left:16px;"><input name='wonderplugin-3dcarousel-arrowsinsidelist' type='checkbox' id='wonderplugin-3dcarousel-arrowsinsidelist'  /> Add the arrows inside the carousel list container</label></p>									
									</td>
								</tr>
								
								<tr>
									<th></th>
									<td><hr></td>
								</tr>
								
								<tr>
									<th>Navigation</th>
									<td><label>
										<select name='wonderplugin-3dcarousel-navstyle' id='wonderplugin-3dcarousel-navstyle'>
										  <option value="bullets">Bullets</option>
										  <option value="numbering">Numbers</option>
										  <option value="none">None</option>
										</select>
									</label>
									<label><span style="display:inline-block;">Width:</span> <input name='wonderplugin-3dcarousel-navwidth' type='number' class="small-text" id='wonderplugin-3dcarousel-navwidth' value='32' /></label>
										<label><span style="display:inline-block;margin-left:12px;">Height:</span> <input name='wonderplugin-3dcarousel-navheight' type='number' class="small-text" id='wonderplugin-3dcarousel-navheight' value='32' /></label>										
										<label><span style="display:inline-block;margin-left:12px;">Spacing:</span> <input name='wonderplugin-3dcarousel-navspacing' type='number' class="small-text" id='wonderplugin-3dcarousel-navspacing' value='32' /></label>	
									</td>
								</tr>
								<tr>
									<th>Bullets</th>
									<td>
										<div>
											<div style="float:left;margin-right:12px;margin-top:4px;">
											<label>
											<img id="wonderplugin-3dcarousel-displaynavimage" />
											</label>
											</div>
											<div style="float:left;">
											<label>
												<input type="radio" name="wonderplugin-3dcarousel-navimagemode" value="custom">
												<span style="display:inline-block;min-width:250px;">Use own image (absolute URL required):</span>
												<input name='wonderplugin-3dcarousel-customnavimage' type='text' class="regular-text" id='wonderplugin-3dcarousel-customnavimage' value='' />
											</label>
											<br />
											<label>
												<input type="radio" name="wonderplugin-3dcarousel-navimagemode" value="defined">
												<span style="display:inline-block;min-width:250px;">Select from pre-defined images:</span>
												<select name='wonderplugin-3dcarousel-navimage' id='wonderplugin-3dcarousel-navimage'>
												<?php 
													$navimage_list = array("bullet-12-12-0.png", "bullet-12-12-1.png", 
															"bullet-16-16-0.png", "bullet-16-16-1.png", "bullet-16-16-2.png", "bullet-16-16-3.png", 
															"bullet-20-20-0.png", "bullet-20-20-1.png", 
															"bullet-24-24-0.png", "bullet-24-24-1.png", "bullet-24-24-2.png", "bullet-24-24-3.png", "bullet-24-24-4.png", "bullet-24-24-5.png", "bullet-24-24-6.png");
													foreach ($navimage_list as $navimage)
														echo '<option value="' . $navimage . '">' . $navimage . '</option>';
												?>
												</select>
											</label>
											</div>
											<div style="clear:both;"></div>
										</div>
										<script language="JavaScript">
										jQuery(document).ready(function(){
											jQuery("input:radio[name=wonderplugin-3dcarousel-navimagemode]").click(function(){
												if (jQuery(this).val() == 'custom')
													jQuery("#wonderplugin-3dcarousel-displaynavimage").attr("src", jQuery('#wonderplugin-3dcarousel-customnavimage').val());
												else
													jQuery("#wonderplugin-3dcarousel-displaynavimage").attr("src", "<?php echo WONDERPLUGIN_3DCAROUSEL_URL . 'engine/'; ?>" + jQuery('#wonderplugin-3dcarousel-navimage').val());
											});

											jQuery("#wonderplugin-3dcarousel-navimage").change(function(){
												if (jQuery("input:radio[name=wonderplugin-3dcarousel-navimagemode]:checked").val() == 'defined')
													jQuery("#wonderplugin-3dcarousel-displaynavimage").attr("src", "<?php echo WONDERPLUGIN_3DCAROUSEL_URL . 'engine/'; ?>" + jQuery(this).val());
												var navsize = jQuery(this).val().split("-");
												if (navsize.length > 2)
												{
													if (!isNaN(navsize[1]))
														jQuery("#wonderplugin-3dcarousel-navwidth").val(navsize[1]);
													if (!isNaN(navsize[2]))
														jQuery("#wonderplugin-3dcarousel-navheight").val(navsize[2]);
												}
													
											});
										});
										</script>									
										</td>
								</tr>
								
								<tr>
									<th></th>
									<td><hr></td>
								</tr>
								
								<tr>
									<th>Video</th>
									<td>
										<div>
											<p><label><input name='wonderplugin-3dcarousel-showplayvideo' type='checkbox' id='wonderplugin-3dcarousel-showplayvideo'  /> Show play button on video item</label></p>
											<div style="float:left;margin-right:12px;">
											<label>
											<img id="wonderplugin-3dcarousel-displayplayvideoimage" />
											</label>
											</div>
											<div style="float:left;">
											<label>
												<input type="radio" name="wonderplugin-3dcarousel-playvideoimagemode" value="custom">
												<span style="display:inline-block;min-width:250px;">Use own image (absolute URL required):</span>
												<input name='wonderplugin-3dcarousel-customplayvideoimage' type='text' class="regular-text" id='wonderplugin-3dcarousel-customplayvideoimage' value='' />
											</label>
											<br />
											<label>
												<input type="radio" name="wonderplugin-3dcarousel-playvideoimagemode" value="defined">
												<span style="display:inline-block;min-width:250px;">Select from pre-defined images:</span>
												<select name='wonderplugin-3dcarousel-playvideoimage' id='wonderplugin-3dcarousel-playvideoimage'>
												<?php 
													$playvideoimage_list = array("playvideo-64-64-0.png", "playvideo-64-64-1.png", "playvideo-64-64-2.png", "playvideo-64-64-3.png", "playvideo-64-64-4.png", "playvideo-64-64-5.png");
													foreach ($playvideoimage_list as $playvideoimage)
														echo '<option value="' . $playvideoimage . '">' . $playvideoimage . '</option>';
												?>
												</select>
											</label>
											</div>
											<div style="clear:both;"></div>
										</div>
										<script language="JavaScript">
										jQuery(document).ready(function(){
											jQuery("input:radio[name=wonderplugin-3dcarousel-playvideoimagemode]").click(function(){
												if (jQuery(this).val() == 'custom')
													jQuery("#wonderplugin-3dcarousel-displayplayvideoimage").attr("src", jQuery('#wonderplugin-3dcarousel-customplayvideoimage').val());
												else
													jQuery("#wonderplugin-3dcarousel-displayplayvideoimage").attr("src", "<?php echo WONDERPLUGIN_3DCAROUSEL_URL . 'engine/'; ?>" + jQuery('#wonderplugin-3dcarousel-playvideoimage').val());
											});
											jQuery("#wonderplugin-3dcarousel-playvideoimage").change(function(){
												if (jQuery("input:radio[name=wonderplugin-3dcarousel-playvideoimagemode]:checked").val() == 'defined')
													jQuery("#wonderplugin-3dcarousel-displayplayvideoimage").attr("src", "<?php echo WONDERPLUGIN_3DCAROUSEL_URL . 'engine/'; ?>" + jQuery(this).val());
											});
										});
										</script>	
										<p>Play button position: <input name='wonderplugin-3dcarousel-playvideoposition' type='text' class="medium-text" id='wonderplugin-3dcarousel-playvideoposition' value='' /></p>			
									</td>
								</tr>
								
								<tr>
									<th></th>
									<td><hr></td>
								</tr>
								
								<tr>
									<th>Add extra tags or attributes to IMG elements:</th>
									<td><label> <input name="wonderplugin-3dcarousel-imgextraprops" type="text" id="wonderplugin-3dcarousel-imgextraprops" value="" class="regular-text" /></label>
									</td>
								</tr>
								
								<tr>
									<th>Add extra tags or attributes to A elements: </th>
									<td><label><input name="wonderplugin-3dcarousel-aextraprops" type="text" id="wonderplugin-3dcarousel-aextraprops" value="" class="regular-text" /></label>
									</td>
								</tr>
								
								<tr>
									<th></th>
									<td><label><input name='wonderplugin-3dcarousel-showimgtitle' type='checkbox' id='wonderplugin-3dcarousel-showimgtitle' value='' /> Add the following text as &lt;img&gt; tag title attribute: </label>
									<select name='wonderplugin-3dcarousel-imgtitle' id='wonderplugin-3dcarousel-imgtitle'>
										  <option value="title">Title</option>
										  <option value="description">Description</option>
										  <option value="alt">Alt</option>
										</select>
									<p><label><input name='wonderplugin-3dcarousel-usedatatitle' type='checkbox' id='wonderplugin-3dcarousel-usedatatitle' value='' /> Use data-title for the &lt;a&gt; tag title attribute</a> </label></p>
									</td>
								</tr>
								
								<tr>
									<th></th>
									<td><hr></td>
								</tr>
								
								<tr>
									<th>WooCommerce Carousel</th>
									<td><label><input name='wonderplugin-3dcarousel-addwoocommerceclass' type='checkbox' id='wonderplugin-3dcarousel-addwoocommerceclass' value='' /> Add class name woocommerce to WordPress custom post type carousels</label>
									</td>
								</tr>	
								
							</table>
						</div>
						
						<div class="wonderplugin-3dcarousel-options-tab">
							<table class="wonderplugin-form-table-noborder">
								
								<tr>
									<th>Slideshow</th>
									<td><label><input name='wonderplugin-3dcarousel-autoslide' type='checkbox' id='wonderplugin-3dcarousel-autoslide'  /> Auto slideshow</label>
									<p><label><input name='wonderplugin-3dcarousel-pauseonmouseover' type='checkbox' id='wonderplugin-3dcarousel-pauseonmouseover'  /> Pause on mouse over</label></p>	
									<p>Slideshow interval (ms): <input name="wonderplugin-3dcarousel-slideinterval" type="number" id="wonderplugin-3dcarousel-slideinterval" value="5000" min="0" class="small-text" />
									<span style="margin-left:8px;">Slideshow direction:</span> <select name='wonderplugin-3dcarousel-autoslidedir' id='wonderplugin-3dcarousel-autoslidedir'>
											<option value="left">Left</option>
											<option value="right">Right</option>
										</select></p>	
									<p><label><input name='wonderplugin-3dcarousel-onlyenablelightboxoncenter' type='checkbox' id='wonderplugin-3dcarousel-onlyenablelightboxoncenter'  /> Only enable lightbox when the image is in the center</label></p>	
									<p class="wp3dcarousel-template wp3dcarousel-template-3dslider"><label><input name='wonderplugin-3dcarousel-loop' type='checkbox' id='wonderplugin-3dcarousel-loop'  /> Loop images</label></p>
									<p class="wp3dcarousel-template wp3dcarousel-template-3dslider"><label><input name='wonderplugin-3dcarousel-loopslide' type='checkbox' id='wonderplugin-3dcarousel-loopslide'  /> Loop slideshow</label></p>	
									</td>
								</tr>
								
								<tr><th></th><td><hr></td></tr>
								
								<tr>
									<th>Orders</th>
									<td>
									<label><input name='wonderplugin-3dcarousel-random' type='checkbox' id='wonderplugin-3dcarousel-random'  /> Random</label>
									<p>First item on page load: <input name="wonderplugin-3dcarousel-firstitem" type="number" id="wonderplugin-3dcarousel-firstitem" value="0" min="0" class="small-text" /></p>
									</td>
								</tr>
								
							</table>
						</div>
							
						<div class="wonderplugin-3dcarousel-options-tab">
							<table class="wonderplugin-form-table-noborder">

								<tr>
									<th>Size and 3D</th>
									<td><label>Item Width / Height (px): <input name="wonderplugin-3dcarousel-width" type="number" id="wonderplugin-3dcarousel-width" value="400" class="small-text" /> / <input name="wonderplugin-3dcarousel-height" type="number" id="wonderplugin-3dcarousel-height" value="300" class="small-text" /></label>
								</tr>
								
								<tr>
									<th></th>
									<td>
									<label>Margin CSS of the entire carousel: <input name="wonderplugin-3dcarousel-carouselmargin" type="text" id="wonderplugin-3dcarousel-carouselmargin" value="" class="regular-text" /></label>
									<p>Transition: <input name="wonderplugin-3dcarousel-transition" type="text" id="wonderplugin-3dcarousel-transition" value="" class="regular-text" /></p>
									</td>
								</tr>
								
								<tr class="wp3dcarousel-template wp3dcarousel-template-3dslider"><th></th><td>
								<label>Visible items: <input name='wonderplugin-3dcarousel-visibleitems' type='number' class='small-text' id='wonderplugin-3dcarousel-visibleitems' value='5' /></label>
								<p>3D perspective: <input name="wonderplugin-3dcarousel-perspective" type="number" id="wonderplugin-3dcarousel-perspective" value="2000" class="small-text" />
								<span style="margin-left:12px;">X distance (px):</span> <input name="wonderplugin-3dcarousel-xdis" type="number" id="wonderplugin-3dcarousel-xdis" value="350" class="small-text" />
								<span style="margin-left:12px;">Z distance (px):</span> <input name="wonderplugin-3dcarousel-zdis" type="number" id="wonderplugin-3dcarousel-zdis" value="200" class="small-text" />
								<span style="margin-left:12px;">Y rotation:</span> <input name="wonderplugin-3dcarousel-yrotate" type="number" id="wonderplugin-3dcarousel-yrotate" value="45" class="small-text" />
								</p>
								</td></tr>
								
								<tr class="wp3dcarousel-template wp3dcarousel-template-3dcarousel"><th></th><td>
								Image face: <select name='wonderplugin-3dcarousel-facemode' id='wonderplugin-3dcarousel-facemode'>
											<option value="circle">Circle</option>
											<option value="front">Front</option>
										</select>
								<p><span>Item space (change the value to adjust the entire carousel size):</span> <input name="wonderplugin-3dcarousel-itemspace" type="number" id="wonderplugin-3dcarousel-itemspace" value="8" class="small-text" /></p>
								<p><span>Rotation angle of the carousel:</span> <input name="wonderplugin-3dcarousel-rotatex" type="number" id="wonderplugin-3dcarousel-rotatex" value="-8" class="small-text" /></p>
								<p><span>Scaling ratio of the carousel when web browser resizing:</span> <input name="wonderplugin-3dcarousel-scaleratio" type="number" step="0.01" id="wonderplugin-3dcarousel-scaleratio" value="1.2" class="small-text" /></p>
								</td></tr>

								<tr><th></th><td><hr></td></tr>
								
								<tr>
									<th>Medium Screens</th>
									<td>
									Use the following parameters when the screen width is less than (px): <input name="wonderplugin-3dcarousel-medium_screenwidth" type="number" id="wonderplugin-3dcarousel-medium_screenwidth" value="768" class="small-text" />
									</td>
								</tr>
								<tr><th></th><td><hr></td></tr>
								<tr>
									<th></th>
									<td>
									<p><label>Item Width / Height (px): <input name="wonderplugin-3dcarousel-medium_width" type="number" id="wonderplugin-3dcarousel-medium_width" value="400" class="small-text" /> / <input name="wonderplugin-3dcarousel-medium_height" type="number" id="wonderplugin-3dcarousel-medium_height" value="300" class="small-text" /></label></p>
									<p><label>Margin CSS of the entire carousel: <input name="wonderplugin-3dcarousel-medium_carouselmargin" type="text" id="wonderplugin-3dcarousel-medium_carouselmargin" value="" class="regular-text" /></label></p>
									<p>Transition: <input name="wonderplugin-3dcarousel-medium_transition" type="text" id="wonderplugin-3dcarousel-medium_transition" value="" class="regular-text" /></p>
									</td>
								</tr>
																
								<tr class="wp3dcarousel-template wp3dcarousel-template-3dslider"><th></th><td>
								<label>Visible items: <input name='wonderplugin-3dcarousel-medium_visibleitems' type='number' class='small-text' id='wonderplugin-3dcarousel-medium_visibleitems' value='5' /></label>
								<p>3D perspective: <input name="wonderplugin-3dcarousel-medium_perspective" type="number" id="wonderplugin-3dcarousel-medium_perspective" value="2000" class="small-text" />
								<span style="margin-left:12px;">X distance (px):</span> <input name="wonderplugin-3dcarousel-medium_xdis" type="number" id="wonderplugin-3dcarousel-medium_xdis" value="350" class="small-text" />
								<span style="margin-left:12px;">Z distance (px):</span> <input name="wonderplugin-3dcarousel-medium_zdis" type="number" id="wonderplugin-3dcarousel-medium_zdis" value="200" class="small-text" />
								<span style="margin-left:12px;">Y rotation:</span> <input name="wonderplugin-3dcarousel-medium_yrotate" type="number" id="wonderplugin-3dcarousel-medium_yrotate" value="45" class="small-text" />
								</p>
								</td></tr>
								
								<tr><th></th><td><hr></td></tr>
								
								<tr>
									<th>Small Screens</th>
									<td>
									Use the following parameters when the screen width is less than (px): <input name="wonderplugin-3dcarousel-small_screenwidth" type="number" id="wonderplugin-3dcarousel-small_screenwidth" value="414" class="small-text" />
									</td>
								</tr>
								<tr><th></th><td><hr></td></tr>
								<tr>
									<th></th>
									<td>
									<p><label>Item Width / Height (px): <input name="wonderplugin-3dcarousel-small_width" type="number" id="wonderplugin-3dcarousel-small_width" value="400" class="small-text" /> / <input name="wonderplugin-3dcarousel-small_height" type="number" id="wonderplugin-3dcarousel-small_height" value="300" class="small-text" /></label></p>
									<p><label>Margin CSS of the entire carousel: <input name="wonderplugin-3dcarousel-small_carouselmargin" type="text" id="wonderplugin-3dcarousel-small_carouselmargin" value="" class="regular-text" /></label></p>
									<p>Transition: <input name="wonderplugin-3dcarousel-small_transition" type="text" id="wonderplugin-3dcarousel-small_transition" value="" class="regular-text" /></p>
									</td>
								</tr>
																
								<tr class="wp3dcarousel-template wp3dcarousel-template-3dslider"><th></th><td>
								<label>Visible items: <input name='wonderplugin-3dcarousel-small_visibleitems' type='number' class='small-text' id='wonderplugin-3dcarousel-small_visibleitems' value='5' /></label>
								<p>3D perspective: <input name="wonderplugin-3dcarousel-small_perspective" type="number" id="wonderplugin-3dcarousel-small_perspective" value="2000" class="small-text" />
								<span style="margin-left:12px;">X distance (px):</span> <input name="wonderplugin-3dcarousel-small_xdis" type="number" id="wonderplugin-3dcarousel-small_xdis" value="350" class="small-text" />
								<span style="margin-left:12px;">Z distance (px):</span> <input name="wonderplugin-3dcarousel-small_zdis" type="number" id="wonderplugin-3dcarousel-small_zdis" value="200" class="small-text" />
								<span style="margin-left:12px;">Y rotation:</span> <input name="wonderplugin-3dcarousel-small_yrotate" type="number" id="wonderplugin-3dcarousel-small_yrotate" value="45" class="small-text" />
								</p>
								</td></tr>
								
							</table>
						</div>
						
						<div class="wonderplugin-3dcarousel-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Skin template</th>
									<td><textarea name='wonderplugin-3dcarousel-skintemplate' id='wonderplugin-3dcarousel-skintemplate' value='' class='large-text' rows="20"></textarea></td>
								</tr>
							</table>
						</div>
						
						<div class="wonderplugin-3dcarousel-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Skin CSS</th>
									<td><textarea name='wonderplugin-3dcarousel-skincss' id='wonderplugin-3dcarousel-skincss' value='' class='large-text' rows="20"></textarea></td>
								</tr>
							</table>
						</div>
						
						<div class="wonderplugin-3dcarousel-options-tab" style="padding:24px;">
						
						
						<ul class="wonderplugin-tab-buttons-horizontal" data-panelsid="wonderplugin-lightbox-panels">
							<li class="wonderplugin-tab-button-horizontal wonderplugin-tab-button-horizontal-selected"><?php _e( 'General', 'wonderplugin_3dcarousel' ); ?></li>
							<li class="wonderplugin-tab-button-horizontal"></span><?php _e( 'Video', 'wonderplugin_3dcarousel' ); ?></li>
							<li class="wonderplugin-tab-button-horizontal"></span><?php _e( 'Thumbnails', 'wonderplugin_3dcarousel' ); ?></li>
							<li class="wonderplugin-tab-button-horizontal"></span><?php _e( 'Text', 'wonderplugin_3dcarousel' ); ?></li>
							<li class="wonderplugin-tab-button-horizontal"></span><?php _e( 'Social Media', 'wonderplugin_3dcarousel' ); ?></li>
							<li class="wonderplugin-tab-button-horizontal"></span><?php _e( 'Lightbox Advanced Options', 'wonderplugin_3dcarousel' ); ?></li>
							<div style="clear:both;"></div>
						</ul>
						
						<ul class="wonderplugin-tabs-horizontal" id="wonderplugin-lightbox-panels">
						
							<li class="wonderplugin-tab-horizontal wonderplugin-tab-horizontal-selected">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>General</th>
									<td><label><input name='wonderplugin-3dcarousel-lightboxresponsive' type='checkbox' id='wonderplugin-3dcarousel-lightboxresponsive'  /> Responsive</label>
									<br><label><input name="wonderplugin-3dcarousel-lightboxfullscreenmode" type="checkbox" id="wonderplugin-3dcarousel-lightboxfullscreenmode" /> Display in fullscreen mode (the close button on top right of the web browser)</label>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">Slideshow</th>
									<td><label><input name="wonderplugin-3dcarousel-lightboxautoslide" type="checkbox" id="wonderplugin-3dcarousel-lightboxautoslide" /> Auto play slideshow</label>
									<label> - slideshow interval (ms): <input name="wonderplugin-3dcarousel-lightboxslideinterval" type="number" min=0 id="wonderplugin-3dcarousel-lightboxslideinterval" value="5000" class="small-text" /></label>
									<br><label><input name="wonderplugin-3dcarousel-lightboximagekeepratio" type="checkbox" id="wonderplugin-3dcarousel-lightboximagekeepratio" /> Keep image aspect ratio</label>
									<br><label><input name="wonderplugin-3dcarousel-lightboxalwaysshownavarrows" type="checkbox" id="wonderplugin-3dcarousel-lightboxalwaysshownavarrows" /> Always show left and right navigation arrows</label>
									<br><label><input name="wonderplugin-3dcarousel-lightboxshowplaybutton" type="checkbox" id="wonderplugin-3dcarousel-lightboxshowplaybutton" /> Show play slideshow button</label>
									<br><label><input name="wonderplugin-3dcarousel-lightboxshowtimer" type="checkbox" id="wonderplugin-3dcarousel-lightboxshowtimer" /> Show line timer for image slideshow</label>
									<br>Timer position: <select name="wonderplugin-3dcarousel-lightboxtimerposition" id="wonderplugin-3dcarousel-lightboxtimerposition">
										  <option value="bottom">Bottom</option>
										  <option value="top">Top</option>
										</select>
									Timer color: <input name="wonderplugin-3dcarousel-lightboxtimercolor" type="text" id="wonderplugin-3dcarousel-lightboxtimercolor" value="#dc572e" class="medium-text" />
									Timer height: <input name="wonderplugin-3dcarousel-lightboxtimerheight" type="number" min=0 id="wonderplugin-3dcarousel-lightboxtimerheight" value="2" class="small-text" />
									Timer opacity: <input name="wonderplugin-3dcarousel-lightboxtimeropacity" type="number" min=0 max=1 step="0.1" id="wonderplugin-3dcarousel-lightboxtimeropacity" value="1" class="small-text" />
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">Overlay</th>
									<td>Color: <input name="wonderplugin-3dcarousel-lightboxoverlaybgcolor" type="text" id="wonderplugin-3dcarousel-lightboxoverlaybgcolor" value="#333" class="medium-text" />
									Opacity: <input name="wonderplugin-3dcarousel-lightboxoverlayopacity" type="number" min=0 max=1 step="0.1" id="wonderplugin-3dcarousel-lightboxoverlayopacity" value="0.9" class="small-text" />
									<label><input name="wonderplugin-3dcarousel-lightboxcloseonoverlay" type="checkbox" id="wonderplugin-3dcarousel-lightboxcloseonoverlay" /> Close the lightbox when clicking on the overlay background</label></td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Background color</th>
									<td><input name="wonderplugin-3dcarousel-lightboxbgcolor" type="text" id="wonderplugin-3dcarousel-lightboxbgcolor" value="#fff" class="medium-text" /></td>
								</tr>
		
								<tr valign="top">
									<th scope="row">Border</th>
									<td>Border radius (px): <input name="wonderplugin-3dcarousel-lightboxborderradius" type="number" min=0 id="wonderplugin-3dcarousel-lightboxborderradius" value="0" class="small-text" />
									Border size (px): <input name="wonderplugin-3dcarousel-lightboxbordersize" type="number" min=0 id="wonderplugin-3dcarousel-lightboxbordersize" value="8" class="small-text" />
									</td>
								</tr>
								<tr>
									<th>Group</th>
									<td><label><input name='wonderplugin-3dcarousel-lightboxnogroup' type='checkbox' id='wonderplugin-3dcarousel-lightboxnogroup'  /> Do not display lightboxes as a group</label>
									</td>
								</tr>
							</table>
							</li>
							
							<li class="wonderplugin-tab-horizontal">
							<table class="wonderplugin-form-table-noborder">
								<tr valign="top">
									<th scope="row">Default volume of MP4/WebM videos</th>
									<td><label><input name="wonderplugin-3dcarousel-lightboxdefaultvideovolume" type="number" min=0 max=1 step="0.1" id="wonderplugin-3dcarousel-lightboxdefaultvideovolume" value="1" class="small-text" /> (0 - 1)</label></td>
								</tr>
		
								<tr>
									<th>Video</th>
									<td><label><input name='wonderplugin-3dcarousel-lightboxvideohidecontrols' type='checkbox' id='wonderplugin-3dcarousel-lightboxvideohidecontrols'  /> Hide MP4/WebM video play control bar</label>
									<p style="font-style:italic;">* Video autoplay is not supported on mobile and tables. The limitation comes from iOS and Android.</p>
									</td>
								</tr>
							</table>
							</li>
							
							<li class="wonderplugin-tab-horizontal">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Thumbnails</th>
									<td><label><input name='wonderplugin-3dcarousel-lightboxshownavigation' type='checkbox' id='wonderplugin-3dcarousel-lightboxshownavigation'  /> Show thumbnails</label>
									<p><label><input name='wonderplugin-3dcarousel-lightboxshownavcontrol' type='checkbox' id='wonderplugin-3dcarousel-lightboxshownavcontrol'  /> Display a button to show/hide the thumbnails</label></p>
									<p><label><input name='wonderplugin-3dcarousel-lightboxhidenavdefault' type='checkbox' id='wonderplugin-3dcarousel-lightboxhidenavdefault'  /> When the show/hide button is displayed, hide the thumbnails by default</label></p>	
									</td>
								</tr>
								<tr>
									<th></th>
									<td><label>Thumbnail size: <input name="wonderplugin-3dcarousel-lightboxthumbwidth" type="number" id="wonderplugin-3dcarousel-lightboxthumbwidth" value="96" class="small-text" /> x <input name="wonderplugin-3dcarousel-lightboxthumbheight" type="number" id="wonderplugin-3dcarousel-lightboxthumbheight" value="72" class="small-text" /></label> 
									<label>Thumbnail top margin: <input name="wonderplugin-3dcarousel-lightboxthumbtopmargin" type="number" id="wonderplugin-3dcarousel-lightboxthumbtopmargin" value="12" class="small-text" /> Thumbnail bottom margin: <input name="wonderplugin-3dcarousel-lightboxthumbbottommargin" type="number" id="wonderplugin-3dcarousel-lightboxthumbbottommargin" value="12" class="small-text" /></label>
									</td>
								</tr>
								<tr>
									<th></th>
									<td>
									Background color: <input name="wonderplugin-3dcarousel-lightboxnavbgcolor" type="text" id="wonderplugin-3dcarousel-lightboxnavbgcolor" value="" class="regular-text" />								
									</td>
								</tr>
							</table>
							</li>
							
							<li class="wonderplugin-tab-horizontal">
							<table class="wonderplugin-form-table-noborder">
								<tr valign="top">
									<th scope="row">Text position</th>
									<td>
										<select name="wonderplugin-3dcarousel-lightboxtitlestyle" id="wonderplugin-3dcarousel-lightboxtitlestyle">
										  <option value="bottom">Bottom</option>
										  <option value="inside">Inside</option>
										  <option value="right">Right</option>
										  <option value="left">Left</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>Maximum text bar height when text position is bottom</th>
									<td><label><input name="wonderplugin-3dcarousel-lightboxbarheight" type="number" id="wonderplugin-3dcarousel-lightboxbarheight" value="64" class="small-text" /></label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Image/video width percentage when text position is right or left</th>
									<td><input name="wonderplugin-3dcarousel-lightboximagepercentage" type="number" id="wonderplugin-3dcarousel-lightboximagepercentage" value="75" class="small-text" />%</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Title</th>
									<td><label><input name="wonderplugin-3dcarousel-lightboxshowtitle" type="checkbox" id="wonderplugin-3dcarousel-lightboxshowtitle" /> Show title</label></td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Add the following prefix to title</th>
									<td><label><input name="wonderplugin-3dcarousel-lightboxshowtitleprefix" type="checkbox" id="wonderplugin-3dcarousel-lightboxshowtitleprefix" /> Add prefix:</label><input name="wonderplugin-3dcarousel-lightboxtitleprefix" type="text" id="wonderplugin-3dcarousel-lightboxtitleprefix" value="" class="regular-text" /></td>
								</tr>
		
								<tr>
									<th>Title CSS</th>
									<td><label><textarea name="wonderplugin-3dcarousel-lightboxtitlebottomcss" id="wonderplugin-3dcarousel-lightboxtitlebottomcss" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Title CSS when text position is inside</th>
									<td><textarea name="wonderplugin-3dcarousel-lightboxtitleinsidecss" id="wonderplugin-3dcarousel-lightboxtitleinsidecss" rows="2" class="large-text code"></textarea></td>
								</tr>
		
								<tr valign="top">
									<th scope="row">Description</th>
									<td><label><input name="wonderplugin-3dcarousel-lightboxshowdescription" type="checkbox" id="wonderplugin-3dcarousel-lightboxshowdescription" /> Show description</label></td>
								</tr>
								
								<tr>
									<th>Description CSS</th>
									<td><label><textarea name="wonderplugin-3dcarousel-lightboxdescriptionbottomcss" id="wonderplugin-3dcarousel-lightboxdescriptionbottomcss" rows="2" class="large-text code"></textarea></label>
									</td>
								</tr>
								
								<tr valign="top">
									<th scope="row">Description CSS when text position is inside</th>
									<td><textarea name="wonderplugin-3dcarousel-lightboxdescriptioninsidecss" id="wonderplugin-3dcarousel-lightboxdescriptioninsidecss" rows="2" class="large-text code"></textarea></td>
								</tr>
							</table>
							</li>
							
							<li class="wonderplugin-tab-horizontal">
							<table class="wonderplugin-form-table-noborder">
							<tr valign="top">
								<th scope="row">Social Media</th>
								<td><label for="wonderplugin-3dcarousel-lightboxshowsocial"><input name="wonderplugin-3dcarousel-lightboxshowsocial" type="checkbox" id="wonderplugin-3dcarousel-lightboxshowsocial" /> Enable social media</label>
								<p><label for="wonderplugin-3dcarousel-lightboxshowfacebook"><input name="wonderplugin-3dcarousel-lightboxshowfacebook" type="checkbox" id="wonderplugin-3dcarousel-lightboxshowfacebook" /> Show Facebook button</label>
								<br><label for="wonderplugin-3dcarousel-lightboxshowtwitter"><input name="wonderplugin-3dcarousel-lightboxshowtwitter" type="checkbox" id="wonderplugin-3dcarousel-lightboxshowtwitter" /> Show Twitter button</label>
								<br><label for="wonderplugin-3dcarousel-lightboxshowpinterest"><input name="wonderplugin-3dcarousel-lightboxshowpinterest" type="checkbox" id="wonderplugin-3dcarousel-lightboxshowpinterest" /> Show Pinterest button</label></p>
								</td>
							</tr>
				        	
				        	<tr valign="top">
								<th scope="row">Position and Size</th>
								<td>
								Position CSS: <input name="wonderplugin-3dcarousel-lightboxsocialposition" type="text" id="wonderplugin-3dcarousel-lightboxsocialposition" value="" class="regular-text" />
								<p>Position CSS on small screen: <input name="wonderplugin-3dcarousel-lightboxsocialpositionsmallscreen" type="text" id="wonderplugin-3dcarousel-lightboxsocialpositionsmallscreen" value="" class="regular-text" /></p>
								<p>Button size: <input name="wonderplugin-3dcarousel-lightboxsocialbuttonsize" type="number" id="wonderplugin-3dcarousel-lightboxsocialbuttonsize" value="32" class="small-text" />
								Button font size: <input name="wonderplugin-3dcarousel-lightboxsocialbuttonfontsize" type="number" id="wonderplugin-3dcarousel-lightboxsocialbuttonfontsize" value="18" class="small-text" />
								Buttons direction:
								<select name="wonderplugin-3dcarousel-lightboxsocialdirection" id="wonderplugin-3dcarousel-lightboxsocialdirection">
								  <option value="horizontal" selected="selected">horizontal</option>
								  <option value="vertical">>vertical</option>
								</select>
								</p>
								<p><label for="wonderplugin-3dcarousel-lightboxsocialrotateeffect"><input name="wonderplugin-3dcarousel-lightboxsocialrotateeffect" type="checkbox" id="wonderplugin-3dcarousel-lightboxsocialrotateeffect" /> Enable button rotating effect on mouse hover</label></p>	
								</td>
							</tr>
							</table>
							</li>
							
							<li class="wonderplugin-tab-horizontal">
							<table class="wonderplugin-form-table-noborder">
								<tr valign="top">
									<th scope="row">Data Options</th>
									<td><textarea name="wonderplugin-3dcarousel-lightboxadvancedoptions" id="wonderplugin-3dcarousel-lightboxadvancedoptions" rows="4" class="large-text code"></textarea></td>
								</tr>
							</table>
							</li>
						</ul>
						
						</div>
						
						<div class="wonderplugin-3dcarousel-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Advanced Options</th>
									<td><label><input name='wonderplugin-3dcarousel-donotinit' type='checkbox' id='wonderplugin-3dcarousel-donotinit'  /> Do not init the carousel when the page is loaded. Check this option if you would like to manually init the 3dcarousel with JavaScript API.</label>
									<p><label><input name='wonderplugin-3dcarousel-addinitscript' type='checkbox' id='wonderplugin-3dcarousel-addinitscript'  /> Add init scripts together with carousel HTML code. Check this option if your WordPress site uses Ajax to load pages and posts.</label></p>
									<p><label><input name='wonderplugin-3dcarousel-doshortcodeontext' type='checkbox' id='wonderplugin-3dcarousel-doshortcodeontext'  /> Support shortcode in title and description</label></p>
									<p><label><input name='wonderplugin-3dcarousel-triggerresize' type='checkbox' id='wonderplugin-3dcarousel-triggerresize'  /> Trigger window resize event after (ms): </label><input name="wonderplugin-3dcarousel-triggerresizedelay" type="number" min=0 id="wonderplugin-3dcarousel-triggerresizedelay" value="0" class="small-text" /></p>
									</td>
								</tr>
								<tr>
									<th>Custom CSS</th>
									<td><textarea name='wonderplugin-3dcarousel-custom-css' id='wonderplugin-3dcarousel-custom-css' value='' class='large-text' rows="10"></textarea></td>
								</tr>
								<tr>
									<th>Data Options</th>
									<td><textarea name='wonderplugin-3dcarousel-data-options' id='wonderplugin-3dcarousel-data-options' value='' class='large-text' rows="10"></textarea></td>
								</tr>
								<tr>
									<th>Custom JavaScript</th>
									<td><textarea name='wonderplugin-3dcarousel-customjs' id='wonderplugin-3dcarousel-customjs' value='' class='large-text' rows="10"></textarea><br />
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div style="clear:both;"></div>
				
			</li>
			<li class="wonderplugin-tab">
				<div id="wonderplugin-3dcarousel-preview-tab">
					<div id="wonderplugin-3dcarousel-preview-message"></div>
					<div id="wonderplugin-3dcarousel-preview-container">
					</div>
				</div>
			</li>
			<li class="wonderplugin-tab">
				<div id="wonderplugin-3dcarousel-publish-loading"></div>
				<div id="wonderplugin-3dcarousel-publish-information"></div>
			</li>
		</ul>
		</div>
		
		<?php
	}
	
	function get_list_data() {
		return array();
	}
}