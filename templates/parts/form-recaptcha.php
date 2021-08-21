<?php if ( ( $siteKey = get_option( ATD_CF_XML_GOOGLE_SITE_KEY_FIELD ) ) && ( $greCaptchaType = get_option( ATD_CF_XML_GOOGLE_TYPE_FIELD ) ) ): ?>
    <div style="visibility: hidden;display: none;" id="atd-cfi-recaptcha-type" data-type="<?php echo $greCaptchaType; ?>"></div>
	<?php switch ( $greCaptchaType ):
		case 'v3': ?>
            <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $siteKey; ?>"></script>
			<?php break;
		case 'v2c': ?>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <div class="atd-cfi-cols__column atd-cfi__mb-2">
                <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
            </div>
			<?php break;
		case 'v2i': ?>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <script>
                function atdCFI_onSubmit() {
                    document.querySelector('#atd-cfi-enquiry-form').dispatchEvent(new CustomEvent('submit', {bubbles: true}));
                }
            </script>
            <div id="recaptcha" class="g-recaptcha"
                 data-sitekey="<?php echo $siteKey; ?>"
                 data-callback="atdCFI_onSubmit"
                 data-size="invisible"></div>
			<?php break;
	endswitch; ?>
<?php endif; ?>