<?php 

require_once 'wonderplugin-tabs-functions.php';

class WonderPlugin_Tabs_Model {

	private $controller;
	
	function __construct($controller) {
		
		$this->controller = $controller;
	}
	
	function get_upload_path() {
		
		$uploads = wp_upload_dir();
		return $uploads['basedir'] . '/wonderplugin-tabs/';
	}
	
	function get_upload_url() {
	
		$uploads = wp_upload_dir();
		return $uploads['baseurl'] . '/wonderplugin-tabs/';
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
					'search' => str_replace('/', '\\/', sanitize_text_field($post['standalonesearch' . $i])),
					'replace' => str_replace('/', '\\/', sanitize_text_field($post['standalonereplace' . $i]))
			);
		}

		global $wpdb;

		if (!$this->is_db_table_exists())
			$this->create_db_table();

		$table_name = $wpdb->prefix . "wonderplugin_tabs";

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
					'message' => 'No tab group modified' .  (isset($wpdb->lasterror) ? $wpdb->lasterror : '')
			);
		}

		return array(
				'success' => true,
				'message' => sprintf( _n( '%s tab group', '%s tab groups', $total), $total) . ' modified'
		);
	}

	function import_tabs($post, $files)
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

		add_filter( 'wp_check_filetype_and_ext', 'wonderplugin_tabs_wp_check_filetype_and_ext', 10, 4);

		$xmlfile = wp_handle_upload($files['importxml'], array(
				'test_form' => false,
				'mimes' => array('xml' => 'text/xml')
		));

		remove_filter( 'wp_check_filetype_and_ext', 'wonderplugin_tabs_wp_check_filetype_and_ext');

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

		if (empty($index) || empty($index['WONDERPLUGINTABS']) || empty($index['ID']))
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
					'message' => 'No tabs found'
			);
		}

		global $wpdb;

		if (!$this->is_db_table_exists())
			$this->create_db_table();

		$table_name = $wpdb->prefix . "wonderplugin_tabs";

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
					'message' => 'No tabs imported' .  (isset($wpdb->lasterror) ? $wpdb->lasterror : '')
			);
		}

		return array(
				'success' => true,
				'message' => sprintf( _n( '%s tab group', '%s tab groups', $total), $total) . ' imported'
		);

	}

	function export_tabs()
	{
		if ( !check_admin_referer('wonderplugin-tabs', 'wonderplugin-tabs-export') || !isset($_POST['alltabs']) || !isset($_POST['tabsid']) || !is_numeric($_POST['tabsid']) )
			exit;

		$alltabs = sanitize_text_field($_POST['alltabs']);
		$tabsid = sanitize_text_field($_POST['tabsid']);
		$exportplaylist = (isset($_POST['exportplaylist']) && ($_POST['exportplaylist'] == '1'));
		
		if ($alltabs)
		{
			$data = $this->get_list_data(true);
			$filename = 'wonderplugin_tabs_export_all.xml';
		}
		else
		{
			$data = array($this->get_list_item_data($tabsid));
			
			if ($exportplaylist)
				$filename = 'wonderplugin_tabs_filelist_' . $tabsid . '.xml';
			else
				$filename = 'wonderplugin_tabs_export_' . $tabsid . '.xml';
		}

		header('Content-Description: File Transfer');
		header("Content-Disposition: attachment; filename=" . $filename);
		header('Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true);
		header("Cache-Control: no-cache, no-store, must-revalidate");
		header("Pragma: no-cache");
		header("Expires: 0");
		$output = fopen("php://output", "w");

		echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
		
		if ($alltabs || !$exportplaylist)
		{
			echo "<WONDERPLUGINTABS>\r\n";
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
			echo '</WONDERPLUGINTABS>';
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
		$table_name = $wpdb->prefix . "wonderplugin_tabs";

		return $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) , ARRAY_A);
	}

	function eacape_html_quotes($str) {
	
		$result = str_replace("\'", "&#39;", $str);
		$result = str_replace('\"', '&quot;', $result);
		$result = str_replace("'", "&#39;", $result);
		$result = str_replace('"', '&quot;', $result);
		return $result;
	}
	
	function get_tab_content($post) {
		
		if (!isset($post['id']) || !is_numeric($post['id'])  || !isset($post['tabid']) || !is_numeric($post['tabid']))
			return;
		
		$id = (int)$post['id'];
		$tabid = (int)$post['tabid'];
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
		
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		
		if ($item_row != null)
		{
			$data = json_decode(trim($item_row->data));
						
			if (isset($data->slides) && count($data->slides) > 0 && isset($data->slides[$tabid]))
			{
				$slide = $data->slides[$tabid];
				
				if ($slide->type ==1 && $slide->pageid > 0)
				{
					$wpautop = !(isset($data->disablewpautop) && strtolower($data->disablewpautop) === 'true');
					
					$pagecontent = $this->get_page_code($slide->pageid, $wpautop);
					
					return array(
						'success'	=> 	true,
						'content'	=> 	$pagecontent
					);
				}
				else
				{
					return array(
						'success'	=> 	true,
						'content'	=> 	$slide->tabcontent
					);
				}
			}
		}
		
		return array(
				'success'	=> 	false,
				'message'	=> 	'Cannot load tab content'
		);
	}
	
	function generate_content_shortcode($id) {
				
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
		
		$ret = '[wonderplugin_tabs id="' . $id . '" inline="1"]' . "\r\n\r\n";
		
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$data = json_decode(trim($item_row->data));
					
			if (isset($data->slides) && count($data->slides) > 0)
			{
				foreach ($data->slides as $index => $slide)
				{
					if ($slide->type == 2)
						continue;
					
					$ret .= '[wonderplugin_tab_content]' . "\r\n";
					
					if ($slide->type ==1 && $slide->pageid > 0)
					{
						$ret .= '[wonderplugin_tab_page id="' . $slide->pageid . '"';
						
						if (isset($data->disablewpautop) && strtolower($data->disablewpautop) === 'true')
							$ret .= ' wpautop="0"';
						
						$ret .= ']';
					}
					else
					{
						$ret .= $slide->tabcontent;
					}
						
					$ret .= "\r\n" . '[/wonderplugin_tab_content]' . "\r\n\r\n";
				}
			}
		}
		
		$ret .= '[/wonderplugin_tabs]';
		
		return $ret;
	}
	
	function get_page_code( $id, $autop ) {
		
		if (!is_numeric($id))
			return 'Please specify a valid page id!';
		
		$publish_status = get_post_status ( $id );
		if ( $publish_status !== 'publish' && $publish_status !== 'private' )
			return 'The specified page id does not exist or the page is not published!';
		
		$page = get_post( $id );
		
		$content = $autop ? wpautop($page->post_content) : $page->post_content;

		$content = do_shortcode($content);
		
		return $content;
	}
	
	function find_id_by_name($name)
	{
		$list = $this->get_list_data();
		
		$id = null;
		
		foreach($list as $item)
		{
			if (strcasecmp($item['name'], $name) == 0)
			{
				$id = $item['id'];
				break;
			}
		}
		
		return $id;
	}
	
	function get_items_from_xml($slide) {
	
		$default = array(
				'type'					=> 0,
				'tabtitle'				=> '',
				'tabcontent'			=> '',
				'pageid'				=> '-1',
				'tabicon'				=> '',
				'tabiconfontawesome'	=> '',
				'tabiconimage'			=> '',
				'tabiconimagewidth'		=> 48,
				'tabiconimageheight'	=> 48
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
		
						$items[] = (object) $new;
					}
				}
			}
		}
	
		return $items;
	}
	
	function generate_body_code($id, $name, $content, $data_attributes, $has_wrapper) {

		if ( !isset($id) )
		{
			if ( isset($name) )
			{
				$id = $this->find_id_by_name($name);
			}
		}
		
		if ( !isset($id) )
		{
			return '<p>Please specify a valid tabs id or name.</p>';
		}
		
		$inline_contents = null;
		
		if ( isset($content) )
		{
			$pattern = '\[(\[?)(wonderplugin_tab_content)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
			preg_match_all('/'.$pattern.'/s', $content, $matches);
			if (isset($matches) && count($matches) > 0 && isset($matches[5]) && count($matches[5]) > 0)
				$inline_contents = $matches[5];
		}
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
		
		if ( !$this->is_db_table_exists() )
		{
			return '<p>The specified tabs do not exist.</p>';
		}
		
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$data = json_decode(trim($item_row->data));
			
			if ( isset($data->publish_status) && ($data->publish_status === 0) )
			{
				return '<p>The specified tab group is trashed.</p>';
			}
			
			if (!empty($data_attributes))
			{
				foreach($data_attributes as $key => $value)
				{
					$data->{$key} = $value;
				}
			}
			
			foreach($data as $datakey => &$value)
			{
				if ($datakey == 'customjs')
					continue;
				
				if ( is_string($value) )
					$value = wp_kses_post($value);
			}
			
			if (isset($data->customcss) && strlen($data->customcss) > 0)
			{
				$customcss = str_replace("\r", " ", $data->customcss);
				$customcss = str_replace("\n", " ", $customcss);
				$customcss = str_replace("&gt;", ">", $customcss);
				$customcss = str_replace("TABSID", $id, $customcss);
				$ret .= '<style type="text/css">' . $customcss . '</style>';
			}
			
			if (isset($data->skincss) && strlen($data->skincss) > 0)
			{
				
				$skincss = str_replace("\r", " ", $data->skincss);
				$skincss = str_replace("\n", " ", $skincss);
				$skincss = str_replace("&gt;", ">", $skincss);
				$skincss = str_replace('TABSID',  $id, $skincss);
				foreach ( $data->skinoptions as $key => $value )
				{
					$skinkey = '@' . $key;
					$skinval =  $value[1];
					if ($value[0] == 'pixel')
						$skinval .= 'px';
					$skincss = str_replace($skinkey, $skinval, $skincss);
				}				
				$ret .= '<style type="text/css">' . $skincss . '</style>';
			}
			
			$ret .= '<div id="wonderplugintabs-container-' . $id . '" style="';
			
			if (isset($data->fullwidth) && strtolower($data->fullwidth) === 'true')
			{
				$ret .= 'max-width:100%;';
			}
			else if (isset($data->responsive) && strtolower($data->responsive) === 'true')
			{
				$ret .= 'max-width:' . $data->width . 'px;';
			}
			
			if ($has_wrapper)
				$ret .= 'margin:0 auto 16px;';
			else
				$ret .= 'margin:0 auto;';
			
			$ret .= '">';
			
			// div data tag
			$ret .= '<div style="display:none;" class="wonderplugintabs" id="wonderplugintabs-' . $id . '" data-tabsid="' . $id . '" data-width="' . $data->width . '" data-height="' . $data->height . '" data-skin="' . $data->skin . '"';
			
			if (isset($data->dataoptions) && strlen($data->dataoptions) > 0)
			{
				$ret .= ' ' . stripslashes($data->dataoptions);
			}
			
			$boolOptions = array('ajaxloading', 'ajaxloadingfirst', 'closeall', 'usetablecellfortitle', 'fixedheadersize', 'enablega', 'enabletabindex', 'keyaccess', 'fullwidthtabs', 'accordion', 'accordionmultiple', 'accordioncloseall', 'switchonmouseover', 'savestatusincookie', 'extendedheight', 'responsive', 'fullwidth', 'applydisplaynonetohiddenpanel', 'triggerresize', 'triggerresizeonload', 'disablewpautop', 'hidetitleonsmallscreen', 'donotinit', 'addinitscript', 'fullwidthtabsonsmallscreen', 'accordiononsmallscreen');
			foreach ( $boolOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . ((strtolower($data->{$key}) === 'true') ? 'true': 'false') .'"';
			}
			
			$valOptions = array('arrowfontsize', 'arrowfontcolor', 'headerwidth', 'headerheight', 'gatrackingid', 'heightmode', 'minheight', 'firstid', 'direction', 'tabposition', 'tabiconposition', 'horizontaltabalign', 'hidetitleonsmallscreenwidth', 'transition',
					'responsivemode', 'tabarrowmode', 'horizontalarrowwidthsameasheight', 'horizontalarrowwidth', 'arrowprevicon', 'arrownexticon', 'dropdownmenutext', 'dropdownmenuicon', 'triggerresizetimeout', 'triggerresizeonloadtimeout', 'fullwidthtabsonsmallscreenwidth');
			
			foreach ( $valOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . $data->{$key} . '"';
			}
			
			$cssOptions = array();
			foreach ( $cssOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . $this->eacape_html_quotes($data->{$key}) . '"';
			}
			
			$ret .= ' data-jsfolder="' . WONDERPLUGIN_TABS_URL . 'engine/" data-skinsfoldername="skins" >'; 
			
			$items = array();
			foreach ($data->slides as $slide)
			{
				if ($slide->type == 2)
				{
					$items = array_merge($items, $this->get_items_from_xml($slide));
				}
				else
				{
					$items[] = $slide;
				}
			}
			
			if (isset($items) && count($items) > 0)
			{
				/* header */
				
				$ret_header = '<div class="wonderplugintabs-header-wrap"><div class="wonderplugintabs-header-inner-wrap"><ul class="wonderplugintabs-header-ul">';
				foreach ($items as $index => $slide)
				{		
					$ret_header .= '<li class="wonderplugintabs-header-li';
					
					if ($index == 0)
						$ret_header .= ' wonderplugintabs-header-li-first';
					
					if ($index == count($items) - 1)
						$ret_header .= ' wonderplugintabs-header-li-last';
					
					$ret_header .= '">'; 
					
					$ret_header .= '<div class="wonderplugintabs-header-caption" style="text-align:' . (($data->tabiconposition == 'top') ? 'center': 'left') . ';">';
					
					$use_tablecell = (isset($data->usetablecellfortitle) && strtolower($data->usetablecellfortitle) === 'true');
					
					if ($slide->tabicon == 'fontawesome')
					{
						if (strlen($slide->tabiconfontawesome) > 0)
						{
							if ($use_tablecell)
							{
								$ret_header .= '<div class="wonderplugintabs-header-icon" style="display:' . (($data->tabiconposition == 'top') ? 'table-row': 'table-cell') . ';">';
							}
							
							$ret_header .= '<span class="wonderplugintabs-header-icon-fontawesome ' . $slide->tabiconfontawesome . '"></span>';
							
							if ($use_tablecell)
							{
								$ret_header .= '</div>';
							}
						}
					}
					else if ($slide->tabicon == 'image')
					{
						if (strlen($slide->tabiconimage) > 0)
						{
							$img_w = (is_numeric($slide->tabiconimagewidth)) ? $slide->tabiconimagewidth : 48;
							$img_h = (is_numeric($slide->tabiconimageheight)) ? $slide->tabiconimageheight : 48;
							if ($use_tablecell)
							{
								$ret_header .= '<div class="wonderplugintabs-header-icon" style="display:' . (($data->tabiconposition == 'top') ? 'table-row': 'table-cell') . ';">';
							}
							$ret_header .= '<img class="wonderplugintabs-header-icon-image"';
							if (strlen($slide->tabtitle) > 0)
								$ret_header .= ' alt="' . esc_attr(wp_strip_all_tags(do_shortcode($slide->tabtitle))) . '"';
							else
								$ret_header .= ' alt=""';
							$ret_header .= ' src="' . $slide->tabiconimage . '" style="width:' . $img_w . 'px;height:' . $img_h . 'px;" />';
							if ($use_tablecell)
							{
								$ret_header .= '</div>';
							}							
						}
					}

					if ($data->tabiconposition == 'top' && !$use_tablecell)
					{
						$ret_header .= '<br>';
					}
					
					if (strlen($slide->tabtitle) > 0)
					{
						$ret_header .= '<div class="wonderplugintabs-header-title"' . ( $use_tablecell ? ' style="display:table-cell;"' : '') . '>' . do_shortcode($slide->tabtitle) . '</div>';
					}
					
					$ret_header .= '</div>';
					
					$ret_header .= '</li>';
				}
				$ret_header .= '</ul></div></div>';
				
				/* panel */
				
				$ret_panel = '<div class="wonderplugintabs-panel-wrap" style="';
				
				if ($data->heightmode == "fixed")
					$ret_panel .= 'height:' . $data->height . 'px;';
				else
					$ret_panel .= 'min-height:' . $data->minheight . 'px;';
				
				$ret_panel .= '">';
								
				if (isset($items) && count($items) > 0)
				{
					foreach ($items as $index => $slide)
					{
						$ret_panel .= '<div class="wonderplugintabs-panel';
						if ($index == 0)
							$ret_panel .= ' wonderplugintabs-panel-first';
						if ($index == count($items) - 1)
							$ret_panel .= ' wonderplugintabs-panel-last';
						$ret_panel .= '"';
												
						if (isset($inline_contents) && is_array($inline_contents) && isset($inline_contents[$index]))
						{
							$ret_panel .= ' data-contentloaded=1><div class="wonderplugintabs-panel-inner">';
							
							$tab_content = $inline_contents[$index];
						}
						else if ( (isset($data->ajaxloading) && strtolower($data->ajaxloading) === 'true') && ( (isset($data->ajaxloadingfirst) && strtolower($data->ajaxloadingfirst) === 'true') || (isset($data->firstid) && $index != $data->firstid)))						
						{
							$ret_panel .= ' data-contentloaded=0><div class="wonderplugintabs-panel-inner">';
							
							$tab_content = '';
						}
						else
						{
							$ret_panel .= ' data-contentloaded=1><div class="wonderplugintabs-panel-inner">';
							
							if ($slide->type ==1 && $slide->pageid > 0)
							{
								$tab_content = '[wonderplugin_tab_page id="' . $slide->pageid . '"';
								
								if (isset($data->disablewpautop) && strtolower($data->disablewpautop) === 'true')
									$tab_content .= ' wpautop="0"';
								
								$tab_content .= ']';								
							}
							else
							{
								$tab_content = $slide->tabcontent;
							}
						}
						
						if ($has_wrapper)
						{
							$ret_panel .= $tab_content;
						}
						else
						{
							$ret_panel .= do_shortcode($tab_content);
						}
						
						$ret_panel .= '</div></div>';
					}
				}
				
				$ret_panel .= '</div>';
			
				if ($data->tabposition == "top" || $data->tabposition == "left")
					$ret .= $ret_header . $ret_panel;
				else
					$ret .= $ret_panel . $ret_header;
			}
			
			if ('F' == 'F')
				$ret .= '<div class="wonderplugin-engine"><a href="http://www.wonderplugin.com/wordpress-tabs/" title="'. get_option('wonderplugin-tabs-engine')  .'">' . get_option('wonderplugin-tabs-engine') . '</a></div>';
			
			$ret .= '</div>';
			
			$ret .= '</div>';
			
			if (isset($data->addinitscript) && strtolower($data->addinitscript) === 'true')
			{
				$ret .= '<script>jQuery(document).ready(function(){jQuery(".wonderplugin-tabs-engine").css({display:"none"});jQuery(".wonderplugintabs").wonderplugintabs({forceinit:true});});</script>';
			}
			
			if (isset($data->customjs) && strlen($data->customjs) > 0)
			{
				$customjs = str_replace("\r", " ", $data->customjs);
				$customjs = str_replace("\n", " ", $customjs);
				$customjs = str_replace('&lt;',  '<', $customjs);
				$customjs = str_replace('&gt;',  '>', $customjs);
				$customjs = str_replace("TABSID", $id, $customjs);
				$ret .= '<script language="JavaScript">' . $customjs . '</script>';
			}
		}
		else
		{
			$ret = '<p>The specified tabs id does not exist.</p>';
		}
		return $ret;
	}
	
	function delete_item($id) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
		
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
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
	
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
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
		
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
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
	
		return ( strtolower($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) == strtolower($table_name) );
	}
	
	function is_id_exist($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
	
		$tabs_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );		
		return ($tabs_row != null);
	}
	
	function create_db_table() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
		
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
			
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
		
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
				"message" => "Tabs published!"
		);
	}
	
	function get_list_data() {
		
		if ( !$this->is_db_table_exists() )
			$this->create_db_table();
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
		
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
		$table_name = $wpdb->prefix . "wonderplugin_tabs";
	
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$ret = $item_row->data;
		}

		return $ret;
	}
	
	
	function get_settings() {
	
		$userrole = get_option( 'wonderplugin_tabs_userrole' );
		if ( $userrole == false )
		{
			update_option( 'wonderplugin_tabs_userrole', 'manage_options' );
			$userrole = 'manage_options';
		}
		
		$keepdata = get_option( 'wonderplugin_tabs_keepdata', 1 );
		
		$disableupdate = get_option( 'wonderplugin_tabs_disableupdate', 0 );
		
		$supportwidget = get_option( 'wonderplugin_tabs_supportwidget', 1 );
		
		$addjstofooter = get_option( 'wonderplugin_tabs_addjstofooter', 0 );
		
		$jsonstripcslash = get_option( 'wonderplugin_tabs_jsonstripcslash', 1 );
		
		$tinymceeditor = get_option( 'wonderplugin_tabs_tinymceeditor', 0 );
		
		$loadfontawesome = get_option( 'wonderplugin_tabs_loadfontawesome', 1 );
		
		$supportajaxloading = get_option( 'wonderplugin_tabs_supportajaxloading', 0 );
		
		$ajaxverifynonce = get_option( 'wonderplugin_tabs_ajaxverifynonce', 0 );
		
		$settings = array(
				"userrole" => $userrole,
				"keepdata" => $keepdata,
				"disableupdate" => $disableupdate,
				"supportwidget" => $supportwidget,
				"addjstofooter" => $addjstofooter,
				"jsonstripcslash" => $jsonstripcslash,
				"tinymceeditor" => $tinymceeditor,
				"loadfontawesome" => $loadfontawesome,
				"supportajaxloading" => $supportajaxloading,
				"ajaxverifynonce" => $ajaxverifynonce
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
		update_option( 'wonderplugin_tabs_userrole', $userrole );
		
		if (!isset($options) || !isset($options['keepdata']))
			$keepdata = 0;
		else
			$keepdata = 1;
		update_option( 'wonderplugin_tabs_keepdata', $keepdata );
		
		if (!isset($options) || !isset($options['disableupdate']))
			$disableupdate = 0;
		else
			$disableupdate = 1;
		update_option( 'wonderplugin_tabs_disableupdate', $disableupdate );
		
		if (!isset($options) || !isset($options['supportwidget']))
			$supportwidget = 0;
		else
			$supportwidget = 1;
		update_option( 'wonderplugin_tabs_supportwidget', $supportwidget );
		
		if (!isset($options) || !isset($options['addjstofooter']))
			$addjstofooter = 0;
		else
			$addjstofooter = 1;
		update_option( 'wonderplugin_tabs_addjstofooter', $addjstofooter );
		
		if (!isset($options) || !isset($options['jsonstripcslash']))
			$jsonstripcslash = 0;
		else
			$jsonstripcslash = 1;
		update_option( 'wonderplugin_tabs_jsonstripcslash', $jsonstripcslash );
		
		if (!isset($options) || !isset($options['tinymceeditor']))
			$tinymceeditor = 0;
		else
			$tinymceeditor = 1;
		update_option( 'wonderplugin_tabs_tinymceeditor', $tinymceeditor );
		
		if (!isset($options) || !isset($options['loadfontawesome']))
			$loadfontawesome = 0;
		else
			$loadfontawesome = 1;
		update_option( 'wonderplugin_tabs_loadfontawesome', $loadfontawesome );
		
		if (!isset($options) || !isset($options['supportajaxloading']))
			$supportajaxloading = 0;
		else
			$supportajaxloading = 1;
		update_option( 'wonderplugin_tabs_supportajaxloading', $supportajaxloading );
		
		if (!isset($options) || !isset($options['ajaxverifynonce']))
			$ajaxverifynonce = 0;
		else
			$ajaxverifynonce = 1;
		update_option( 'wonderplugin_tabs_ajaxverifynonce', $ajaxverifynonce );
	}
	
	function get_plugin_info() {
	
		$info = get_option('wonderplugin_tabs_information');
		if ($info === false)
			return false;
	
		return unserialize($info);
	}
	
	function save_plugin_info($info) {
	
		update_option( 'wonderplugin_tabs_information', serialize($info) );
	}
	
	function check_license($options) {
	
		$ret = array(
				"status" => "empty"
		);
	
		if ( !isset($options) || empty($options['wonderplugin-tabs-key']) )
		{
			return $ret;
		}
	
		$key = sanitize_text_field( $options['wonderplugin-tabs-key'] );
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
	
		if ( !isset($options) || empty($options['wonderplugin-tabs-key']) )
			return $ret;
	
		$key = sanitize_text_field( $options['wonderplugin-tabs-key'] );
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
