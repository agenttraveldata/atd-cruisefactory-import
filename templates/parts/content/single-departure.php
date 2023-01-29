<?php

/**
 * @var ATD\CruiseFactory\Entity\Departure $atdDeparture
 * @var ATD\CruiseFactory\Entity\Special $atdSpecial
 */
global $atdDeparture, $atdSpecial;
$cruiseLinePost = atd_cf_get_post_by_meta_value( ATD\CruiseFactory\Post\CruiseLine::$postType, $atdDeparture->getCruise()->getCruiseLine()->getId(), true );
$shipPost       = atd_cf_get_post_by_meta_value( ATD\CruiseFactory\Post\Ship::$postType, $atdDeparture->getCruise()->getShip()->getId(), true );

?>
<h2><?php the_title(); ?></h2>
<div data-controller="atd-cfi-popover">
    <div class="atd-cfi-departure__logo atd-cfi__mb-2">
        <img class="atd-cfi__img-fluid atd-cfi__mb-2"
             src="data:<?php echo $atdDeparture->getCruise()->getCruiseLine()->getLogoType(); ?>;base64,<?php echo base64_encode( $atdDeparture->getCruise()->getCruiseLine()->getLogoData() ); ?>"
             alt="Cruise Line">
        <p>
			<?php echo $atdDeparture->getCruise()->getDuration(); ?> nights onboard
            <a href="<?php echo get_permalink( $shipPost->post->ID ); ?>">
				<?php echo $atdDeparture->getCruise()->getShip()->getName(); ?>
            </a> from
            <a href="<?php echo get_permalink( $cruiseLinePost->post->ID ); ?>">
				<?php echo $atdDeparture->getCruise()->getCruiseLine()->getName(); ?>
            </a> departing <?php echo $atdDeparture->getSailingDate()->format( 'd M Y' ); ?>
        </p>
    </div>
    <div>
        <div class="atd-cfi__tabs" data-controller="atd-cfi-tabs">
            <div class="atd-cfi-tabs__anchors" data-atd-cfi-tabs-target="anchors">
				<?php if ( ! empty( $atdSpecial ) ): ?>
                    <a href="#atd-tab-offer">Offer Details</a>
				<?php endif; ?>
                <a href="#atd-tab-overview">Overview</a>
                <a href="#atd-tab-itinerary">Itinerary</a>
                <a href="#atd-tab-cruise-line">Cruise Line</a>
                <a href="#atd-tab-ship">Ship</a>
                <a href="#atd-tab-cabins">Cabins</a>
                <a href="#atd-tab-decks">Decks</a>
            </div>

            <div class="atd-cfi-tabs__contents" data-atd-cfi-tabs-target="contents">
				<?php if ( ! empty( $atdSpecial ) ): ?>
                    <div id="atd-tab-offer">
                        <p>
                            Valid from
							<?php echo $atdSpecial->getValidFrom()->format( get_option( 'date_format' ) ); ?>
                            until
							<?php echo $atdSpecial->getValidTo()->format( get_option( 'date_format' ) ); ?>
                        </p>
                        <br>
                        <p><?php echo nl2br( $atdSpecial->getInclusions() ); ?></p>
						<?php if ( ! empty( $atdSpecial->getConditions() ) ): ?>
                            <br>
                            <p>
                                <small>
                                    <strong>Terms &amp; Conditions</strong><br>
									<?php echo nl2br( $atdSpecial->getConditions() ); ?>
                                </small>
                            </p>
						<?php endif; ?>
                    </div>
				<?php endif; ?>
                <div id="atd-tab-overview">
					<?php if ( $atdDeparture->getCruise()->hasImage() ): ?>
                        <div class="atd-cfi__float-end atd-cfi__ml-2 atd-cfi__mb-2 atd-cfi__mw-40">
                            <a data-action="atd-cfi-popover#image"
                               href="<?php echo $atdDeparture->getCruise()->getImage(); ?>">
                                <img class="atd-cfi__img-fluid"
                                     src="<?php echo $atdDeparture->getCruise()->getImage(); ?>" alt="Map">
                            </a>
                        </div>
					<?php endif; ?>

					<?php $the_content = apply_filters( 'the_content', get_the_content() );
					if ( ! empty( $the_content ) ): ?>
						<?php echo $the_content; ?>
					<?php else: ?>
						<?php echo $atdDeparture->getCruise()->getBriefDescription(); ?>
					<?php endif; ?>
                </div>
                <div id="atd-tab-itinerary"
                     data-controller="atd-cfi-ajax-results"
                     data-atd-cfi-ajax-results-endpoint-value="/wp-json/atd/cfi/v1/<?php echo ! empty( $atdSpecial ) ? 'special-departure' : 'departure'; ?>/<?php echo ! empty( $atdSpecial ) ? $atdSpecial->getDepartureId() : $atdDeparture->getId(); ?>/itinerary">
                    <div data-atd-cfi-ajax-results-target="results">
                        <div class="spinner-loader"></div>
                    </div>
                </div>
                <div id="atd-tab-cruise-line">
					<?php while ( $cruiseLinePost->have_posts() ): $cruiseLinePost->the_post(); ?>
						<?php atd_cf_get_template_part( 'content/cruise-line', 'overview' ); ?>
					<?php endwhile;
					wp_reset_postdata(); ?>
                </div>
				<?php while ( $shipPost->have_posts() ): $shipPost->the_post(); ?>
                    <div id="atd-tab-ship">
						<?php atd_cf_get_template_part( 'content/ship', 'overview' ); ?>
                    </div>
                    <div id="atd-tab-cabins">
						<?php atd_cf_get_template_part( 'content/ship', 'cabins', [
							'departureId' => $atdDeparture->getId(),
							'prices'      => ! is_null( $atdSpecial ) && $atdSpecial->getSpecialPrices()->count() > 0
								? $atdSpecial->getSpecialPrices()
								: $atdDeparture->getCruisePrices()
						] ); ?>
                    </div>
                    <div id="atd-tab-decks">
						<?php atd_cf_get_template_part( 'content/ship', 'decks' ); ?>
                    </div>
				<?php endwhile;
				wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
</div>