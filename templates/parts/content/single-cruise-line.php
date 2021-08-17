<?php
/**
 * The template partial for displaying cruise line details
 */

global $atdCruiseLine;

?>

<div class="atd-cfi-single__details">
    <h1><?php echo the_title(); ?></h1>
</div>

<div class="atd-cfi__tabs" data-controller="atd-cfi-tabs">
    <div class="atd-cfi-tabs__anchors" data-atd-cfi-tabs-target="anchors">
        <a href="#atd-tab-overview">Overview</a>
        <a href="#atd-tab-departures">Cruise Departures</a>
		<?php if ( $atdCruiseLine->getShips()->count() > 0 ): ?>
            <a href="#atd-tab-ships">Ships</a>
		<?php endif; ?>
    </div>

    <div class="atd-cfi-tabs__contents" data-atd-cfi-tabs-target="contents" data-controller="atd-cfi-popover">
        <div id="atd-tab-overview"><?php atd_cf_get_template_part('content/cruise-line', 'overview'); ?></div>
        <div id="atd-tab-departures"
             data-controller="atd-cfi-ajax-results"
             data-atd-cfi-ajax-results-param-value='{"atd_cf_cruise_line": "<?php echo $atdCruiseLine->getId(); ?>"}'
             data-atd-cfi-ajax-results-endpoint-value="/wp-json/wp/v2/departure">
            <div class="atd-cfi-sr" data-atd-cfi-ajax-results-target="results">
                <div class="spinner-loader"></div>
            </div>
            <p>For more itineraries,
                <a href="<?php echo get_post_type_archive_link( 'departure' ); ?>">click here</a> to search our site.
            </p>
        </div>
		<?php if ( $atdCruiseLine->getShips()->count() > 0 ): ?>
            <div id="atd-tab-ships">
				<?php $q = atd_cf_get_query_for_posts_by_meta( 'ship', array_column( $atdCruiseLine->getShips()->toArray(), 'id' ) ); ?>
                <div class="atd-cfi-ar">
					<?php while ( $q->have_posts() ): $q->the_post();
						atd_cf_get_template_part( 'content/content', 'boxed' );
					endwhile; ?>
                </div>
				<?php wp_reset_postdata(); ?>
            </div>
		<?php endif; ?>
    </div>
</div>