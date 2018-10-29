<?php

class WonderPlugin_Audio_Creator {

	private $parent_view, $list_table;
	
	function __construct($parent) {
		
		$this->parent_view = $parent;
	}
	
	function render( $id, $config ) {
		
		?>
		
		<h3><?php _e( 'General Options', 'wonderplugin_audio' ); ?></h3>
		
		<div id="wonderplugin-audio-id" style="display:none;"><?php echo $id; ?></div>
		
		<?php 
		$config = str_replace('\\\"', '"', $config);
		$config = str_replace("\\\'", "'", $config);
		$config = str_replace("<", "&lt;", $config);
		$config = str_replace(">", "&gt;", $config);
		$config = str_replace("&quot;", "", $config);
		?>
		
		<div id="wonderplugin-audio-id-config" style="display:none;"><?php echo $config; ?></div>
		<div id="wonderplugin-audio-license" style="display:none;"><?php echo WONDERPLUGIN_AUDIO_VERSION_TYPE; ?></div>
		<div id="wonderplugin-audio-jsfolder" style="display:none;"><?php echo WONDERPLUGIN_AUDIO_URL . 'engine/'; ?></div>
		<div id="wonderplugin-audio-viewadminurl" style="display:none;"><?php echo admin_url('admin.php?page=wonderplugin_audio_show_item'); ?></div>
		<div id="wonderplugin-audio-wp-history-media-uploader" style="display:none;"><?php echo ( function_exists("wp_enqueue_media") ? "0" : "1"); ?></div>
		<div id="wonderplugin-audio-ajaxnonce" style="display:none;"><?php echo wp_create_nonce( 'wonderplugin-audio-ajaxnonce' ); ?></div>
		<div id="wonderplugin-audio-saveformnonce" style="display:none;"><?php wp_nonce_field('wonderplugin-audio', 'wonderplugin-audio-saveform'); ?></div>
				
		<div style="margin:0 12px;">
		<table class="wonderplugin-form-table">
			<tr>
				<th><?php _e( 'Name', 'wonderplugin_audio' ); ?></th>
				<td><input name="wonderplugin-audio-name" type="text" id="wonderplugin-audio-name" value="My Audio Player" class="regular-text" /></td>
			</tr>
		</table>
		</div>
		
		<h3><?php _e( 'Designing', 'wonderplugin_audio' ); ?></h3>
		
		<div style="margin:0 12px;">
		<ul class="wonderplugin-tab-buttons" id="wonderplugin-audio-toolbar">
			<li class="wonderplugin-tab-button step1 wonderplugin-tab-buttons-selected"><?php _e( 'MP3', 'wonderplugin_audio' ); ?></li>
			<li class="wonderplugin-tab-button step2"><?php _e( 'Skins', 'wonderplugin_audio' ); ?></li>
			<li class="wonderplugin-tab-button step3"><?php _e( 'Options', 'wonderplugin_audio' ); ?></li>
			<li class="wonderplugin-tab-button step4"><?php _e( 'Preview', 'wonderplugin_audio' ); ?></li>
			<li class="laststep"><input class="button button-primary" type="button" value="<?php _e( 'Save & Publish', 'wonderplugin_audio' ); ?>"></input></li>
		</ul>
				
		<ul class="wonderplugin-tabs" id="wonderplugin-audio-tabs">
			<li class="wonderplugin-tab wonderplugin-tab-selected">	
			
				<div class="wonderplugin-toolbar">	
					<input type="button" class="button" id="wonderplugin-add-mp3" value="<?php _e( 'Add Audio', 'wonderplugin_audio' ); ?>" />
					<label style="float:right;"><input type="button" class="button" id="wonderplugin-reverselist" value="<?php _e( 'Reverse List', 'wonderplugin_audio' ); ?>" /></label>
					<label style="float:right;padding-top:4px;margin-right:8px;"><input type='checkbox' id='wonderplugin-newestfirst' value='' /> Add new item to the beginning</label>
				</div>
        		  
			    <ul class="wonderplugin-table" id="wonderplugin-audio-media-table">
			    </ul>
			    <div style="clear:both;"></div>
      
			</li>
			<li class="wonderplugin-tab">
				<form>
					<fieldset>
						
						<?php 
						$skins = array(
								"bar" => "Bar",
								"barwithplaylist" => "Bar with Playlist",
								"barwhite" => "White Bar",
								"barwhitewithplaylist" => "White Bar with Playlist",
								"darkbox" => "Dark Box",
								"jukebox" => "Jukebox",
								"whitebox" => "White Box",
								"whiteboxlive" => "White Box Live",
								"greybox" => "Grey Box",
								"greyboxlive" => "Grey Box Live",
								"bartitle" => "Bar with Title",
								"barwhitetitle" => "White Bar with Title",
								"musicbox" => "Music Box",
								"lightbox" => "LightBox",
								"threebuttons" => "Three Buttons",
								"button24" => "Button 24",
								"button48" => "Button 48",
								"buttonblue" => "Button Blue",
								"blueplaystop" => "Blue Play and Stop"
								);
						
						$index = 0;
						foreach ($skins as $key => $value) {
						?>
							<div class="wonderplugin-tab-skin">
							<label><input type="radio" name="wonderplugin-audio-skin" value="<?php echo $key; ?>" selected> <?php echo $value; ?> <br /><img class="selected" style="max-width:100%;" src="<?php echo WONDERPLUGIN_AUDIO_URL; ?>images/<?php echo $key; ?>.jpg" /></label>
							</div>
						<?php
							$index++;
							if ($index % 3 == 0)
								echo '<div style="clear:both;"></div>';
						}
						?>
						
					</fieldset>
				</form>
			</li>
			<li class="wonderplugin-tab">
			
				<div class="wonderplugin-audio-options">
					<div class="wonderplugin-audio-options-menu" id="wonderplugin-audio-options-menu">
						<div class="wonderplugin-audio-options-menu-item wonderplugin-audio-options-menu-item-selected"><?php _e( 'Skin Options', 'wonderplugin_audio' ); ?></div>
						<div class="wonderplugin-audio-options-menu-item"><?php _e( 'Playlist', 'wonderplugin_audio' ); ?></div>
						<div class="wonderplugin-audio-options-menu-item"><?php _e( 'Player Control', 'wonderplugin_audio' ); ?></div>
						<div class="wonderplugin-audio-options-menu-item"><?php _e( 'Skin CSS', 'wonderplugin_audio' ); ?></div>
						<div class="wonderplugin-audio-options-menu-item"><?php _e( 'Advanced Options', 'wonderplugin_audio' ); ?></div>
					</div>
					
					<div class="wonderplugin-audio-options-tabs" id="wonderplugin-audio-options-tabs">
					
						<div class="wonderplugin-audio-options-tab wonderplugin-audio-options-tab-selected">
							<p class="wonderplugin-audio-options-tab-title"><?php _e( 'Options will be restored to the default value if you switch to a new skin in the Skins tab.', 'wonderplugin_audio' ); ?></p>
							<table class="wonderplugin-form-table-noborder">
							
								<tr>
									<th>Width</th>
									<td><label><input name="wonderplugin-audio-width" type="number" id="wonderplugin-audio-width" value="300" class="small-text" /></label></td>
								</tr>
								<tr>
									<th>Height</th>
									<td>
									<label>
										<select name='wonderplugin-audio-heightmode' id='wonderplugin-audio-heightmode'>
										  <option value="auto">Auto</option>
										  <option value="fixed">Fixed</option>
										</select>
									<input name="wonderplugin-audio-height" type="number" id="wonderplugin-audio-height" value="300" class="small-text" /></label></td>
								</tr>
								<tr>
									<th>Responsive</th>
									<td><label><input name='wonderplugin-audio-autoresize' type='checkbox' id='wonderplugin-audio-autoresize'  /> Create a responsive audio player</label>
									<br><label><input name='wonderplugin-audio-responsive' type='checkbox' id='wonderplugin-audio-responsive'  /> Create a fullwidth audio player</label>
									</td>
								</tr>								
								<tr>
									<th>Play mode</th>
									<td><label><input name='wonderplugin-audio-autoplay' type='checkbox' id='wonderplugin-audio-autoplay'  /> Auto play (not working on mobile/tablets and Safari)</label>
									<br /><label><input name='wonderplugin-audio-random' type='checkbox' id='wonderplugin-audio-random'  /> Random</label>
									<br><label><input name='wonderplugin-audio-preloadaudio' type='checkbox' id='wonderplugin-audio-preloadaudio'  /> Preload audio on page load</label>
									<br><label>HTML5 audio playback speed: <input name="wonderplugin-audio-playbackrate" type="number" min="0.1" max="10" step="0.1" id="wonderplugin-audio-playbackrate" value="1" class="small-text" /></label>
									</td>
								</tr>
								
								<tr>
									<th>HTML5</th><td>
									<label><input name='wonderplugin-audio-forceflash' type='checkbox' id='wonderplugin-audio-forceflash'  /> Use Flash as default player</label>
									<br /><label><input name='wonderplugin-audio-forcehtml5' type='checkbox' id='wonderplugin-audio-forcehtml5'  /> Force to use HTML5 player</label>
									<p>* By default, the player will use HTML5 as default player and fallback to Flash when HTML5 is not supported.</p>
									
									</td>
								</tr>
								<tr>
									<th>Default loop mode</th>
									<td><label>
										<select name='wonderplugin-audio-loop' id='wonderplugin-audio-loop'>
										  <option value="0">No loop</option>
										  <option value="1">Loop all</option>
										  <option value="2">Loop single</option>
										</select>
									</label></td>
								</tr>
								
								<tr>
									<th>Default volume</th>
									<td><label><input name='wonderplugin-audio-setdefaultvolume' type='checkbox' id='wonderplugin-audio-setdefaultvolume'  /> Set default volume ( 0 to 100 ): </label>
									<label><input name="wonderplugin-audio-defaultvolume" type="number" min="0" max="100" id="wonderplugin-audio-defaultvolume" value="100" class="small-text" /></label>
									</td>
								</tr>
								
								<tr>
									<th>Google Analytics</th>
									<td><label><input name='wonderplugin-audio-enablega' type='checkbox' id='wonderplugin-audio-enablega'  /> Enable Google Analytics.</label>
									&nbsp;&nbsp;<label>Tracking ID: <input name="wonderplugin-audio-gatrackingid" type="text" id="wonderplugin-audio-gatrackingid" value="" class="medium-text" /></label>
									</td>
								</tr>
								
								<tr>
									<th>Live Streaming</th>
									<td><label><input name='wonderplugin-audio-showliveplayedlist' type='checkbox' id='wonderplugin-audio-showliveplayedlist'  /> Show live streaming played tracks (for Radionomy and Shoutcast)</label>
									<p><label>Played tracks title: <input name="wonderplugin-audio-playedlisttitle" type="text" id="wonderplugin-audio-playedlisttitle" value="" size="40" /></label></p>
									<p><label>Maximum played tracks: <input name="wonderplugin-audio-maxplayedlist" type="number" min="1" id="wonderplugin-audio-maxplayedlist" value="8" class="small-text"/ ><span style="font-style:italic;">&nbsp;&nbsp;* The free version will only display last 2 tracks. The commercial version will remove the limitation.</span></label></p>
									<p><label>Live update interval (ms): <input name="wonderplugin-audio-liveupdateinterval" type="number" min="1" id="wonderplugin-audio-liveupdateinterval" value="10000" style="width:80px;"/></label></p>
									</td>
								</tr>
								
							</table>
						</div>
						
						<div class="wonderplugin-audio-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Playlist</th>
									<td><label><input name='wonderplugin-audio-showtracklist' type='checkbox' id='wonderplugin-audio-showtracklist'  /> Show playlist</label>
									<p><label>The number of tracks displayed in one page: <input name="wonderplugin-audio-tracklistitem" type="number" id="wonderplugin-audio-tracklistitem" value="10" class="small-text" /></label></p>
									<p><label><input name='wonderplugin-audio-tracklistscroll' type='checkbox' id='wonderplugin-audio-tracklistscroll'  /> Display a vertical scroll bar when the number of tracks exceeds the above value</label></p>
									<p><label><input name='wonderplugin-audio-showtracklistsearch' type='checkbox' id='wonderplugin-audio-showtracklistsearch'  /> Show search box for playlist</label></p>
									</td>
								<tr>
									<th>Playlist item format</th>
									<td>
									<p><label><input name='wonderplugin-audio-customisetracklistitemformat' type='checkbox' id='wonderplugin-audio-customisetracklistitemformat'  /> Customise the playlist item format:</label></p>
									<textarea name='wonderplugin-audio-tracklistitemformat' id='wonderplugin-audio-tracklistitemformat' value='' class='large-text' rows="5"></textarea>
									</td>
								</tr>
							</table>
						</div>
							
						<div class="wonderplugin-audio-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Buttons</th>
									<td><label><input name='wonderplugin-audio-showprevnext' type='checkbox' id='wonderplugin-audio-showprevnext'  /> Show previous and next button</label>
									<p><label><input name='wonderplugin-audio-showloop' type='checkbox' id='wonderplugin-audio-showloop'  /> Show loop button</label></p>
									<p><label><input name='wonderplugin-audio-showtime' type='checkbox' id='wonderplugin-audio-showtime'  /> Show time</label></p>
									<p><label><input name='wonderplugin-audio-showvolume' type='checkbox' id='wonderplugin-audio-showvolume'  /> Show volume button</label></p>
									<p><label><input name='wonderplugin-audio-showvolumebar' type='checkbox' id='wonderplugin-audio-showvolumebar'  /> Show volume bar</label></p>
									</td>
								</tr>
								
								<tr>
									<th>Information format</th>
									<td>
									<input name='wonderplugin-audio-infoformat' id='wonderplugin-audio-infoformat' type="text" class='large-text' rows="5">
									</td>
								</tr>
								
								<tr>
									<th>Progress bar</th>
									<td><label><input name='wonderplugin-audio-showprogress' type='checkbox' id='wonderplugin-audio-showprogress'  /> Show progress bar</label>
									</td>
								</tr>
								
								<tr>
									<th>Title in bar player</th>
									<td>
									<label><input name='wonderplugin-audio-showtitleinbar' type='checkbox' id='wonderplugin-audio-showtitleinbar'  /> Show title in the bar player</label>
									<p><label style="margin-right:24px;">Title width: <input name="wonderplugin-audio-titleinbarwidth" type="number" id="wonderplugin-audio-titleinbarwidth" value="80" class="small-text" /></label>
									<label><input name='wonderplugin-audio-titleinbarscroll' type='checkbox' id='wonderplugin-audio-titleinbarscroll'  /> Automatically scroll title</label></p>
									</td>
								</tr>
								
								<tr>
									<th>Loading</th>
									<td><label><input name='wonderplugin-audio-showloading' type='checkbox' id='wonderplugin-audio-showloading'  /> Show loading message</label>
									</td>
								</tr>
								
								<tr>
									<th></th>
									<td><hr></td>
								</tr>
								
								<tr>
									<th>Play/pause button image</th>
									<td>
										<img id="wonderplugin-audio-displayplaypauseimage" />
										<br />
										<label>
											<input type="radio" name="wonderplugin-audio-playpauseimagemode" value="defined">
											<span style="display:inline-block;min-width:240px;">Select from pre-defined images:</span>
											<select name='wonderplugin-audio-playpauseimage' id='wonderplugin-audio-playpauseimage'>
											<?php 
												$playpauseimage_list = array("playpause-24-24-0.png", "playpause-24-24-1.png", "playpause-24-24-2.png", 
														"playpause-36-36-0.png", "playpause-36-36-1.png",
														"playpause-48-48-0.png", "playpause-48-48-1.png", "playpause-48-48-2.png", "playpause-48-48-3.png",
														"playpause-90-90-0.png");
												foreach ($playpauseimage_list as $playpauseimage)
													echo '<option value="' . $playpauseimage . '">' . $playpauseimage . '</option>';
											?>
											</select>
										</label>
										<br>
										<label>
											<input type="radio" name="wonderplugin-audio-playpauseimagemode" value="custom">
											<span style="display:inline-block;min-width:240px;">Use own image (absolute URL required):</span>
											<input name='wonderplugin-audio-customplaypauseimage' type='text' class="regular-text" id='wonderplugin-audio-customplaypauseimage' value='' />
										</label>
										<br />
										<script language="JavaScript">
										jQuery(document).ready(function(){
											jQuery("input:radio[name=wonderplugin-audio-playpauseimagemode]").click(function(){
												if (jQuery(this).val() == 'custom')
													jQuery("#wonderplugin-audio-displayplaypauseimage").attr("src", jQuery('#wonderplugin-audio-customplaypauseimage').val());
												else
													jQuery("#wonderplugin-audio-displayplaypauseimage").attr("src", "<?php echo WONDERPLUGIN_AUDIO_URL . 'engine/'; ?>" + jQuery('#wonderplugin-audio-playpauseimage').val());
											});

											jQuery("#wonderplugin-audio-playpauseimage").change(function(){
												if (jQuery("input:radio[name=wonderplugin-audio-playpauseimagemode]:checked").val() == 'defined')
													jQuery("#wonderplugin-audio-displayplaypauseimage").attr("src", "<?php echo WONDERPLUGIN_AUDIO_URL . 'engine/'; ?>" + jQuery(this).val());
												var playpauseimagemodesize = jQuery(this).val().split("-");
												if (playpauseimagemodesize.length > 2)
												{
													if (!isNaN(playpauseimagemodesize[1]))
														jQuery("#wonderplugin-audio-playpauseimagewidth").val(playpauseimagemodesize[1]);
													if (!isNaN(playpauseimagemodesize[2]))
														jQuery("#wonderplugin-audio-playpauseimageheight").val(playpauseimagemodesize[2]);
												}
													
											});
										});
										</script>
										<label><span style="display:inline-block;margin-right:24px;">Width:</span> <input name='wonderplugin-audio-playpauseimagewidth' type='number' class='small-text' id='wonderplugin-audio-playpauseimagewidth' value='32' /></label>
										<label><span style="display:inline-block;">Height:</span> <input name='wonderplugin-audio-playpauseimageheight' type='number' class='small-text' id='wonderplugin-audio-playpauseimageheight' value='32' /></label><br />										
									</td>
								</tr>
								
								
								<tr>
									<th>Previous/next button image</th>
									<td>
										<img id="wonderplugin-audio-displayprevnextimage" />
										<br />
										<label>
											<input type="radio" name="wonderplugin-audio-prevnextimagemode" value="defined">
											<span style="display:inline-block;min-width:240px;">Select from pre-defined images:</span>
											<select name='wonderplugin-audio-prevnextimage' id='wonderplugin-audio-prevnextimage'>
											<?php 
												$prevnextimage_list = array("prevnext-24-24-0.png", "prevnext-24-24-1.png", "prevnext-24-24-2.png", 
														"prevnext-36-36-0.png",
														"prevnext-48-48-0.png", "prevnext-48-48-1.png",
														"prevnext-90-90-0.png");
												foreach ($prevnextimage_list as $prevnextimage)
													echo '<option value="' . $prevnextimage . '">' . $prevnextimage . '</option>';
											?>
											</select>
										</label>
										<br>
										<label>
											<input type="radio" name="wonderplugin-audio-prevnextimagemode" value="custom">
											<span style="display:inline-block;min-width:240px;">Use own image (absolute URL required):</span>
											<input name='wonderplugin-audio-customprevnextimage' type='text' class="regular-text" id='wonderplugin-audio-customprevnextimage' value='' />
										</label>
										<br />
										<script language="JavaScript">
										jQuery(document).ready(function(){
											jQuery("input:radio[name=wonderplugin-audio-prevnextimagemode]").click(function(){
												if (jQuery(this).val() == 'custom')
													jQuery("#wonderplugin-audio-displayprevnextimage").attr("src", jQuery('#wonderplugin-audio-customprevnextimage').val());
												else
													jQuery("#wonderplugin-audio-displayprevnextimage").attr("src", "<?php echo WONDERPLUGIN_AUDIO_URL . 'engine/'; ?>" + jQuery('#wonderplugin-audio-prevnextimage').val());
											});

											jQuery("#wonderplugin-audio-prevnextimage").change(function(){
												if (jQuery("input:radio[name=wonderplugin-audio-prevnextimagemode]:checked").val() == 'defined')
													jQuery("#wonderplugin-audio-displayprevnextimage").attr("src", "<?php echo WONDERPLUGIN_AUDIO_URL . 'engine/'; ?>" + jQuery(this).val());
												var prevnextimagemodesize = jQuery(this).val().split("-");
												if (prevnextimagemodesize.length > 2)
												{
													if (!isNaN(prevnextimagemodesize[1]))
														jQuery("#wonderplugin-audio-prevnextimagewidth").val(prevnextimagemodesize[1]);
													if (!isNaN(prevnextimagemodesize[2]))
														jQuery("#wonderplugin-audio-prevnextimageheight").val(prevnextimagemodesize[2]);
												}
													
											});
										});
										</script>
										<label><span style="display:inline-block;margin-right:24px;">Width:</span> <input name='wonderplugin-audio-prevnextimagewidth' type='number' class='small-text' id='wonderplugin-audio-prevnextimagewidth' value='32' /></label>
										<label><span style="display:inline-block;">Height:</span> <input name='wonderplugin-audio-prevnextimageheight' type='number' class='small-text' id='wonderplugin-audio-prevnextimageheight' value='32' /></label><br />										
									</td>
								</tr>
								
								<tr>
									<th>Volume button image</th>
									<td>
										<img id="wonderplugin-audio-displayvolumeimage" />
										<br />
										<label>
											<input type="radio" name="wonderplugin-audio-volumeimagemode" value="defined">
											<span style="display:inline-block;min-width:240px;">Select from pre-defined images:</span>
											<select name='wonderplugin-audio-volumeimage' id='wonderplugin-audio-volumeimage'>
											<?php 
												$volumeimage_list = array("volume-24-24-0.png", "volume-24-24-1.png", "volume-24-24-2.png", 
														"volume-36-36-0.png");
												foreach ($volumeimage_list as $volumeimage)
													echo '<option value="' . $volumeimage . '">' . $volumeimage . '</option>';
											?>
											</select>
										</label>
										<br>
										<label>
											<input type="radio" name="wonderplugin-audio-volumeimagemode" value="custom">
											<span style="display:inline-block;min-width:240px;">Use own image (absolute URL required):</span>
											<input name='wonderplugin-audio-customvolumeimage' type='text' class="regular-text" id='wonderplugin-audio-customvolumeimage' value='' />
										</label>
										<br />
										<script language="JavaScript">
										jQuery(document).ready(function(){
											jQuery("input:radio[name=wonderplugin-audio-volumeimagemode]").click(function(){
												if (jQuery(this).val() == 'custom')
													jQuery("#wonderplugin-audio-displayvolumeimage").attr("src", jQuery('#wonderplugin-audio-customvolumeimage').val());
												else
													jQuery("#wonderplugin-audio-displayvolumeimage").attr("src", "<?php echo WONDERPLUGIN_AUDIO_URL . 'engine/'; ?>" + jQuery('#wonderplugin-audio-volumeimage').val());
											});

											jQuery("#wonderplugin-audio-volumeimage").change(function(){
												if (jQuery("input:radio[name=wonderplugin-audio-volumeimagemode]:checked").val() == 'defined')
													jQuery("#wonderplugin-audio-displayvolumeimage").attr("src", "<?php echo WONDERPLUGIN_AUDIO_URL . 'engine/'; ?>" + jQuery(this).val());
												var volumeimagemodesize = jQuery(this).val().split("-");
												if (volumeimagemodesize.length > 2)
												{
													if (!isNaN(volumeimagemodesize[1]))
														jQuery("#wonderplugin-audio-volumeimagewidth").val(volumeimagemodesize[1]);
													if (!isNaN(volumeimagemodesize[2]))
														jQuery("#wonderplugin-audio-volumeimageheight").val(volumeimagemodesize[2]);
												}
													
											});
										});
										</script>
										<label><span style="display:inline-block;margin-right:24px;">Width:</span> <input name='wonderplugin-audio-volumeimagewidth' type='number' class='small-text' id='wonderplugin-audio-volumeimagewidth' value='32' /></label>
										<label><span style="display:inline-block;">Height:</span> <input name='wonderplugin-audio-volumeimageheight' type='number' class='small-text' id='wonderplugin-audio-volumeimageheight' value='32' /></label><br />										
									</td>
								</tr>
								
								<tr>
									<th>Loop button image</th>
									<td>
										<img id="wonderplugin-audio-displayloopimage" />
										<br />
										<label>
											<input type="radio" name="wonderplugin-audio-loopimagemode" value="defined">
											<span style="display:inline-block;min-width:240px;">Select from pre-defined images:</span>
											<select name='wonderplugin-audio-loopimage' id='wonderplugin-audio-loopimage'>
											<?php 
												$loopimage_list = array("loop-24-24-0.png", "loop-24-24-1.png", "loop-24-24-2.png", 
														"loop-36-36-0.png");
												foreach ($loopimage_list as $loopimage)
													echo '<option value="' . $loopimage . '">' . $loopimage . '</option>';
											?>
											</select>
										</label>
										<br>
										<label>
											<input type="radio" name="wonderplugin-audio-loopimagemode" value="custom">
											<span style="display:inline-block;min-width:240px;">Use own image (absolute URL required):</span>
											<input name='wonderplugin-audio-customloopimage' type='text' class="regular-text" id='wonderplugin-audio-customloopimage' value='' />
										</label>
										<br />
										<script language="JavaScript">
										jQuery(document).ready(function(){
											jQuery("input:radio[name=wonderplugin-audio-loopimagemode]").click(function(){
												if (jQuery(this).val() == 'custom')
													jQuery("#wonderplugin-audio-displayloopimage").attr("src", jQuery('#wonderplugin-audio-customloopimage').val());
												else
													jQuery("#wonderplugin-audio-displayloopimage").attr("src", "<?php echo WONDERPLUGIN_AUDIO_URL . 'engine/'; ?>" + jQuery('#wonderplugin-audio-loopimage').val());
											});

											jQuery("#wonderplugin-audio-loopimage").change(function(){
												if (jQuery("input:radio[name=wonderplugin-audio-loopimagemode]:checked").val() == 'defined')
													jQuery("#wonderplugin-audio-displayloopimage").attr("src", "<?php echo WONDERPLUGIN_AUDIO_URL . 'engine/'; ?>" + jQuery(this).val());
												var loopimagemodesize = jQuery(this).val().split("-");
												if (loopimagemodesize.length > 2)
												{
													if (!isNaN(loopimagemodesize[1]))
														jQuery("#wonderplugin-audio-loopimagewidth").val(loopimagemodesize[1]);
													if (!isNaN(loopimagemodesize[2]))
														jQuery("#wonderplugin-audio-loopimageheight").val(loopimagemodesize[2]);
												}
													
											});
										});
										</script>
										<label><span style="display:inline-block;margin-right:24px;">Width:</span> <input name='wonderplugin-audio-loopimagewidth' type='number' class='small-text' id='wonderplugin-audio-loopimagewidth' value='32' /></label>
										<label><span style="display:inline-block;">Height:</span> <input name='wonderplugin-audio-loopimageheight' type='number' class='small-text' id='wonderplugin-audio-loopimageheight' value='32' /></label><br />										
									</td>
								</tr>
							</table>
						</div>
							
						<div class="wonderplugin-audio-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th>Skin CSS</th>
									<td><textarea name='wonderplugin-audio-skincss' id='wonderplugin-audio-skincss' value='' class='large-text' rows="20"></textarea></td>
								</tr>
							</table>
						</div>
						
						<div class="wonderplugin-audio-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th></th>
									<td><p><label><input name='wonderplugin-audio-donotinit' type='checkbox' id='wonderplugin-audio-donotinit'  /> Do not init the audio player when the page is loaded. Check this option if you would like to manually init the audio player with JavaScript API.</label></p>
									<p><label><input name='wonderplugin-audio-addinitscript' type='checkbox' id='wonderplugin-audio-addinitscript'  /> Add init scripts together with the audio player HTML code. Check this option if your WordPress site uses Ajax to load pages and posts.</label></p></td>
								</tr>
								<tr>
									<th>Custom CSS</th>
									<td><textarea name='wonderplugin-audio-custom-css' id='wonderplugin-audio-custom-css' value='' class='large-text' rows="10"></textarea></td>
								</tr>
								<tr>
									<th>Data Options</th>
									<td><textarea name='wonderplugin-audio-data-options' id='wonderplugin-audio-data-options' value='' class='large-text' rows="10"></textarea></td>
								</tr>
								<tr>
									<th>Custom JavaScript</th>
									<td><textarea name='wonderplugin-audio-customjs' id='wonderplugin-audio-customjs' value='' class='large-text' rows="10"></textarea><br />
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div style="clear:both;"></div>
				
			</li>
			<li class="wonderplugin-tab">
				<div id="wonderplugin-audio-preview-tab">
					<div id="wonderplugin-audio-preview-container">
					</div>
				</div>
			</li>
			<li class="wonderplugin-tab">
				<div id="wonderplugin-audio-publish-loading"></div>
				<div id="wonderplugin-audio-publish-information"></div>
			</li>
		</ul>
		</div>
		
		<?php
	}
	
	function get_list_data() {
		return array();
	}
}