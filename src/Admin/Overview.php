<?php


namespace ATD\CruiseFactory\Admin;


class Overview {
	public static function register() {
		add_menu_page( 'Cruise Factory XML Options', 'Cruise Factory', 'manage_options', ATD_CF_XML_MENU_SLUG );
		add_submenu_page( ATD_CF_XML_MENU_SLUG, 'Cruise Factory - Settings', 'Settings', 'manage_options', 'atd-cruisefactory-xml-settings', function () {
			include __DIR__ . '/../../templates/admin/overview/index.php';
		} );
	}
}