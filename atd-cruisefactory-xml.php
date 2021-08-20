<?php

/*
Plugin Name: ATD - Cruisefactory XML
Plugin URI: https://www.agenttraveldata.com/integrations/wordpress
Description: Cruise Factory XML import for creating dynamic automatically updated cruise websites. ^Cruise Factory account required.
Version: 1.0
Author: Agent Travel Data
Author URI: https://www.agenttraveldata.com
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/includes/core-functions.php';

const ATD_CF_PLUGIN_FILE                 = __FILE__;
const ATD_CF_PLUGIN_VERSION              = 1;
const ATD_CF_XML_KEY_FIELD               = 'atd_cf_xml_key';
const ATD_CF_XML_DB_VERSION_FIELD        = 'atd_cfi_db_version';
const ATD_CF_XML_ENQUIRY_PAGE_ID_FIELD   = 'atd_cfi_enquiry_page_id';
const ATD_CF_XML_GOOGLE_SITE_KEY_FIELD   = 'atd_cfi_recaptcha_site_key';
const ATD_CF_XML_GOOGLE_SECRET_KEY_FIELD = 'atd_cfi_recaptcha_secret_key';
const ATD_CF_XML_DATE_FORMAT             = 'Y-m-d H:i:s';
const ATD_CF_XML_VERIFIED_FIELD          = 'atd_cf_xml_verified';
const ATD_CF_XML_CHUNK_LIMIT             = 500;

define( 'ATD_CF_XML_VERIFIED', get_option( ATD_CF_XML_VERIFIED_FIELD ) );

use ATD\CruiseFactory\Importer;

( new Importer() );