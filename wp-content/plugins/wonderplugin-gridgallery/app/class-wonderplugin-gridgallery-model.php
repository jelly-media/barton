<?php 

require_once 'wonderplugin-gridgallery-functions.php';

class WonderPlugin_Gridgallery_Model {

	private $controller;
	
	function __construct($controller) {
		
		$this->controller = $controller;
	}
	
	function get_upload_path() {
		
		$uploads = wp_upload_dir();
		return $uploads['basedir'] . '/wonderplugin-gridgallery/';
	}
	
	function get_upload_url() {
	
		$uploads = wp_upload_dir();
		return $uploads['baseurl'] . '/wonderplugin-gridgallery/';
	}
	
	function get_socialmedia_color($item) {
	
		$socialbgcolor = array(
				'facebook' => '#3b5998',
				'dribbble'=> '#d94a8b',
				'dropbox'=> '#477ff2',
				'mail'=> '#4d83ff',
				'flickr'=> '#3c58e6',
				'git'=> '#4174ba',
				'gplus'=> '#e45104',
				'instagram'=> '#d400c8',
				'linkedin'=> '#458bb7',
				'pinterest'=> '#c92228',
				'reddit'=> '#ee5300',
				'skype'=> '#53adf5',
				'tumblr'=> '#415878',
				'twitter'=> '#03b3ee',
				'link'=> '#517dd9',
				'whatsapp'=> '#72be44',
				'youtube'=> '#c7221b'
		);
	
		if ( array_key_exists($item, $socialbgcolor))
			return $socialbgcolor[$item];
		else
			return '#333333';
	}
	
	function xml_cdata( $str ) {

		if ( ! seems_utf8( $str ) ) {
			$str = utf8_encode( $str );
		}

		$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

		return $str;
	}

	function replace_data($replace_list, $data)
	{
		foreach($replace_list as $replace)
		{
			$data = str_replace($replace['search'], $replace['replace'], $data);
		}

		return $data;
	}

	function search_replace_items($post)
	{
		$allitems = sanitize_text_field($_POST['allitems']);
		$itemid = sanitize_text_field($_POST['itemid']);

		$replace_list = array();
		for ($i = 0; ; $i++)
		{
			if (empty($post['standalonesearch' . $i]) || empty($post['standalonereplace' . $i]))
				break;

			$replace_list[] = array(
					'search' => str_replace('/', '\\/', $post['standalonesearch' . $i]),
					'replace' => str_replace('/', '\\/', $post['standalonereplace' . $i])
			);
		}

		global $wpdb;

		if (!$this->is_db_table_exists())
			$this->create_db_table();

		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";

		$total = 0;

		foreach($replace_list as $replace)
		{
			$search = $replace['search'];
			$replace = $replace['replace'];

			if ($allitems)
			{
				$ret = $wpdb->query( $wpdb->prepare(
						"UPDATE $table_name SET data = REPLACE(data, %s, %s) WHERE INSTR(data, %s) > 0",
						$search,
						$replace,
						$search
				));
			}
			else
			{
				$ret = $wpdb->query( $wpdb->prepare(
						"UPDATE $table_name SET data = REPLACE(data, %s, %s) WHERE INSTR(data, %s) > 0 AND id = %d",
						$search,
						$replace,
						$search,
						$itemid
				));
			}

			if ($ret > $total)
				$total = $ret;
		}

		if (!$total)
		{
			return array(
					'success' => false,
					'message' => 'No grid gallery modified' .  (isset($wpdb->lasterror) ? $wpdb->lasterror : '')
			);
		}

		return array(
				'success' => true,
				'message' => sprintf( _n( '%s grid gallery', '%s grid galleries', $total), $total) . ' modified'
		);
	}

	function import_gridgallery($post, $files)
	{
		if (!isset($files['importxml']))
		{
			return array(
					'success' => false,
					'message' => 'No file or invalid file sent.'
			);
		}

		if (!empty($files['importxml']['error']))
		{
			$message = 'XML file error.';

			switch ($files['importxml']['error']) {
				case UPLOAD_ERR_NO_FILE:
					$message = 'No file sent.';
					break;
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$message = 'Exceeded filesize limit.';
					break;
			}

			return array(
					'success' => false,
					'message' => $message
			);
		}

		if ($files['importxml']['type'] != 'text/xml')
		{
			return array(
					'success' => false,
					'message' => 'Not an xml file'
			);
		}

		add_filter( 'wp_check_filetype_and_ext', 'wonderplugin_gridgallery_wp_check_filetype_and_ext', 10, 4);

		$xmlfile = wp_handle_upload($files['importxml'], array(
				'test_form' => false,
				'mimes' => array('xml' => 'text/xml')
		));

		remove_filter( 'wp_check_filetype_and_ext', 'wonderplugin_gridgallery_wp_check_filetype_and_ext');

		if ( empty($xmlfile) || !empty( $xmlfile['error'] ) ) {
			return array(
					'success' => false,
					'message' => (!empty($xmlfile) && !empty( $xmlfile['error'] )) ? $xmlfile['error']: 'Invalid xml file'
			);
		}

		$content = file_get_contents($xmlfile['file']);

		$xmlparser = xml_parser_create();
		xml_parse_into_struct($xmlparser, $content, $values, $index);
		xml_parser_free($xmlparser);

		if (empty($index) || empty($index['WONDERPLUGINGRIDGALLERY']) || empty($index['ID']))
		{
			return array(
					'success' => false,
					'message' => 'Not an exported xml file'
			);
		}

		$keepid = (!empty($post['keepid'])) ? true : false;
		$authorid = sanitize_text_field($post['authorid']);

		$replace_list = array();
		for ($i = 0; ; $i++)
		{
			if (empty($post['olddomain' . $i]) || empty($post['newdomain' . $i]))
				break;

			$replace_list[] = array(
					'search' => str_replace('/', '\\/', sanitize_text_field($post['olddomain' . $i])),
					'replace' => str_replace('/', '\\/', sanitize_text_field($post['newdomain' . $i]))
			);
		}

		$import_items = Array();
		foreach($index['ID'] as $key => $val)
		{
			$import_items[] = Array(
					'id' => ($keepid ? $values[$index['ID'][$key]]['value'] : 0),
					'name' => $values[$index['NAME'][$key]]['value'],
					'data' => $this->replace_data($replace_list, $values[$index['DATA'][$key]]['value']),
					'time' => $values[$index['TIME'][$key]]['value'],
					'authorid' => $authorid
			);
		}

		if (empty($import_items))
		{
			return array(
					'success' => false,
					'message' => 'No gallery found'
			);
		}

		global $wpdb;

		if (!$this->is_db_table_exists())
			$this->create_db_table();

		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";

		$total = 0;
		foreach($import_items as $import_item)
		{
			$ret = $wpdb->query($wpdb->prepare(
					"
					INSERT INTO $table_name (id, name, data, time, authorid)
					VALUES (%d, %s, %s, %s, %s) ON DUPLICATE KEY UPDATE
					name=%s, data=%s, time=%s, authorid=%s
					",
					$import_item['id'], $import_item['name'], $import_item['data'], $import_item['time'], $import_item['authorid'],
					$import_item['name'], $import_item['data'], $import_item['time'], $import_item['authorid']
			));

			if ($ret)
				$total++;
		}

		if (!$total)
		{
			return array(
					'success' => false,
					'message' => 'No gallery imported' .  (isset($wpdb->lasterror) ? $wpdb->lasterror : '')
			);
		}

		return array(
				'success' => true,
				'message' => sprintf( _n( '%s gallery', '%s galleries', $total), $total) . ' imported'
		);

	}

	function export_gridgallery()
	{
		if ( !check_admin_referer('wonderplugin-gridgallery', 'wonderplugin-gridgallery-export') || !isset($_POST['allgridgallery']) || !isset($_POST['gridgalleryid']) || !is_numeric($_POST['gridgalleryid']) )
			exit;

		$allgridgallery = sanitize_text_field($_POST['allgridgallery']);
		$gridgalleryid = sanitize_text_field($_POST['gridgalleryid']);
		$exportplaylist = (isset($_POST['exportplaylist']) && ($_POST['exportplaylist'] == '1'));
				
		if ($allgridgallery)
		{
			$data = $this->get_list_data(true);
			$filename = 'wonderplugin_gridgallery_export_all.xml';
		}
		else
		{
			$data = array($this->get_list_item_data($gridgalleryid));
			
			if ($exportplaylist)
				$filename = 'wonderplugin_gridgallery_filelist_' . $gridgalleryid . '.xml';
			else
				$filename = 'wonderplugin_gridgallery_export_' . $gridgalleryid . '.xml';
		}

		header('Content-Description: File Transfer');
		header("Content-Disposition: attachment; filename=" . $filename);
		header('Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true);
		header("Cache-Control: no-cache, no-store, must-revalidate");
		header("Pragma: no-cache");
		header("Expires: 0");
		$output = fopen("php://output", "w");

		echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
		
		if ($allgridgallery || !$exportplaylist)
		{
			echo "<WONDERPLUGINGRIDGALLERY>\r\n";
			foreach($data as $row)
			{
				if (empty($row))
					continue;
			
				echo "<ID>" . intval($row["id"]) . "</ID>\r\n";
				echo "<NAME>" . $this->xml_cdata($row["name"]) . "</NAME>\r\n";
				echo "<DATA>" . $this->xml_cdata($row["data"]) . "</DATA>\r\n";
				echo "<TIME>" . $this->xml_cdata($row["time"]) . "</TIME>\r\n";
				echo "<AUTHORID>" . $this->xml_cdata($row["authorid"]) . "</AUTHORID>\r\n";
			}
			echo '</WONDERPLUGINGRIDGALLERY>';
		}
		else
		{
			echo "<list>\r\n";
			
			foreach($data as $row)
			{
				if (empty($row))
					continue;
				
				$data = json_decode(trim($row["data"]));

				if (isset($data) && isset($data->slides) && (count($data->slides) > 0))
				{
					foreach ($data->slides as $slide)
					{
						echo "\t<item>\r\n";
						foreach($slide as $key => $value)
						{
							echo "\t\t<" . $key . ">" . $this->xml_cdata($value) . "</" . $key . ">\r\n";
						}
						echo "\t</item>\r\n";
					}
				}
			}
			
			echo "</list>";
		}

		fclose($output);
		exit;
	}

	function get_list_item_data($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";

		return $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) , ARRAY_A);
	}
		
	function generate_lightbox_code($id, $data, $slide, $isbutton) {
		
		$code_template = '<a';
		
		$isinlinevideo = ($slide->type >= 1 && $slide->type <= 5 && ($slide->playvideoinline || $slide->loadvideoinline) && !$isbutton);
		
		if ($slide->type == 1 && isset($data->lightboxenablehtml5poster) && strtolower($data->lightboxenablehtml5poster) == 'true')
		{
			$code_template .= ' data-html5videoposter="' . $slide->image . '"';
		}
		
		if (!$isinlinevideo)
		{
			$code_template .= ' class="wpgridlightbox wpgridlightbox-' . $id . '" data-thumbnail="' . $slide->thumbnail . '"';
				
			if ( isset($data->lightboxcategorygroup) && strtolower($data->lightboxcategorygroup) === 'true' && !empty($slide->category))
			{
				$categories = explode(':', $slide->category);
				for ($i = 0; $i < count($categories); $i++)
				{
				if ($i == 0)
					$code_template .= ' data-wpggroup="wpgridgallery-' . $id . '-' . $categories[$i];
					else
					$code_template .= ':wpgridgallery-' . $id . '-' . $categories[$i];
				}
				if (count($categories) > 0)
					$code_template .= '"';
			}
			else if ( !isset($data->lightboxnogroup) || strtolower($data->lightboxnogroup) !== 'true' )
			{
				$code_template .= ' data-wpggroup="wpgridgallery-' . $id . '"';
			}
		}
		else
		{
			if ($slide->loadvideoinline)
			{
				if ($slide->type == 1)
					$code_template .= ' class="wpgridloadhtml5video"';
				else
					$code_template .= ' class="wpgridloadiframevideo"';
			}
			else
			{
				if ($slide->type == 1)
					$code_template .= ' class="wpgridinlinehtml5video"';
				else
					$code_template .= ' class="wpgridinlineiframevideo"';
			}
		}
			
		if ($slide->type >= 1 && $slide->type <= 5)
		{
			$code_template .= ' data-isvideo="1"';
		}
		
		if ($slide->type == 0)
		{
			$code_template .= ' href="' . $slide->image . '"';
		}
		else if ($slide->type == 1)
		{
			$code_template .= ' href="' . $slide->mp4 . '"';
			if ($slide->webm)
				$code_template .= ' data-webm="' . $slide->webm . '"';
		}
		else if ($slide->type == 2 || $slide->type == 3 || $slide->type == 4 || $slide->type == 5)
		{
			$code_template .= ' href="' . $slide->video . '"';
			
			if ($slide->type == 5)
				$code_template .= ' data-mediatype=12';
		}
		else if ($slide->type == 7)
		{
			$code_template .= ' href="' . $slide->weblink . '"';
		}
		
		if (!$isinlinevideo && $slide->lightboxsize)
			$code_template .= ' data-width="' . $slide->lightboxwidth . '" data-height="' . $slide->lightboxheight . '"';
		
		return $code_template;
	}
				
	function gen_categories($categorylist, $categoryposition, $categorystyle, $categorydefault, $categoryhideall, $categorymenucaption) {
		
		$list = json_decode($categorylist);
		
		$categoryregulardropdown = (substr($categorystyle, -16) == 'regular-dropdown');
		
		if ($categoryregulardropdown)
		{
			$ret = '<div class="wonderplugin-gridgallery-tags wonderplugin-gridgallery-tags-' . $categoryposition . ' wpp-category-regular-dropdown">';
			$ret .= '<label><span class="wonderplugin-gridgallery-tag-dropdown-caption">' . $categorymenucaption . '</span><select class="wonderplugin-gridgallery-tag-dropdown">';
			
			foreach($list as $category)
			{
				if ($categoryhideall && $category->slug == 'all')
					continue;
					
				$ret .= '<option value="' . $category->slug . '">' . $category->caption . '</option>';
			}
			
			$ret .= '</select></label>';
			$ret .= '</div>';
		}
		else
		{
			$ismenu = (substr($categorystyle, -12) == 'dropdownmenu');
			
			$ret = '<div class="wonderplugin-gridgallery-tags wonderplugin-gridgallery-tags-' . $categoryposition . ' ' . $categorystyle . '">';
			
			if ($ismenu)
			{
				$ret .= '<div class="wonderplugin-gridgallery-selectcategory">' . $categorymenucaption . '</div>';
				$ret .= '<div class="wonderplugin-gridgallery-menu">';
			}
			
			foreach($list as $category)
			{
				if ($categoryhideall && $category->slug == 'all')
					continue;
					
				$ret .= '<div class="wonderplugin-gridgallery-tag" data-slug="' . $category->slug . '">' . $category->caption . '</div>';
			}
			
			if ($ismenu)
			{
				$ret .= '</div>';
			}
			
			$ret .= '</div>';
		}

		return $ret;
	}
	
	function generate_button_code($id, $data, $slide, $socialmedia) {

		$button_code = '';
		
		if (isset($slide->button) && strlen($slide->button) > 0)
		{
			if (isset($slide->buttonlightbox) && $slide->buttonlightbox)
			{
				$button_code .= $this->generate_lightbox_code($id, $data, $slide, true);
			}
			else if ($slide->buttonlink && strlen($slide->buttonlink) > 0)
			{
				$button_code .= '<a href="' . $slide->buttonlink . '"';
				if ($slide->buttonlinktarget && strlen($slide->buttonlinktarget) > 0)
					$button_code .= ' target="' . $slide->buttonlinktarget . '"';
			}
			
			if ( (isset($slide->buttonlightbox) && $slide->buttonlightbox)  || ($slide->buttonlink && strlen($slide->buttonlink) > 0) )
			{
				if ( !isset($data->donotaddtext) || strtolower($data->donotaddtext) === 'false')
				{
					if (isset($slide->title) && strlen($slide->title) > 0)
						$button_code .= ' data-title="' . str_replace("\"", "&quot;", $slide->title) . '"';
				
					if (isset($slide->description) && strlen($slide->description) > 0)
						$button_code .= ' data-description="' .  str_replace("\"", "&quot;", $slide->description) . '"';
						
					if (isset($data->lightboxaddsocialmedia) && (strtolower($data->lightboxaddsocialmedia) === 'true'))
						$button_code .= ' data-socialmedia="' .  str_replace("\"", "&quot;", $socialmedia) . '"';
				}
				
				$button_code .= '>';
			}
			
			$button_code .= '<button class="' . $slide->buttoncss . '">' . $slide->button . '</button>';
				
			if ( (isset($slide->buttonlightbox) && $slide->buttonlightbox)  || ($slide->buttonlink && strlen($slide->buttonlink) > 0) )
			{
				$button_code .= '</a>';
			}
		}
		
		return $button_code;
	}
	
	function generate_image_code($slide, $data, $id, $hide_item, $socialmedia) {

		$code_template = '';
		
		if (isset($slide->lightbox) && $slide->lightbox)
		{
			$code_template .= $this->generate_lightbox_code($id, $data, $slide, false);
		}
		else if (!empty($slide->weblink))
		{
			$code_template .= '<a href="' . $slide->weblink . '"';

			if (isset($slide->clickhandler) && $slide->clickhandler && strlen($slide->clickhandler) > 0)
				$code_template .= ' onclick="' . str_replace('"', '&quot;', $slide->clickhandler) . '"';

			if (isset($slide->linktarget) && $slide->linktarget && strlen($slide->linktarget) > 0)
				$code_template .= ' target="' . $slide->linktarget . '"';

			if ( isset($slide->weblinklightbox) && $slide->weblinklightbox )
			{
				$code_template .= '" class="wpgridlightbox wpgridlightbox-' . $id . '" data-thumbnail="' . $slide->thumbnail . '"';

				if ( isset($data->lightboxcategorygroup) && strtolower($data->lightboxcategorygroup) === 'true' && !empty($slide->category))
				{
					$categories = explode(':', $slide->category);
					for ($i = 0; $i < count($categories); $i++)
					{
						if ($i == 0)
							$code_template .= ' data-wpggroup="wpgridgallery-' . $id . '-' . $categories[$i];
						else
							$code_template .= ':wpgridgallery-' . $id . '-' . $categories[$i];
					}
					if (count($categories) > 0)
						$code_template .= '"';
				}
				else if ( !isset($data->lightboxnogroup) || strtolower($data->lightboxnogroup) !== 'true' )
				{
					$code_template .= ' data-wpggroup="wpgridgallery-' . $id . '"';
				}

				if ($slide->lightboxsize)
					$code_template .= ' data-width="' .  $slide->lightboxwidth . '" data-height="' .  $slide->lightboxheight . '"';
			}
		}
		else
		{
			$code_template .= '<a href="#" onClick="return false;" style="cursor:default;"';
		}

		if ( !isset($data->donotaddtext) || strtolower($data->donotaddtext) === 'false')
		{
			if (isset($slide->title) && strlen($slide->title) > 0)
				$code_template .= ' data-title="' . str_replace("\"", "&quot;", $slide->title) . '"';

			if (isset($slide->description) && strlen($slide->description) > 0)
				$code_template .= ' data-description="' .  str_replace("\"", "&quot;", $slide->description) . '"';
			
			if (isset($data->lightboxaddsocialmedia) && (strtolower($data->lightboxaddsocialmedia) === 'true'))
				$code_template .= ' data-socialmedia="' .  str_replace("\"", "&quot;", $socialmedia) . '"';
		}

		$code_template .= '>';

		$code_template .= '<img class="wonderplugin-gridgallery-item-img"';

		if (!isset($data->donotaddtext) || strtolower($data->donotaddtext) === 'false')
		{
			if (isset($slide->altusetitle) && strtolower($slide->altusetitle) === 'false' && isset($slide->alt) && strlen($slide->alt) > 0)
				$code_template .= ' alt="' . str_replace("\"", "&quot;", $slide->alt) . '"';
			else if (isset($slide->title) && strlen($slide->title) > 0)
				$code_template .= ' alt="' . str_replace("\"", "&quot;", $slide->title) . '"';
		}

		if ( (isset($data->deferloading) && (strtolower($data->deferloading) === 'true')) || ($hide_item && isset($data->lazyloadimages) && (strtolower($data->lazyloadimages) === 'true')))
			$code_template .= ' data-wpplazysrc="';
		else
			$code_template .= ' src="';

		if ($slide->displaythumbnail)
			$code_template .= $slide->thumbnail;
		else
			$code_template .= $slide->image;
		$code_template .= '" />';

		$code_template .= '</a>';

		if (!isset($data->usetemplatefortextoverlay) || strtolower($data->usetemplatefortextoverlay) === 'false')
		{
			if (isset($slide->button) && strlen($slide->button) > 0)
			{
				$button_code = '<div class="wonderplugin-gridgallery-item-button" style="display:none;">';
				$button_code .= $this->generate_button_code($id, $data, $slide, $socialmedia);
				$button_code .= '</div>';

				$code_template .= $button_code;
			}
		}
		
		return $code_template;
	}
	
	function generate_socialmedia_code($slide) {
	
		$socialmedia = '';
	
		try
		{
			$sociallist = json_decode($slide->socialmedia, true);
		}
		catch (Exception $e) {
		}
			
		$socialtarget = empty($slide->socialmediatarget) ? '' : (' target="' . $slide->socialmediatarget . '"');
		$socialrotate = (isset($slide->socialmediarotate) && (strtolower($slide->socialmediarotate) === 'true')) ? ' wpgridgallery-socialmedia-rotate' : '';
			
		if (!empty($sociallist))
		{
			foreach($sociallist as $social)
			{
				$socialurl = ($social['name'] == 'mail' && substr( $social['url'], 0, 7 ) !== 'mailto:') ? ('mailto:' . $social['url']) : $social['url'];
				$socialmedia .= '<div class="wpgridgallery-socialmedia-button"><a' . $socialtarget . ' href="' . $socialurl . '">' .
						'<div class="wpgridgallery-socialmedia-icon' . $socialrotate . ' mh-icon-' . $social['name'] . '" style="background-color:' . $this->get_socialmedia_color($social['name']). ';"></div>'. '</a></div>';
			}
		}
	
		return $socialmedia;
	}
	
	function generate_body_code($id, $has_wrapper, $datatags = null) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";
		
		if ( !$this->is_db_table_exists() )
		{
			return '<p>The specified grid gallery does not exist.</p>';
		}
		
		$sanitizehtmlcontent = get_option( 'wonderplugin_gridgallery_sanitizehtmlcontent', 1 );
		
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{	
			$data = $item_row->data;
			
			$data = json_decode(trim($data));
			
			if (!empty($datatags))
			{
				foreach($datatags as $key => $attr)
				{
					$data->{$key} = $attr;
				}
			}
			
			if ( isset($data->publish_status) && ($data->publish_status === 0) )
			{
				return '<p>The specified gallery is trashed.</p>';
			}
			
			if ($sanitizehtmlcontent == 1)
			{
				add_filter('safe_style_css', 'wonderplugin_gridgallery_css_allow');
				add_filter('wp_kses_allowed_html', 'wonderplugin_gridgallery_tags_allow', 'post');
				
				foreach($data as $datakey => &$value)
				{
					if ($datakey == 'gridtemplate' || $datakey == 'customjs')
						continue;
					
					if ( is_string($value) )
						$value = wp_kses_post($value);
				}
			}
			
			if (isset($data->customcss) && strlen($data->customcss) > 0)
			{
				$customcss = str_replace("\r", " ", $data->customcss);
				$customcss = str_replace("\n", " ", $customcss);
				$customcss = str_replace("GRIDGALLERYID", $id, $customcss);
				$ret .= '<style type="text/css">' . $customcss . '</style>';
			}
			
			if (isset($data->skincss) && strlen($data->skincss) > 0)
			{
				$skincss = str_replace("\r", " ", $data->skincss);
				$skincss = str_replace("\n", " ", $skincss);
				
				if (strpos($skincss, 'wpgridgallery-socialmedia-button') === false)
				{
					$skincss .= ' .wpgridgallery-socialmedia-button { display: inline-block; margin: 4px; }.wpgridgallery-socialmedia-button a { box-shadow: none; }.wpgridgallery-socialmedia-icon { display:table-cell; width:32px; height:32px; font-size:18px; border-radius:50%; color:#fff; vertical-align:middle; text-align:center; cursor:pointer; padding:0;}.wpgridgallery-socialmedia-rotate { transition: transform .4s ease-in; } .wpgridgallery-socialmedia-rotate:hover { transform: rotate(360deg); }';
				}
				
				$skincss = str_replace('#wonderplugingridgallery-GRIDGALLERYID',  '#wonderplugingridgallery-' . $id, $skincss);
				$ret .= '<style type="text/css">' . $skincss . '</style>';
			}
			
			if (isset($data->categorycss) && strlen($data->categorycss) > 0)
			{
				$categorycss = str_replace("\r", " ", $data->categorycss);
				$categorycss = str_replace("\n", " ", $categorycss);
				$categorycss = str_replace('#wonderplugingridgallery-GRIDGALLERYID',  '#wonderplugingridgallery-' . $id, $categorycss);
				$ret .= '<style type="text/css">' . $categorycss . '</style>';
			}
			
			if (isset($data->lazyloadmode) && ($data->lazyloadmode == 'loadmore')  && isset($data->loadmorecss) && strlen($data->loadmorecss) > 0)
			{
				$loadmorecss = str_replace("\r", " ", $data->loadmorecss);
				$loadmorecss = str_replace("\n", " ", $loadmorecss);
				$loadmorecss = str_replace('#wonderplugingridgallery-GRIDGALLERYID',  '#wonderplugingridgallery-' . $id, $loadmorecss);
				$ret .= '<style type="text/css">' . $loadmorecss . '</style>';
			}
						
			if (isset($data->lazyloadmode) && ($data->lazyloadmode == 'pagination') && isset($data->paginationcss) && strlen($data->paginationcss) > 0)
			{
				$paginationcss = str_replace("\r", " ", $data->paginationcss);
				$paginationcss = str_replace("\n", " ", $paginationcss);
				$paginationcss = str_replace('#wonderplugingridgallery-GRIDGALLERYID',  '#wonderplugingridgallery-' . $id, $paginationcss);
				$ret .= '<style type="text/css">' . $paginationcss . '</style>';
			}
			
			if (isset($data->lightboxadvancedoptions) && strlen($data->lightboxadvancedoptions) > 0)
			{
				$ret .= '<div id="wpgridlightbox_advanced_options_' . $id . '" ' . stripslashes($data->lightboxadvancedoptions) . ' ></div>';
			}
			
			$has_woocommerce = false;
			if (class_exists('WooCommerce'))
			{
				$has_custom = false;
				if (isset($data->slides) && count($data->slides) > 0)
				{
					foreach ($data->slides as $index => $slide)
					{
						if ($slide->type == 7)
						{
							$has_custom = true;
							break;
						}
					}
				}
				if ($has_custom)
					$has_woocommerce = true;
			}
			
			// div data tag
			$ret .= '<div class="wonderplugingridgallery' . ($has_woocommerce ? ' woocommerce' : '') . '" id="wonderplugingridgallery-' . $id . '" data-gridgalleryid="' . $id . '" data-width="' . $data->width . '" data-height="' . $data->height . '" data-skin="' . $data->skin . '" data-style="' . $data->style . '"';
						
			if (!empty($data->categorylist))
				$ret .= ' data-categorylist="' . htmlspecialchars($data->categorylist, ENT_QUOTES, 'UTF-8') . '"';
			
			$boolOptions = array('fullwidth', 'supportshortcode', 'donotzoomin', 'enabletabindex', 'masonrymode', 'random', 'shownavigation', 'shownavcontrol', 'hidenavdefault', 'nohoverontouchscreen', 'hoverzoomin', 'hoverzoominimageonly', 'hoverzoominimagecenter', 'hoverfade', 'responsive', 'mediumscreen', 'smallscreen', 'showtitle', 'showtexttitle', 'showtextdescription', 'showtextbutton', 'overlaylink','donotaddtext', 'lightboxnogroup', 'lightboxcategorygroup', 
					'usetemplatefortextoverlay', 'usetemplateforgrid', 'deferloading',
					'lightboxshowallcategories', 'lightboxshowtitle', 'lightboxshowdescription', 'lightboxaddsocialmedia', 'lightboxresponsive', 'centerimage', 'circularimage', 'firstimage', 'textinsidespace', 'donotinit', 'addinitscript',
					'lightboxshowsocial', 'lightboxshowemail', 'lightboxshowfacebook', 'lightboxshowtwitter', 'lightboxshowpinterest', 'lightboxsocialrotateeffect', 'lightboxenablehtml5poster',
					'categoryshow', 'categoryhideall', 'categorymulticat', 'categoryatleastone', 'addvideoplaybutton', 'lightboxfullscreenmode', 'lightboxcloseonoverlay', 'lightboxvideohidecontrols',
					'lightboxfullscreenmodeonsmallscreen', 'lightboxfullscreentextinside', 'triggerresizeafterinit',
					'lightboxresponsivebarheight', 'lightboxnotkeepratioonsmallheight', 'lazyloadimages', 'triggerresize', 'loadallremaining',
					'lightboxautoslide', 'lightboxshowtimer', 'lightboxshowplaybutton', 'lightboxalwaysshownavarrows', 'lightboxshowtitleprefix');
			foreach ( $boolOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . ((strtolower($data->{$key}) === 'true') ? 'true': 'false') .'"';
			}
			
			$valOptions = array('thumbwidth', 'thumbheight', 'thumbtopmargin', 'thumbbottommargin', 'barheight', 'titlebottomcss', 'descriptionbottomcss', 'googleanalyticsaccount', 'gap', 'margin', 'borderradius', 
					'hoverzoominvalue', 'hoverzoominduration', 'hoverzoominimagescale', 'hoverzoominimageduration', 'hoverfadeopacity', 'hoverfadeduration',
					'videoplaybutton', 'column', 'mediumcolumn', 'mediumscreensize', 'smallcolumn', 'smallscreensize', 'titlemode', 'titleeffect', 'titleeffectduration', 'titleheight', 'scalemode',
					'lightboxbgcolor', 'lightboxoverlaybgcolor', 'lightboxoverlayopacity', 'navbgcolor', 'deferloadingdelay',
					'categoryposition', 'categorystyle', 'categorydefault', 'verticalcategorysmallscreenwidth', 'triggerresizedelay', 'triggerresizeafterinitdelay',
					'lightboxfullscreensmallscreenwidth', 'lightboxbordertopmargin', 'lightboxbordertopmarginsmall',
					'lightboxresizespeed', 'lightboxfadespeed', 'lightboxtransition', 'lightboxtransitionduration',
					'lightboxsocialposition', 'lightboxsocialpositionsmallscreen', 'lightboxsocialdirection', 'lightboxsocialbuttonsize', 'lightboxsocialbuttonfontsize',
					'lightboxtitlestyle', 'lightboximagepercentage', 'lightboxdefaultvideovolume', 'lightboxtitleprefix', 'lightboxtitleinsidecss', 'lightboxdescriptioninsidecss', 'lightboxfullscreentitlebottomcss', 'lightboxfullscreendescriptionbottomcss',
					'lightboxsmallscreenheight', 'lightboxbarheightonsmallheight', 'lazyloadmode', 'itemsperpage', 'loadmorecaption', 'loadmorecssstyle', 'paginationcssstyle', 'paginationpos',
					'lightboxslideinterval', 'lightboxtimerposition', 'lightboxtimerheight:', 'lightboxtimercolor', 'lightboxtimeropacity', 'lightboxbordersize', 'lightboxborderradius');
			foreach ( $valOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . $data->{$key} . '"';
			}
			
			if (isset($data->categorystyle))
			{
				$ret .= ' data-categoryregulardropdown="' . ((substr($data->categorystyle, -16) == 'regular-dropdown') ? 'true': 'false') . '"';
			}
			
			if (isset($data->dataoptions) && strlen($data->dataoptions) > 0)
			{
				$ret .= ' ' . stripslashes($data->dataoptions);
			}	
			
			$ret .= ' data-jsfolder="' . WONDERPLUGIN_GRIDGALLERY_URL . 'engine/"'; 
			$ret .= ' data-skinsfoldername="skins/default/"';
			
			$totalwidth = ( isset($data->firstimage) && strtolower($data->firstimage) === 'true' ) ? $data->width : $data->width * $data->column + $data->gap * ($data->column -1);
				
			$maxwidth = (isset($data->fullwidth) && strtolower($data->fullwidth) === 'true') ? '100%' : ($totalwidth . 'px');
			
			if (strtolower($data->responsive) === 'true')
				$ret .= ' style="display:none;position:relative;margin:0 auto;width:100%;max-width:' . $maxwidth . ';"';
			else 
				$ret .= ' style="display:none;position:relative;margin:0 auto;width:' . $totalwidth . 'px;"';
			
			$ret .= ' >';

			if (!empty($data->categorylist) && isset($data->categoryshow) && (strtolower($data->categoryshow) === 'true') && isset($data->categoryposition) && in_array($data->categoryposition, array('topleft', 'topcenter', 'topright', 'lefttop', 'righttop')))
				$ret .= $this->gen_categories($data->categorylist, $data->categoryposition, $data->categorystyle, $data->categorydefault, (isset($data->categoryhideall) && (strtolower($data->categoryhideall) === 'true')), $data->categorymenucaption);
				
			if (isset($data->slides) && count($data->slides) > 0)
			{
				// do shortcode
				if (isset($data->supportshortcode) && strtolower($data->supportshortcode) === 'true')
				{
					foreach ($data->slides as $slide)
					{
						if (isset($slide->title) && strlen($slide->title) > 0)
							$slide->title = do_shortcode($slide->title);
						
						if (isset($slide->description) && strlen($slide->description) > 0)
							$slide->description = do_shortcode($slide->description);
					}
				}
				
				// process posts
				$items = array();
				foreach ($data->slides as $slide)
				{
					if ($slide->type == 6)
					{
						$items = array_merge($items, $this->get_post_items($slide));
					}
					else if ($slide->type == 7)
					{
						$items = array_merge($items, $this->get_custom_post_items($slide));
					}
					else if ($slide->type == 11)
					{
						$items = array_merge($items, $this->get_items_from_folder($slide));
					}
					else if ($slide->type == 13)
					{
						$items = array_merge($items, $this->get_items_from_xml($slide));
					}
					else
					{
						$items[] = $slide;
					}
				}
				
				// random
				if (isset($data->random) && strtolower($data->random) === 'true')
				{
					shuffle($items);
				}
				
				$ret .= '<div class="wonderplugin-gridgallery-list" style="display:block;position:relative;max-width:100%;margin:0 auto;">';
				
				preg_match_all("/&lt;div\s.+&lt;\/div&gt;/", $data->gridtemplate, $templates);
				
				if ( !isset($templates) || count($templates) <= 0 || count($templates[0]) <= 0)
				{
					$templates = array(
							array('&lt;div data-row="1" data-col="1"&gt;&lt;/div&gt;')
					);
				}

				if (isset($templates) && count($templates) > 0 && count($templates[0]) > 0)
				{
					foreach ($templates[0] as &$template)
					{
						$template = str_replace('&lt;', '<', $template);
						$template = str_replace('&gt;', '>', $template);
					}
						
					$j = 0; $index = 0;
					foreach ($items as $slide)
					{		
						
						$hide_item = ((isset($data->lazyloadmode) && isset($data->itemsperpage) && ($data->lazyloadmode == 'loadmore' || $data->lazyloadmode == 'pagination') && ($index >= $data->itemsperpage)) 
								|| (isset($data->firstimage) && strtolower($data->firstimage) === 'true' && $index > 0));
						
						$socialmedia = empty($slide->socialmedia) ? '' : $this->generate_socialmedia_code($slide);
						
						if ($sanitizehtmlcontent == 1)
						{
							foreach($slide as &$value)
							{
								if ( is_string($value) )
									$value = wp_kses_post($value);
							}
						}
											
						$boolOptions = array('lightbox', 'displaythumbnail', 'lightboxsize', 'weblinklightbox', 'buttonlightbox', 'playvideoinline', 'loadvideoinline');
						foreach ( $boolOptions as $key )
						{
							$slide->{$key} = ( ( isset($slide->{$key})  && (strtolower($slide->{$key}) === 'true') ) ? true: false);
						}
						
						if ($slide->type == 12)
						{
							$content = do_shortcode($slide->htmlcode);
						}
						else
						{
							$content = $this->generate_image_code($slide, $data, $id, $hide_item, $socialmedia);
						}
						
						$code_template = '';
						
						if (isset($data->usetemplateforgrid) && strtolower($data->usetemplateforgrid) === 'true' && !empty($data->templateforgrid))
						{
							$gridtext = $data->templateforgrid;
							
							$gridtext = str_replace('__IMAGE__',  $content, $gridtext);
							$gridtext = str_replace('__SOCIALMEDIA__',  $socialmedia, $gridtext);
							$gridtext = str_replace('__TITLE__',  $slide->title, $gridtext);
							$gridtext = str_replace('__DESCRIPTION__',  $slide->description, $gridtext);
							$gridtext = str_replace('__BUTTON__',  $this->generate_button_code($id, $data, $slide, $socialmedia), $gridtext);
							
							$code_template .= $gridtext;							
						}
						else
						{
							$code_template .= '<div class="wonderplugin-gridgallery-item-container">' . $content . '</div>';
						}
						
						if (isset($data->usetemplatefortextoverlay) && strtolower($data->usetemplatefortextoverlay) === 'true' && !empty($data->templatefortextoverlay))
						{
							$itemtext = $data->templatefortextoverlay;
							
							$itemtext = str_replace('__SOCIALMEDIA__',  $socialmedia, $itemtext);
							$itemtext = str_replace('__TITLE__',  $slide->title, $itemtext);
							$itemtext = str_replace('__DESCRIPTION__',  $slide->description, $itemtext);
							$itemtext = str_replace('__BUTTON__',  $this->generate_button_code($id, $data, $slide, $socialmedia), $itemtext);
							
							$itemtext = '<div class="wonderplugin-gridgallery-item-text"><div class="wonderplugin-gridgallery-item-wrapper">' . $itemtext . '</div></div>';
							
							$code_template .= $itemtext;
						}
						
						$div_item = ($hide_item) ? '<div class="wonderplugin-gridgallery-item" style="display:none;"': '<div class="wonderplugin-gridgallery-item"';
						if ( !empty($slide->category) )
							$div_item .= ' data-category="' . $slide->category . '"';
						
						$div_template = str_replace('<div', $div_item, $templates[0][$j]);
						$div_template = str_replace('</div>', $code_template . "</div>", $div_template);	

						$ret .= $div_template;
						
						$j++;
						if ($j >= count($templates[0]))
							$j = 0;
						
						$index++;
					}
				}
				$ret .= '<div style="clear:both;"></div>';
				$ret .= '</div>';	
			}
			
			if (!empty($data->categorylist) && isset($data->categoryshow) && (strtolower($data->categoryshow) === 'true') && isset($data->categoryposition) && in_array($data->categoryposition, array('bottomleft', 'bottomcenter', 'bottomright')))
				$ret .= $this->gen_categories($data->categorylist, $data->categoryposition, $data->categorystyle, $data->categorydefault, (isset($data->categoryhideall) && (strtolower($data->categoryhideall) === 'true')), $data->categorymenucaption);
				
			$ret .= '<div style="clear:both;"></div>';
			
			if ('F' == 'F')
				$ret .= '<div class="wonderplugin-gridgallery-engine"><a href="http://www.wonderplugin.com/wordpress-gridgallery/" title="'. get_option('wonderplugin-gridgallery-engine')  .'">' . get_option('wonderplugin-gridgallery-engine') . '</a></div>';
			$ret .= '</div>';
			
			if (isset($data->addinitscript) && strtolower($data->addinitscript) === 'true')
			{
				$ret .= '<script>jQuery(document).ready(function(){jQuery(".wonderplugin-gridgallery-engine").css({display:"none"});jQuery(".wonderplugingridgallery").wonderplugingridgallery({forceinit:true});});</script>';
			}
			
			if (isset($data->triggerresize) && strtolower($data->triggerresize) === 'true')
			{
				$ret .= '<script>jQuery(document).ready(function(){';
				if ($data->triggerresizedelay > 0)
					$ret .= 'setTimeout(function(){jQuery(window).trigger("resize");},' . $data->triggerresizedelay . ');';
				else
					$ret .= 'jQuery(window).trigger("resize");';
				$ret .= '});</script>';
			}
			
			if (isset($data->customjs) && strlen($data->customjs) > 0)
			{
				$customjs = str_replace("\r", " ", $data->customjs);
				$customjs = str_replace("\n", " ", $customjs);
				$customjs = str_replace('&lt;',  '<', $customjs);
				$customjs = str_replace('&gt;',  '>', $customjs);
				$customjs = str_replace("GRIDGALLERYID", $id, $customjs);
				$ret .= '<script language="JavaScript">' . $customjs . '</script>';
			}
			
			if ($sanitizehtmlcontent == 1)
			{
				remove_filter('wp_kses_allowed_html', 'wonderplugin_gridgallery_tags_allow', 'post');
				remove_filter('safe_style_css', 'wonderplugin_gridgallery_css_allow');
			}
		}
		else
		{
			$ret = '<p>The specified grid gallery id does not exist.</p>';
		}
		return $ret;
	}
	
	function get_post_items($options) {
	
		$posts = array();
	
		$args = array(
				'numberposts' 	=> $options->postnumber,
				'post_status' 	=> 'publish'
		);
		
		if (isset($options->selectpostbytags) && !empty($options->posttags))
		{
			$args['tag'] = $options->posttags;
		}
		
		if (isset($options->postdaterange) && isset($options->postdaterangeafter) && (strtolower($options->postdaterange) === 'true'))
		{
			$args['date_query'] = array(
					'after' => date('Y-m-d', strtotime('-' . $options->postdaterangeafter . ' days'))
			);
		}
		
		if ($options->postcategory == -1)
		{
			$posts = wp_get_recent_posts($args);
		}
		else
		{
			if ($options->postcategory != -2)
			{
				$args['category'] = $options->postcategory;
			}
				
			if (!empty($options->postorderby))
			{
				$args['orderby'] = $options->postorderby;
			}
				
			$posts = get_posts($args);
		}
	
		$items = array();
	
		foreach($posts as $post)
		{
			if (is_object($post))
				$post = get_object_vars($post);
	
			$thumbnail = '';
			$image = '';
			if ( has_post_thumbnail($post['ID']) )
			{
				$featured_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post['ID']), $options->featuredimagesize);
				$thumbnail = $featured_thumb[0];
	
				$featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($post['ID']), 'full');
				$image = $featured_image[0];
			}
	
			$excerpt = $post['post_excerpt'];
			if (empty($excerpt))
			{
				$excerpts = explode( '<!--more-->', $post['post_content'] );
				$excerpt = $excerpts[0];
				$excerpt = strip_tags( str_replace(']]>', ']]&gt;', strip_shortcodes($excerpt)) );
			}
			$excerpt = wonderplugin_gridgallery_wp_trim_words($excerpt, $options->excerptlength);
	
			$post_item = array(
					'type'			=> 0,
					'image'			=> $image,
					'thumbnail'		=> $thumbnail,
					'displaythumbnail'	=> true,
					'title'			=> $post['post_title'],
					'description'	=> $excerpt,
					'weblink'		=> get_permalink($post['ID']),
					'linktarget'	=> $options->postlinktarget,
					'button'		=> $options->button,
					'buttoncss'		=> $options->buttoncss,
					'buttonlightbox'	=> false,
					'buttonlink'	=> get_permalink($post['ID']),
					'buttonlinktarget'	=> $options->postlinktarget,
					'category'		=> $options->category
			);
			
			if (isset($options->postlightbox))
			{
				$post_item['lightbox'] = $options->postlightbox;
				$post_item['lightboxsize'] = $options->postlightboxsize;
				$post_item['lightboxwidth'] = $options->postlightboxwidth;
				$post_item['lightboxheight'] = $options->postlightboxheight;
			
				if (isset($options->posttitlelink) && strtolower($options->posttitlelink) === 'true')
				{
					$post_item['title'] = '<a class="wonderplugin-gridgallery-posttitle-link" href="' . $post_item['weblink'] . '"';
					if (isset($post_item['linktarget']) && strlen($post_item['linktarget']) > 0)
						$post_item['title'] .= ' target="' . $post_item['linktarget'] . '"';
					$post_item['title'] .= '>' . $post['post_title'] . '</a>';
				}
			}
				
			$items[] = (object) $post_item;
		}
	
		if (isset($options->postorder) && ($options->postorder == 'ASC'))
			$items = array_reverse($items);
		
		$items = apply_filters( 'wonderplugin_gridgallery_modify_post_items', $items );
		
		return $items;
	}
	
	
	function get_custom_post_items($options) {

		global $post;

		$items = array();

		$args = array(
				'post_type' 		=> $options->customposttype,
				'posts_per_page'	=> $options->postnumber,
				'post_status' 	=> 'publish'
		);
		
		if (isset($options->postdaterange) && (strtolower($options->postdaterange) === 'true') && isset($options->postdaterangeafter) )
		{
			$args['date_query'] = array(
					'after' => date('Y-m-d', strtotime('-' . $options->postdaterangeafter . ' days'))
			);
		}

		$taxonomytotal = 0;

		$tax_query = array();

		for ($i = 0; ; $i++)
		{
			if (isset($options->{'taxonomy' . $i}) && isset($options->{'term' . $i}) && ($options->{'taxonomy' . $i} != '-1') && ($options->{'term' . $i} != '-1') )
			{
				$taxonomytotal++;
				$tax_query[] = array(
						'taxonomy' => $options->{'taxonomy' . $i},
						'field'    => 'slug',
						'terms'    => $options->{'term' . $i}
				);
			}
			else
			{
				break;
			}
		}

		if ($taxonomytotal > 1)
		{
			$tax_query['relation'] = $options->taxonomyrelation;
		}

		if ($taxonomytotal > 0)
		{
			$args['tax_query'] = $tax_query;
		}

		// woocommerce meta query
		if ( class_exists('WooCommerce') && ((isset($options->metatotalsales) && (strtolower($options->metatotalsales) === 'true')) || (isset($options->metafeatured) && (strtolower($options->metafeatured) === 'true'))) )
		{
			$meta_query = array();

			if (isset($options->metatotalsales) && (strtolower($options->metatotalsales) === 'true'))
			{
				$meta_query[] = array(
						'key'       => 'total_sales',
						'value'     => '0',
						'compare'   => '>'
				);

				$args['orderby'] = 'total_sales';
			}

			if (isset($options->metafeatured) && (strtolower($options->metafeatured) === 'true'))
			{
				$meta_query[] = array(
						'key'       => '_featured',
						'value'     => 'yes',
						'compare'   => '='
				);
			}

			if ( (isset($options->metatotalsales) && (strtolower($options->metatotalsales) === 'true')) && (isset($options->metafeatured) && (strtolower($options->metafeatured) === 'true')) )
			{
				$meta_query['relation'] = $options->metarelation;
			}

			$args['meta_query'] = $meta_query;
		}
		
		$query = new WP_Query($args);
		if ($query->have_posts())
		{
			while ( $query->have_posts() )
			{
				$query->the_post();

				if ($post)
				{					
					$postvars = get_object_vars($post);
					
					$postdata = array();
					
					foreach ($postvars as $key => $value) {
						$postdata[$key] = $value;
					}
					
					$featured_image = '';
					if (has_post_thumbnail($postdata['ID']))
					{
						$featured_image_size = (!empty($options->customfeaturedimagesize)) ? $options->customfeaturedimagesize : 'full';
						$attachment_image = wp_get_attachment_image_src(get_post_thumbnail_id($postdata['ID']), $featured_image_size);
						$featured_image = $attachment_image[0];
					}
					$postdata['featured_image'] = $featured_image;

					$postmeta = get_post_meta($postdata['ID']);

					if (class_exists('WooCommerce') && isset($postdata['ID']))
					{
						global $woocommerce;

						$is_woocommerce3 = version_compare( $woocommerce->version, '3.0', ">=");

						$product = wc_get_product($postdata['ID']);
						if ($product)
						{
							$postmeta['wc_price_html'] = $product->get_price_html();
							$postmeta['wc_price'] = wc_price( $product->get_price() );
							$postmeta['wc_regular_price'] = wc_price( $product->get_regular_price() );
							$postmeta['wc_sale_price'] = wc_price( $product->get_sale_price() );
							$postmeta['wc_rating_html'] = $is_woocommerce3 ? wc_get_rating_html( $product->get_average_rating() ) : $product->get_rating_html();
							$postmeta['wc_review_count'] = $product->get_review_count();
							$postmeta['wc_rating_count'] = $product->get_rating_count();
							$postmeta['wc_average_rating'] = $product->get_average_rating();
							$postmeta['wc_total_sales'] = (int) get_post_meta( $postdata['ID'], 'total_sales', true );

							if (method_exists($product,'get_category_ids'))
							{
								$cat_ids = $product->get_category_ids();
								$cat_index = 0;
	
								foreach($cat_ids as $cat_id)
								{
									if( $term = get_term_by( 'id', $cat_id, 'product_cat' ) ){
	
										if ($cat_index == 0)
										{
											$postmeta['wc_product_cat_id'] = $cat_id;
											$postmeta['wc_product_cat_name'] = $term->name;
											$postmeta['wc_product_cat_slug'] = $term->slug;
											$postmeta['wc_product_cat_link'] = get_term_link( $term );
										}
	
										$postmeta['wc_product_cat_id' . $cat_index] = $cat_id;
										$postmeta['wc_product_cat_name' . $cat_index] = $term->name;
										$postmeta['wc_product_cat_slug' . $cat_index] = $term->slug;
										$postmeta['wc_product_cat_link' . $cat_index] = get_term_link( $term );
	
										$cat_index++;
									}
								}
							}
						}
					}

					$title = $this->replace_custom_field($postdata, $postmeta, $options->titlefield, $options->textlength);
					$description = $this->replace_custom_field($postdata, $postmeta, $options->descriptionfield, $options->textlength);
					$image = $this->replace_custom_field($postdata, $postmeta, $options->imagefield, $options->textlength);
					
					$display_title = $title;
					if (strtolower($options->titlelink) === 'true')
					{
						$display_title = '<a class="wonderplugin-gridgallery-posttitle-link" href="' . get_permalink($postdata['ID']) . '"';
						if (isset($options->postlinktarget) && strlen($options->postlinktarget) > 0)
							$display_title .= ' target="' . $options->postlinktarget . '"';
						$display_title .= '>' . $title . '</a>';
					}
					
					
					$postlink = '';
					$lightbox = false;
					if (strtolower($options->imageaction) === 'true')
					{
						if (strtolower($options->imageactionlightbox) === 'true')
						{
							$postlink = $image;
							$lightbox = true;
						}
						else
						{
							$postlink = get_permalink($postdata['ID']);
							$lightbox = (strtolower($options->openpostinlightbox) === 'true');
						}	
					}
					
					$post_item = array(
							'type'					=> 7,
							'image'					=> $image,
							'thumbnail'				=> $image,
							'displaythumbnail'		=> true,
							'title'					=> $display_title,
							'description'			=> $description,
							'weblink'				=> $postlink,
							'linktarget'			=> $options->postlinktarget,
							'button'				=> '',
							'buttoncss'				=> '',
							'buttonlightbox'		=> false,
							'buttonlink'			=> '',
							'buttonlinktarget'		=> '',
							'lightbox'				=> $lightbox,
							'lightboxsize'			=> $options->postlightboxsize,
							'lightboxwidth'			=> $options->postlightboxwidth,
							'lightboxheight'		=> $options->postlightboxheight,							
							'category'				=> $options->category
					);
					
					// match regular items
					foreach($post_item as $key => &$value)
					{
						if (is_bool($value))
							$value = $value ? 'true' : 'false';
					}
					
					$items[] = (object) $post_item;
				}

			}
			wp_reset_postdata();
		}

		if (isset($options->postorder) && ($options->postorder == 'ASC'))
			$items = array_reverse($items);
	
		$items = apply_filters( 'wonderplugin_gridgallery_modify_custom_post_items', $items );
		
		return $items;
	}

	function replace_custom_field($postdata, $postmeta, $field, $textlength) {
	
		$postdata = array_merge($postdata, $postmeta);
	
		$postdata = apply_filters( 'wonderplugin_gridgallery_custom_post_field_content', $postdata );
	
		$result = $field;
	
		preg_match_all('/\\%(.*?)\\%/s', $field, $matches);
	
		if (!empty($matches) && count($matches) > 1)
		{
			foreach($matches[1] as $match)
			{
				$replace = '';
				if (array_key_exists($match, $postdata))
				{
					if (is_array($postdata[$match]))
					{
						$replace = implode(' ', $postdata[$match]);
					}
					else
					{
						$replace = $postdata[$match];
					}
						
					if ($match == 'post_content' || $match == 'post_excerpt')
						$replace = wonderplugin_gridgallery_wp_trim_words($replace, $textlength);
				}
				$result = str_replace('%' . $match . '%', $replace, $result);
			}
		}
	
		return $result;
	}

	function get_items_from_xml($slide) {			
		
		$default = array(
					'type'				=> 0,
					'image'				=> '',
					'thumbnail'			=> '',
					'displaythumbnail'	=> true,
					'title'				=> 'The specified XML file does not exist',
					'description'		=> '',
					'weblink'			=> '',
					'linktarget'		=> '',
					'button'			=> '',
					'buttoncss'			=> '',
					'buttonlightbox'	=> false,
					'buttonlink'		=> '',
					'buttonlinktarget'	=> '',
					'category'			=> '',
					'lightbox' 			=> false,
					'lightboxsize' 		=> false,
					'lightboxwidth' 	=> 640,
					'lightboxheight' 	=> 480
				);
		
		$items = array();
		
		if (!empty($slide->xmlurl) && function_exists("simplexml_load_string"))
		{
			$content = @file_get_contents($slide->xmlurl);
			if ($content === FALSE)
			{
				$items[] = (object) $default;
			}
			else
			{
				$xml = simplexml_load_string($content);
				
				if ($xml && isset($xml->item))
				{
					foreach($xml->item as $xmlitem)
					{
						$new = $default;
						
						foreach ($xmlitem as $key => $value)
						{
							$new[$key] = (string) $value;
						}
						
						if (empty($new['thumbnail']))
							$new['thumbnail'] = $new['image'];
						
						$items[] = (object) $new;
					}
				}
			}
		}
		
		return $items;
	}
	
	function get_items_from_folder($slide) {
			
		$dir = ABSPATH . $slide->folder;
	
		$items = array();
	
		if (!is_readable($dir) || !file_exists($dir))
		{
			$item = array(
					'type'			=> 0,
					'image'			=> '',
					'thumbnail'		=> '',
					'displaythumbnail'	=> true,
					'title'			=> 'No permissions to browse the folder or the folder does not exist',
					'description'	=> '',
					'weblink'		=> '',
					'linktarget'	=> '',
					'button'			=> '',
					'buttoncss'			=> '',
					'buttonlightbox'	=> false,
					'buttonlink'		=> '',
					'buttonlinktarget'	=> '',
					'category'			=> $slide->category,
					'lightbox' 			=> $slide->lightbox,
					'lightboxsize' 		=> $slide->lightboxsize,
					'lightboxwidth' 	=> $slide->lightboxwidth,
					'lightboxheight' 	=> $slide->lightboxheight
			);
				
			$items[] = (object) $item;
				
			return $items;
		}
	
		$dirurl = get_home_url(). '/' . str_replace(DIRECTORY_SEPARATOR, '/', $slide->folder) . '/';
		
		if (isset($slide->onlyusexml) && strtolower($slide->onlyusexml) === 'true')
		{
				
			$xmlfile = $dir . DIRECTORY_SEPARATOR . 'list.xml';
			if (file_exists($xmlfile) && function_exists("simplexml_load_string"))
			{
				$content = file_get_contents($xmlfile);
					
				$xml = simplexml_load_string($content);
					
				if ($xml && isset($xml->item))
				{
					foreach($xml->item as $xmlitem)
					{
						$new = array(
									'type'				=> 0,
									'image'				=> '',
									'thumbnail'			=> '',
									'displaythumbnail'	=> true,
									'title'				=> '',
									'description'		=> '',
									'weblink'			=> '',
									'linktarget'		=> '',
									'button'			=> '',
									'buttoncss'			=> '',
									'buttonlightbox'	=> false,
									'buttonlink'		=> '',
									'buttonlinktarget'	=> '',
									'category'			=> $slide->category,
									'lightbox' 			=> $slide->lightbox,
									'lightboxsize' 		=> $slide->lightboxsize,
									'lightboxwidth' 	=> $slide->lightboxwidth,
									'lightboxheight' 	=> $slide->lightboxheight
							);
		
						foreach ($xmlitem as $key => $value)
						{
							$new[$key] = $value;
						}
		
						$props = array('image', 'thumbnail', 'mp4', 'webm');
						foreach($props as $prop)
						{
							if (!empty($new[$prop]) && (strpos(strtolower($new[$prop]), 'http://') !== 0) && (strpos(strtolower($new[$prop]), 'https://') !== 0) && (strpos(strtolower($new[$prop]), '/') !== 0))
							{
								$new[$prop] = $dirurl . $new[$prop];
							}
						}
		
						if (empty($new['thumbnail']))
							$new['thumbnail'] = $new['image'];
		
						$items[] = (object) $new;
					}
				}
			}
				
			return $items;
		}
		
		$exts = explode('|', $slide->imageext);
	
		if ($slide->sortorder == 'ASC')
			$cdir = scandir($dir);
		else
			$cdir = scandir($dir, 1);
	
		$usefilenameastitle = isset($slide->usefilenameastitle) && strtolower($slide->usefilenameastitle) === 'true';
	
		foreach ($cdir as $key => $value)
		{
			if (!is_dir($dir . DIRECTORY_SEPARATOR . $value))
			{
				if (preg_match('/(?<!' . $slide->thumbname . '|' . $slide->postername . ')\.(' . $slide->imageext . ')$/i', $value))
				{
					$info = pathinfo($value);
					$thumb = $info['filename'] . $slide->thumbname . '.' . $info['extension'];
						
					$imageurl = $dirurl . $value;
					$thumburl = (in_array($thumb, $cdir)) ? $dirurl . $thumb : $imageurl;
						
					$item = array(
							'type'				=> 0,
							'image'				=> $imageurl,
							'thumbnail'			=> $thumburl,
							'displaythumbnail'	=> true,
							'title'				=> $usefilenameastitle ? $info['filename'] : '',
							'description'		=> '',
							'weblink'			=> '',
							'linktarget'		=> '',
							'button'			=> '',
							'buttoncss'			=> '',
							'buttonlightbox'	=> false,
							'buttonlink'		=> '',
							'buttonlinktarget'	=> '',
							'category'			=> $slide->category,
							'lightbox' 			=> $slide->lightbox,
							'lightboxsize' 		=> $slide->lightboxsize,
							'lightboxwidth' 	=> $slide->lightboxwidth,
							'lightboxheight' 	=> $slide->lightboxheight
					);
	
					$items[] = (object) $item;
				}
				else if (preg_match('/\.(' . $slide->videoext . ')$/i', $value))
				{
					$info = pathinfo($value);
						
					$videourl = $dirurl . $value;
						
					$thumburl = '';
					foreach($exts as $ext)
					{
						$thumb = $info['filename'] . $slide->thumbname . '.' . $ext;
	
						if (in_array($thumb, $cdir))
						{
							$thumburl = $dirurl . $thumb;
							break;
						}
					}
						
					$posterurl = '';
					foreach($exts as $ext)
					{
						$poster = $info['filename'] . $slide->postername . '.' . $ext;
						if (in_array($poster, $cdir))
						{
							$posterurl = $dirurl . $poster;
							break;
						}
					}
	
					if ( empty($thumburl) && !empty($posterurl) )
					{	
						$thumburl = $posterurl;
					}
					else if ( empty($posterurl) && !empty($thumburl) )
					{	
						$posterurl = $thumburl;
					}
					
					$item = array(
							'type'				=> 1,
							'mp4'				=> $videourl,
							'webm'				=> '',
							'image'				=> $posterurl,
							'thumbnail'			=> $thumburl,
							'displaythumbnail'	=> true,
							'title'				=> $usefilenameastitle ? $info['filename'] : '',
							'description'		=> '',
							'weblink'			=> '',
							'linktarget'		=> '',
							'button'			=> '',
							'buttoncss'			=> '',
							'buttonlightbox'	=> false,
							'buttonlink'		=> '',
							'buttonlinktarget'	=> '',
							'category'			=> $slide->category,
							'lightbox' 			=> $slide->lightbox,
							'lightboxsize' 		=> $slide->lightboxsize,
							'lightboxwidth' 	=> $slide->lightboxwidth,
							'lightboxheight' 	=> $slide->lightboxheight
					);
	
					$items[] = (object) $item;
				}
			}
		}
				
		// read config.xml file
		$xmlfile = $dir . DIRECTORY_SEPARATOR . 'list.xml';
		if (file_exists($xmlfile) && function_exists("simplexml_load_string"))
		{
			$content = file_get_contents($xmlfile);
		
			$xml = simplexml_load_string($content);
		
			if ($xml && isset($xml->item))
			{
				foreach($xml->item as $xmlitem)
				{
					if (isset($xmlitem->image) && (strpos(strtolower($xmlitem->image), 'http://') !== 0) && (strpos(strtolower($xmlitem->image), 'https://') !== 0) && (strpos(strtolower($xmlitem->image), '/') !== 0))
					{
						$xmlitem->image = $dirurl . $xmlitem->image;
							
						foreach($items as &$item)
						{
							if (isset($item->image) && (strtolower($item->image) == strtolower($xmlitem->image)))
							{
								unset($xmlitem->image);
		
								foreach ($xmlitem as $key => $value)
								{
									if (($key == 'thumbnail' || $key == 'mp4' || $key == 'webm') && !empty($value) && (strpos(strtolower($value), 'http://') !== 0) && (strpos(strtolower($value), 'https://') !== 0) && (strpos(strtolower($value), '/') !== 0))
									{
										$value = $dirurl . $value;
									}
									$item->{$key} = $value;
								}
		
								break;
							}
						}
					}
					else
					{
		
						$new = array(
									'type'				=> 0,
									'image'				=> '',
									'thumbnail'			=> '',
									'displaythumbnail'	=> true,
									'title'				=> '',
									'description'		=> '',
									'weblink'			=> '',
									'linktarget'		=> '',
									'button'			=> '',
									'buttoncss'			=> '',
									'buttonlightbox'	=> false,
									'buttonlink'		=> '',
									'buttonlinktarget'	=> '',
									'category'			=> $slide->category,
									'lightbox' 			=> $slide->lightbox,
									'lightboxsize' 		=> $slide->lightboxsize,
									'lightboxwidth' 	=> $slide->lightboxwidth,
									'lightboxheight' 	=> $slide->lightboxheight
							);
		
						foreach ($xmlitem as $key => $value)
						{
							$new[$key] = $value;
						}
		
						if (empty($new['thumbnail']))
							$new['thumbnail'] = $new['image'];
		
						$items[] = (object) $new;
					}
				}
			}
		}
				
		return $items;
	}
	
	function delete_item($id) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";
		
		$ret = $wpdb->query( $wpdb->prepare(
				"
				DELETE FROM $table_name WHERE id=%s
				",
				$id
		) );
		
		return $ret;
	}
	
	function trash_item($id) {
	
		return $this->set_item_status($id, 0);
	}
	
	function restore_item($id) {
	
		return $this->set_item_status($id, 1);
	}
	
	function set_item_status($id, $status) {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";
	
		$ret = false;
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$data = json_decode($item_row->data, true);
			$data['publish_status'] = $status;
			$data = json_encode($data);
	
			$update_ret = $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET data=%s WHERE id=%d", $data, $id ) );
			if ( $update_ret )
				$ret = true;
		}
	
		return $ret;
	}
	
	function clone_item($id) {
	
		global $wpdb, $user_ID;
		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";
		
		$cloned_id = -1;
		
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$time = current_time('mysql');
			$authorid = $user_ID;
			
			$ret = $wpdb->query( $wpdb->prepare(
					"
					INSERT INTO $table_name (name, data, time, authorid)
					VALUES (%s, %s, %s, %s)
					",
					$item_row->name . " Copy",
					$item_row->data,
					$time,
					$authorid
			) );
				
			if ($ret)
				$cloned_id = $wpdb->insert_id;
		}
	
		return $cloned_id;
	}
	
	function is_db_table_exists() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";
	
		return ( strtolower($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) == strtolower($table_name) );
	}
	
	function is_id_exist($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";
	
		$gridgallery_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		return ($gridgallery_row != null);
	}
	
	function create_db_table() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";
		
		$charset = '';
		if ( !empty($wpdb -> charset) )
			$charset = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( !empty($wpdb -> collate) )
			$charset .= " COLLATE $wpdb->collate";
	
		$sql = "CREATE TABLE $table_name (
		id INT(11) NOT NULL AUTO_INCREMENT,
		name tinytext DEFAULT '' NOT NULL,
		data MEDIUMTEXT DEFAULT '' NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		authorid tinytext NOT NULL,
		PRIMARY KEY  (id)
		) $charset;";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	function save_item($item) {
		
		global $wpdb, $user_ID;
		
		if ( !$this->is_db_table_exists() )
		{
			$this->create_db_table();
		
			$create_error = "CREATE DB TABLE - ". $wpdb->last_error;
			if ( !$this->is_db_table_exists() )
			{
				return array(
						"success" => false,
						"id" => -1,
						"message" => $create_error
				);
			}
		}
		
		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";
		
		$id = $item["id"];
		$name = $item["name"];
		
		unset($item["id"]);
		$data = json_encode($item);
		
		if ( empty($data) )
		{
			$json_error = "json_encode error";
			if ( function_exists('json_last_error_msg') )
				$json_error .= ' - ' . json_last_error_msg();
			else if ( function_exists('json_last_error') )
				$json_error .= 'code - ' . json_last_error();
		
			return array(
					"success" => false,
					"id" => -1,
					"message" => $json_error
			);
		}
		
		$time = current_time('mysql');
		$authorid = $user_ID;
		
		if ( ($id > 0) && $this->is_id_exist($id) )
		{
			$ret = $wpdb->query( $wpdb->prepare(
					"
					UPDATE $table_name
					SET name=%s, data=%s, time=%s, authorid=%s
					WHERE id=%d
					",
					$name,
					$data,
					$time,
					$authorid,
					$id
			) );
			
			if (!$ret)
			{
				return array(
						"success" => false,
						"id" => $id, 
						"message" => "UPDATE - ". $wpdb->last_error
					);
			}
		}
		else
		{
			$ret = $wpdb->query( $wpdb->prepare(
					"
					INSERT INTO $table_name (name, data, time, authorid)
					VALUES (%s, %s, %s, %s)
					",
					$name,
					$data,
					$time,
					$authorid
			) );
			
			if (!$ret)
			{
				return array(
						"success" => false,
						"id" => -1,
						"message" => "INSERT - " . $wpdb->last_error
				);
			}
			
			$id = $wpdb->insert_id;
		}
		
		return array(
				"success" => true,
				"id" => intval($id),
				"message" => "Gallery published!"
		);
	}
	
	function get_list_data() {
		
		if ( !$this->is_db_table_exists() )
			$this->create_db_table();
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";
		
		$rows = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A);
		
		$ret = array();
		
		if ( $rows )
		{
			foreach ( $rows as $row )
			{
				$ret[] = array(
							"id" => $row['id'],
							'name' => $row['name'],
							'data' => $row['data'],
							'time' => $row['time'],
							'authorid' => $row['authorid']
						);
			}
		}
	
		return $ret;
	}
	
	function get_item_data($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_gridgallery";
	
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$ret = $item_row->data;
		}

		return $ret;
	}
	
	function get_settings() {
	
		$userrole = get_option( 'wonderplugin_gridgallery_userrole' );
		if ( $userrole == false )
		{
			update_option( 'wonderplugin_gridgallery_userrole', 'manage_options' );
			$userrole = 'manage_options';
		}
		
		$thumbnailsize = get_option( 'wonderplugin_gridgallery_thumbnailsize' );
		if ( $thumbnailsize == false )
		{
			update_option( 'wonderplugin_gridgallery_thumbnailsize', 'medium' );
			$thumbnailsize = 'medium';
		}
		
		$keepdata = get_option( 'wonderplugin_gridgallery_keepdata', 1 );
		
		$disableupdate = get_option( 'wonderplugin_gridgallery_disableupdate', 0 );
		
		$supportwidget = get_option( 'wonderplugin_gridgallery_supportwidget', 1 );
		
		$addjstofooter = get_option( 'wonderplugin_gridgallery_addjstofooter', 0 );
		
		$jsonstripcslash = get_option( 'wonderplugin_gridgallery_jsonstripcslash', 1 );
		
		$displaytitleineditor = get_option( 'wonderplugin_gridgallery_displaytitleineditor', 1 );
		
		$usepostsave = get_option( 'wonderplugin_gridgallery_usepostsave', 0 );
		
		$sanitizehtmlcontent = get_option( 'wonderplugin_gridgallery_sanitizehtmlcontent', 1 );
		
		$settings = array(
				"userrole" => $userrole,
				"thumbnailsize" => $thumbnailsize,
				"keepdata" => $keepdata,
				"disableupdate" => $disableupdate,
				"supportwidget" => $supportwidget,
				"addjstofooter" => $addjstofooter,
				"jsonstripcslash" => $jsonstripcslash,
				"displaytitleineditor" => $displaytitleineditor,
				"usepostsave" => $usepostsave,
				"sanitizehtmlcontent" => $sanitizehtmlcontent
		);
		
		return $settings;

	}
	
	function save_settings($options) {
	
		if (!isset($options) || !isset($options['userrole']))
			$userrole = 'manage_options';
		else if ( $options['userrole'] == "Editor")
			$userrole = 'moderate_comments';
		else if ( $options['userrole'] == "Author")
			$userrole = 'upload_files';
		else
			$userrole = 'manage_options';
		update_option( 'wonderplugin_gridgallery_userrole', $userrole );
	
		if (isset($options) && isset($options['thumbnailsize']))
			$thumbnailsize = $options['thumbnailsize'];
		else
			$thumbnailsize = 'medium';
		update_option( 'wonderplugin_gridgallery_thumbnailsize', $thumbnailsize );
		
		if (!isset($options) || !isset($options['keepdata']))
			$keepdata = 0;
		else
			$keepdata = 1;
		update_option( 'wonderplugin_gridgallery_keepdata', $keepdata );
		
		if (!isset($options) || !isset($options['disableupdate']))
			$disableupdate = 0;
		else
			$disableupdate = 1;
		update_option( 'wonderplugin_gridgallery_disableupdate', $disableupdate );
		
		if (!isset($options) || !isset($options['supportwidget']))
			$supportwidget = 0;
		else
			$supportwidget = 1;
		update_option( 'wonderplugin_gridgallery_supportwidget', $supportwidget );
		
		if (!isset($options) || !isset($options['addjstofooter']))
			$addjstofooter = 0;
		else
			$addjstofooter = 1;
		update_option( 'wonderplugin_gridgallery_addjstofooter', $addjstofooter );
		
		if (!isset($options) || !isset($options['jsonstripcslash']))
			$jsonstripcslash = 0;
		else
			$jsonstripcslash = 1;
		update_option( 'wonderplugin_gridgallery_jsonstripcslash', $jsonstripcslash );
		
		if (!isset($options) || !isset($options['displaytitleineditor']))
			$displaytitleineditor = 0;
		else
			$displaytitleineditor = 1;
		update_option( 'wonderplugin_gridgallery_displaytitleineditor', $displaytitleineditor );
		
		if (!isset($options) || !isset($options['usepostsave']))
			$usepostsave = 0;
		else
			$usepostsave = 1;
		update_option( 'wonderplugin_gridgallery_usepostsave', $usepostsave );
		
		if (!isset($options) || !isset($options['sanitizehtmlcontent']))
			$sanitizehtmlcontent = 0;
		else
			$sanitizehtmlcontent = 1;
		update_option( 'wonderplugin_gridgallery_sanitizehtmlcontent', $sanitizehtmlcontent );
	}
	
	function get_plugin_info() {
	
		$info = get_option('wonderplugin_gridgallery_information');
		if ($info === false)
			return false;
	
		return unserialize($info);
	}
	
	function save_plugin_info($info) {
	
		update_option( 'wonderplugin_gridgallery_information', serialize($info) );
	}
	
	function check_license($options) {
	
		$ret = array(
				"status" => "empty"
		);
	
		if ( !isset($options) || empty($options['wonderplugin-gridgallery-key']) )
		{
			return $ret;
		}
	
		$key = sanitize_text_field( $options['wonderplugin-gridgallery-key'] );
		if ( empty($key) )
			return $ret;
	
		$update_data = $this->controller->get_update_data('register', $key);
		if( $update_data === false )
		{
			$ret['status'] = 'timeout';
			return $ret;
		}
	
		if ( isset($update_data->key_status) )
			$ret['status'] = $update_data->key_status;
	
		return $ret;
	}
	
	function deregister_license($options) {
	
		$ret = array(
				"status" => "empty"
		);
	
		if ( !isset($options) || empty($options['wonderplugin-gridgallery-key']) )
			return $ret;
	
		$key = sanitize_text_field( $options['wonderplugin-gridgallery-key'] );
		if ( empty($key) )
			return $ret;
	
		$info = $this->get_plugin_info();
		$info->key = '';
		$info->key_status = 'empty';
		$info->key_expire = 0;
		$this->save_plugin_info($info);
	
		$update_data = $this->controller->get_update_data('deregister', $key);
		if ($update_data === false)
		{
			$ret['status'] = 'timeout';
			return $ret;
		}
	
		$ret['status'] = 'success';
	
		return $ret;
	}
}
