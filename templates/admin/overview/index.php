<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'error' );
}
global $current_user; ?>
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
                <small class="atd-cfi-xml-verification <?php echo ATD_CF_XML_VERIFIED ? 'atd-cfi-verified' : 'atd-cfi-not-verified'; ?>"><?php echo ATD_CF_XML_VERIFIED ? '' : 'Not '; ?>
                    Verified</small>
            </h3>
            <label for="<?php echo ATD_CF_XML_KEY_FIELD; ?>">Cruise Factory XML Key</label>
            <input type="text" id="<?php echo ATD_CF_XML_KEY_FIELD; ?>" class="regular-textbutton-primary"
                   name="<?php echo ATD_CF_XML_KEY_FIELD; ?>" value="<?php echo get_option( ATD_CF_XML_KEY_FIELD ); ?>">
            <button id="atd-cfi-verify-xml" class="button-primary">Verify</button>
        </div>
    </div>

	<?php if ( ATD_CF_XML_VERIFIED ): ?>
        <div class="postbox">
            <div class="inside">
                <h3>Google ReCaptcha</h3>
                <p>For your enquiry form, please enter your Google ReCaptcha keys below.</p>
                <fieldset>
                    <label>
                        <input class="atd-cfi-recaptcha-type" type="radio"
                               name="<?php echo ATD_CF_XML_GOOGLE_TYPE_FIELD; ?>"
                               value="v2c"<?php echo get_option( ATD_CF_XML_GOOGLE_TYPE_FIELD ) === 'v2c' ? ' checked="checked"' : ''; ?>>
                        <span>v2 Checkbox</span>
                    </label>
                    <br>
                    <label>
                        <input class="atd-cfi-recaptcha-type" type="radio"
                               name="<?php echo ATD_CF_XML_GOOGLE_TYPE_FIELD; ?>"
                               value="v2i"<?php echo get_option( ATD_CF_XML_GOOGLE_TYPE_FIELD ) === 'v2i' ? ' checked="checked"' : ''; ?>>
                        <span>v2 Invisible</span>
                    </label>
                    <br>
                    <label>
                        <input class="atd-cfi-recaptcha-type" type="radio"
                               name="<?php echo ATD_CF_XML_GOOGLE_TYPE_FIELD; ?>"
                               value="v3"<?php echo get_option( ATD_CF_XML_GOOGLE_TYPE_FIELD ) === 'v3' ? ' checked="checked"' : ''; ?>>
                        <span>v3</span>
                    </label>
                    <br><br>
                    <label for="<?php echo ATD_CF_XML_GOOGLE_SITE_KEY_FIELD; ?>">Site Key</label>
                    <input size="50" type="text" id="<?php echo ATD_CF_XML_GOOGLE_SITE_KEY_FIELD; ?>"
                           class="regular-textbutton-primary"
                           name="<?php echo ATD_CF_XML_GOOGLE_SITE_KEY_FIELD; ?>"
                           value="<?php echo get_option( ATD_CF_XML_GOOGLE_SITE_KEY_FIELD ); ?>">
                    <label for="<?php echo ATD_CF_XML_GOOGLE_SECRET_KEY_FIELD; ?>">Secret Key</label>
                    <input size="50" type="text" id="<?php echo ATD_CF_XML_GOOGLE_SECRET_KEY_FIELD; ?>"
                           class="regular-textbutton-primary"
                           name="<?php echo ATD_CF_XML_GOOGLE_SECRET_KEY_FIELD; ?>"
                           value="<?php echo get_option( ATD_CF_XML_GOOGLE_SECRET_KEY_FIELD ); ?>">
                    <button id="atd-cfi-recaptcha-save" class="button-primary">Save</button>
                </fieldset>
            </div>
        </div>
		<?php if ( in_array( 'administrator', $current_user->roles ) ): ?>
            <div class="postbox">
                <div class="inside">
					<?php $current_capability = get_option( ATD_CF_XML_ADMIN_MENU_CAPABILITY_FIELD, 'manage_options' );
					$capabilities             = array_keys( get_role( 'administrator' )->capabilities );
					sort( $capabilities ); ?>
                    <h3>Permissions</h3>
                    <p>For access to Cruise Factory in the administration area, please select the minimum user
                        capability.</p>

                    <fieldset>
                        <label for="<?php echo ATD_CF_XML_ADMIN_MENU_CAPABILITY_FIELD; ?>">Minimum capability</label>
                        <select type="radio" name="<?php echo ATD_CF_XML_ADMIN_MENU_CAPABILITY_FIELD; ?>"
                                id="<?php echo ATD_CF_XML_ADMIN_MENU_CAPABILITY_FIELD; ?>">
							<?php foreach ( $capabilities as $capability ): ?>
								<?php if ( substr( $capability, 0, 5 ) === 'level' ): continue; endif; ?>
                                <option value="<?php echo $capability; ?>"<?php echo( $current_capability === $capability ? ' selected' : '' ); ?>><?php echo $capability; ?></option>
							<?php endforeach; ?>
                        </select>
                        <button id="atd-cfi-capability-save" class="button-primary">Save</button>
                    </fieldset>
                </div>
            </div>
		<?php endif; ?>
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
                <div class="spinner is-active"
                     style="float:none;width:auto;height:auto;padding:10px 0 10px 50px;background-position:20px 0;"></div>
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