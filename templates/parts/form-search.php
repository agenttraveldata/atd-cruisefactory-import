<?php $isTax = is_tax( ATD\CruiseFactory\Taxonomy\DepartureType::$name, 'special' ); ?>
<form class="atd-cfi-sf" action="<?php echo $isTax ? get_term_link( 'special', ATD\CruiseFactory\Taxonomy\DepartureType::$name ) : get_post_type_archive_link( ATD\CruiseFactory\Post\Departure::$postType ); ?>" data-controller="atd-cfi-search-form">
	<?php if ( $isTax ): ?>
        <input type="hidden" name="<?php echo ATD\CruiseFactory\Taxonomy\DepartureType::$name; ?>" value="special">
	<?php endif; ?>
    <div class="atd-cfi-sf__col">
        <label for="<?php echo ATD\CruiseFactory\Taxonomy\Destination::$name; ?>" class="form-label visually-hidden">Destination</label>
        <select id="<?php echo ATD\CruiseFactory\Taxonomy\Destination::$name; ?>" name="<?php echo ATD\CruiseFactory\Taxonomy\Destination::$name; ?>" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Destination...</option>
        </select>
    </div>
    <div class="atd-cfi-sf__col">
        <label for="<?php echo ATD\CruiseFactory\Taxonomy\CruiseLine::$name; ?>" class="form-label visually-hidden">Cruise Line</label>
        <select id="<?php echo ATD\CruiseFactory\Taxonomy\CruiseLine::$name; ?>" name="<?php echo ATD\CruiseFactory\Taxonomy\CruiseLine::$name; ?>" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Cruise Line...</option>
        </select>
    </div>
    <div class="atd-cfi-sf__col">
        <label for="<?php echo ATD\CruiseFactory\Taxonomy\Ship::$name; ?>" class="form-label visually-hidden">Ship</label>
        <select id="<?php echo ATD\CruiseFactory\Taxonomy\Ship::$name; ?>" name="<?php echo ATD\CruiseFactory\Taxonomy\Ship::$name; ?>" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Ship...</option>
        </select>
    </div>
    <div class="atd-cfi-sf__col">
        <label for="<?php echo ATD\CruiseFactory\Taxonomy\Month::$name; ?>_from" class="form-label visually-hidden">Month from</label>
        <select id="<?php echo ATD\CruiseFactory\Taxonomy\Month::$name; ?>_from" name="<?php echo ATD\CruiseFactory\Taxonomy\Month::$name; ?>_from" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Month From...</option>
        </select>
    </div>
    <div class="atd-cfi-sf__col">
        <label for="<?php echo ATD\CruiseFactory\Taxonomy\Month::$name; ?>_to" class="form-label visually-hidden">Month to</label>
        <select id="<?php echo ATD\CruiseFactory\Taxonomy\Month::$name; ?>_to" name="<?php echo ATD\CruiseFactory\Taxonomy\Month::$name; ?>_to" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Month To...</option>
        </select>
    </div>
    <div class="atd-cfi-sf__col">
        <label for="<?php echo ATD\CruiseFactory\Taxonomy\Duration::$name; ?>" class="form-label visually-hidden">Duration</label>
        <select id="<?php echo ATD\CruiseFactory\Taxonomy\Duration::$name; ?>" name="<?php echo ATD\CruiseFactory\Taxonomy\Duration::$name; ?>" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
            <option value="">Any Duration...</option>
        </select>
    </div>
	<?php if ( $isTax ): ?>
        <div class="atd-cfi-sf__col">
            <label for="<?php echo ATD\CruiseFactory\Taxonomy\SpecialType::$name; ?>" class="form-label visually-hidden">Special Type</label>
            <select id="<?php echo ATD\CruiseFactory\Taxonomy\SpecialType::$name; ?>" name="<?php echo ATD\CruiseFactory\Taxonomy\SpecialType::$name; ?>" class="atd-cfi__input" data-atd-cfi-search-form-target="dropDown" data-action="atd-cfi-search-form#update">
                <option value="">Any Special Type...</option>
            </select>
        </div>
	<?php endif; ?>
    <div class="atd-cfi-sf__col">
        <label for="atd_cf_keyword" class="form-label visually-hidden">Keywords</label>
        <input id="atd_cf_keyword" type="text" name="atd_cf_keyword" class="atd-cfi__input" placeholder="Optional keywords..." value="<?php echo $_GET['atd_cf_keyword'] ?? ''; ?>">
    </div>
    <div class="atd-cfi-sf__col atd-cfi-sf__btns">
        <button class="atd-cfi__btn" type="submit">Search</button>
        <button class="atd-cfi__btn" type="reset" data-action="atd-cfi-search-form#reset">Reset</button>
    </div>
</form>