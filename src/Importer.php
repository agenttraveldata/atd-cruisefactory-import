<?php


namespace ATD\CruiseFactory;


use ATD\CruiseFactory\Services;
use wpdb;

class Importer {
	private wpdb $wpdb;
	public static array $endpoints;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;

		add_action( 'init', [ $this, 'init' ] );

		if ( is_admin() ) {
			register_activation_hook( ATD_CF_PLUGIN_FILE, [ $this, 'activate' ] );
			register_deactivation_hook( ATD_CF_PLUGIN_FILE, [ $this, 'deactivate' ] );
			register_uninstall_hook( ATD_CF_PLUGIN_FILE, [ self::class, 'uninstall' ] );
		}
	}

	public function init() {
		if ( class_exists( 'WP_CLI' ) ) {
			Services\WordPress\Commands\Provider::registerCommands();
		}

		$postHydrator = new Services\WordPress\Posts\Hydrator( $this->wpdb->__get( 'dbh' ) );

		Taxonomy\Provider::registerTaxonomies();
		Post\Provider::registerPosts();
		Services\WordPress\Templates\Provider::register();
		Services\WordPress\Shortcodes\Provider::register();

		/**
		 * Basic plugin required setup
		 */
		if ( is_admin() ) {
			Admin\Provider::registerAdminPages();
			Admin\Provider::registerAdminAjaxCalls();

			add_action( 'admin_enqueue_scripts', function () {
				wp_enqueue_script( 'atd-cf-xml-admin-js', plugins_url( '/dist/admin/atd-cfi.js', ATD_CF_PLUGIN_FILE ), [
					'jquery',
					'wp-util'
				], ATD_CF_PLUGIN_VERSION, false );

				wp_localize_script( 'atd-cf-xml-admin-js', 'atd_cfi', [
					'verify_xml' => wp_create_nonce( 'atd_cfi_verify_xml' ),
					'get_feeds'  => wp_create_nonce( 'atd_cfi_get_feeds' ),
					'xml_import' => wp_create_nonce( 'atd_cfi_import_xml' )
				] );
			} );
		}

		/**
		 * Add rest api endpoints
		 */
		add_action( 'rest_api_init', function () use ( $postHydrator ) {
			Controller\Provider::registerControllers();

			add_filter( 'rest_' . Post\Departure::$postType . '_query', [
				new Services\Search\Results( $postHydrator ),
				'restDepartureQuery'
			], 10, 2 );
			add_filter( 'rest_prepare_' . Post\Departure::$postType, [
				$postHydrator,
				'restDepartureHydration'
			], 10, 1 );
		} );

		/**
		 * Add post query manipulators
		 */
		if ( ! is_admin() ) {
			add_action( 'pre_get_posts', [ new Services\Search\Results(), 'searchQuery' ] );
			add_action( 'loop_start', [ $postHydrator, 'loopStart' ] );
			add_action( 'the_post', [ $postHydrator, 'thePost' ] );

			add_filter( 'get_attached_media_args', [
				new Services\WordPress\Posts\Finder(),
				'sortAttachedMediaQuery'
			], 10, 3 );

			add_action( 'wp_enqueue_scripts', function () {
				wp_enqueue_script( 'atd-cf-xml-js', plugins_url( '/dist/main.js', ATD_CF_PLUGIN_FILE ), [], false, true );
				wp_enqueue_style( 'atd-cf-xml-css', plugins_url( '/dist/main.css', ATD_CF_PLUGIN_FILE ) );
			} );
		}
	}

	public function activate() {
		Post\Provider::registerPosts();
		$this->installTables();

		if ( ! get_option( ATD_CF_XML_ENQUIRY_PAGE_ID_FIELD ) ) {
			$shortcode = new Services\WordPress\Blocks\Shortcode( '[atd-cfi-enquiry-form/]' );

			update_option( ATD_CF_XML_ENQUIRY_PAGE_ID_FIELD, wp_insert_post( [
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_name'      => 'cruise-enquiry',
				'post_title'     => 'Cruise Enquiry',
				'post_content'   => $shortcode->render(),
				'comment_status' => 'closed',
			] ) );
		}

		flush_rewrite_rules();
	}

	public function installTables() {
		if ( ATD_CF_PLUGIN_VERSION !== (int) get_option( ATD_CF_XML_DB_VERSION_FIELD ) ) {
			Feed\Provider::registerTables();
		}
	}

	public function deactivate() {
		Post\Provider::unregisterPosts();
		flush_rewrite_rules();
	}

	public static function uninstall() {
		global $wpdb;

		foreach ( Feed\Provider::getFeeds() as $feed ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$feed::getTableName($wpdb->prefix)}" );
		}

		delete_option( ATD_CF_XML_KEY_FIELD );
		delete_option( ATD_CF_XML_KEY_FIELD );
		delete_option( ATD_CF_XML_VERIFIED_FIELD );
		delete_option( ATD_CF_XML_VERIFIED_FIELD );
		delete_option( ATD_CF_XML_DB_VERSION_FIELD );
		delete_option( ATD_CF_XML_DB_VERSION_FIELD );

		wp_trash_post( get_option( ATD_CF_XML_ENQUIRY_PAGE_ID_FIELD ) );
	}
}