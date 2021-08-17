<?php


namespace ATD\CruiseFactory\Admin;


class Overview {
	public static function register() {
		add_menu_page( 'Cruise Factory XML Options', 'CF Import', 'manage_options', 'atd-cruisefactory-xml', function () {
			include __DIR__ . '/../../templates/admin/overview/index.php';
		} );
	}
}