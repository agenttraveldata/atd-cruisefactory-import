<form action="/wp-json/atd/cfi/v1/enquire/send" method="post" data-controller="atd-cfi-submit-form" data-action="atd-cfi-submit-form#submit">
    <div class="atd-cfi__cols-enquiry">
        <div class="atd-cfi-cols__column atd-cfi-cols-column-3 atd-cfi__mb-2">
            <label for="email_address">
                <input id="email_address" name="email_address" type="text" class="atd-cfi__input" placeholder="Email address">
            </label>
        </div>
        <div class="atd-cfi-cols__column atd-cfi-cols-column-2 atd-cfi__mb-2">
            <label for="phone_number">
                <input id="phone_number" name="phone_number" type="text" class="atd-cfi__input" placeholder="Phone Number">
            </label>
        </div>
        <div class="atd-cfi-cols__column atd-cfi-cols-column__half atd-cfi__mb-2">
            <label for="first_name">
                <input id="first_name" name="first_name" type="text" class="atd-cfi__input" placeholder="First Name">
            </label>
        </div>
        <div class="atd-cfi-cols__column atd-cfi-cols-column__half atd-cfi__mb-2">
            <label for="last_name">
                <input id="last_name" name="last_name" type="text" class="atd-cfi__input" placeholder="Last Name">
            </label>
        </div>
        <div class="atd-cfi-cols__column atd-cfi-cols-column__half atd-cfi__mb-2">
            <label for="num_adults">
                <input id="num_adults" name="num_adults" type="number" class="atd-cfi__input" placeholder="Number of adults">
            </label>
        </div>
        <div class="atd-cfi-cols__column atd-cfi-cols-column__half atd-cfi__mb-2">
            <label for="num_children">
                <input id="num_children" name="num_children" type="number" class="atd-cfi__input" placeholder="Number of children">
            </label>
        </div>
        <div class="atd-cfi-cols__column atd-cfi-cols-column__stretch atd-cfi__mb-2">
            <label for="message">
                <textarea name="message" id="message" cols="30" rows="10" placeholder="Tell us about your enquiry"></textarea>
            </label>
        </div>
		<?php if ( $siteKey = get_option( ATD_CF_XML_GOOGLE_SITE_KEY_FIELD ) ): ?>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <div class="atd-cfi-cols__column atd-cfi__mb-2">
                <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
            </div>
		<?php endif; ?>
        <div class="atd-cfi-cols__column atd-cfi-cols-column__half atd-cfi__mb-2 atd-cfi__align-start">
            <button type="reset">Reset</button>
        </div>
        <div class="atd-cfi-cols__column atd-cfi-cols-column__half atd-cfi__mb-2 atd-cfi__align-end">
            <button type="submit">Submit</button>
        </div>
    </div>
</form>