<?php

/**
 * Plugin Name: Image Background Focus Position
 * Description: Set background focus position for the media images
 * Version: 1.0
 * Author: florianmatthias
 * Author URI: https://florianmatthias.com/
 * Tested up to: 5.7
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

//custom media field
function custom_media_add_media_custom_field($form_fields, $post)
{
	$field_value = get_post_meta($post->ID, 'bg_pos_desktop', true);
	$disabled = ($field_value && $field_value != '50% 50%') ? '' : 'style="display:none"';
	$label = ($field_value && $field_value != '50% 50%') ? 'Change' : 'Set';
	$desktop_text = ($field_value && $field_value != '50% 50%') ? '<b>Desktop</b>: ' . $field_value : '<b>Desktop</b>: Centered (default)';
	$field_value = ($field_value) ? $field_value : '50% 50%';

	$html = "
			<input type='hidden' value='" . $field_value . "'	id='bg_pos_desktop_id' name='attachments[$post->ID][bg_pos_desktop]'>
			<input type='hidden' value='" . $field_value_2 . "'	id='bg_pos_mobile_id' name='attachments[$post->ID][bg_pos_mobile]'>
			<div class='overlay image_focus_point'>
				<div class='img-container'>
					<div class='header'>
						<div class='wrapp'>
							<h3>Click on the image to set the focus point</h3>
							<div class='controls'>
								<span class='button button-secondary' onclick='cancel_focus()'>Cancel</span>
								<span class='button button-primary' onclick='close_overlay()'>Save</span>
							</div>
						</div>
					</div>
					<div class='container'>
						<div class='pin'></div>
						<img src='" . wp_get_attachment_url($post->ID) . "'>
					</div>
				</div>
			</div>
			<div class='focusp_label_holder'>
				<div id='desktop_value'>" . $desktop_text . "</div>
				<input type='button' class='button button-small' value='" . $label . "' id='label_desktop' onclick='set_focus(0)'>
				<span class='close button button-small' id='reset_desktop' " . $disabled . " onclick='reset_focus()'>Reset</span>
			</div>
		";

	$form_fields['background_postion_desktop'] = array(
		'value' => $field_value ? $field_value : '',
		'label' => __('Focus Point'),
		'helps' => __(''),
		'input'  => 'html',
		'html' => $html
	);

	return $form_fields;
}
add_filter('attachment_fields_to_edit', 'custom_media_add_media_custom_field', null, 2);

//save custom media field
function custom_media_save_attachment($attachment_id)
{
	if (isset($_REQUEST['attachments'][$attachment_id]['bg_pos_desktop'])) {
		$bg_pos_desktop = $_REQUEST['attachments'][$attachment_id]['bg_pos_desktop'];
		update_post_meta($attachment_id, 'bg_pos_desktop', $bg_pos_desktop);
	}
}
add_action('edit_attachment', 'custom_media_save_attachment');

//apply filter in frontend to object-position
function filter_gallery_img_atts(array $attrs, WP_Post $attachment)
{
	$styles = $attrs["style"] ?? "";
	if ($styles) {
		$styles .= ";";
	}
	$focal_point = get_post_meta($attachment->ID, "bg_pos_desktop", true);
	$attrs["style"] = $styles . "object-position:" . ((string) $focal_point ?: "50% 50%") . ";";
	return $attrs;
}
add_filter('wp_get_attachment_image_attributes', 'filter_gallery_img_atts', 10, 2);

//enqueue script in admin
function image_bg_admin_scripts()
{
	wp_enqueue_style('image-bg-css', plugin_dir_url(__FILE__) . '/admin.css?_=1' . time());
	wp_enqueue_script('image-bg-js', plugin_dir_url(__FILE__) . '/script.js?_=1' . time());
}

add_action('admin_enqueue_scripts', 'image_bg_admin_scripts');
