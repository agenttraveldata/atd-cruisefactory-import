<div class="atd-cfi-ar__col">
    <div class="atd-cfi-ar-col__img">
		<?php the_post_thumbnail( 'medium' ); ?>
    </div>
    <div class="atd-cfi-ar-col__details">
        <h4><?php the_title(); ?></h4>
		<?php echo get_the_excerpt(); ?>
        <div class="atd-cfi-ar-col-details__btn"><a href="<?php echo get_permalink(); ?>">View More</a></div>
    </div>
</div>