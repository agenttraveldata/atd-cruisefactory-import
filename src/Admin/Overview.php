<?php


namespace ATD\CruiseFactory\Admin;


class Overview {
	public static function register() {
		add_menu_page( 'Cruise Factory XML Options', 'Cruise Factory', get_option( ATD_CF_XML_ADMIN_MENU_CAPABILITY_FIELD, 'manage_options' ), ATD_CF_XML_MENU_SLUG );
		add_submenu_page( ATD_CF_XML_MENU_SLUG, 'Cruise Factory - Settings', 'Settings', get_option( ATD_CF_XML_ADMIN_MENU_CAPABILITY_FIELD, 'manage_options' ), 'atd-cruisefactory-xml-settings', function () {
			include __DIR__ . '/../../templates/admin/overview/index.php';
		} );
		add_submenu_page( ATD_CF_XML_MENU_SLUG, 'Cruise Factory - Synchronize', 'Synchronize', get_option( ATD_CF_XML_ADMIN_MENU_CAPABILITY_FIELD, 'manage_options' ), 'atd-cruisefactory-xml-synchronize', function () {
			include __DIR__ . '/../../templates/admin/overview/synchronize.php';
		} );
	}
}