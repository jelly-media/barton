<?php
/**
 * Adds validation for the NextGen Pro ImageBrowser display type
 * @mixin C_Display_Type
 * @adapts I_Display_Type
 */
class A_NextGen_Pro_ImageBrowser extends Mixin
{
    function validation()
    {
        return $this->call_parent('validation');
    }
}
/**
 * Provides rendering logic for the NextGen Pro ImageBrowser
 * @mixin C_Display_Type_Controller
 * @adapts I_Display_Type_Controller for "photocrati-nextgen_pro_imagebrowser" context
 */
class A_NextGen_Pro_ImageBrowser_Controller extends Mixin
{
    /**
     * Renders the front-end display for the imagebrowser display type
     *
     * @param C_Displayed_Gallery $displayed_gallery
     * @param bool $return
     * @return string
     */
    function index_action($displayed_gallery, $return = FALSE)
    {
        $picture_list = array();
        foreach ($displayed_gallery->get_included_entities() as $image) {
            $picture_list[$image->{$image->id_field}] = $image;
        }
        if ($picture_list) {
            $retval = $this->render_image_browser($displayed_gallery, $picture_list);
            if ($return) {
                return $retval;
            } else {
                echo $retval;
            }
        } else {
            return $this->object->render_partial('photocrati-nextgen_gallery_display#no_images_found', array(), $return);
        }
    }
    /**
     * Returns the rendered template of an image browser display
     *
     * @param C_Displayed_Gallery
     * @param array $picture_list
     * @return string Rendered HTML (probably)
     */
    function render_image_browser($displayed_gallery, $picture_list)
    {
        $display_settings = $displayed_gallery->display_settings;
        $storage = C_Gallery_Storage::get_instance();
        $imap = C_Image_Mapper::get_instance();
        $application = C_Router::get_instance()->get_routed_app();
        // the pid may be a slug so we must track it & the slug target's database id
        $pid = $this->object->param('pid');
        $numeric_pid = NULL;
        // makes the upcoming which-image-am-I loop easier
        $picture_array = array();
        foreach ($picture_list as $picture) {
            $picture_array[] = $picture->{$imap->get_primary_key_column()};
        }
        // Determine which image in the list we need to display
        if (!empty($pid)) {
            if (is_numeric($pid) && !empty($picture_list[$pid])) {
                $numeric_pid = intval($pid);
            } else {
                // in the case it's a slug we need to search for the pid
                foreach ($picture_list as $key => $picture) {
                    if ($picture->image_slug == $pid) {
                        $numeric_pid = $key;
                        break;
                    }
                }
            }
        } else {
            reset($picture_array);
            $numeric_pid = current($picture_array);
        }
        // get ids to the next and previous images
        $total = count($picture_array);
        $key = array_search($numeric_pid, $picture_array);
        if (!$key) {
            $numeric_pid = reset($picture_array);
            $key = key($picture_array);
        }
        // for "viewing image #13 of $total"
        $picture_list_pos = $key + 1;
        // our image to display
        $picture = new C_Image_Wrapper($imap->find($numeric_pid), $displayed_gallery, TRUE);
        $picture = apply_filters('ngg_image_object', $picture, $numeric_pid);
        // determine URI to the next & previous images
        $back_pid = $key >= 1 ? $picture_array[$key - 1] : end($picture_array);
        // 'show' is set when using the imagebrowser as an alternate view to a thumbnail or slideshow
        // for which the basic-gallery module will rewrite the show parameter into existence as long as 'image'
        // is set. We remove 'show' here so navigation appears fluid.
        $current_url = $application->get_routed_url(TRUE);
        if ($this->object->param('ajax_pagination_referrer')) {
            $current_url = $this->object->param('ajax_pagination_referrer');
        }
        $prev_image_link = $this->object->set_param_for($current_url, 'pid', $picture_list[$back_pid]->image_slug);
        $prev_image_link = $this->object->remove_param_for($prev_image_link, 'show', $displayed_gallery->id());
        $next_pid = $key < $total - 1 ? $picture_array[$key + 1] : reset($picture_array);
        $next_image_link = $this->object->set_param_for($current_url, 'pid', $picture_list[$next_pid]->image_slug);
        $next_image_link = $this->object->remove_param_for($next_image_link, 'show', $displayed_gallery->id());
        // css class
        $anchor = 'ngg-imagebrowser-' . $displayed_gallery->id() . '-' . (get_the_ID() == false ? 0 : get_the_ID());
        // try to read EXIF data, but fallback to the db presets
        $meta = new C_NextGen_Metadata($picture);
        $meta->sanitize();
        $meta_results = array('exif' => $meta->get_EXIF(), 'iptc' => $meta->get_IPTC(), 'xmp' => $meta->get_XMP(), 'db' => $meta->get_saved_meta());
        $meta_results['exif'] = $meta_results['exif'] == false ? $meta_results['db'] : $meta_results['exif'];
        // disable triggers IF we're rendering inside of an ajax-pagination request; var set in common.js
        if (!empty($_POST['ajax_referrer'])) {
            $displayed_gallery->display_settings['ngg_triggers_display'] = 'never';
        }
        if (!empty($display_settings['template']) && $display_settings['template'] != 'default') {
            $this->object->add_mixin('Mixin_NextGen_Pro_Templates');
            $picture->href_link = $picture->get_href_link();
            $picture->previous_image_link = $prev_image_link;
            $picture->previous_pid = $back_pid;
            $picture->next_image_link = $next_image_link;
            $picture->next_pid = $next_pid;
            $picture->number = $picture_list_pos;
            $picture->total = $total;
            $picture->anchor = $anchor;
            return $this->object->legacy_render($display_settings['template'], array('image' => $picture, 'meta' => $meta, 'exif' => $meta_results['exif'], 'iptc' => $meta_results['iptc'], 'xmp' => $meta_results['xmp'], 'db' => $meta_results['db'], 'displayed_gallery' => $displayed_gallery), TRUE, 'imagebrowser');
        } else {
            $params = $display_settings;
            $params['anchor'] = $anchor;
            $params['image'] = $picture;
            $params['storage'] =& $storage;
            $params['previous_pid'] = $back_pid;
            $params['next_pid'] = $next_pid;
            $params['number'] = $picture_list_pos;
            $params['total'] = $total;
            $params['previous_image_link'] = $prev_image_link;
            $params['next_image_link'] = $next_image_link;
            $params['effect_code'] = $this->object->get_effect_code($displayed_gallery);
            $params = $this->object->prepare_display_parameters($displayed_gallery, $params);
            return $this->object->render_partial('photocrati-nextgen_pro_imagebrowser#nextgen_pro_imagebrowser', $params, TRUE);
        }
    }
    /**
     * Enqueues all static resources required by this display type
     *
     * @param C_Displayed_Gallery $displayed_gallery
     */
    function enqueue_frontend_resources($displayed_gallery)
    {
        $this->call_parent('enqueue_frontend_resources', $displayed_gallery);
        wp_enqueue_style('nextgen_pro_imagebrowser_style', $this->get_static_url('photocrati-nextgen_pro_imagebrowser#style.css'), FALSE, NGG_SCRIPT_VERSION);
        $this->enqueue_ngg_styles();
    }
}
/**
 * Class A_NextGen_Pro_ImageBrowser_Form
 * @mixin C_Form
 * @adapts I_Form for "photocrati-nextgen_pro_imagebrowser" context
 */
class A_NextGen_Pro_ImageBrowser_Form extends Mixin_Display_Type_Form
{
    function get_display_type_name()
    {
        return NGG_PRO_IMAGEBROWSER;
    }
    /**
     * Returns a list of fields to render on the settings page
     */
    function _get_field_names()
    {
        return array('ajax_pagination', 'display_type_view');
    }
}
/**
 * Class A_NextGen_Pro_ImageBrowser_Mapper
 * @mixin C_Display_Type_Mapper
 * @adapts I_Display_Type_Mapper
 */
class A_NextGen_Pro_ImageBrowser_Mapper extends Mixin
{
    function set_defaults($entity)
    {
        $this->call_parent('set_defaults', $entity);
        if (isset($entity->name) && $entity->name == NGG_PRO_IMAGEBROWSER) {
            $this->object->_set_default_value($entity, 'settings', 'ajax_pagination', '1');
            $this->object->_set_default_value($entity, 'settings', 'ngg_triggers_display', 'never');
            $this->object->_set_default_value($entity, 'settings', 'display_type_view', 'default');
        }
    }
}
/**
 * Class A_NextGen_Pro_ImageBrowser_Urls
 * @mixin C_Routing_App
 * @adapts I_Routing_App
 */
class A_NextGen_Pro_ImageBrowser_Urls extends Mixin
{
    function create_parameter_segment($key, $value, $id = NULL, $use_prefix = FALSE)
    {
        if ($key == 'pid') {
            return "image/{$value}";
        } else {
            return $this->call_parent('create_parameter_segment', $key, $value, $id, $use_prefix);
        }
    }
}