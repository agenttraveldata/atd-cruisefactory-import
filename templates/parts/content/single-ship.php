<?php
/**
 * The template partial for displaying ship details
 */

global $atdShip;
$cruiseLinePost = atd_cf_get_post_by_meta_value( 'cruise-line', $atdShip->getCruiseLine()->getId() );

?>

<div class="atd-cfi-single__details">
    <h1><?php echo the_title(); ?></h1>

    <div class="atd-cfi-single-details__cruise-line">
        <img class="atd-cfi__img-fluid" src="<?php echo get_the_post_thumbnail_url( $cruiseLinePost ); ?>" alt="<?php echo $cruiseLinePost->post_title; ?>">
        <p><a href="<?php echo get_permalink( $cruiseLinePost->ID ); ?>"><?php echo $cruiseLinePost->post_title; ?></a></p>
    </div>
</div>

<div class="atd-cfi__tabs" data-controller="atd-cfi-tabs">
    <div class="atd-cfi-tabs__anchors" data-atd-cfi-tabs-target="anchors">
        <a href="#atd-tab-overview">Overview</a>
        <a href="#atd-tab-departures">Cruise Departures</a>
        <a href="#atd-tab-cabins">Cabins</a>
        <a href="#atd-tab-decks">Decks</a>
    </div>

    <div class="atd-cfi-tabs__contents" data-atd-cfi-tabs-target="contents" data-controller="atd-cfi-popover">
        <div id="atd-tab-overview">
			<?php atd_cf_get_template_part( 'content/ship', 'overview' ); ?>
        </div>
        <div id="atd-tab-departures"
             data-controller="atd-cfi-ajax-results"
             data-atd-cfi-ajax-results-param-value='{"atd_cf_ship": "<?php echo $atdShip->getId(); ?>"}'
             data-atd-cfi-ajax-results-endpoint-value="/wp-json/wp/v2/departure">
            <div class="atd-cfi-sr" data-atd-cfi-ajax-results-target="results">
                <div class="spinner-loader"></div>
            </div>
            <p>For more itineraries,
                <a href="<?php echo get_post_type_archive_link( 'departure' ); ?>">click here</a> to search our site.
            </p>
        </div>
        <div id="atd-tab-cabins">
			<?php atd_cf_get_template_part( 'content/ship', 'cabins' ); ?>
        </div>
        <div id="atd-tab-decks">
			<?php atd_cf_get_template_part( 'content/ship', 'decks' ); ?>
        </div>
    </div>
</div>