<?php

/*
 * The GitHub-hosted updater. This is only used when the plugin is distributed
 * outside the WordPress.org plugin directory. Builds intended for .org should
 * define `MIRAE_DISABLE_GITHUB_UPDATER` (typically via the build pipeline or in
 * wp-config.php) to ensure no external service calls are made. WordPress will
 * then handle updates through its standard channel using the `Update URI` header.
 */
if ( ! class_exists( 'Mirae_GitHub_Updater' ) ) {
	class Mirae_GitHub_Updater {

		const VERSION_TRANSIENT = 'mirae_github_remote_version';
		const RELEASE_TRANSIENT = 'mirae_github_release_body';
		const CACHE_TTL         = 12 * HOUR_IN_SECONDS;

		private $plugin_file;
		private $plugin_slug;
		private $plugin_data;
		private $github_api_url = 'https://api.github.com/repos/';
		private $username       = 'mrtn';
		private $repository     = 'Mirae';
		private $branch         = 'release';

		public function __construct( $plugin_file ) {
			$this->plugin_file = $plugin_file;
			$this->plugin_slug = plugin_basename( $plugin_file );

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
			add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
			add_action( 'upgrader_process_complete', array( $this, 'flush_cache' ), 10, 2 );
		}

		private function request_args() {
			return array(
				'timeout' => 15,
				'headers' => array(
					'Accept'     => 'application/vnd.github.v3+json',
					'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url( '/' ),
				),
			);
		}

		/**
		 * Fetch the remote bootstrap file and return its raw contents.
		 * Result is cached for CACHE_TTL to stay under GitHub's anonymous rate limit.
		 */
		private function get_remote_info() {
			$cached = get_site_transient( self::VERSION_TRANSIENT );
			if ( false !== $cached ) {
				return $cached;
			}

			$request_uri = $this->github_api_url . "{$this->username}/{$this->repository}/contents/mirae.php?ref={$this->branch}";

			$args                      = $this->request_args();
			$args['headers']['Accept'] = 'application/vnd.github.v3.raw';

			$response = wp_remote_get( $request_uri, $args );
			if ( is_wp_error( $response ) ) {
				set_site_transient( self::VERSION_TRANSIENT, '', MINUTE_IN_SECONDS * 15 );
				return false;
			}
			if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				set_site_transient( self::VERSION_TRANSIENT, '', MINUTE_IN_SECONDS * 15 );
				return false;
			}

			$body = wp_remote_retrieve_body( $response );
			set_site_transient( self::VERSION_TRANSIENT, $body, self::CACHE_TTL );

			return $body;
		}

		public function check_for_update( $transient ) {
			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			$this->plugin_data = get_plugin_data( $this->plugin_file );
			$remote_code       = $this->get_remote_info();

			if ( ! $remote_code ) {
				return $transient;
			}

			if ( ! preg_match( '/Version:\s*(.*)/i', $remote_code, $matches ) ) {
				return $transient;
			}

			$remote_version  = trim( $matches[1] );
			$current_version = $this->plugin_data['Version'];

			if ( version_compare( $remote_version, $current_version, '>' ) ) {
				$transient->response[ $this->plugin_slug ] = (object) array(
					'slug'        => dirname( $this->plugin_slug ),
					'plugin'      => $this->plugin_slug,
					'new_version' => $remote_version,
					'url'         => "https://github.com/{$this->username}/{$this->repository}",
					'package'     => "https://github.com/{$this->username}/{$this->repository}/releases/download/v{$remote_version}/mirae-v{$remote_version}.zip",
				);
			}

			return $transient;
		}

		public function plugin_info( $result, $action, $args ) {
			if ( 'plugin_information' !== $action ) {
				return $result;
			}
			if ( ! isset( $args->slug ) || $args->slug !== dirname( $this->plugin_slug ) ) {
				return $result;
			}

			if ( empty( $this->plugin_data ) ) {
				$this->plugin_data = get_plugin_data( $this->plugin_file );
			}

			return (object) array(
				'name'     => $this->plugin_data['Name'],
				'slug'     => dirname( $this->plugin_slug ),
				'version'  => $this->plugin_data['Version'],
				'author'   => $this->plugin_data['AuthorName'],
				'homepage' => $this->plugin_data['PluginURI'],
				'sections' => array(
					'description' => $this->plugin_data['Description'],
					'changelog'   => $this->get_latest_release_body(),
				),
			);
		}

		private function get_latest_release_body() {
			$cached = get_site_transient( self::RELEASE_TRANSIENT );
			if ( false !== $cached ) {
				return $cached;
			}

			$url      = "https://api.github.com/repos/{$this->username}/{$this->repository}/releases/latest";
			$response = wp_remote_get( $url, $this->request_args() );

			if ( is_wp_error( $response ) ) {
				$msg = esc_html__( 'Could not fetch changelog.', 'mirae' );
				set_site_transient( self::RELEASE_TRANSIENT, $msg, MINUTE_IN_SECONDS * 15 );
				return $msg;
			}

			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( isset( $body['body'] ) ) {
				$rendered = nl2br( esc_html( $body['body'] ) );
				set_site_transient( self::RELEASE_TRANSIENT, $rendered, self::CACHE_TTL );
				return $rendered;
			}

			$msg = esc_html__( 'No changelog available.', 'mirae' );
			set_site_transient( self::RELEASE_TRANSIENT, $msg, MINUTE_IN_SECONDS * 15 );
			return $msg;
		}

		/**
		 * Drop cached lookups after a plugin update completes so the next
		 * cycle re-checks against GitHub instead of serving the pre-update value.
		 */
		public function flush_cache( $upgrader, $hook_extra ) {
			if ( ! is_array( $hook_extra ) || empty( $hook_extra['type'] ) || 'plugin' !== $hook_extra['type'] ) {
				return;
			}
			delete_site_transient( self::VERSION_TRANSIENT );
			delete_site_transient( self::RELEASE_TRANSIENT );
		}
	}
}
