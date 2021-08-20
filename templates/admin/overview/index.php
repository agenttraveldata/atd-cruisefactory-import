<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'error' );
} ?>
<style>
    .atd-cfi-xml-verification {
        color: #fff;
        padding: 4px;
        border-radius: 4px;
    }

    .atd-cfi-verified {
        background-color: #46B450;
    }

    .atd-cfi-not-verified {
        background-color: #DC3232;
    }
</style>
<div class="wrap">
    <h1 class="wp-heading-inline">Cruise Factory Import</h1>

    <div class="postbox">
        <div class="inside">
            <h3>
                XML Verification
                <small class="atd-cfi-xml-verification <?php echo ATD_CF_XML_VERIFIED ? 'atd-cfi-verified' : 'atd-cfi-not-verified'; ?>"><?php echo ATD_CF_XML_VERIFIED ? '' : 'Not '; ?>Verified</small>
            </h3>
            <label for="atd_cfi_xml_key">Cruise Factory XML Key</label>
            <input type="text" id="atd_cfi_xml_key" class="regular-textbutton-primary" name="<?php echo ATD_CF_XML_KEY_FIELD; ?>" value="<?php echo get_option( ATD_CF_XML_KEY_FIELD ); ?>">
            <button id="atd-cfi-verify-xml" class="button-primary">Verify</button>
        </div>
    </div>

	<?php if ( ATD_CF_XML_VERIFIED ): ?>
        <div class="postbox">
            <div class="inside">
                <h3>Google ReCaptcha</h3>
                <p>For your enquiry form, please enter your Google ReCaptcha keys below.</p>
                <label for="atd_cfi_recaptcha_site_key">Site Key</label>
                <input type="text" id="atd_cfi_recaptcha_site_key" class="regular-textbutton-primary" name="<?php echo ATD_CF_XML_GOOGLE_SITE_KEY_FIELD; ?>" value="<?php echo get_option( ATD_CF_XML_GOOGLE_SITE_KEY_FIELD ); ?>">
                <label for="atd_cfi_recaptcha_secret_key">Secret Key</label>
                <input type="text" id="atd_cfi_recaptcha_secret_key" class="regular-textbutton-primary" name="<?php echo ATD_CF_XML_GOOGLE_SECRET_KEY_FIELD; ?>" value="<?php echo get_option( ATD_CF_XML_GOOGLE_SECRET_KEY_FIELD ); ?>">
                <button id="atd-cfi-recaptcha-save" class="button-primary">Save</button>
            </div>
        </div>
	<?php endif; ?>

    <div class="tablenav bottom">
        <div class="alignright actions bulkactions">
            <button id="atd-cfi-increment-import" class="button action" role="button">Synchronize Now</button>
        </div>
    </div>

    <table id="atd-cfi-services" class="widefat striped table-view-list">
        <thead>
        <tr>
            <th class="row-title">Service</th>
            <th>Last Updated</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="2" style="text-align: center;">
                <div class="spinner is-active" style="float:none;width:auto;height:auto;padding:10px 0 10px 50px;background-position:20px 0;"></div>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <th class="row-title">Service</th>
            <th>Last Updated</th>
        </tr>
        </tfoot>
    </table>
</div>