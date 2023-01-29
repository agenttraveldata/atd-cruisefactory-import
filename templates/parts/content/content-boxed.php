<div class="atd-cfi-ar__col">
    <div class="atd-cfi-ar-col__img">
		<?php if ( has_post_thumbnail() ): ?>
			<?php the_post_thumbnail( 'medium' ); ?>
		<?php elseif ( ( $entity = match ( get_post_type() ) {
				\ATD\CruiseFactory\Post\Ship::$postType => $GLOBALS['atdShip'],
				\ATD\CruiseFactory\Post\CruiseLine::$postType => $GLOBALS['atdCruiseLine'],
				\ATD\CruiseFactory\Post\Destination::$postType => $GLOBALS['atdDestination'],
				default => false
			} ) && is_object( $entity ) && method_exists( $entity, 'hasImage' ) && $entity->hasImage() ): ?>
            <img src="<?php echo $entity->getImage(); ?>" alt="">
		<?php endif; ?>
    </div>
    <div class="atd-cfi-ar-col__details">
        <h4><?php the_title(); ?></h4>
		<?php echo get_the_excerpt(); ?>
        <div class="atd-cfi-ar-col-details__btn"><a href="<?php echo get_permalink(); ?>">View More</a></div>
    </div>
</div>