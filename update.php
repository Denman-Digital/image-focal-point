<?php

/**
 * Handle Updates
 *
 * @since   2.0
 * @package wp-image-focal-point
 */

namespace WP_Image_Focal_Point;

class WP_Image_Focal_Point_Update
{
	private static $instance = null;

	private $repo_version_branch = "main";

	private $remote_plugin_endpoint_base = "";

	private $did_fetch_remote_data = false;

	private function __construct()
	{
		$this->remote_plugin_endpoint_base = "https://raw.githubusercontent.com/Denman-Digital/wp-image-focal-point/{$this->repo_version_branch}/";
		add_filter("update_plugins_wp-image-focal-point", [$this, "update_plugins_wp_image_focal_point_data"], 10, 1);
		add_filter("pre_set_site_transient_update_plugins", [$this, "modify_plugins_transient"], 10, 1);
		add_filter("plugins_api", [$this, "modify_plugin_details"], 99999, 3);
		add_filter("upgrader_post_install",  [$this, "post_install"], 10, 3);
	}

	public static function instance(): WP_Image_Focal_Point_Update
	{
		if (self::$instance == null) {
			self::$instance = new WP_Image_Focal_Point_Update();
		}
		return self::$instance;
	}

	public function get_remote_plugin_data(): array
	{
		$remote_plugin_data = get_plugin_data($this->remote_plugin_endpoint_base . WPIFP_PLUGIN_FILE);
		if (!$remote_plugin_data) return [];
		return [
			"slug" => "wp-image-focal-point",
			"plugin" => WPIFP_PLUGIN_BASENAME,
			"name" => $remote_plugin_data["Name"],
			"version" => $remote_plugin_data["Version"],
			"new_version" => $remote_plugin_data["Version"],
			"url" => $remote_plugin_data["PluginURI"],
			"package" => "https://github.com/Denman-Digital/wp-image-focal-point/archive/{$this->repo_version_branch}.zip",
			"requires" => $remote_plugin_data["RequiresWP"],
			"require_php" => $remote_plugin_data["RequiresPHP"],
			"author" => $remote_plugin_data["Author"],
			"icons" => [
				"2x" => $this->remote_plugin_endpoint_base . "assets/icon-256x256.png",
				"1x" => $this->remote_plugin_endpoint_base . "assets/icon-128x128.png",
				"svg" => $this->remote_plugin_endpoint_base . "assets/icon.svg",
			],
			"banners" => [
				"high" => $this->remote_plugin_endpoint_base . "assets/banner-1544x500.jpg",
				"low" => $this->remote_plugin_endpoint_base . "assets/banner-772x250.jpg",
			]
		];
	}

	public function update_plugins_wp_image_focal_point_data($value)
	{
		if ($data = $this->get_remote_plugin_data()) {
			$this->did_fetch_remote_data = true;
			$value = $data;
		}
		return $value;
	}

	public function modify_plugins_transient($transient)
	{
		// bail early if no response (error)
		if (!isset($transient->response)) {
			return $transient;
		}

		if (
			isset(
				$transient->checked,
				$transient->checked[WPIFP_PLUGIN_BASENAME],
				$transient->no_update[WPIFP_PLUGIN_BASENAME]
			)
			&& !$this->did_fetch_remote_data
		) {
			$remote_data = (object) $this->get_remote_plugin_data();
			$local_data = get_plugin_data(WPIFP_PLUGIN_PATH . "plugin.php");

			if (version_compare($remote_data->new_version, $local_data['Version'], '>')) {
				$transient->response[WPIFP_PLUGIN_BASENAME] = $remote_data;
				unset($transient->no_update[WPIFP_PLUGIN_BASENAME]);
			}
		}

		return $transient;
	}

	function modify_plugin_details($result, $action = null, $args = null)
	{
		if (!isset($args->slug) || $args->slug !== "wp-image-focal-point" || $action !== 'plugin_information') {
			return $result;
		}
		$result = $this->get_remote_plugin_data();
		if (!is_array($result)) {
			return $result;
		}

		$local_data = get_plugin_data(WPIFP_PLUGIN_PATH . "plugin.php");

		$result = (object) $result;

		$sections = [
			'description' => $local_data["Description"],
			'installation' => sprintf(
				// translators: %s: link URL
				__('<a href="%s" download>Download the latest release from GitHub</a>, and either install it through the Add New Plugins page in the WordPress admin, or manually extract the contents into your WordPress installations plugin folder.', "wp-img-focal-point"),
				esc_url("https://github.com/Denman-Digital/wp-image-focal-point/archive/{$this->repo_version_branch}.zip")
			),
			'changelog' => sprintf(
				'<a href="%s">%s</a>',
				esc_url("https://github.com/Denman-Digital/wp-image-focal-point/releases"),
				__("Full list of releases", "wp-img-focal-point"),
			),
		];
		$result->sections = $sections;
		return $result;
	}


	/**
	 * Finalize install
	 * @param bool $_response
	 * @param array $_hook_extra
	 * @param array $result
	 * @return bool
	 */
	public function post_install(bool $response, array $_hook_extra, array $result): bool
	{
		// Remember if our plugin was previously activated
		$wasActivated = is_plugin_active(WPIFP_PLUGIN_BASENAME);

		if (isset($_hook_extra["plugin"]) && $_hook_extra["plugin"] === WPIFP_PLUGIN_BASENAME) {
			global $wp_filesystem;
			$pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname(WPIFP_PLUGIN_BASENAME);
			$wp_filesystem->move($result['destination'], $pluginFolder);
			$result['destination'] = $pluginFolder;

			if ($wasActivated) {
				$activate = activate_plugin(WPIFP_PLUGIN_BASENAME);
			}
		}
		return $response;
	}
}

WP_Image_Focal_Point_Update::instance();
