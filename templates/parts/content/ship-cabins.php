<?php global $atdShip; ?>
<div class="atd-cfi-ar">
	<?php if ( $atdShip->getCabins()->count() > 0 ): ?>
		<?php foreach ( $atdShip->getCabins() as $cabin ) : ?>
            <div class="atd-cfi-ar__col atd-cfi-ar-col__double">
                <div class="atd-cfi-ar-col__img">
					<?php if ( ! empty( $cabin->getImage() ) ): ?>
                        <a data-action="atd-cfi-popover#image" href="<?php echo $cabin->getImage(); ?>">
                            <img src="<?php echo $cabin->getImage(); ?>" alt="">
                        </a>
					<?php endif; ?>
					<?php if ( ! empty( $cabin->getPhoto() ) ): ?>
                        <a data-action="atd-cfi-popover#image" href="<?php echo $cabin->getPhoto(); ?>">
                            <img src="<?php echo $cabin->getPhoto(); ?>" alt="">
                        </a>
					<?php endif; ?>
                </div>
                <div class="atd-cfi-ar-col__details">
                    <h4><?php echo $cabin->getName(); ?></h4>
					<?php echo apply_filters( 'the_content', $cabin->getDescription() ); ?>
                </div>
            </div>
		<?php endforeach; ?>
	<?php else: ?>
        <p>No cabin details are currently available for this ship.</p>
	<?php endif; ?>
</div>