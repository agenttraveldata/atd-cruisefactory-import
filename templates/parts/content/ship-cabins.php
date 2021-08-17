<div class="atd-cfi-ar">
	<?php foreach ( atd_cf_get_post_attached_images( get_the_ID(), 'cabin' ) as $image ) : ?>
        <div class="atd-cfi-ar__col atd-cfi-ar-col__double">
            <div class="atd-cfi-ar-col__img">
				<?php if ( ! empty( $image['image'] ) ): ?>
                    <a data-action="atd-cfi-popover#image" href="<?php echo wp_get_attachment_image_url( $image['image'], 'full-size' ); ?>">
                        <img src="<?php echo wp_get_attachment_thumb_url( $image['image'] ); ?>" alt="">
                    </a>
				<?php endif; ?>
				<?php if ( ! empty( $image['photo'] ) ): ?>
                    <a data-action="atd-cfi-popover#image" href="<?php echo wp_get_attachment_image_url( $image['photo'], 'full-size' ); ?>">
                        <img src="<?php echo wp_get_attachment_thumb_url( $image['photo'] ); ?>" alt="">
                    </a>
				<?php endif; ?>
            </div>
            <div class="atd-cfi-ar-col__details">
                <h4><?php echo $image['name']; ?></h4>
				<?php echo apply_filters('the_content', $image['description']); ?>
            </div>
        </div>
	<?php endforeach; ?>
</div>