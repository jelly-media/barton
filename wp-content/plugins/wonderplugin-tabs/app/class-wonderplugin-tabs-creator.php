<?php

class WonderPlugin_Tabs_Creator {

	private $parent_view, $list_table;
	
	function __construct($parent) {
		
		$this->parent_view = $parent;
	}
	
	function render( $id, $config ) {
		
		?>
		
		<?php 
		$config = str_replace("<", "&lt;", $config);
		$config = str_replace(">", "&gt;", $config);
		$config = str_replace("&quot;", "", $config);
		?>
		
		<h3><?php _e( 'General Options', 'wonderplugin_tabs' ); ?></h3>
		
		<div id="wonderplugin-tabs-id" style="display:none;"><?php echo $id; ?></div>
		<div id="wonderplugin-tabs-id-config" style="display:none;"><?php echo $config; ?></div>
		<div id="wonderplugin-tabs-jsfolder" style="display:none;"><?php echo WONDERPLUGIN_TABS_URL . 'engine/'; ?></div>
		<div id="wonderplugin-tabs-viewadminurl" style="display:none;"><?php echo admin_url('admin.php?page=wonderplugin_tabs_show_item'); ?></div>
		<div id="wonderplugin-tabs-wp-history-media-uploader" style="display:none;"><?php echo ( function_exists("wp_enqueue_media") ? "0" : "1"); ?></div>
		<div id="wonderplugin-tabs-ajaxnonce" style="display:none;"><?php echo wp_create_nonce( 'wonderplugin-tabs-ajaxnonce' ); ?></div>
		<div id="wonderplugin-tabs-saveformnonce" style="display:none;"><?php wp_nonce_field('wonderplugin-tabs', 'wonderplugin-tabs-saveform'); ?></div>
		<div id="wonderplugin-tabs-enabletinymce" style="display:none;"><?php echo get_option( 'wonderplugin_tabs_tinymceeditor', 0 ); ?></div>
		
		<?php 
			$pages = get_pages();
			$pagelist = array();
			foreach ( $pages as $page ) 
			{
				$pagelist[] = array(
					'ID' => $page->ID,
					'post_author' => get_the_author_meta('display_name', $page->post_author),
					'post_title' => esc_html(strip_tags($page->post_title)),
					'post_level' => $page->post_parent ? count(get_post_ancestors($page->ID)) : 0
				);
			}
		?>
		<div id="wonderplugin-tabs-pagelist" style="display:none;"><?php echo json_encode($pagelist); ?></div>
			
		<div style="margin:0 12px;">
		<table class="wonderplugin-form-table">
			<tr>
				<th><?php _e( 'Name', 'wonderplugin_tabs' ); ?></th>
				<td><input name="wonderplugin-tabs-name" type="text" id="wonderplugin-tabs-name" value="My Tabs" class="regular-text" /></td>
			</tr>
		</table>
		</div>

		<h3><?php _e( 'Designing', 'wonderplugin_tabs' ); ?></h3>
		
		<div style="margin:0 12px;">
		<ul class="wonderplugin-tab-buttons" id="wonderplugin-tabs-toolbar">
			<li class="wonderplugin-tab-button step1 wonderplugin-tab-buttons-selected"><?php _e( 'Tabs', 'wonderplugin_tabs' ); ?></li>
			<li class="wonderplugin-tab-button step2"><?php _e( 'Skins', 'wonderplugin_tabs' ); ?></li>
			<li class="wonderplugin-tab-button step3"><?php _e( 'Options', 'wonderplugin_tabs' ); ?></li>
			<li class="wonderplugin-tab-button step4"><?php _e( 'Preview', 'wonderplugin_tabs' ); ?></li>
			<li class="laststep"><input class="button button-primary" type="button" value="<?php _e( 'Save & Publish', 'wonderplugin_tabs' ); ?>"></input></li>
		</ul>
				
		<ul class="wonderplugin-tabs" id="wonderplugin-tabs-tabs">
			<li class="wonderplugin-tab wonderplugin-tab-selected">	
			
				<div class="wonderplugin-toolbar">	
					<input type="button" class="button" id="wonderplugin-add-tab-htmlcode" value="<?php _e( 'Add HTML Code', 'wonderplugin_tabs' ); ?>" /> 
					&nbsp;&nbsp;<input type="button" class="button" id="wonderplugin-add-tab-page" value="<?php _e( 'Add Page', 'wonderplugin_tabs' ); ?>" />
					&nbsp;&nbsp;<input type="button" class="button" id="wonderplugin-import-xml" value="<?php _e( 'Import XML', 'wonderplugin_tabs' ); ?>" />
					<label style="float:right;"><input type="button" class="button" id="wonderplugin-reverselist" value="<?php _e( 'Reverse List', 'wonderplugin_tabs' ); ?>" /></label>
					<label style="float:right;padding-top:4px;margin-right:8px;"><input type='checkbox' id='wonderplugin-newestfirst' value='' /> Add new tab to the beginning</label>
				</div>
        		
        		<ul class="wonderplugin-table" id="wonderplugin-tabs-media-table">
			    </ul>
			    <div style="clear:both;"></div>
      
			</li>
			<li class="wonderplugin-tab">
				<form>
					<fieldset>
						
						<?php 
						$skins = array(
								"horizontaltoptabs" => "Horizontal Top Tabs",
								"horizontalbottomtabs" => "Horizontal Bottom Tabs",
								"verticallefttabs" => "Vertical Left Tabs",
								"verticalleftdarktabs" => "Vertical Dark Tabs",
								"faqtabs" => "FAQ",
								"topcarouseltabs" => "Top Carousel",
								"leftcarouseltabs" => "Left Carousel",
								"accordiontabs" => "Accordion Tabs",
								"horizontalbluetabs" => "Horizontal Blue Tabs",
								"horizontaltopnav" => "Horizontal Top",
								"horizontalbottomnav" => "Horizontal Bottom"
								);
						
						foreach ($skins as $key => $value) {
						?>
							<div class="wonderplugin-tab-skin">
							<label><input type="radio" name="wonderplugin-tabs-skin" value="<?php echo $key; ?>" selected> <?php echo $value; ?> <br /><img class="selected" src="<?php echo WONDERPLUGIN_TABS_URL; ?>images/<?php echo $key; ?>.png" /></label>
							</div>
						<?php
						}
						?>
						
					</fieldset>
				</form>
			</li>
			<li class="wonderplugin-tab">
			
				<div class="wonderplugin-tabs-options">
					<div class="wonderplugin-tabs-options-menu" id="wonderplugin-tabs-options-menu">
						<div class="wonderplugin-tabs-options-menu-item wonderplugin-tabs-options-menu-item-selected"><?php _e( 'Tab Options', 'wonderplugin_tabs' ); ?></div>
						<div class="wonderplugin-tabs-options-menu-item"><?php _e( 'Responsive Options', 'wonderplugin_tabs' ); ?></div>
						<div class="wonderplugin-tabs-options-menu-item"><?php _e( 'Skin CSS', 'wonderplugin_tabs' ); ?></div>
						<div class="wonderplugin-tabs-options-menu-item"><?php _e( 'Transition Effects', 'wonderplugin_tabs' ); ?></div>
						<div class="wonderplugin-tabs-options-menu-item"><?php _e( 'Advanced Options', 'wonderplugin_tabs' ); ?></div>
					</div>
					
					<div class="wonderplugin-tabs-options-tabs" id="wonderplugin-tabs-options-tabs">
										
						<div class="wonderplugin-tabs-options-tab wonderplugin-tabs-options-tab-selected">
						
							<p class="wonderplugin-tabs-options-tab-title"><?php _e( 'Tab options will be restored to its default value if you switch to a new skin in the Skins tab.', 'wonderplugin_tabs' ); ?></p>
						
							<table class="wonderplugin-form-table-noborder">
							
								<tr>
									<th>Width</th>
									<td><label><input name="wonderplugin-tabs-width" type="number" id="wonderplugin-tabs-width" value="800" class="small-text" /> px</label>
									</td>
								</tr>
								
								<tr>
									<th>Height</th>
									<td>
									
									<label><input type="radio" name="wonderplugin-tabs-heightmode" value="fixed"> Fixed height (px): 
									<input name="wonderplugin-tabs-height" type="number" id="wonderplugin-tabs-height" value="300" class="small-text" />
									</label>
									
									<br />
									<label><input type="radio" name="wonderplugin-tabs-heightmode" value="auto"> Auto height - set minimum height (px): 
									<input name="wonderplugin-tabs-minheight" type="number" id="wonderplugin-tabs-minheight" value="300" class="small-text" />
									</label>
									
									<br />
									<label style="margin-left:14px;"><input name='wonderplugin-tabs-extendedheight' type='checkbox' id='wonderplugin-tabs-extendedheight'  /> Extend to maximum height</label>
									</td>
								</tr>
								
								<tr>
									<th>Responsive</th>
									<td><label><input name='wonderplugin-tabs-responsive' type='checkbox' id='wonderplugin-tabs-responsive'  /> Create a responsive tab group</label>
									<br /><label><input name='wonderplugin-tabs-fullwidth' type='checkbox' id='wonderplugin-tabs-fullwidth'  /> Create a full width tab group</label>
									<br /><label><input name='wonderplugin-tabs-applydisplaynonetohiddenpanel' type='checkbox' id='wonderplugin-tabs-applydisplaynonetohiddenpanel'  /> Apply CSS display:none to hidden panel</label>
									</td>
								</tr>
								
								<tr>
									<th>Horizontal Header Tab Size</th>
									<td>
									<label><input name='wonderplugin-tabs-fixedheadersize' type='checkbox' id='wonderplugin-tabs-fixedheadersize'  /> Fixed header tab size (px): </label>
									<input name="wonderplugin-tabs-headerwidth" type="number" id="wonderplugin-tabs-headerwidth" value="180" class="small-text" />
									by
									<input name="wonderplugin-tabs-headerheight" type="number" id="wonderplugin-tabs-headerheight" value="90" class="small-text" />
									</td>
								</tr>
								
								<tr class="wonderplugin-tabs-horizontal-options">
									<th>Horizontal Tabs Position</th>
									<td><label>
										<select name='wonderplugin-tabs-horizontaltabalign' id='wonderplugin-tabs-horizontaltabalign'>
										  <option value="left">Left</option>
										  <option value="center">Center</option>
										  <option value="right">Right</option>
										</select>
									</label></td>
								</tr>
								
								<tr>
									<th>Tab Icon Position</th>
									<td><label>
										<select name='wonderplugin-tabs-tabiconposition' id='wonderplugin-tabs-tabiconposition'>
										  <option value="left">Left</option>
										  <option value="top">Top</option>
										</select>
									</label></td>
								</tr>
								
								<tr>
									<th>First Visible Tab ID</th>
									<td><input name="wonderplugin-tabs-firstid" type="number" id="wonderplugin-tabs-firstid" value="0" class="small-text" />
									</td>
								</tr>

								<tr>
									<th></th>
									<td>
										<label><input name='wonderplugin-tabs-savestatusincookie' type='checkbox' id='wonderplugin-tabs-savestatusincookie'  /> Save active tab id in session cookie</label>
										<p style="margin-left:20px;font-style:italic;">If the above option is enabled, the activated tab id will be saved in the session cookie, so in the same web browser session, if the visitor goes back to the webpage, it will open the last activated tab. Please update your website cookie policy to let your visitors know the cookies are used if it's needed.</p>
										<p><label><input name='wonderplugin-tabs-switchonmouseover' type='checkbox' id='wonderplugin-tabs-switchonmouseover'  /> Switch tabs on mouse over</label></p>
										<p><label><input name='wonderplugin-tabs-closeall' type='checkbox' id='wonderplugin-tabs-closeall'  /> Hide all panels on page load</label></p>
									</td>
								</tr>
								
								<tr>
									<th>AJAX Loading</th>
									<td>
										<label><input name='wonderplugin-tabs-ajaxloading' type='checkbox' id='wonderplugin-tabs-ajaxloading'  /> AJAX loading tab content</label>
										<br><label><input name='wonderplugin-tabs-ajaxloadingfirst' type='checkbox' id='wonderplugin-tabs-ajaxloadingfirst'  /> AJAX loading the first visible tab content</label>
										<p style="margin-left:20px;font-style:italic;">To enable AJAX loading, in the WordPress backend, left menu, WonderPlugin Tabs -> Settings, select the option <strong>Allow AJAX tab content loading</strong>".</p>
									</td>
								</tr>
								
								<tr>
									<th>Accordion Mode</th>
									<td>
										<label><input name='wonderplugin-tabs-accordionmultiple' type='checkbox' id='wonderplugin-tabs-accordionmultiple'  /> Allow multiple tabs beging open at the same time</label>
										<br><label><input name='wonderplugin-tabs-accordioncloseall' type='checkbox' id='wonderplugin-tabs-accordioncloseall'  /> Close all tabs on page load</label>
									</td>
								</tr>
								
								<tr>
									<th>Keyboard Accessibility</th>
									<td>
										<label><input name='wonderplugin-tabs-keyaccess' type='checkbox' id='wonderplugin-tabs-keyaccess'  /> Use left and right arrows to navigate between tabs</label><br />
										<label><input name='wonderplugin-tabs-enabletabindex' type='checkbox' id='wonderplugin-tabs-enabletabindex'  /> Support tabindex attribute: use Tab key to navigate and focus on a tab, then press the Enter key to switch to the tab</label>
									</td>
								</tr>
								
								<tr>
									<th>Page Tabs</th>
									<td>
										<label><input name='wonderplugin-tabs-disablewpautop' type='checkbox' id='wonderplugin-tabs-disablewpautop'  /> Disable wpautop for WordPress page contents </label>
									</td>
								</tr>
								
								<tr>
									<th>Google Analytics</th>
									<td>
										<label><input name='wonderplugin-tabs-enablega' type='checkbox' id='wonderplugin-tabs-enablega'  /> Enable Google Analytics </label>
										<p>Google tracking ID: <input name="wonderplugin-tabs-gatrackingid" type="text" id="wonderplugin-tabs-gatrackingid" value="" class="medium-text" /></p>
									</td>
								</tr>
								
							</table>
						</div>					
						
						<div class="wonderplugin-tabs-options-tab">
							<table class="wonderplugin-form-table-noborder">
								
								<tr>
									<th>Tabs on small screen</th>
									<td>
									When screen width is less than (px): <input name="wonderplugin-tabs-fullwidthtabsonsmallscreenwidth" type="number" id="wonderplugin-tabs-fullwidthtabsonsmallscreenwidth" value="480" class="small-text" />
									<br><label><input name='wonderplugin-tabs-fullwidthtabsonsmallscreen' type='checkbox' id='wonderplugin-tabs-fullwidthtabsonsmallscreen'  /> Show tabs as full width</label>
									<br><label><input name='wonderplugin-tabs-accordiononsmallscreen' type='checkbox' id='wonderplugin-tabs-accordiononsmallscreen'  /> Switch to accordion mode</label>
									</td>
								</tr>
								
								<tr>
									<th>Responsive mode when tabs can not fit in</th>
									<td>
									
									<div style="margin-bottom:12px;">
									<label><input name='wonderplugin-tabs-hidetitleonsmallscreen' type='checkbox' id='wonderplugin-tabs-hidetitleonsmallscreen'  /> Hide tab title when screen width is less than (px): 
									<input name="wonderplugin-tabs-hidetitleonsmallscreenwidth" type="number" id="wonderplugin-tabs-hidetitleonsmallscreenwidth" value="643" class="small-text" /></label>
									</div>
									
									<label><input type="radio" name="wonderplugin-tabs-responsivemode" value="menu"> Display drop-down menu</label>
									
									<div class="wonderplugin-tabs-responsivemode-menu-options" style="margin:8px 0px 8px 14px;">
									<p>
									<label>Menu text: <input name="wonderplugin-tabs-dropdownmenutext" type="text" id="wonderplugin-tabs-dropdownmenutext" value="More" class="medium-text" /></label>
									</p><p>
									<label>Menu icon: <input name="wonderplugin-tabs-dropdownmenuicon" type="text" id="wonderplugin-tabs-dropdownmenuicon" value="fa-angle-down" class="medium-text" /></label>
									</p><p>
									<a href='http://fortawesome.github.io/Font-Awesome/cheatsheet/' target='_blank' class='wonderplugintabs-help'>View The Complete Font Awesome Icon List</a>
									</p>
									</div>
									
									<label><input type="radio" name="wonderplugin-tabs-responsivemode" value="arrow"> Display navigation arrows</label>
									
									<div class="wonderplugin-tabs-responsivemode-arrow-options" style="margin:8px 0px 8px 14px;">
									<p>
									<label><input type="radio" name="wonderplugin-tabs-tabarrowmode" value="slide"> Click arrow to slide tabs</label>
									</p><p>
									<label><input type="radio" name="wonderplugin-tabs-tabarrowmode" value="switch"> Click arrow to switch tabs</label>
									</p><p>
									<label>Previous arrow icon: <input name="wonderplugin-tabs-arrowprevicon" type="text" id="wonderplugin-tabs-arrowprevicon" value="fa-angle-up" class="medium-text" /></label>
									<button class="button" id="wonderplugin-tabs-select-arrowprevicon">Select Font Awesome Icon</button>
									</p><p>
									<label>Next arrow icon: <input name="wonderplugin-tabs-arrownexticon" type="text" id="wonderplugin-tabs-arrownexticon" value="fa-angle-down" class="medium-text" /></label>
									<button class="button" id="wonderplugin-tabs-select-arrownexticon">Select Font Awesome Icon</button>
									</p>
									<p><label>Arrow icon font size: <input name="wonderplugin-tabs-arrowfontsize" type="number" id="wonderplugin-tabs-arrowfontsize" value="18" class="small-text" /></label>
									<label style="margin-left:12px;">Arrow icon color: <input name="wonderplugin-tabs-arrowfontcolor" type="text" id="wonderplugin-tabs-arrowfontcolor" value="#666" class="medium-text" /></label>
									</p>
									<p>
									Arrow width of horizontal skins: <input name="wonderplugin-tabs-horizontalarrowwidth" type="number" id="wonderplugin-tabs-horizontalarrowwidth" value="32" class="small-text" />
									</p>
									</div>
									
									</td>
								</tr>
								
								<tr>
									<th>Trigger resize event</th>
									<td>
										<label><input name='wonderplugin-tabs-triggerresize' type='checkbox' id='wonderplugin-tabs-triggerresize'  /> Trigger window resize event when switching tabs: </label><label> <input name="wonderplugin-tabs-triggerresizetimeout" type="number" id="wonderplugin-tabs-triggerresizetimeout" value="100" class="small-text" /> milliseconds after the tab is switched </label>
										<br><label><input name='wonderplugin-tabs-triggerresizeonload' type='checkbox' id='wonderplugin-tabs-triggerresizeonload'  /> Trigger window resize event: </label><label> <input name="wonderplugin-tabs-triggerresizeonloadtimeout" type="number" id="wonderplugin-tabs-triggerresizeonloadtimeout" value="100" class="small-text" /> milliseconds after tabs have inited </label>
										
									</td>
								</tr>
								
							</table>
						</div>
						
						<div class="wonderplugin-tabs-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr class="wonderplugin-tabs-skinoptions">
									<th>Skin CSS Options</th>
									<td></td>
								</tr>
								<tr>
									<th>Skin CSS</th>
									<td><textarea name='wonderplugin-tabs-skincss' id='wonderplugin-tabs-skincss' value='' class='large-text' rows="20"></textarea></td>
								</tr>
							</table>
						</div>
						
						<div class="wonderplugin-tabs-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th><p>Transition Effect</p></th>
									<td>
										<p><label><input name='wonderplugin-tabs-effect-fade' type='checkbox' id='wonderplugin-tabs-effect-fade' value='fade' /> Fade</label></p>
										<p><label><input name='wonderplugin-tabs-effect-fadeout' type='checkbox' id='wonderplugin-tabs-effect-fadeout' value='fadeout' /> Fade Out</label></p>
										<p><label><input name='wonderplugin-tabs-effect-slide' type='checkbox' id='wonderplugin-tabs-effect-slide' value='slide' /> Slide</label></p>
									</td>
								</tr>
							</table>
						</div>
						
						<div class="wonderplugin-tabs-options-tab">
							<table class="wonderplugin-form-table-noborder">
								<tr>
									<th></th>
									<td><p><label><input name='wonderplugin-tabs-donotinit' type='checkbox' id='wonderplugin-tabs-donotinit'  /> Do not init the tabs when the page is loaded. Check this option if you would like to manually init the tabs with JavaScript API.</label></p>
									<p><label><input name='wonderplugin-tabs-addinitscript' type='checkbox' id='wonderplugin-tabs-addinitscript'  /> Add init scripts together with tabs HTML code. Check this option if your WordPress site uses Ajax to load pages and posts.</label></p></td>
								</tr>
								<tr>
								<tr>
									<th>Custom CSS</th>
									<td><textarea name='wonderplugin-tabs-customcss' id='wonderplugin-tabs-customcss' value='' class='large-text' rows="10"></textarea></td>
								</tr>
								<tr>
									<th>Data Options</th>
									<td><textarea name='wonderplugin-tabs-dataoptions' id='wonderplugin-tabs-dataoptions' value='' class='large-text' rows="10"></textarea></td>
								</tr>
								<tr>
									<th>Custom JavaScript</th>
									<td><textarea name='wonderplugin-tabs-customjs' id='wonderplugin-tabs-customjs' value='' class='large-text' rows="10"></textarea><br />
									</td>
								</tr>
							</table>
						</div>
						
					</div>
				</div>
				<div style="clear:both;"></div>
				
			</li>
			<li class="wonderplugin-tab">
				<div id="wonderplugin-tabs-preview-title" style="font-weight:bold;">The XML content, HTML content CSS, WordPress shortcode and page content are not available in the Preview tab. To view the CSS effect and shortcode, save and publish the tab group, then paste the provided shortcode to a post or page.</div>
				<div id="wonderplugin-tabs-preview-tab">
					<div id="wonderplugin-tabs-preview-container">
					</div>
				</div>
			</li>
			<li class="wonderplugin-tab">
				<div id="wonderplugin-tabs-publish-loading"></div>
				<div id="wonderplugin-tabs-publish-information"></div>
			</li>
		</ul>
		</div>
		
		<?php
	}
	
	function get_list_data() {
		return array();
	}
}