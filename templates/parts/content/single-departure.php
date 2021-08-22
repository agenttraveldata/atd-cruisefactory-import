<?php

global $atdFactory, $atdDeparture, $atdSpecial;
$cruiseLinePost = atd_cf_get_post_by_meta_value( 'cruise-line', $atdDeparture->getCruise()->getCruiseLine()->getId(), true );
$shipPost       = atd_cf_get_post_by_meta_value( 'ship', $atdDeparture->getCruise()->getShip()->getId(), true );

?>
<h2><?php the_title(); ?></h2>
<div class="atd-cfi__cols" data-controller="atd-cfi-popover">
    <div class="atd-cfi-cols__column">
        <div class="atd-cfi-departure__logo">
            <img class="atd-cfi__img-fluid atd-cfi__mb-2" src="data:<?php echo $atdDeparture->getCruise()->getCruiseLine()->getLogoType(); ?>;base64,<?php echo base64_encode( $atdDeparture->getCruise()->getCruiseLine()->getLogoData() ); ?>" alt="Cruise Line">
            <p><?php echo $atdDeparture->getCruise()->getDuration(); ?> nights onboard
                <a href="<?php echo get_permalink( $shipPost->post->ID ); ?>">
					<?php echo $atdDeparture->getCruise()->getShip()->getName(); ?>
                </a> from
                <a href="<?php echo get_permalink( $cruiseLinePost->post->ID ); ?>">
					<?php echo $atdDeparture->getCruise()->getCruiseLine()->getName(); ?>
                </a> departing <?php echo $atdDeparture->getSailingDate()->format( 'd M Y' ); ?>
            </p>
        </div>
        <div class="atd-cfi-departure__pricing atd-cfi__mt-2" data-controller="atd-cfi-toggle-element" data-atd-cfi-toggle-element-prefix-value="atd-cfi-departure-price-">
            <form action="<?php echo get_permalink( get_option( ATD_CF_XML_ENQUIRY_PAGE_ID_FIELD ) ); ?>" method="get">
                <input type="hidden" name="departure_id" value="<?php echo $atdSpecial ? $atdSpecial->getDepartureId() : $atdDeparture->getId(); ?>">
                <input type="hidden" name="departure_type" value="<?php echo $atdSpecial ? 'special' : 'cruise'; ?>">
                <div class="atd-cfi-departure-pricing__prices">
					<?php if ( $atdSpecial ): ?>
                        <h3>Special Pricing</h3>
                        <h4 class="atd-cfi__mb-2">
                            <small>From</small>
                            <small><?php echo $atdFactory->getCurrency()->getSign(); ?></small><?php echo number_format( $atdSpecial->getStartPrice() ); ?>
                            <small>pp twin share</small>
                        </h4>
						<?php atd_cf_get_template_part( 'content/departure', 'special-pricing' ); ?>
					<?php elseif ( $atdDeparture->getCruisePrices()->count() > 0 ): ?>
                        <h3>Pricing</h3>
                        <h4 class="atd-cfi__mb-2">
                            <small>From</small>
                            <small><?php echo $atdFactory->getCurrency()->getSign(); ?></small><?php echo number_format( $atdDeparture->getCruisePrices()->get( 0 )->getPriceDouble() ); ?>
                            <small>pp twin share</small>
                        </h4>
						<?php atd_cf_get_template_part( 'content/departure', 'cruise-pricing' ); ?>
					<?php else: ?>
                        <h4>Request Price</h4>
                        <div class="atd-cfi__mb-2">
                            <label for="atd-cfi-departure-price-request">Cabin type</label>
                            <select name="request_price" class="atd-cfi__input" id="atd-cfi-departure-price-request">
                                <option value="inside">Inside</option>
                                <option value="outside">Outside</option>
                                <option value="balcony">Balcony</option>
                                <option value="suite">Suite</option>
                            </select>
                        </div>
					<?php endif; ?>
                </div>
                <div class="atd-cfi-departure-pricing__buttons">
                    <button type="submit">Continue</button>
                </div>
            </form>
        </div>
    </div>
    <div class="atd-cfi-cols__column atd-cfi-cols-column-2">
        <div class="atd-cfi__tabs" data-controller="atd-cfi-tabs">
            <div class="atd-cfi-tabs__anchors" data-atd-cfi-tabs-target="anchors">
                <a href="#atd-tab-overview">Overview</a>
                <a href="#atd-tab-itinerary">Itinerary</a>
                <a href="#atd-tab-cruise-line">Cruise Line</a>
                <a href="#atd-tab-ship">Ship</a>
                <a href="#atd-tab-cabins">Cabins</a>
                <a href="#atd-tab-decks">Decks</a>
            </div>

            <div class="atd-cfi-tabs__contents" data-atd-cfi-tabs-target="contents">
                <div id="atd-tab-overview">
                    <div class="atd-cfi__float-end atd-cfi__ml-2 atd-cfi__mb-2 atd-cfi__mw-40">
                        <a data-action="atd-cfi-popover#image" href="<?php echo get_the_post_thumbnail_url(); ?>">
                            <img class="atd-cfi__img-fluid" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="">
                        </a>
                    </div>

					<?php the_content(); ?>
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
						<?php atd_cf_get_template_part( 'content/ship', 'cabins' ); ?>
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