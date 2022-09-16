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
                <div class="inside">
                    <h3>Options</h3>

                    <fieldset>
                        <p><strong><span>Email address configuration for enquiries</span></strong></p>
                        <label for="<?php echo ATD_CF_XML_AGENT_EMAIL_FIELD; ?>">Agent email address</label><br>
                        <input size="50" type="text" id="<?php echo ATD_CF_XML_AGENT_EMAIL_FIELD; ?>"
                               class="atd-cfi-options regular-textbutton-primary"
                               name="atd_cf_options[<?php echo ATD_CF_XML_AGENT_EMAIL_FIELD; ?>"
                               value="<?php echo get_option( ATD_CF_XML_AGENT_EMAIL_FIELD, get_option( 'admin_email' ) ); ?>">
                        <br><br>
                        <label for="<?php echo ATD_CF_XML_SEND_FROM_EMAIL_FIELD; ?>">Sent From email address</label><br>
                        <input size="50" type="text" id="<?php echo ATD_CF_XML_SEND_FROM_EMAIL_FIELD; ?>"
                               class="atd-cfi-options regular-textbutton-primary"
                               name="atd_cf_options[<?php echo ATD_CF_XML_SEND_FROM_EMAIL_FIELD; ?>]"
                               value="<?php echo get_option( ATD_CF_XML_SEND_FROM_EMAIL_FIELD, get_option( 'admin_email' ) ); ?>">
                    </fieldset>
                    <br>
                    <fieldset>
                    <label for="<?php echo ATD_CF_XML_BCC_EMAIL_FIELD; ?>">BCC email address (leave blank to
                        skip)</label><br>
                    <input size="50" type="text" id="<?php echo ATD_CF_XML_BCC_EMAIL_FIELD; ?>"
                           class="atd-cfi-options regular-textbutton-primary"
                           name="atd_cf_options[<?php echo ATD_CF_XML_BCC_EMAIL_FIELD; ?>]"
                           value="<?php echo get_option( ATD_CF_XML_BCC_EMAIL_FIELD, null ); ?>">
                    </fieldset>
                    <br>
                    <fieldset>
                        <p><strong><span>Post options</span></strong></p>
                        <label for="<?php echo ATD_CF_XML_SLUG_FIELD; ?>">
                            <input class="atd-cfi-options" name="atd_cf_options[<?php echo ATD_CF_XML_SLUG_FIELD; ?>]"
                                   type="checkbox"
                                   id="<?php echo ATD_CF_XML_SLUG_FIELD; ?>" value="1"
								<?php echo get_option( ATD_CF_XML_SLUG_FIELD, false ) ? 'checked="checked"' : ''; ?>/>
                            <span>Retain original post slug when cruise is converted to special</span>
                        </label>
                    </fieldset>

                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Display "special" departures first when viewing search results</span>
                        </legend>
                        <label for="<?php echo ATD_CF_XML_RESULTS_SPECIALS_FIRST_FIELD; ?>">
                            <input class="atd-cfi-options"
                                   name="atd_cf_options[<?php echo ATD_CF_XML_RESULTS_SPECIALS_FIRST_FIELD; ?>]"
                                   type="checkbox"
                                   id="<?php echo ATD_CF_XML_RESULTS_SPECIALS_FIRST_FIELD; ?>" value="1"
								<?php echo get_option( ATD_CF_XML_RESULTS_SPECIALS_FIRST_FIELD, false ) ? 'checked="checked"' : ''; ?>/>
                            <span>Display "special" departures first when viewing search results</span>
                        </label>
                    </fieldset>

                    <button id="atd-cfi-options-save" class="button-primary" style="margin-top: 10px;">Save</button>
                </div>
            </div>
		<?php endif; ?>
	<?php endif; ?>
</div>