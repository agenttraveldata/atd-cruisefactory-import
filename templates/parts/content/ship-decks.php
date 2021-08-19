<div class="atd-cfi-ar">
	<?php if ( $decks = atd_cf_get_post_attached_images( get_the_ID(), 'deck' ) ): ?>
		<?php foreach ( $decks as $image ) : ?>
            <div class="atd-cfi-ar__col">
                <div class="atd-cfi-ar-col__img">
                    <a data-action="atd-cfi-popover#image" href="<?php echo wp_get_attachment_image_url( $image['id'], 'full-size' ); ?>">
                        <img src="<?php echo wp_get_attachment_thumb_url( $image['id'] ); ?>" alt="">
                    </a>
                </div>
                <div class="atd-cfi-ar-col__details">
                    <h4><?php echo $image['name']; ?></h4>
					<?php echo $image['description']; ?>
                </div>
            </div>
		<?php endforeach; ?>
	<?php else: ?>
        <p>No deck details are currently available for this ship.</p>
	<?php endif; ?>
</div>