<?php
if ( ! class_exists( 'Mirae_GitHub_Updater' ) ) {
	class Mirae_GitHub_Updater {
	
		private $plugin_file;
		private $plugin_data;
		private $github_api_url = 'https://api.github.com/repos/';
		private $username = 'mrtn';
		private $repository = 'mirae';
		private $branch = 'release';

		public function __construct( $plugin_file ) {
			$this->plugin_file = $plugin_file;

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
			add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
		}

		private function get_remote_info() {
			$request_uri = $this->github_api_url . "{$this->username}/{$this->repository}/contents/{$this->repository}.php?ref={$this->branch}";
			$response = wp_remote_get( $request_uri, array(
				'headers' => array( 'Accept' => 'application/vnd.github.v3.raw' ),
			) );

			if ( is_wp_error( $response ) ) return false;

			return wp_remote_retrieve_body( $response );
		}

		public function check_for_update( $transient ) {

			if ( empty( $transient->checked ) ) return $transient;

			$this->plugin_data = get_plugin_data( $this->plugin_file );
			$remote_code = $this->get_remote_info();

			if ( ! $remote_code ) return $transient;

			preg_match( '/Version:\s*(.*)/i', $remote_code, $matches );
			if ( ! isset( $matches[1] ) ) return $transient;

			$remote_version = trim( $matches[1] );
			$current_version = $this->plugin_data['Version'];


			if ( version_compare( $remote_version, $current_version, '>' ) ) {
				$plugin_slug = plugin_basename( realpath( $this->plugin_file ) );
				$transient->response[$plugin_slug] = (object) array(
					'slug'        => $this->repository,
					'plugin'      => $plugin_slug,
					'new_version' => $remote_version,
					'url'         => "https://github.com/{$this->username}/{$this->repository}",
					'package' => "https://github.com/{$this->username}/{$this->repository}/releases/download/v{$remote_version}/mirae-v{$remote_version}.zip",
				);
			}

			return $transient;
		}

		public function plugin_info( $false, $action, $args ) {
			if ( $action !== 'plugin_information' || $args->slug !== $this->repository ) {
				return false;
			}

			return (object) array(
				'name'        => $this->plugin_data['Name'],
				'slug'        => $this->repository,
				'version'     => $this->plugin_data['Version'],
				'author'      => $this->plugin_data['AuthorName'],
				'homepage'    => $this->plugin_data['PluginURI'],
				'sections'    => array(
					'description' => $this->plugin_data['Description'],
					'changelog' => $this->get_latest_release_body(),
				),
			);
		}

		
		private function get_latest_release() {
				$url = "https://api.github.com/repos/{$this->username}/{$this->repository}/releases/latest";
				$response = wp_remote_get( $url, array(
				'headers' => array( 'Accept' => 'application/vnd.github.v3+json' ),
				)
			);
		
			if ( is_wp_error( $response ) ) return false;
		
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			return $body['body'] ?? '';
		}
	
		private function get_latest_release_body() {
			$url = "https://api.github.com/repos/{$this->username}/{$this->repository}/releases/latest";
			$response = wp_remote_get( $url, array(
				'headers' => array(
					'Accept' => 'application/vnd.github.v3+json',
					'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' )
				),
			) );
		
			if ( is_wp_error( $response ) ) {
				return 'Kon changelog niet ophalen.';
			}
		
			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			return isset( $body['body'] ) ? nl2br( esc_html( $body['body'] ) ) : 'Geen changelog beschikbaar.';
		}
	}
}
