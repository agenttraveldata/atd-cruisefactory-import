<?php global $atdFactory, $atdDeparture, $atdSpecial; ?>

<div class="atd-cfi-sr__row">
    <?php if ( has_post_thumbnail() ): ?>
        <?php $resultImage = get_the_post_thumbnail_url( get_the_ID(), 'large' ); ?>
    <?php else: ?>
        <?php $resultImage = $atdDeparture->getCruise()->hasMap() ? $atdDeparture->getCruise()->getMap() : ''; ?>
    <?php endif; ?>
    <a data-action="atd-cfi-popover#image" class="atd-cfi-sr-row__img" href="<?php echo $resultImage; ?>" style="background-image: url('<?php echo $resultImage; ?>');"></a>
    <div class="atd-cfi-sr-row__details atd-cfi__flex-1">
        <div class="atd-cfi-sr-row-details__title">
            <h4><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h4>
        </div>
        <p><?php the_excerpt(); ?></p>
    </div>
    <div class="atd-cfi-sr-row__actions">
        <img class="atd-cfi-sr-row-actions__logo" src="data:<?php echo $atdDeparture->getCruise()->getCruiseLine()->getLogoType(); ?>;base64,<?php echo base64_encode( $atdDeparture->getCruise()->getCruiseLine()->getLogoData() ); ?>" alt="">
        <div class="atd-cfi-sr-row-actions__price">
			<?php if ( ! empty( $atdSpecial ) || $atdDeparture->getCruisePrices()->count() > 0 ): ?>
                From:
                <div>
					<?php echo ! empty( $atdSpecial )
						? '<sup>' . $atdSpecial->getCurrency()->getSign() . '</sup>' . number_format( $atdSpecial->getStartPrice() )
						: ( $atdDeparture->getCruisePrices()->count() > 0
							? '<sup>' . $atdDeparture->getCruisePrices()->get( 0 )->getCurrency() . '</sup>' . number_format( $atdDeparture->getCruisePrices()->get( 0 )->getPriceDouble() )
							: ''
						);
					?>
                </div>
			<?php else: ?>
                <div>Request Price</div>
			<?php endif; ?>
        </div>
        <a href="<?php the_permalink(); ?>" class="atd-cfi-sr-row-actions__btn">View Itinerary</a>
    </div>
</div>