<?php global $atdShip; /** @var array $args */ ?>
<div class="atd-cfi-ar">
	<?php if ( $atdShip->getCabins()->count() > 0 ): ?>
		<?php $atdShip->getCabins()->map( function ( $c ) use ( $args ) {
			if ( $price = $args['prices']->filter( fn( $p ) => $p->getCabin()->getId() === $c->getId() )->first() ) {
				$c->setPrice( $price->getPrice() );
			}
		} ); ?>
		<?php foreach (
			$atdShip->getCabins()->usort( function ( $a, $b ) {
				if ( ! $a->hasPrice() ) {
					return 1;
				}
				if ( ! $b->hasPrice() ) {
					return - 1;
				}

				return $a->getPrice() <=> $b->getPrice();
			} ) as $cabin
		): ?>
            <div class="atd-cfi-ar__col atd-cfi-ar-col__double">
                <div class="atd-cfi-ar-col__block">
					<?php if ( ! empty( $cabin->hasImage() ) || ! empty( $cabin->hasPhoto() ) ): ?>
                        <div class="atd-cfi-ar-col__img">
							<?php if ( ! empty( $cabin->hasImage() ) ): ?>
                                <a data-action="atd-cfi-popover#image" href="<?php echo $cabin->getImage(); ?>">
                                    <img src="<?php echo $cabin->getImage(); ?>" alt="">
                                </a>
							<?php endif; ?>
							<?php if ( ! empty( $cabin->hasPhoto() ) ): ?>
                                <a data-action="atd-cfi-popover#image" href="<?php echo $cabin->getPhoto(); ?>">
                                    <img src="<?php echo $cabin->getPhoto(); ?>" alt="">
                                </a>
							<?php endif; ?>
                        </div>
					<?php endif; ?>
                    <div class="atd-cfi-ar-col__details">
                        <h4><?php echo $cabin->getName(); ?></h4>
						<?php if ( isset( $args['prices'] ) && isset( $args['type'] ) ): ?>
                            <div class="atd-cfi__cabins-price">
								<?php if ( ! empty( $args['prices'] ) && $args['prices']->count() > 0 ): ?>
									<?php if ( $price = $args['prices']->filter( fn( $p ) => $p->getCabin()->getId() === $cabin->getId() )->first() ): ?>
                                        <span class="atd-cfi__cabins-price__price-from">
                                    from <?php echo is_object( $price->getCurrency() ) ? $price->getCurrency()->getSign() : $price->getCurrency(); ?><?php echo number_format( $price->getPrice() ); ?>
                                    <span class="atd-cfi__cabins-price__price-from-price">
                                        <?php if ( ! $price->isSinglePrice() ): ?>twin share<?php else: ?>solo<?php endif; ?>
                                    </span>
                                </span>

                                        <a href="<?php echo get_permalink( get_option( ATD_CF_XML_ENQUIRY_PAGE_ID_FIELD ) ); ?>?departure_id=<?php echo $args['departureId']; ?>&departure_type=<?php echo $args['type']; ?>&pax=<?php echo $price->isSinglePrice() ? 'single' : 'twin'; ?>&cabin_price=<?php echo $price->getId(); ?>"
                                           class="atd-cfi__btn atd-cfi__btn-enquire">Enquire</a>
									<?php else: ?>
                                        <span class="atd-cfi-cabins-price__sold-out">Sold out</span>
									<?php endif; ?>
								<?php else: ?>
                                    <a href="<?php echo get_permalink( get_option( ATD_CF_XML_ENQUIRY_PAGE_ID_FIELD ) ); ?>?departure_id=<?php echo $args['departureId']; ?>&departure_type=<?php echo $args['type']; ?>&pax=twin&request_cabin=<?php echo $cabin->getId(); ?>"
                                       class="atd-cfi__btn atd-cfi__btn-enquire">Enquire</a>
								<?php endif; ?>
                            </div>
						<?php endif; ?>
						<?php echo apply_filters( 'the_content', $cabin->getDescription() ); ?>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
	<?php else: ?>
        <p>No cabin details are currently available for this ship.</p>
	<?php endif; ?>
</div>