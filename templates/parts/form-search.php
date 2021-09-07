<?php $isTax = is_tax( 'atd_cf_departure_type', 'special' ); ?>
<form class="atd-cfi-sf" action="<?php echo $isTax ? get_term_link( 'special', 'atd_cf_departure_type' ) : get_post_type_archive_link( 'departure' ); ?>" data-controller="atd-cfi-search-form">
	<?php if ( $isTax ): ?>
        <input type="hidden" name="<?php echo 'atd_cf_departure_type'; ?>" value="special">
	<?php endif; ?>
    <div class="atd-cfi-sf__col">
        <label for="atd_cf_destination" class="form-label visually-hidden">Destination</label>
        <select id="atd_cf_destination" name="atd_cf_destination" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Destination...</option>
        </select>
    </div>
    <div class="atd-cfi-sf__col">
        <label for="atd_cf_cruise_line" class="form-label visually-hidden">Cruise Line</label>
        <select id="atd_cf_cruise_line" name="atd_cf_cruise_line" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Cruise Line...</option>
        </select>
    </div>
    <div class="atd-cfi-sf__col">
        <label for="atd_cf_ship" class="form-label visually-hidden">Ship</label>
        <select id="atd_cf_ship" name="atd_cf_ship" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Ship...</option>
        </select>
    </div>
    <div class="atd-cfi-sf__col">
        <label for="atd_cf_month_from" class="form-label visually-hidden">Month from</label>
        <select id="atd_cf_month_from" name="atd_cf_month_from" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Month From...</option>
        </select>
    </div>
    <div class="atd-cfi-sf__col">
        <label for="atd_cf_month_to" class="form-label visually-hidden">Month to</label>
        <select id="atd_cf_month_to" name="atd_cf_month_to" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Month To...</option>
        </select>
    </div>
    <div class="atd-cfi-sf__col">
        <label for="atd_cf_duration" class="form-label visually-hidden">Duration</label>
        <select id="atd_cf_duration" name="atd_cf_duration" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Duration...</option>
        </select>
    </div>
	<?php if ( $isTax ): ?>
        <div class="atd-cfi-sf__col">
            <label for="atd_cf_special_type" class="form-label visually-hidden">Special Type</label>
            <select id="atd_cf_special_type" name="atd_cf_special_type" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
                <option value="">Any Special Type...</option>
            </select>
        </div>
	<?php endif; ?>
    <div class="atd-cfi-sf__col">
        <label for="atd_cf_keyword" class="form-label visually-hidden">Keywords</label>
        <input id="atd_cf_keyword" type="text" name="atd_cf_keyword" class="atd-cfi__input" placeholder="Optional keywords..." value="<?php echo $_GET['atd_cf_keyword'] ?? ''; ?>">
    </div>
    <div class="atd-cfi-sf__col atd-cfi-sf__btns">
        <button class="atd-cfi-sf-btns__btn atd-cfi-sf-btns-btn__submit" type="submit">Search</button>
        <button class="atd-cfi-sf-btns__btn atd-cfi-sf-btns-btn__reset" type="reset" data-action="atd-cfi-search-form#reset">Reset</button>
    </div>
</form>