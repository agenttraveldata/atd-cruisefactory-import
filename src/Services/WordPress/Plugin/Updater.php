<?php

namespace ATD\CruiseFactory\Services\WordPress\Plugin;

class Updater {
	private array $plugin;
	private bool $active;
	private string $basename;
	private string $username = 'agenttraveldata';
	private string $repository = 'atd-cruisefactory-import';

	public function __construct() {
		$this->plugin   = get_plugin_data( ATD_CF_PLUGIN_FILE );
		$this->basename = plugin_basename( ATD_CF_PLUGIN_FILE );
		$this->active   = is_plugin_active( $this->basename );

		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'modifyPluginTransient' ], 10, 2 );
		add_filter( 'plugins_api_result', [ $this, 'fetchPopupDetailsForPlugin' ], 10, 3 );
		add_filter( 'upgrader_post_install', [ $this, 'moveDirectoryPostInstall' ], 10, 3 );
	}

	public function modifyPluginTransient( object $transient, string $action ): object {
		if ( $action === 'update_plugins' && ! empty( $transient->checked ) ) {
			$releases = $this->fetchReleases();
			if ( ! empty( $releases ) ) {
				$currentVersion = str_replace( 'v', '', $releases[0]['tag_name'] );

				if ( version_compare( $currentVersion, $transient->checked[ $this->basename ], 'gt' ) ) {
					$transient->response[ $this->basename ] = (object) [
						'url'         => $this->plugin['PluginURI'],
						'slug'        => ATD_CF_XML_MENU_SLUG,
						'package'     => $releases[0]['assets'][0]['browser_download_url'],
						'new_version' => $releases[0]['tag_name']
					];
				}
			}
		}

		return $transient;
	}

	public function fetchPopupDetailsForPlugin( object $result, string $action, object $args ): object {
		if ( $action === 'plugin_information' && ! empty( $args->slug ) && $args->slug === ATD_CF_XML_MENU_SLUG ) {
			$releases = $this->fetchReleases();

			if ( ! empty( $releases ) ) {
				$updates = '';
				foreach ( $releases as $release ) {
					$date    = \DateTime::createFromFormat( 'Y-m-d\TH:i:s\Z', $release['published_at'] );
					$updates .= '<strong>' . $date->setTimezone( wp_timezone() )->format( 'd M Y - g:ia' ) . '</strong>' . PHP_EOL .
					            $release['body'] . PHP_EOL . PHP_EOL;
				}

				return (object) [
					'name'              => $this->plugin['Name'],
					'slug'              => $this->basename,
					'version'           => $releases[0]['tag_name'],
					'author'            => $this->plugin['AuthorName'],
					'author_profile'    => $this->plugin['AuthorURI'],
					'last_updated'      => $releases[0]['published_at'],
					'homepage'          => $this->plugin['PluginURI'],
					'short_description' => $this->plugin['Description'],
					'sections'          => [
						'Updates'     => nl2br( trim( $updates ) . PHP_EOL . PHP_EOL ),
						'Description' => $this->plugin['Description']
					],
					'download_link'     => $releases[0]['assets'][0]['browser_download_url']
				];
			}
		}

		return $result;
	}

	public function moveDirectoryPostInstall( bool $response, array $hook_extra, array $result ): array {
		global $wp_filesystem;

		$install_directory = plugin_dir_path( ATD_CF_PLUGIN_FILE );
		$wp_filesystem->move( $result['destination'], $install_directory );
		$result['destination'] = $install_directory;

		if ( $this->active ) {
			activate_plugin( $this->basename );
		}

		return $result;
	}

	private function fetchReleases(): array {
		$response = wp_remote_get( sprintf( 'https://api.github.com/repos/%s/%s/releases', $this->username, $this->repository ) );
		if ( is_wp_error( $response ) ) {
			return [];
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}