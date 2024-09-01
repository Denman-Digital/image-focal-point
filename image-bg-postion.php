<?php

/**
 * Plugin Name: WP Image Focal Point
 * Description: Set background focus position for the media images
 * Version: 1.1
 * Author: Denman Digital
 * Author URI: https://denman.digital/
 * Tested up to: 5.7
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * text-domain: wp-img-focal-point
 */

namespace WP_Image_Focal_Point;

use WP_Post;

function add_attachment_custom_field($form_fields, $post)
{
	$field_value = get_post_meta($post->ID, 'focal_point', true) ?: '50% 50%';
	$is_value_not_default = $field_value != '50% 50%';

	$label_instructions = esc_html__("Click on the image to set the focus point", "wp-img-focal-point");
	$label_cancel = esc_html__("Cancel", "wp-img-focal-point");
	$label_save = esc_html__("Save", "wp-img-focal-point");
	$label_change = esc_html__("Change", "wp-img-focal-point");
	$label_set = esc_html__("Set", "wp-img-focal-point");
	$label_reset = esc_html__("Reset", "wp-img-focal-point");
	$label_default = esc_html__('Centered (default)', "wp-img-focal-point");

	$html = "
			<input type='hidden' value='$field_value'	id='focal_point_hidden_input' name='attachments[$post->ID][focal_point]'>
			<div id='focal-point-overlay' class='overlay'>
				<div class='img-container'>
					<div class='header'>
						<div class='wrapp'>
							<h3>$label_instructions</h3>
							<div class='controls'>
								<span class='button button-secondary' onclick='WP_Image_Focal_Point.cancelFocus()'>$label_cancel</span>
								<span class='button button-primary' onclick='WP_Image_Focal_Point.closeOverlay()'>$label_save</span>
							</div>
						</div>
					</div>
					<div class='container'>
						<div class='pin-field'>
							<div class='pin'></div>
							<img id='focal-point-image' draggable='false' src='" . wp_get_attachment_url($post->ID) . "'>
						</div>
					</div>
				</div>
			</div>
			<div id='focal-point-dashboard'>
				<div id='focal-point-value'>" . ($is_value_not_default ? esc_html($field_value) : $label_default) . "</div>
				<input
					type='button'
					id='focal-point-set'
					class='button button-small'
					onclick='WP_Image_Focal_Point.setFocus()'
					value='" . ($is_value_not_default ? $label_change : $label_set) . "'>
				<input
					type='button'
					id='focal-point-reset'
					class='close button button-small " . ($is_value_not_default ? '' : 'button-disabled') ."'
					onclick='WP_Image_Focal_Point.resetFocus()'
					value='$label_reset'" .
					($is_value_not_default ? '' : 'aria-disabled="true"') . ">
			</div>
		";

	$form_fields['background_postion_desktop'] = array(
		'value' => $field_value ?: '',
		'label' => __('Focal Point', "wp-img-focal-point"),
		'helps' => __(''),
		'input'  => 'html',
		'html' => $html
	);

	return $form_fields;
}
add_filter('attachment_fields_to_edit', __NAMESPACE__ . '\add_attachment_custom_field', null, 2);

//save custom media field
function save_attachment_custom_field($attachment_id)
{
	if (isset($_REQUEST['attachments'][$attachment_id]['focal_point'])) {
		$focal_point = $_REQUEST['attachments'][$attachment_id]['focal_point'];
		update_post_meta($attachment_id, 'focal_point', $focal_point);
	}
}
add_action('edit_attachment', __NAMESPACE__ . '\save_attachment_custom_field');

/**
 * Add object position to images on frontend (defaults to centered if not set)
 * @param array $attrs
 * @param WP_Post $attachment
 * @return array
 */
function filter_gallery_img_atts(array $attrs, WP_Post $attachment): array
{
	$styles = $attrs["style"] ?? "";
	if ($styles) {
		$styles .= ";";
	}
	$focal_point = get_post_meta($attachment->ID, "focal_point", true);
	$attrs["style"] = $styles . "object-position:" . ((string) $focal_point ?: "50% 50%") . ";";
	return $attrs;
}
add_filter('wp_get_attachment_image_attributes', __NAMESPACE__ . '\filter_gallery_img_atts', 10, 2);

/**
 * Enqueue script in Admin
 */
function enqueue_admin_scripts()
{
	wp_enqueue_style('wp-image-focal-point-css', plugin_dir_url(__FILE__) . '/admin.css?_=1' . time());
	wp_enqueue_script('wp-image-focal-point-js', plugin_dir_url(__FILE__) . '/script.js?_=1' . time(), ["jquery"]);
	wp_localize_script("wp-image-focal-point-js", __NAMESPACE__, [
		"labels" => [
			"change" => esc_html__("Change", "wp-img-focal-point"),
			"set" => esc_html__("Set", "wp-img-focal-point"),
			"reset" => esc_html__("Reset", "wp-img-focal-point"),
			"default" => esc_html__('Centered (default)', "wp-img-focal-point"),
		]
	]);
}
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_admin_scripts');
