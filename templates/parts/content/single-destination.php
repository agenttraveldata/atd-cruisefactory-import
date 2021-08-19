<?php
/**
 * The template partial for displaying destination details
 */

global $atdDestination;

?>

<div class="atd-cfi-single__details">
    <h1><?php echo the_title(); ?></h1>
</div>

<div class="atd-cfi__tabs" data-controller="atd-cfi-tabs">
    <div class="atd-cfi-tabs__anchors" data-atd-cfi-tabs-target="anchors">
        <a href="#atd-tab-overview">Overview</a>
        <a href="#atd-tab-departures">Cruise Departures</a>
    </div>

    <div class="atd-cfi-tabs__contents" data-atd-cfi-tabs-target="contents" data-controller="atd-cfi-popover">
        <div id="atd-tab-overview">
            <a class="atd-cfi__float-end atd-cfi__ml-2 atd-cfi__mb-2 atd-cfi__mw-40" data-action="atd-cfi-popover#image" href="<?php echo get_the_post_thumbnail_url(); ?>">
				<?php the_post_thumbnail( 'large', [ 'class' => 'atd-cfi__img-fluid' ] ); ?>
            </a>
			<?php the_content(); ?>
        </div>
        <div id="atd-tab-departures"
             data-controller="atd-cfi-ajax-results"
             data-atd-cfi-ajax-results-param-value='{"atd_cf_destination": "<?php echo $atdDestination->getId(); ?>"}'
             data-atd-cfi-ajax-results-endpoint-value="/wp-json/wp/v2/departure">
            <div class="atd-cfi-sr" data-atd-cfi-ajax-results-target="results">
                <div class="spinner-loader"></div>
            </div>
            <p>For more itineraries,
                <a href="<?php echo get_post_type_archive_link( \ATD\CruiseFactory\Post\Departure::$postType ); ?>">click here</a> to search our site.
            </p>
        </div>
    </div>
</div>